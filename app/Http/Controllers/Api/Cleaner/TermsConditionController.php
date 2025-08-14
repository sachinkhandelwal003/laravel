<?php

namespace App\Http\Controllers\Api\Cleaner;


use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Cms;
use App\Models\EmployeeLead;
use App\Models\EmployeeLeave;
use App\Models\Order;
use App\Models\CleanerEarning;
use App\Models\ServiceDay;
use App\Models\UserVehicle;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TermsConditionController extends Controller
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

    public function index(): JsonResponse
{
    try {
        $terms = Cms::where('title', 'Term and Condition')->first();

        if (!$terms) {
            return response()->json([
                'status' => false,
                'message' => 'Terms and Conditions not found',
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Terms and Conditions fetched successfully',
            'data' => [
                'title' => $terms->title,
                'description' => $terms->description
            ]
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong',
            'data' => []
        ], 500);
    }
}

}
