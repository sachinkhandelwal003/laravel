<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\AppUser;
use App\Models\Cleaner;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\Vehicle;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Transaction::with('cleaner')->whereNull('deleted_at');
            return Datatables::of($data)

                ->addColumn('cleaner_name', function ($row) {
                    return $row->cleaner->name ?? 'N/A';
                })

                ->editColumn('type', function ($row) {
                    return $row['type'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Paid</small>' :  '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Recieved</small>';
                })

                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(116, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.transactions.edit', $row['id']) . '">Edit</a>';
                    }
                    if (Helper::userCan(116, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }
                    if (Helper::userAllowed(116)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })

                ->orderColumn('id', function ($query, $order) {
                    $query->orderBy('id', $order);
                })
                ->rawColumns(['action', 'cleaner_name','type'])
                ->make(true);
        }

        $cleaners = Cleaner::select('id', 'name')->get()->toArray();
        return view('admin.transactions.index', compact('cleaners'));
    }

    public function add(): View
    {

        $cleaners = Cleaner::select('id', 'name')->get()->toArray();

        return view('admin.transactions.add', compact('cleaners'));
    }

    public function save(Request $request): RedirectResponse
    {

        $validated = $request->validate([
            'cleaner_id'              => ['required'],
            'type'                    => ['required'],
            'amount'                  => ['required'],
        ]);

        $data = [...$validated];


        Transaction::create($data);
        return to_route('admin.transactions')->withSuccess('Transaction Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return to_route('admin.transactions')->withError('Transaction Not Found..!!');
        }
        $cleaners = Cleaner::select('id', 'name')->get()->toArray();

        return view('admin.transactions.edit', compact('transaction', 'cleaners'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return to_route('admin.transactions')->withError('Transaction Not Found..!!');
        }

        $data = $request->validate([
            'cleaner_id'              => ['required'],
            'type'                    => ['required'],
            'amount'                  => ['required'],
        ]);


        $transaction->update($data);
        return to_route('admin.transactions')->withSuccess('Transaction Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Transaction(), $request->id);
    }
}
