<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\AppUser;

use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AppUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = AppUser::whereNull('deleted_at');
            return Datatables::of($data)


                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(104, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.app-users.edit', $row['id']) . '">Edit</a>';
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
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('admin.app_users.index');
    }

    public function add(): View
    {
        return view('admin.app_users.add',);
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'phone'         => ['required', 'unique:app_users,phone'],
            'email'         => ['required', 'email', 'unique:app_users,email'],
            'status'        => ['required', 'integer'],

        ]);

        $data = [...$validated];

        AppUser::create($data);
        return to_route('admin.app-users')->withSuccess('User Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $app_users = AppUser::find($id);
        if (!$app_users) {
            return to_route('admin.app-users')->withError('User Not Found..!!');
        }

        return view('admin.app_users.edit', compact('app_users'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $app_users = AppUser::find($id);
        if (!$app_users) {
            return to_route('admin.app-users')->withError('User Not Found..!!');
        }

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'phone'         => ['required', 'unique:app_users,phone,' . $app_users->id],
            'email'         => ['required', 'email', 'unique:app_users,email,' . $app_users->id],
            'status'        => ['required', 'integer'],
        ]);
     
        $app_users->update($data);
        return to_route('admin.app-users')->withSuccess('User Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new AppUser(), $request->id);
    }
}
