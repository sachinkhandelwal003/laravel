<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Testimonial;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class TestimonialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Testimonial::select('id', 'name', 'video', 'status');
            return Datatables::of($data)
                ->editColumn('video', function ($row) {
                    $btn = '<div class="img-group"><video class="" src="' . asset('storage/' . $row['video']) . '" alt="" width="50" height="50"></video></div>';
                    return $btn;
                })

                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    // if (Helper::userCan(104, 'can_edit')) {
                    //     $btn .= '<a class="dropdown-item" href="' . route('admin.testimonials.edit', $row['id']) . '">Edit</a>';
                    // }
                    if (Helper::userCan(109, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }
                    if (Helper::userAllowed(109)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })

                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('name', $order);
                })
                ->rawColumns(['action', 'video', 'status'])
                ->make(true);
        }
        return view('admin.testimonials.index');
    }

    public function add(): View
    {
        return view('admin.testimonials.add',);
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'status'        => ['required', 'integer'],
            'video'          => ['required',  'max:5048']
        ]);

        $data = [...$validated, 'video' => 'testimonials/image.png'];
        if ($request->file('video')) {
            $data['video'] = Helper::saveFile($request->file('video'), 'testimonials');
        }

        Testimonial::create($data);
        return to_route('admin.testimonials')->withSuccess('Testimonial Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $testimonial = Testimonial::find($id);
        if (!$testimonial) {
            return to_route('admin.testimonials')->withError('Testimonial Not Found..!!');
        }

        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $testimonial = Testimonial::find($id);
        if (!$testimonial) {
            return to_route('admin.testimonials')->withError('Testimonial Not Found..!!');
        }

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:200'],
            'status'        => ['required', 'integer'],
            'video'         => [ 'mimes:mp4,webm,mkv,flv','max:5048']
        ]);

        if ($request->file('video')) {
            Helper::deleteFile($testimonial->video);
            $data['video'] = Helper::saveFile($request->file('video'), 'testimonials');
        }

        $testimonial->update($data);
        return to_route('admin.testimonials')->withSuccess('Testimonial Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Testimonial(), $request->id);
    }
}
