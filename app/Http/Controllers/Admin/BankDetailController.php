<?php

namespace App\Http\Controllers\Admin;

use App\Models\BankDetail;
use App\Models\Cleaner;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class BankDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = BankDetail::select('id', 'bank_name', 'acouunt_no','ifsc_code','cleaner_id', 'status', 'created_at');
            return Datatables::of($data)
                ->editColumn('image', function ($row) {
                    $btn = '<div class="img-group"><img class="" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(116, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.bank-details.edit', $row['id']) . '">Edit</a>';
                    }
                    if (Helper::userAllowed(116)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }
        return view('admin.bankdetails.index');
    }

    public function add(): View
    {
        $cleaners = Cleaner::select('id', 'name')->get()->toArray();
        return view('admin.bankdetails.add', compact('cleaners'));
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name'         => ['required'],
            'acouunt_no'   => ['required'],
            'status'        => ['required'],
            'ifsc_code'         => ['required'],
            'cleaner_id'         => ['required']
        ]);


        BankDetail::create($validated);
        return to_route('admin.bank-details')->withSuccess('Bank Details Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $cms = BankDetail::find($id);
        if (!$cms) {
            return to_route('admin.bank-details')->withError('Bank Detils Not Found..!!');
        }

         $cleaners = Cleaner::select('id', 'name')->get()->toArray();
        return view('admin.bankdetails.edit', compact('cms','cleaners'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $cms = BankDetail::find($id);
        if (!$cms) {
            return to_route('admin.bank-details')->withError('Bank Details Not Found..!!');
        }

        $data = $request->validate([
            'bank_name'    => ['required'],
            'account_no'   => ['required'],
            'ifsc_code'    => ['required'],
            'cleaner_id'   => ['required'],
            'status'       => ['required']
        ]);

        $cms->update($data);

        return to_route('admin.bank-details')->withSuccess('Bank Details Updated Successfully..!!');
    }


    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Cms, $request->id);
    }
}
