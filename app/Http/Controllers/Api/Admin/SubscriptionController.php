<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\AppUser;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Review;
use App\Models\Subscription;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Concerns\ToArray;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {

        if ($request->ajax()) {
            $data = Subscription::with('user')->whereNull('deleted_at');
            return Datatables::of($data)
                ->addColumn('user_name', function ($row) {
                    return $row->user->name ?? 'N/A';
                })
                ->editColumn('payment_type', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Online</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-primary"> Cash</small>';
                })
                ->editColumn('payment_status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Paid</small>' : ($row['payment_status'] == 2 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Failed</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-warning"> Pending</small>');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Approved</small>' : ($row['status'] == 2 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Rejected</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-warning"> Pending</small>');
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(104, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.subscriptions.edit', $row['id']) . '">Edit</a>';
                    }
                    if (Helper::userCan(104, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }
                    if (Helper::userAllowed(104)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })

                ->orderColumn('id', function ($query, $order) {
                    $query->orderBy('id', $order);
                })
                ->rawColumns(['action', 'status', 'user_name','payment_type', 'payment_status'])
                ->make(true);
        }
        return view('admin.subscriptions.index');
    }

    public function add(): View
    {
        $users = AppUser::orderBy('id', 'desc')->get()->toArray();
        return view('admin.subscriptions.add', compact('users'));
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'                   => ['required'],
            'price'                     => ['required'],
            'payment_type'              => ['required'],
            'payment_status'            => ['required'],
            'status'                    => ['required'],
        ]);

        $data = [...$validated];
        $data['start_date']=now()->toDateString();
        $data['end_date']=now()->addYear()->toDateString();
        $data['transaction_id']=uniqid(12);
        Subscription::create($data);
        return to_route('admin.subscriptions')->withSuccess('Subscription Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $subscription = Subscription::find($id);
        if (!$subscription) {
            return to_route('admin.subscriptions')->withError('Subscription Not Found..!!');
        }
        $users = AppUser::orderBy('id', 'desc')->get()->toArray();
        return view('admin.subscriptions.edit', compact('subscription', 'users'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $subscription = Subscription::find($id);
        if (!$subscription) {
            return to_route('admin.subscriptions')->withError('Subscription Not Found..!!');
        }

        $data = $request->validate([
            'user_id'                   => ['required'],
            'price'                     => ['required'],
            'payment_type'              => ['required'],
            'payment_status'            => ['required'],
            'status'                    => ['required'],
        ]);

        $subscription->update($data);
        return to_route('admin.subscriptions')->withSuccess('Subscription Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Subscription(), $request->id);
    }
}
