<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Address::with(['user'])->whereNull('deleted_at');
            return Datatables::of($data)

           ->addColumn('name', function ($row) {
                return $row->user ? $row->user->name : 'N/A';
            })

                ->editColumn('is_default', function ($row) {
                    return $row['is_default'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Yes</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> No</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';

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
                ->rawColumns(['action', 'is_default','name'])
                ->make(true);
        }
        return view('admin.addresses.index');
    }



    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Address(), $request->id);
    }

    public function get_address(Request $request): string
    {
        $user_id   = $request->user_id;
        $address_id    = $request->address_id??0;

        $html   = '<option value="">Select Address</option>';
        $addresses = Address::where('user_id', $user_id)->get()->map(function ($address) use ($address_id) {
            return '<option value="' . $address->id . '" ' . ($address_id == $address->id ? 'selected' : '') . '>' . $address->address . '</option > ';
        });

        return $html . $addresses->implode('');
    }
}
