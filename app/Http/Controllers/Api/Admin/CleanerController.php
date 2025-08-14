<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class CleanerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Cleaner::whereNull('deleted_at');
            return Datatables::of($data)

                ->editColumn('image', function ($row) {
                    $btn = '<div class="img-group"><img class="" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                
                ->editColumn('id_card', function ($row) {
                    $btn = '<div class="img-group"><img class="" src="' . asset('storage/' . $row['id_card']) . '" alt=""></div>';
                    return $btn;
                })

                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                
                ->editColumn('id_proof', function ($row) {
                    return $row['id_proof'] == 1 ? '<small class="badge fw-semi-bold rounded-pill id_proof badge-light-success"> Approve</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Disapprove</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(104, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.cleaners.edit', $row['id']) . '">Edit</a>';
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
                ->rawColumns(['action', 'status','image','id_card','id_proof'])
                ->make(true);
        }
        return view('admin.cleaners.index');
    }

    public function add(): View
    {
        return view('admin.cleaners.add',);
    }

    public function save(Request $request): RedirectResponse
    {

        // dd($request);
        $validated = $request->validate([
            'user_id'       => ['required'],
            'name'          => ['required'],
            'mobile'        => ['required'],
            'email'         => ['required'],
            'status'        => ['required'],
            'address'       => ['required'],
            'area'          => ['required'],
            'bank_name'     => ['required'],
            'account_no'    => ['required'],
            'ifsc_code'     => ['required'],
            'id_card'       => ['required'],
            'superviser'    => ['required'],
            'id_proof'      => ['required'],
            'performance'   => ['required'],
            'password'      => ['required', 'confirmed'],
            'image'         => ['required'],
        ]);
    
       
        $data = [...$validated];
        
        if ($request->file('image')) {
            $data['image'] = Helper::saveFile($request->file('image'), 'cleaners');
        }
    
        
         $data['password'] = $request->password;//Hash::make($request->password);

         $data['referral_code'] = 'REF' . date('YmdHis'); 
         $data['use_code'] = $request->use_code	;

    // dd($data);
        
        Cleaner::create($data);
    
        return to_route('admin.cleaners')->withSuccess('Cleaner Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $cleaner = Cleaner::find($id);
        if (!$cleaner) {
            return to_route('admin.cleaners')->withError('Cleaner Not Found..!!');
        }

        return view('admin.cleaners.edit', compact('cleaner'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $cleaner = Cleaner::find($id);
        if (!$cleaner) {
            return to_route('admin.cleaners')->withError('Cleaner Not Found..!!');
        }

        $data = $request->validate([
            'user_id'          => ['required', 'string', 'max:20'],
            'name'             => ['required', 'string', 'max:200'],
            'mobile'           => ['required', 'unique:cleaners,mobile,'.$id],
            'email'            => ['required', 'email', 'unique:cleaners,email,'.$id],
            'status'           => ['required', 'integer'],
            'address'          => ['required'],
            'area'             => ['required'],
            'image'            => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:5048'],
        ]);
       
        if ($request->file('image')) {
            Helper::deleteFile($cleaner->image);
            $data['image'] = Helper::saveFile($request->file('image'), 'cleaners');
        }
        if ($request->password) {
            // $passvalidate = $request->validate([
            //     'password'                => ['required',  'confirmed'],
            // ]);
            $data['password'] = $request->password;//Hash::make($request->password);
        }
 
        $cleaner->update($data);
        return to_route('admin.cleaners')->withSuccess('Cleaner Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Cleaner(), $request->id);
    }
}
