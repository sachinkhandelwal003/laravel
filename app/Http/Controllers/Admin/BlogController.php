<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Blog::whereNull('deleted_at');
            return Datatables::of($data)
                ->editColumn('image', function ($row) {
                    $btn = '<div class="img-group"><img class="" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('icon', function ($row) {
                    $btn = '<div class="img-group"><img class="" src="' . asset('storage/' . $row['icon']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(118, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.blogs.edit', $row['id']) . '">Edit</a>';
                    }
                    if (Helper::userCan(118, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }
                    if (Helper::userAllowed(118)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })

                ->orderColumn('id', function ($query, $order) {
                    $query->orderBy('id', $order);
                })
                ->rawColumns(['action', 'status', 'image','icon'])
                ->make(true);
        }
        return view('admin.blogs.index');
    }

    public function add(): View
    {

        return view('admin.blogs.add',);
    }

    public function save(Request $request): RedirectResponse
    {
        // dd($request->all());
        $validated = $request->validate([
            'title'                    => ['required', 'string', 'max:200'],
            'description'             => ['required'],
            'status'                  => ['required'],
            'image'                   => ['required', 'image', 'mimes:jpg,png,jpeg', 'max:5048'],
            'icon'                   => ['required', 'image', 'mimes:jpg,png,jpeg', 'max:5048'],

        ]);

        $data = [...$validated, 'image' => 'blogs/image.png', 'icon' => 'blogs/icon.png'];
        if ($request->file('image')) {
            $data['image'] = Helper::saveFile($request->file('image'), 'blogs');
        }
        if ($request->file('icon')) {
            $data['icon'] = Helper::saveFile($request->file('icon'), 'blogs');
        }
        $data['key_details'] = json_encode($request->key_details)??'';

        Blog::create($data);
        return to_route('admin.blogs')->withSuccess('Blog Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return to_route('admin.blogs')->withError('Blog Not Found..!!');
        }

        return view('admin.blogs.edit', compact('blog',));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return to_route('admin.blogs')->withError('Blog Not Found..!!');
        }

        $data = $request->validate([
            'title'                    => ['required', 'string', 'max:200'],
            'description'             => ['required'],
            'status'                  => ['required'],
            'image'                   => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:5048'],
            'icon'                   => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:5048'],

        ]);


        if ($request->file('image')) {
            Helper::deleteFile($blog->image);
            $data['image'] = Helper::saveFile($request->file('image'), 'blogs');
        }
        if ($request->file('icon')) {
            Helper::deleteFile($blog->image);
            $data['icon'] = Helper::saveFile($request->file('icon'), 'blogs');
        }
        $data['key_details'] = json_encode($request->key_details)??'';
        $blog->update($data);
        return to_route('admin.blogs')->withSuccess('Blog Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Blog(), $request->id);
    }
}
