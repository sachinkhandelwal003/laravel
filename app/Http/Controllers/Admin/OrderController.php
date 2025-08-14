<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\AppUser;
use App\Models\Cleaner;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Vehicle;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        
     
        if ($request->ajax()) {
            $data = Order::with('user', 'plan', 'vehicle', 'cleaner')->whereNull('deleted_at');
            return Datatables::of($data)

                ->addColumn('cleaner_name', function ($row) {
                    return $row->cleaner->name ?? 'N/A';
                })
                ->addColumn('user_name', function ($row) {
                    return $row->user->name;
                })
                ->addColumn('plan_name', function ($row) {
                    return $row->plan->name;
                })
                ->addColumn('vehicle_name', function ($row) {
                    return $row->vehicle->name;
                })
                ->editColumn('start_date', function ($row) {
                    if ($row['start_date'] != null) {
                        return date('d M, Y', strtotime($row['start_date']));
                    }
                    return "N/A";
                })
                ->editColumn('end_date', function ($row) {
                    if ($row['end_date'] != null) {
                        return date('d M, Y', strtotime($row['end_date']));
                    }
                    return "N/A";
                })
                ->addColumn('order_date', function ($row) {
                    if ($row['created_at'] != null) {
                        return date('d M, Y', strtotime($row['created_at']));
                    }
                    return "N/A";
                })

                ->editColumn('is_cleaner_assign', function ($row) {
                    if ($row['is_cleaner_assign'] == 0) {
                        $btn = '<button class="btn btn-sm btn-outline-info text-info assign" data-id="' . $row['id'] . '">Assign</button>';
                        return $btn;
                    }
                    return '<small class="badge fw-semi-bold rounded-pill badge-light-info"> Assigned</small>';
                })

                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Started</small>' : ($row['status'] == 2 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Completed</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-warning"> Pending</small>');
                })
                ->editColumn('payment_status', function ($row) {
                    return $row['payment_status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Paid</small>' : ($row['payment_status'] == 2 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Failed</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-warning"> Pending</small>');
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(104, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.orders.edit', $row['id']) . '">Edit</a>';
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
                ->rawColumns(['action', 'status', 'payment_status', 'order_date', 'is_cleaner_assign', 'user_name', 'plan_name', 'vehicle_name', 'cleaner_name'])
                ->make(true);
        }

        $cleaners = Cleaner::select('id', 'name')->get()->toArray();
        return view('admin.orders.index', compact('cleaners'));
    }

    public function add(): View
    {
        $plans = Plan::select('id', 'name')->get()->toArray();
        $users = AppUser::select('id', 'name')->get()->toArray();
        $vehicles = Vehicle::select('id', 'name')->get()->toArray();
        return view('admin.orders.add', compact('plans', 'users', 'vehicles'));
    }

    public function save(Request $request): RedirectResponse
    {

        $validated = $request->validate([
            'user_id'              => ['required'],
            'plan_id'              => ['required'],
            'vehicle_id'           => ['required'],
            'service_date'         => ['required'],
            'service_time'         => ['required'],
            'payment_type'         => ['required'],
            'payment_status'       => ['required'],
            'status'               => ['required'],
        ]);

        $data = [...$validated];
        if (!empty($request->address_id)) {
            $address = Address::find($request->address_id);
            $data['address_json'] = !empty($address) ? json_encode($address) : null;
        } else {
            return redirect()->back()->withError('Address Not Found..!!');
        }

        Order::create($data);
        return to_route('admin.orders')->withSuccess('Order Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $order = Order::find($id);
        if (!$order) {
            return to_route('admin.orders')->withError('Order Not Found..!!');
        }
        $address_id = !empty($order['address_json']) ? json_decode($order['address_json'], true)['id'] : null;
        $plans = Plan::select('id', 'name')->get()->toArray();
        $users = AppUser::select('id', 'name')->get()->toArray();
        $vehicles = Vehicle::select('id', 'name')->get()->toArray();

        return view('admin.orders.edit', compact('order', 'plans', 'users', 'vehicles', 'address_id'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $order = Order::find($id);
        if (!$order) {
            return to_route('admin.orders')->withError('Order Not Found..!!');
        }

        $data = $request->validate([
            'user_id'              => ['required'],
            'plan_id'              => ['required'],
            'vehicle_id'           => ['required'],
            'service_date'         => ['required'],
            'service_time'         => ['required'],
            'payment_type'         => ['required'],
            'payment_status'       => ['required'],
            'status'               => ['required'],
        ]);

        if (!empty($request->address_id)) {
            $address = Address::find($request->address_id);
            $data['address_json'] = !empty($address) ? json_encode($address) : null;
        } else {
            return redirect()->back()->withError('Address Not Found..!!');
        }

        $order->update($data);
        return to_route('admin.orders')->withSuccess('Order Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Order(), $request->id);
    }

    public function assign_cleaner(Request $request): JsonResponse
    {


        $order = Order::find($request->id);
        if (empty($order)) {
            return response()->json([
                'message' => 'Order Not Found..!!',
                'status' => false,
            ]);
        }

        if(empty($request->cleaner_id)) {
            return response()->json([
                'message' => 'Cleaner not found..!!',
                'status' => false,
            ]);
        }

        $order->cleaner_id = $request->cleaner_id;
        $order->is_cleaner_assign = 1;
        $order->save();

        return response()->json([
            'status' => true,
            'message' => 'Cleaner Assigned Successfully',
        ]);
    }
}
