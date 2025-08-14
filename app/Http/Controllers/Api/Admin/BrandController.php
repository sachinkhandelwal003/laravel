<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Brand::select('id', 'name', 'logo', 'status','is_popular');
            return Datatables::of($data)
                ->editColumn('logo', function ($row) {
                    $btn = '<div class="img-group"><img class="" src="' . asset('storage/' . $row['logo']) . '" alt=""></div>';
                    return $btn;
                })
             
                ->editColumn('is_popular', function ($row) {
                    return $row['is_popular'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Yes</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> No</small>';
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(104, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.brands.edit', $row['id']) . '">Edit</a>';
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
                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('name', $order);
                })
                ->rawColumns(['action', 'logo', 'status', 'is_popular'])
                ->make(true);
        }
        return view('admin.brands.index');
    }

    public function add(): View
    {
        return view('admin.brands.add');
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'status'        => ['required', 'integer'],
            'is_popular'    => ['required', 'integer'],
            'logo'          => ['required','image', 'mimes:jpg,png,jpeg', 'max:5048'],
            'brand_type'        => ['required'],

        ]);

        $data = [...$validated, 'logo' => 'brands/image.png'];
        if ($request->file('logo')) {
            $data['logo'] = Helper::saveFile($request->file('logo'), 'brands');
        }

        Brand::create($data);
        return to_route('admin.brands')->withSuccess('Brand Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return to_route('admin.brands')->withError('Brand Not Found..!!');
        }
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return to_route('admin.brands')->withError('Brand Not Found..!!');
        }

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'status'        => ['required', 'integer'],
            'is_popular'    => ['required', 'integer'],
            'logo'          => ['image', 'mimes:jpg,png,jpeg', 'max:5048'],
            'brand_type'    => ['required'],

        ]);

        if ($request->file('logo')) {
            Helper::deleteFile($brand->logo);
            $data['logo'] = Helper::saveFile($request->file('logo'), 'brands');
        }

        $brand->update($data);
        return to_route('admin.brands')->withSuccess('Brand Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Brand, $request->id);
    }
}
