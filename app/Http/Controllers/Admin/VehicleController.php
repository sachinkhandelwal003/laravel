<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Vehicle;
use App\Models\CarPrice;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Vehicle::with('brand')->select('id', 'name', 'image', 'status', 'brand_id');
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
                    if (Helper::userCan(110, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.vehicles.edit', $row['id']) . '">Edit</a>';
                    }
                    if (Helper::userCan(110, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }
                    if (Helper::userAllowed(110)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->addColumn('brand_name', function ($row) {
                    return $row->brand->name;
                })
                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('name', $order);
                })
                ->rawColumns(['action', 'image', 'status', 'brand_name'])
                ->make(true);
        }
        return view('admin.vehicles.index');
    }

    public function add(): View
    {
        $brands = Brand::select('id', 'name')->get()->toArray();

        return view('admin.vehicles.add', compact('brands'));
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'status'        => ['required', 'integer'],
            'brand_id'    => ['required', 'integer'],
            'image'          => ['required', 'image', 'mimes:jpg,png,jpeg', 'max:5048']
        ]);

        $data = [...$validated, 'image' => 'vehicles/image.png'];
        if ($request->file('image')) {
            $data['image'] = Helper::saveFile($request->file('image'), 'vehicles');
        }

        Vehicle::create($data);
        return to_route('admin.vehicles')->withSuccess('Vehicle Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) {
            return to_route('admin.vehicles')->withError('Vehicle Not Found..!!');
        }
        $brands = Brand::select('id', 'name')->get()->toArray();
        return view('admin.vehicles.edit', compact('vehicle','brands'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) {
            return to_route('admin.vehicles')->withError('Vehicle Not Found..!!');
        }

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'status'        => ['required', 'integer'],
            'brand_id'      => ['required', 'integer'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:5048']
        ]);

        if ($request->file('image')) {
            Helper::deleteFile($vehicle->image);
            $data['image'] = Helper::saveFile($request->file('image'), 'vehicles');
        }

        $vehicle->update($data);
        return to_route('admin.vehicles')->withSuccess('Vehicle Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Vehicle(), $request->id);
    }














}
