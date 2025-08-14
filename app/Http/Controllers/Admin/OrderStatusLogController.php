<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\AppUser;
use App\Models\Cleaner;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Plan;
use App\Models\ServiceDay;
use App\Models\Transaction;
use App\Models\Vehicle;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class OrderStatusLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = OrderStatusLog::with('cleaner')->whereNull('deleted_at');
            return Datatables::of($data)

                ->addColumn('cleaner_name', function ($row) {
                    return $row->cleaner->name ?? 'N/A';
                })

                ->editColumn('image', function ($row) {
                    $btn = '<div class="img-group"><a href="' . asset('storage/' . $row['image']) . '" target="_blank"><img class="" src="' . asset('storage/' . $row['image']) . '" alt=""></a></div>';
                    return $btn;
                })
                ->editColumn('date', function ($row) {
                    
                    return date('d M,Y', strtotime($row['date']));
                })
                
                ->editColumn('time', function ($row) {
                    
                    return date('h:i A', strtotime($row['time']));
                })

                ->editColumn('is_issue', function ($row) {
                    return $row['is_issue'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Yes</small>' :  '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> No</small>';
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' :  '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })

                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(104, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.order-status-logs.edit', $row['id']) . '">Edit</a>';
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
                ->rawColumns(['action', 'cleaner_name', 'image','is_issue','status'])
                ->make(true);
        }

        $cleaners = Cleaner::select('id', 'name')->get()->toArray();
        return view('admin.order_status_logs.index', compact('cleaners'));
    }

    public function add(): View
    {
        $cleaners = Cleaner::select('id', 'name')->get()->toArray();
        $service_days = ServiceDay::select('id')->get()->toArray();

        return view('admin.order_status_logs.add', compact('cleaners', 'service_days'));
    }

    public function save(Request $request): RedirectResponse
    {
        
        $validated = $request->validate([
            'cleaner_id'                => ['required'],
            'service_day_id'            => ['required'],
            'date'                      => ['required'],
            'time'                      => ['required'],
            'is_issue'                  => ['required'],
            'status'                    => ['required'],
            'comments'                  => ['required'],
            'image'                     => ['required','image', 'mimes:jpg,png,jpeg', 'max:5048'],
        ]);

        $data = [...$validated, 'image' => 'brands/image.png'];
        if ($request->file('image')) {
            $data['image'] = Helper::saveFile($request->file('image'), 'order_status_logs');
        }


        OrderStatusLog::create($data);
        return to_route('admin.order-status-logs')->withSuccess('OrderStatusLog Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $order_status_log = OrderStatusLog::find($id);
        if (!$order_status_log) {
            return to_route('admin.order-status-logs')->withError('OrderStatusLog Not Found..!!');
        }
        $cleaners = Cleaner::select('id', 'name')->get()->toArray();
        $service_days = ServiceDay::select('id')->get()->toArray();

        return view('admin.order_status_logs.edit', compact('order_status_log', 'cleaners','service_days'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $order_status_log = OrderStatusLog::find($id);
        if (!$order_status_log) {
            return to_route('admin.order-status-logs')->withError('OrderStatusLog Not Found..!!');
        }

        $data = $request->validate([
            'cleaner_id'                => ['required'],
            'service_day_id'            => ['required'],
            'date'                      => ['required'],
            'time'                      => ['required'],
            'is_issue'                  => ['required'],
            'status'                    => ['required'],
            'comments'                  => ['required'],
            'image'                     => ['nullable','image', 'mimes:jpg,png,jpeg', 'max:5048'],
        ]);

        if ($request->file('image')) {
            Helper::deleteFile($order_status_log->image);
            $data['image'] = Helper::saveFile($request->file('image'), 'order_status_logs');
        }

        $order_status_log->update($data);
        return to_route('admin.order-status-logs')->withSuccess('OrderStatusLog Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new OrderStatusLog(), $request->id);
    }
}
