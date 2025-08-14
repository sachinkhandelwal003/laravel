<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\AddOn;
use App\Models\Banner;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AddOnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = AddOn::select('id', 'name', 'image', 'price','description');
            return Datatables::of($data)
                ->editColumn('image', function ($row) {
                    $btn = '<div class="img-group"><img class="" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(104, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.add-ons.edit', $row['id']) . '">Edit</a>';
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
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }
        return view('admin.add_on.index');
    }

    public function add(): View
    {
        return view('admin.add_on.add',);
    }

    public function save(Request $request): RedirectResponse
        {
            $validated = $request->validate([
                 'name'          => ['required', 'string', 'max:200'],
                'price'         => ['required'],
                 'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:5048'],
                 'description'  => ['required']
            ]);
    
            $data = [...$validated, 'image' => 'add_on/image.png'];
            if ($request->file('image')) {
                $data['image'] = Helper::saveFile($request->file('image'), 'add_on');
            }
    
            AddOn::create($data);
            return to_route('admin.add-ons')->withSuccess('AddOn Added Successfully..!!');
        }
    
    
 
    public function edit($id): View|RedirectResponse
    {
        $add_on = AddOn::find($id);
        if (!$add_on) {
            return to_route('admin.add-ons')->withError('AddOn Not Found..!!');
        }

        return view('admin.add_on.edit', compact('add_on'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $add_on = AddOn::find($id);
        if (!$add_on) {
            return to_route('admin.add-ons')->withError('AddOn Not Found..!!');
        }

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'price'         => ['required'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:5048'],
            'description'   => ['required']
        ]);

        if ($request->file('image')) {
            Helper::deleteFile($add_on->image);
            $data['image'] = Helper::saveFile($request->file('image'), 'add_on');
        }

        $add_on->update($data);
        return to_route('admin.add-ons')->withSuccess('AddOn Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new AddOn(), $request->id);
    }
}
