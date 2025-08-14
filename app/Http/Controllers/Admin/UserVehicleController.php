<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\UserVehicle;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class UserVehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {


        if ($request->ajax()) {
            $data = UserVehicle::with(['user', 'vehicle', 'brands'])->whereNull('deleted_at');
            return Datatables::of($data)

                ->addColumn('name', function ($row) {
                    return $row->user->name ?? 'N/A';
                })
                ->addColumn('vehicle_name', function ($row) {
                    return $row->vehicle->name ?? 'N/A';
                })
                ->addColumn('brand_name', function ($row) {
                    return $row->brands->name ?? 'N/A';
                })

                ->editColumn('is_default', function ($row) {
                    return $row['is_default'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Yes</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> No</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    // if (Helper::userCan(117, 'can_edit')) {
                    //     $btn .= '<a class="dropdown-item" href="' . route('admin.addresses.edit', $row['id']) . '">Edit</a>';
                    // }
                    if (Helper::userCan(117, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }
                    if (Helper::userAllowed(117)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })

                ->orderColumn('id', function ($query, $order) {
                    $query->orderBy('id', $order);
                })
                ->rawColumns(['action', 'is_default', 'name', 'vehicle_name', 'brand_name'])
                ->make(true);
        }
        return view('admin.user_vehicles.index');
    }

    // public function add(): View
    // {
    //     return view('admin.addresses.add',);
    // }

    // public function save(Request $request): RedirectResponse
    // {
    //     $validated = $request->validate([
    //         'name'          => ['required', 'string', 'max:200'],
    //         'phone'         => ['required', 'unique:addresses,phone'],
    //         'email'         => ['required', 'email', 'unique:addresses,email'],
    //         'status'        => ['required', 'integer'],

    //     ]);

    //     $data = [...$validated];

    //     Address::create($data);
    //     return to_route('admin.addresses')->withSuccess('Address Added Successfully..!!');
    // }

    // public function edit($id): View|RedirectResponse
    // {
    //     $address = Address::find($id);
    //     if (!$address) {
    //         return to_route('admin.addresses')->withError('Address Not Found..!!');
    //     }

    //     return view('admin.addresses.edit', compact('address'));
    // }

    // public function update(Request $request, $id): RedirectResponse
    // {
    //     $address = Address::find($id);
    //     if (!$address) {
    //         return to_route('admin.addresses')->withError('Address Not Found..!!');
    //     }

    //     $data = $request->validate([
    //         'name'          => ['required', 'string', 'max:200'],
    //         'phone'         => ['required', 'unique:addresses,phone,' . $address->id],
    //         'email'         => ['required', 'email', 'unique:addresses,email,' . $address->id],
    //         'status'        => ['required', 'integer'],
    //     ]);


    //     $address->update($data);
    //     return to_route('admin.addresses')->withSuccess('Address Updated Successfully..!!');
    // }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new UserVehicle(), $request->id);
    }
}
