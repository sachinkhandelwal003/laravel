<?php

namespace App\Http\Controllers\Admin;

use App\Models\Discount;
use App\Models\Plan;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class DiscountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Discount::select('id', 'door_step_fee', 'magic_wash_discount', 'status', 'created_at','plateform_fee','gst','plan_id');
            return Datatables::of($data)

              ->addColumn('plans_name', function ($row) {
                    $ids = !empty($row['plan_id']) ? explode(',', $row['plan_id']) : [];
                    if (!empty($ids)) {
                        $all_names = Plan::whereIn('id', $ids)->pluck('name')->toArray();
                        return implode(',', $all_names);
                    }
                    return '';
                })

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
                    if (Helper::userCan(115, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.discount.edit', $row['id']) . '">Edit</a>';
                    }
                     if (Helper::userCan(115, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }
                    if (Helper::userAllowed(115)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'status','plans_name'])
                ->make(true);
        }
        return view('admin.discount.index');
    }

     public function add(): View
    {
        $plans = Plan::active()->get();
        return view('admin.discount.add', compact('plans'));
    }


       public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'door_step_fee'         => ['required'],
            'magic_wash_discount'   => ['required'],
            'status'                => ['required'],
            'plateform_fee'         => ['required'],
            'gst'                   => ['required'],
            'plan_id'               => ['required', 'array'],
            'plan_id.*'             => ['exists:plans,id'],
        ]);


        $validated['plan_id'] = implode(',', $validated['plan_id']);

        Discount::create($validated);
        return to_route('admin.discount')->withSuccess('Discount Added Successfully..!!');
    }


    public function edit($id): View|RedirectResponse
    {
        $cms = Discount::find($id);
        if (!$cms) {
            return to_route('admin.discount')->withError('Discount Not Found..!!');
        }
        return view('admin.discount.edit', compact('cms'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $cms = Discount::find($id);
        if (!$cms) {
            return to_route('admin.discount')->withError('Discount Not Found..!!');
        }

        $data = $request->validate([
             'door_step_fee'         => ['required'],
            'magic_wash_discount'   => ['required'],
            'status'        => ['required'],
            'plateform_fee'         => ['required'],
            'gst' =>  ['required']
        ]);


        $cms->update($data);
        return to_route('admin.discount')->withSuccess('Discount Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Discount, $request->id);
    }
}
