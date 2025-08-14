<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\AppUser;
use App\Models\Cleaner;
use App\Models\CleaningLog;
use App\Models\Order;
use App\Models\Plan;
use App\Models\ServiceDay;
use App\Models\Transaction;
use App\Models\Vehicle;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ServiceDayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = ServiceDay::with('cleaner', 'user')->whereNull('deleted_at');
            return Datatables::of($data)

                ->addColumn('cleaner_name', function ($row) {
                    return $row->cleaner->name ?? 'N/A';
                })
                ->addColumn('user_name', function ($row) {
                    return $row->user->name ?? 'N/A';
                })
                ->editColumn('date', function ($row) {
                    return date('d M, Y', strtotime($row['date'])) ?? 'N/A';
                })

                ->editColumn('is_full_cleaning', function ($row) {
                    return $row['is_full_cleaning'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Yes</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> No</small>';
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Completed</small>' : ($row['status'] == 2 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Not Completed</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-warning"> Pending</small>');
                })

                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(104, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.service-days.edit', $row['id']) . '">Edit</a>';
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
                ->rawColumns(['action', 'cleaner_name', 'status', 'user_name', 'is_full_cleaning'])
                ->make(true);
        }


        return view('admin.service_days.index');
    }

    public function add(): View
    {
        $orders = Order::select('id', 'order_number')->get()->toArray();
        $cleaners = Cleaner::select('id', 'name')->get()->toArray();

        return view('admin.service_days.add', compact('cleaners', 'orders'));
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cleaner_id'              => ['required'],
            'order_id'                    => ['required'],
            'date'                  => ['required'],
            'is_full_cleaning'                  => ['required'],
            'status'                  => ['required'],
        ]);
        $already_exist = ServiceDay::where('order_id', $validated['order_id'])
            ->where('date', $validated['date'])
            ->first();
        if ($already_exist) {
            return redirect()->back()->withInput()->withError('ServiceDay Already Exist..!!');
        }

        $order = Order::where('id', $validated['order_id'])->first();
        if (empty($order)) {
            return redirect()->back()->withInput()->withError('Order Not Found..!!');
        }

        $address = !empty($order['address']) ? json_decode($order['address']) : null;

        $data = [...$validated];
        $data['user_id'] = $order['user_id'];
        $data['address'] = $address['address'] ?? 'N/A';
        $data['from_time'] = now()->toTimeString();
        $data['to_time'] = now()->toTimeString();


        ServiceDay::create($data);
        return to_route('admin.service-days')->withSuccess('ServiceDay Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $service_day = ServiceDay::find($id);
        if (!$service_day) {
            return to_route('admin.service-days')->withError('ServiceDay Not Found..!!');
        }
        $orders = Order::select('id', 'order_number')->get()->toArray();
        $cleaners = Cleaner::select('id', 'name')->get()->toArray();

        return view('admin.service_days.edit', compact('service_day', 'cleaners', 'orders'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $service_day = ServiceDay::find($id);
        if (!$service_day) {
            return to_route('admin.service-days')->withError('ServiceDay Not Found..!!');
        }

        $data = $request->validate([
            'cleaner_id'                  => ['required'],
            'order_id'                    => ['required'],
            'date'                        => ['required'],
            'is_full_cleaning'            => ['required'],
            'status'                      => ['required'],
        ]);
        $order = Order::where('id', $data['order_id'])->first();
        if (empty($order)) {
            return redirect()->back()->withInput()->withError('Order Not Found..!!');
        }

        $address = !empty($order['address']) ? json_decode($order['address']) : null;

        $data['user_id'] = $order['user_id'];
        $data['address'] = $address['address'] ?? 'N/A';
        $data['from_time'] = now()->toTimeString();
        $data['to_time'] = now()->toTimeString();

        $service_day->update($data);
        return to_route('admin.service-days')->withSuccess('ServiceDay Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new ServiceDay(), $request->id);
    }
}
