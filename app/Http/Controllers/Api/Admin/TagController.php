<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Tag::select('id', 'name', 'image', 'status');
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
                        $btn .= '<a class="dropdown-item" href="' . route('admin.tags.edit', $row['id']) . '">Edit</a>';
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
        return view('admin.tags.index');
    }

    public function add(): View
    {
        return view('admin.tags.add',);
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'status'        => ['required', 'integer'],
            'image'          => ['required', 'image', 'mimes:jpg,png,jpeg', 'max:5048']
        ]);

        $data = [...$validated, 'image' => 'tags/image.png'];
        if ($request->file('image')) {
            $data['image'] = Helper::saveFile($request->file('image'), 'tags');
        }

        Tag::create($data);
        return to_route('admin.tags')->withSuccess('Tag Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return to_route('admin.tags')->withError('Tag Not Found..!!');
        }

        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return to_route('admin.tags')->withError('Tag Not Found..!!');
        }

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'status'        => ['required', 'integer'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:5048']
        ]);

        if ($request->file('image')) {
            Helper::deleteFile($tag->image);
            $data['image'] = Helper::saveFile($request->file('image'), 'tags');
        }

        $tag->update($data);
        return to_route('admin.tags')->withSuccess('Tag Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Tag(), $request->id);
    }
}
