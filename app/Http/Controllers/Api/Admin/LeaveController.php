<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Models\Leave;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {

        if ($request->ajax()) {
            $data = Leave::with(['cleaner'])->whereNull('deleted_at');
            return Datatables::of($data)
           
                ->addColumn('name', function ($row) {
                    return $row->cleaner->name ?? 'N/A';
                })

                ->editColumn('start_date', function ($row) {
                    return date('d M, Y', strtotime($row->start_date)) ?? 'N/A';
                })
                ->editColumn('end_date', function ($row) {
                    return date('d M, Y', strtotime($row->end_date)) ?? 'N/A';
                })

                ->editColumn('status', function ($row) {
                    if ($row['status'] == 0) {
                        $status_button = '<div class="btn-group">
                        <button class="btn btn-sm btn-success  status" data-status="1" data-id="' . $row['id'] . '">Approve</button>
                        <button class="btn btn-sm btn-danger  status" data-status="2" data-id="' . $row['id'] . '">Reject</button>
                        </div>';
                        return $status_button;
                    } else {
                        return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill  badge-light-success"> Approved</small>' : '<small class="badge fw-semi-bold rounded-pill  badge-light-danger"> Rejected</small>';
                    }
                })

                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(104, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('admin.leaves.edit', $row['id']) . '">Edit</a>';
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

                ->rawColumns(['action', 'status', 'name'])
                ->make(true);
        }
        return view('admin.leaves.index');
    }

    public function add(): View
    {
        $cleaners = Cleaner::select('id', 'name')->get()->toArray();
        return view('admin.leaves.add', compact('cleaners'));
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cleaner_id'    => ['required', 'exists:cleaners,id'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date'],
            'reason'        => ['required', 'string', 'max:1000'],
            'status'        => ['required', 'integer'],
        ]);

        $data = [...$validated];

        Leave::create($data);
        return to_route('admin.leaves')->withSuccess('Leave Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $leave = Leave::find($id);
        if (!$leave) {
            return to_route('admin.leaves')->withError('Leave Not Found..!!');
        }
        $cleaners = Cleaner::select('id', 'name')->get()->toArray();
        return view('admin.leaves.edit', compact('leave', 'cleaners'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $leave = Leave::find($id);
        if (!$leave) {
            return to_route('admin.leaves')->withError('Leave Not Found..!!');
        }

        $data =  $request->validate([
            'cleaner_id'    => ['required', 'exists:cleaners,id'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date'],
            'reason'        => ['required', 'string', 'max:1000'],
            'status'        => ['required', 'integer'],
        ]);

        $leave->update($data);
        return to_route('admin.leaves')->withSuccess('Leave Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Leave(), $request->id);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $id = $request->id;
        $status = $request->status;

        $leave = Leave::find($id);
        if (!$leave) {
            return response()->json([
                'status'  => false,
                'message' => 'Leave Not Found..!!',
            ]);
        }

        $leave->status = $status;
        $leave->save();

        return response()->json(
            [
                'status'  => true,
                'message' => 'Status Updated Successfully..!!'
            ]
        );
    }
}
