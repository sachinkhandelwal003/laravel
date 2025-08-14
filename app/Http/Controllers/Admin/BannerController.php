<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Banner::select('id', 'name', 'image', 'status');
            return Datatables::of($data)
                ->editColumn('image', function ($row) {
                    $btn = '<div class="img-group"><img class="" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })

                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(115, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }
                    if (Helper::userAllowed(115)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })

                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('name', $order);
                })
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }
        return view('admin.banners.index');
    }

    public function add(): View
    {
        return view('admin.banners.add',);
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'status'        => ['required', 'integer'],
            'image'          => ['required', 'image', 'mimes:jpg,png,jpeg', 'max:5048'],
            'banner_type'          => ['required'],

        ]);

        $data = [...$validated, 'image' => 'banners/image.png'];
        if ($request->file('image')) {
            $data['image'] = Helper::saveFile($request->file('image'), 'banners');
        }

        Banner::create($data);
        return to_route('admin.banners')->withSuccess('Banner Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return to_route('admin.banners')->withError('Banner Not Found..!!');
        }

        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return to_route('admin.banners')->withError('Banner Not Found..!!');
        }

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'status'        => ['required', 'integer'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:5048'],
            'banner_type'          => ['required'],

        ]);

        if ($request->file('image')) {
            Helper::deleteFile($banner->image);
            $data['image'] = Helper::saveFile($request->file('image'), 'banners');
        }

        $banner->update($data);
        return to_route('admin.banners')->withSuccess('Banner Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Banner(), $request->id);
    }
}
