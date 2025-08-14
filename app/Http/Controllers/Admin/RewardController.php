<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppUser;
use App\Models\Rewards;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class RewardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Rewards::select('id', 'user_id', 'reward_type', 'amount', 'created_at','code','status');
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
                    if (Helper::userCan(107, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.cms.edit', $row['id']) . '">Edit</a>';
                    }
                    if (Helper::userAllowed(107)) {
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
        return view('admin.rewards.index');
    }

    public function add(): View
    {
        $users = AppUser::select('id', 'phone')->get()->toArray();
        return view('admin.rewards.add',compact('users'));
    }

public function save(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'user_id'     => ['required'],
        'reward_type' => ['required'],
        'status'      => ['required'],
        'amount'      => ['required'],
        'valid_at'    => ['required']
    ]);

    $validated['code'] = $this->generateUniqueCode();

    Rewards::create($validated);

    return to_route('admin.rewards')->withSuccess('Rewards Added Successfully..!!');
}


/**
 * Generate a unique coupon code.
 */
private function generateUniqueCode(): string
{
    do {
        $code = strtoupper(substr(md5(uniqid()), 0, 8)); // Example: "A1B2C3D4"
    } while (Rewards::where('code', $code)->exists());

    return $code;
}

    public function edit($id): View|RedirectResponse
    {
        $cms = Cms::find($id);
        if (!$cms) {
            return to_route('admin.cms')->withError('Cms Not Found..!!');
        }
        return view('admin.cms.edit', compact('cms'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $cms = Cms::find($id);
        if (!$cms) {
            return to_route('admin.cms')->withError('Cms Not Found..!!');
        }

        $data = $request->validate([
            'title'         => ['required', 'string', 'max:200'],
            'description'   => ['required', 'string', 'max:10000'],
            'status'        => ['required', 'integer'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:5048']
        ]);

        if ($request->file('image')) {
            Helper::deleteFile($cms->image);
            $data['image'] = Helper::saveFile($request->file('image'), 'cms');
        }

        $cms->update($data);
        return to_route('admin.cms')->withSuccess('Cms Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Cms, $request->id);
    }
}
