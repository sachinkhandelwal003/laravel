<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActiveDeals;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ActiveDealsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = ActiveDeals::select('id', 'offer_type', 'valid_date','discount','price','code','description', 'status', 'created_at');
            return Datatables::of($data)
                ->editColumn('image', function ($row) {
                    $btn = '<div class="img-group"><img class="" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(104, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.cms.edit', $row['id']) . '">Edit</a>';
                    }
                    if (Helper::userAllowed(104)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }
        return view('admin.activedeals.index');
    }

    public function add(): View
    {
        return view('admin.activedeals.add');
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'offer_type'   => ['required'],
            'valid_date'   => ['required', 'date'],
            'discount'     => ['required', 'numeric'],
            'price'        => ['required', 'numeric'],
            'description'  => ['required', 'string'],
            'status'       => ['required', 'boolean']
        ]);
    
        // Generate a unique code
        $validated['code'] = $this->generateUniqueCode();
    
        // Create Active Deal with generated code
        ActiveDeals::create($validated);
    
        return to_route('admin.active-deals')->withSuccess('Active Deals Added Successfully..!!');
    }
    
    /**
     * Generate a unique code.
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = 'DEAL-' . strtoupper(substr(md5(uniqid()), 0, 6)); // Example: "DEAL-ABC123"
        } while (ActiveDeals::where('code', $code)->exists());
    
        return $code;
    }

    public function edit($id): View|RedirectResponse
    {
        $cms = Cms::find($id);
        if (!$cms) {
            return to_route('admin.cms')->withError('Cms Not Found..!!');
        }
        return view('admin.cms.edit', compact('cms'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $cms = Cms::find($id);
        if (!$cms) {
            return to_route('admin.cms')->withError('Cms Not Found..!!');
        }

        $data = $request->validate([
            'title'         => ['required', 'string', 'max:200'],
            'description'   => ['required', 'string', 'max:10000'],
            'status'        => ['required', 'integer'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:5048']
        ]);

        if ($request->file('image')) {
            Helper::deleteFile($cms->image);
            $data['image'] = Helper::saveFile($request->file('image'), 'cms');
        }

        $cms->update($data);
        return to_route('admin.cms')->withSuccess('Cms Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Cms, $request->id);
    }
}
