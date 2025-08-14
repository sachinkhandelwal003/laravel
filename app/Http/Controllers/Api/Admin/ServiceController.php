<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\BasePlan;
use App\Models\Plan;
use App\Models\Service;
use App\Models\Tag;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Service::whereNull('deleted_at');
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
                    if (Helper::userCan(104, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.services.edit', $row['id']) . '">Edit</a>';
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
                ->rawColumns(['action', 'status', 'image'])
                ->make(true);
        }
        return view('admin.services.index');
    }

    public function add(): View
    {
        $tags = Tag::select('id', 'name')->get()->toArray();
        return view('admin.services.add', compact('tags'));
    }

    public function save(Request $request): RedirectResponse
    {
        // dd($request->all());
        $validated = $request->validate([
            'name'                    => ['required', 'string', 'max:200'],
            'tags'                    => ['required', 'array', 'min:1'],
            'title'                   => ['required'],
            'detail'             => ['required'],
            'description'             => ['required'],
            'status'                  => ['required'],
            'image'                   => ['required', 'image', 'mimes:jpg,png,jpeg', 'max:5048'],

        ]);

        $data = [...$validated, 'image' => 'services/image.png'];
        if ($request->file('image')) {
            $data['image'] = Helper::saveFile($request->file('image'), 'services');
        }
        if (!empty($request['tags'])) {
            $data['tags'] = implode(',', $request['tags']);
        }else{
            $data['tags'] = '';
        }
        Service::create($data);
        return to_route('admin.services')->withSuccess('Service Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $service = Service::find($id);
        if (!$service) {
            return to_route('admin.services')->withError('Service Not Found..!!');
        }
        $tags = Tag::select('id', 'name')->get()->toArray();
        return view('admin.services.edit', compact('service', 'tags'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $service = Service::find($id);
        if (!$service) {
            return to_route('admin.services')->withError('Service Not Found..!!');
        }

        $data = $request->validate([
            'name'                    => ['required', 'string', 'max:200'],
            'tags'                    => ['required', 'array', 'min:1'],
            'title'                   => ['required'],
            'detail'                  => ['required'],
            'description'             => ['required'],
            'status'                  => ['required'],
            'image'                   => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:5048'],

        ]);


        if ($request->file('image')) {
            Helper::deleteFile($service->image);
            $data['image'] = Helper::saveFile($request->file('image'), 'services');
        }
        if (!empty($request['tags'])) {
            $data['tags'] = implode(',', $request['tags']);
        }else{
            $data['tags'] = '';
        }
        $service->update($data);
        return to_route('admin.services')->withSuccess('Service Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Service(), $request->id);
    }
}
