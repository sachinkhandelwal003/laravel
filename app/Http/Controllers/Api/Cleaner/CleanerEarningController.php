<?php

namespace App\Http\Controllers\Api\Cleaner;


use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\EmployeeLead;
use App\Models\EmployeeLeave;
use App\Models\Order;
use App\Models\CleanerEarning;
use App\Models\ServiceDay;
use App\Models\UserVehicle;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\CleanerEarn;


class CleanerEarningController extends Controller
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
    
        try {
            $user = Auth::user();
    
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            if (is_null($startDate) || is_null($endDate)) {
                $today = now()->toDateString();
                $startDate = $today;
                $endDate = $today;
            }

    
            $cleanerEarnings = CleanerEarn::where('cleaners_id', $user->id)
                ->whereBetween('clean_date', [$startDate, $endDate])
                ->groupBy('earning_id','car_name')
                ->selectRaw('earning_id,car_name, SUM(amount) as total_price, COUNT(*) as total_count')
                ->get();
            //    dd($cleanerEarnings);
    
            $data = $cleanerEarnings->map(function ($earning) {
                // dd($earning);
                $totalEarnings = $earning->total_count * $earning->total_price;
    
                return [
                    'car_name' => $earning->car_name,
                    'price' => $earning->total_price,
                    'total_count' => $earning->total_count,
                    'total_earnings' => $totalEarnings,
                ];
            });
    
            return response()->json([
                'status' => true,
                'message' => 'Booking Summary by Unit',
                'data' => $data,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve bookings: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
    


}
