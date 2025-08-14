<?php

namespace App\Http\Controllers\Api\Cleaner;


use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\UserVehicle;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    protected $userId;
    protected $user;

    public function __construct()
    {

        $this->middleware(['auth:cleanerApi']);
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::guard('cleanerApi')->id();
            $this->user = Auth::guard('cleanerApi')->user();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $leaves = Leave::with('cleaner')->where('cleaner_id', $this->userId)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        if (count($leaves) > 0) {
            $status_names = ['0' => 'Pending', '1' => 'Approved', '2' => 'Rejected'];
            $data = [];
            foreach ($leaves as $leave) {
                $data[] = [
                    'id' => $leave['id'],
                    'cleaner_id'      => $leave['cleaner_id'] ?? 'N/A',
                    'start_date'      => $leave['start_date'] ?? 'N/A',
                    'end_date'        => $leave['end_date'] ?? 'N/A',
                    'reason'          => $leave['reason'] ?? 'N/A',
                    'status'          => $leave['status'] ?? 'N/A',
                    'status_name'     => $status_names[$leave['status']] ?? 'N/A',
                ];
            }

            return response()->json([
                'status'    => true,
                'message'   => 'Leave List',
                'data'      => $data,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Leave Found',
                'data' => [],
            ], 200);
        }
    }


    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date'    => 'required',
            'end_date'       => 'required',
            'reason'         => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation Error",
                'errors' => $validator->errors(),
            ], 422);
        };
        $data = [...$validator->validated()];
        $data['cleaner_id'] = $this->userId;
        $data['status'] = 0;
        $leave = Leave::create($data);
        return response()->json([
            'status' => true,
            'message' => 'Leave Added Successfully.',
            'data' => $leave,
        ], 200);
    }

    public function show($slug): JsonResponse
    {
        $leave = Leave::where('id', $slug)
            ->where('cleaner_id', $this->userId)
            ->firstOrFail();
        if (!$leave) {
            return response()->json([
                'status' => false,
                'message' => 'Leave Not Found~!!!!',
                'data' => [],
            ], 404);
        }
        $status_names = ['0' => 'Pending', '1' => 'Approved', '2' => 'Rejected'];
        $data = [
            'id'              => $leave['id'],
            'cleaner_id'      => $leave['cleaner_id'] ?? 'N/A',
            'start_date'      => $leave['start_date'] ?? 'N/A',
            'end_date'        => $leave['end_date'] ?? 'N/A',
            'reason'          => $leave['reason'] ?? 'N/A',
            'status'          => $leave['status'] ?? 'N/A',
            'status_name'     => $status_names[$leave['status']] ?? 'N/A',
        ];
        return response()->json([
            'status' => true,
            'message' => 'Leave Found!!!!',
            'data' => $data,
        ], 200);
    }


    public function destroy(Request $request, $slug): JsonResponse
    {
        $leave = Leave::where('id', $slug)
            ->where('cleaner_id', $this->userId)
            ->where('status', 0)
            ->first();

        if (!$leave) {
            return response()->json([
                'status' => false,
                'message' => 'Leave Not Found',
                'data' => [],
            ], 404);
        }

        Helper::deleteRecord(new Leave, $slug);

        return response()->json([
            'status' => true,
            'message' => 'Leave Deleted',
            'data' => ['id' => $slug],
        ], 200);
    }
}
