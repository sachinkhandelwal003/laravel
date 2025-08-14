<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\AppUser;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Review;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Concerns\ToArray;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
    
        if ($request->ajax()) {
            $data = Review::with('order')->whereNull('deleted_at');
            return Datatables::of($data)
                ->addColumn('order_number', function ($row) {
                    return $row->order->order_number??'N/A';
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Approved</small>' : ($row['status'] == 2 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Rejected</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-warning"> Pending</small>');
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(104, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.reviews.edit', $row['id']) . '">Edit</a>';
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
                ->rawColumns(['action', 'status', 'order_number'])
                ->make(true);
        }
        return view('admin.reviews.index');
    }

    public function add(): View
    {
        $orders = Order::orderBy('id', 'desc')->get()->toArray();
        return view('admin.reviews.add', compact('orders'));
    }

    public function save(Request $request): RedirectResponse
    {
        
        $validated = $request->validate([
            'order_id'                   => ['required'],
            'rating'                     => ['required'],
            'comments'                   => ['required'],
            'status'                     => ['required'],
        ]);

        $order= Order::where('id', $validated['order_id'])->first();
        if (empty($order)) {
            return redirect()->back()->withInput()->withError('Order Not Found..!!');
        }
   
        $data = [...$validated];
        $data['user_id']=$order['user_id'];
        $data['plan_id']=$order['plan_id'];
       
        Review::create($data);
        return to_route('admin.reviews')->withSuccess('Review Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $review = Review::find($id);
        if (!$review) {
            return to_route('admin.reviews')->withError('Review Not Found..!!');
        }
        $orders = Order::orderBy('id', 'desc')->get()->toArray();
        return view('admin.reviews.edit', compact('review', 'orders'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $review = Review::find($id);
        if (!$review) {
            return to_route('admin.reviews')->withError('Review Not Found..!!');
        }

        $data = $request->validate([
            'order_id'                   => ['required'],
            'rating'                     => ['required'],
            'comments'                   => ['required'],
            'status'                     => ['required'],
        ]);

        $order= Order::where('id', $data['order_id'])->first();
        if (empty($order)) {
            return redirect()->back()->withInput()->withError('Order Not Found..!!');
        }

        $data['user_id']=$order['user_id'];
        $data['plan_id']=$order['plan_id'];

        $review->update($data);
        return to_route('admin.reviews')->withSuccess('Review Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Review(), $request->id);
    }
}
