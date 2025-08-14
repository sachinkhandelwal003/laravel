<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\BasePlan;
use App\Models\Plan;
use App\Models\Service;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class PlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Plan::with(['base_plan'])->whereNull('deleted_at');
            return Datatables::of($data)
                ->editColumn('base_plan_id', function ($row) {
                    return $row->base_plan->name??'N/A';
                })
                ->editColumn('image', function ($row) {
                    if ($row['image']) {
                        // Direct asset path approach
                        $imagePath = 'storage/' . $row['image'];
                        $btn = '<div class="img-group"><img src="' . asset($imagePath) . '" alt="Plan Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" onerror="this.src=\'' . asset('assets/img/img-not-found.png') . '\'"></div>';
                    } else {
                        $btn = '<div class="img-group"><img src="' . asset('assets/img/img-not-found.png') . '" alt="No Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"></div>';
                    }
                    return $btn;
                })
                ->editColumn('category_id', function ($row) {
                    $categories=['1'=>'Car Subscription','2'=>'Bike Subscription','3'=>'Scooty Subscription','4'=>'Other Subscription'];
                    return $categories[$row['category_id']]??'N/A';
                })
                ->editColumn('is_popular', function ($row) {
                    return $row['is_popular'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Yes</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> No</small>';
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(113, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.plans.edit', $row['id']) . '">Edit</a>';
                    }
                    if (Helper::userCan(113, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }
                    if (Helper::userAllowed(113)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })

                ->orderColumn('id', function ($query, $order) {
                    $query->orderBy('id', $order);
                })
                ->rawColumns(['action', 'status', 'image', 'base_plan_id', 'is_popular','category_id'])
                ->make(true);
        }
        return view('admin.plans.index');
    }

    public function add(): View
    {
        $base_plans = BasePlan::select('id', 'name')->get()->toArray();
        $services = Service::select('id', 'name')->get()->toArray();
        return view('admin.plans.add', compact('base_plans', 'services'));
    }

    public function save(Request $request): RedirectResponse
    {

        $validated = $request->validate([
            'category_id'             => ['required'],
            'name'                    => ['required', 'string', 'max:200'],
            'base_plan_id'            => ['required_if:category_id,4'],
            'price'                   => ['required'],
            'offer_price'             => ['required_if:category_id,4'],
            'duration'                => ['required_if:category_id,4'],
            'description'             => ['required_if:category_id,4'],
            'status'                  => ['required'],
            'rating'                  => ['required_if:category_id,4'],
            'rating_count'            => ['required_if:category_id,4'],
            'discount'                => ['required_if:category_id,4'],
            'image'                   => ['required'],
            'is_recommended'          => ['required_if:category_id,4'],
            'recommendation'          => ['required_if:is_recommended,1'],
            'is_popular'              => ['required'],
            'body_type'               => ['nullable'],
            'services'                => ['nullable', 'array'],
            'services.*'              => ['required_with:services', 'integer'],
            'interior_days'           => ['required_unless:category_id,4'],
            'exterior_days'           => ['required_unless:category_id,4'],
            // 'cleaning'                => ['nullable'],


        ]);

        $data = [...$validated, 'image' => 'plans/image.png'];
        if (!$request->hasFile('image')) {
            $data['image'] = 'plans/image.png';
        } else {
            $data['image'] = Helper::saveFile($request->file('image'), 'plans');
        }


        if ($request['is_recommended'] == 0) {
            $data['recommendation'] = '';
        }
        $data['services']      = !empty($request['services']) ?  implode(',', $request['services']) : '';
        // $data['interior_days'] = !empty($request['interior_days']) && $request['category_id'] != 4 ? $request['interior_days'] : 0;
        // $data['exterior_days'] = !empty($request['exterior_days']) && $request['category_id'] != 4 ? $request['exterior_days'] : 0;

        Plan::create($data);
        return to_route('admin.plans')->withSuccess('Plan Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return to_route('admin.plans')->withError('Plan Not Found..!!');
        }
        $base_plans = BasePlan::select('id', 'name')->get()->toArray();
        $services = Service::select('id', 'name')->get()->toArray();
        return view('admin.plans.edit', compact('plan', 'base_plans', 'services'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        //dd($request);
        $plan = Plan::find($id);
        if (!$plan) {
            return to_route('admin.plans')->withError('Plan Not Found..!!');
        }


        $data = $request->validate([
            'category_id'             => ['required'],
            'name'                    => ['required', 'string', 'max:200'],
            'base_plan_id'            => ['required_if:category_id,4'],
            'price'                   => ['required'],
            'offer_price'             => ['required_if:category_id,4'],
            'duration'                => ['required_if:category_id,4'],
            'description'             => ['required_if:category_id,4'],
            'status'                  => ['required'],
            'rating'                  => ['required_if:category_id,4'],
            'rating_count'            => ['required_if:category_id,4'],
            'discount'                => ['required_if:category_id,4'],
            'image'                   => ['nullable'],
            'is_recommended'          => ['required_if:category_id,4'],
            'recommendation'          => ['required_if:is_recommended,1'],
            'is_popular'              => ['required'],
            'services'                => ['nullable', 'array'],
            'services.*'              => ['required_with:services', 'integer'],
            'interior_days'           => ['required_unless:category_id,4'],
            'exterior_days'           => ['required_unless:category_id,4'],
        ]);


        if ($request->file('image')) {
            Helper::deleteFile($plan->image);
            $data['image'] = Helper::saveFile($request->file('image'), 'plans');
        }

        //dd($data['image']);
        if ($request['is_recommended'] == 0) {
            $data['recommendation'] = '';
        }

        $data['services'] = !empty($request['services']) ?  implode(',', $request['services']) : '';


        $data['interior_days'] = !empty($request['interior_days']) && $request['category_id'] != 4 ? $request['interior_days'] : 0;
        $data['exterior_days'] = !empty($request['exterior_days']) && $request['category_id'] != 4 ? $request['exterior_days'] : 0;

        $plan->update($data);
        return to_route('admin.plans')->withSuccess('Plan Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Plan(), $request->id);
    }
}
