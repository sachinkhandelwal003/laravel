<?php

namespace App\Http\Controllers\Api\Cleaner;


use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\EmployeeLead;
use App\Models\EmployeeLeave;
use App\Models\Order;
use App\Models\Plan;
use App\Models\ServiceDay;
use App\Models\Booking;

use App\Models\Transaction;
use App\Models\CleanerEarn;
use App\Models\CleanerEarning;

use App\Models\UserVehicle;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\BookingAdd;

class HomeController extends Controller
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




    public function index(Request $request): JsonResponse
{
    $date = $request->date ?? now()->format('Y-m-d');
    $user = Auth::user();

    // dd($user);
    // BookingAdd se fetch karo jahan cleaner ka ID match karta ho aur date bhi
    $bookingAddRecords = BookingAdd::with(['booking.user', 'booking.uservehicle', 'booking.plan', 'booking.address', 'booking.vehicle','booking.uservehicleid'])
        ->where('cleaners_id', $user->id)
        ->where(function ($query) use ($date) {
            $query->where('interior_days', $date)
                  ->orWhere('exterior_days', $date);
        })
        ->orderby('id','desc')
        ->get();

    if ($bookingAddRecords->count() > 0) {
        $filteredBookings = $bookingAddRecords->map(function ($bookingAdd) {
            $booking = $bookingAdd->booking;
// dd($booking);
            // Decode add_on
            $addonList = [];
            if (!empty($booking->add_on)) {
                $rawItems = explode('},{', trim($booking->add_on));
                foreach ($rawItems as $index => $item) {
                    if ($index === 0) $item = ltrim($item, '{');
                    if ($index === count($rawItems) - 1) $item = rtrim($item, '}');
                    $item = '{' . $item . '}';

                    preg_match_all('/(\w+):\s*([^,}]+)/', $item, $matches);
                    $keys = $matches[1];
                    $values = $matches[2];

                    $addonItem = [];
                    foreach ($keys as $i => $key) {
                        $addonItem[$key] = trim($values[$i], '" ');
                    }

                    if (!empty($addonItem)) {
                        $addonList[] = $addonItem;
                    }
                }
            }

            return [
                'id' => $bookingAdd->id,
                'start_date' => $booking->start_date,
                'selectedtime_slots' => $booking->selectedtime_slots,
                'cupon' => $booking->cupon,
                'not_at_home' => $booking->not_at_home,
                'cumplited' => $booking->cumplited,
                'day_type' => $bookingAdd->day_type,

                'status' => $bookingAdd->status,

                'date' => $bookingAdd->day_type === 'interior' ? $bookingAdd->exterior_days : $bookingAdd->interior_days,

              'user' => [
    'id' => $booking->user->id ?? 'N/A',
    'name' => $booking->user->name ?? 'N/A',
    'phone' => $booking->user->phone ?? 'N/A',
],

'Address' => [
    'id' => $booking->address->id ?? 'N/A',
    'latitude' => $booking->address->latitude ?? 'N/A',
    'longitude' => $booking->address->longitude ?? 'N/A',
    'address' => $booking->address->address ?? 'N/A',
],

'vehicle' => [
    'id' => $booking->uservehicle->id ?? 'N/A',
    'vehicle_number' => $booking->uservehicle->vehicle_number ?? 'N/A',
    'name' => $booking->vehicle->name ?? 'N/A',
    'parking_number' => $booking->uservehicle->parking_number ?? 'N/A',
],

'plan' => [
    'id' => $booking->plan->id ?? 'N/A',
    'name' => $booking->plan->name ?? 'N/A',
    'interior_days' => $booking->plan->interior_days ?? 'N/A',
    'exterior_days' => $booking->plan->exterior_days ?? 'N/A',
],

                'addon' => $addonList,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Service List',
            'data' => [
                'booking' => $filteredBookings,
            ],
        ], 200);
    }

    return response()->json([
        'status' => false,
        'message' => 'No Service Found',
        'data' => [],
    ], 200);
}


    public function indextt(Request $request): JsonResponse
    {
        $date = $request->date ?? now()->format('Y-m-d');
        $user = Auth::user();
        // dd($user);
    
        $bookings = Booking::with(['user', 'uservehicle', 'plan', 'Address', 'vehicle'])
            ->where('cleaners_id', $user->id)
            ->where(function ($query) use ($date) {
                $query->whereRaw("JSON_CONTAINS(selected_date, JSON_OBJECT('date', ?))", [$date])
                      ->orWhereRaw("JSON_CONTAINS(selected_date_2, JSON_OBJECT('date', ?))", [$date]);
            })
            ->orderBy('id', 'desc')
            ->get();
    
        if ($bookings->count() > 0) {
            $filteredBookings = $bookings->map(function ($booking) {
                $addonList = [];
    
                // Parse add_on string to structured array
                if (!empty($booking->add_on)) {
                    $rawItems = explode('},{', trim($booking->add_on));
                    foreach ($rawItems as $index => $item) {
                        if ($index === 0) $item = ltrim($item, '{');
                        if ($index === count($rawItems) - 1) $item = rtrim($item, '}');
                        $item = '{' . $item . '}';
    
                        preg_match_all('/(\w+):\s*([^,}]+)/', $item, $matches);
                        $keys = $matches[1];
                        $values = $matches[2];
    
                        $addonItem = [];
                        foreach ($keys as $i => $key) {
                            $addonItem[$key] = trim($values[$i], '" ');
                        }
    
                        if (!empty($addonItem)) {
                            $addonList[] = $addonItem;
                        }
                    }
                }
    
                // Decode both selected_date and selected_date_2
                $selectedDates = json_decode($booking->selected_date, true) ?? [];
                $selectedDates2 = json_decode($booking->selected_date_2, true) ?? [];
    
                return [
                    'id' => $booking->id,
                    'start_date' => $booking->start_date,
                    'selectedtime_slots' => $booking->selectedtime_slots,
                    'cupon' => $booking->cupon,
                    'not_at_home' => $booking->not_at_home,
                    'cumplited' => $booking->cumplited,
                    'interior_dates' => $selectedDates,
                    'exterior_dates' => $selectedDates2,
    
                    'user' => [
                        'id' => $booking->user->id,
                        'name' => $booking->user->name,
                        'phone' => $booking->user->phone,
                    ],
    
                    'Address' => [
                        'id' => $booking->Address->id,
                        'latitude' => $booking->Address->latitude,
                        'longitude' => $booking->Address->longitude,
                    ],
    
                    'vehicle' => [
                        'id' => $booking->uservehicle->id,
                        'vehicle_number' => $booking->uservehicle->vehicle_number,
                        'name' => $booking->vehicle->name,
                        'parking_number' => $booking->uservehicle->parking_number,
                    ],
    
                    'plan' => [
                        'id' => $booking->plan->id,
                        'name' => $booking->plan->name,
                        'interior_days' => $booking->plan->interior_days,
                        'exterior_days' => $booking->plan->exterior_days,
                    ],
    
                    'addon' => $addonList,
                ];
            });
    
            return response()->json([
                'status' => true,
                'message' => 'Service List',
                'data' => [
                    'booking' => $filteredBookings,
                ],
            ], 200);
        }
    
        return response()->json([
            'status' => false,
            'message' => 'No Service Found',
            'data' => [],
        ], 200);
    }
    


    public function update_status(Request $request): JsonResponse
{
    $user = Auth::user();

    $validated = Validator::make($request->all(), [
        'id'     => 'required',
        'date'   => 'required',
        'image'  => 'nullable|image|mimes:jpg,png,jpeg|max:5048',
        'reason' => 'nullable|string',
        'status' => 'required|in:0,1', // Assuming status is either 0 or 1
    ]);

    if ($validated->fails()) {
        return response()->json([
            'status'  => false,
            'message' => 'Validation Error',
            'errors'  => $validated->errors(),
            'data'    => [],
        ], 422);
    }

    $booking = BookingAdd::where('id', $request->id)
        ->where('cleaners_id', $user->id)
        ->first();

    if (!$booking) {
        return response()->json([
            'status'  => false,
            'message' => 'No Booking Found',
            'data'    => [],
        ], 404);
    }

    $price = null;
    $car_type = null;
    $earningid = null;

    $bookingData = CleanerEarning::where('unit', $booking->unit)->first();
    if ($bookingData) {
        $price     = $bookingData->price;
        $car_type  = $bookingData->car_type;
        $earningid = $bookingData->unit;
    }


    $path = null;
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('image'), $fileName);
        $path = 'image/' . $fileName;
    }


    $booking->image  = $path ?? $booking->image;
    $booking->reason = $request->input('reason');
    $booking->status = $request->status;
    $booking->save();

    if ($request->status === '1') {
        CleanerEarn::create([
            'booking_id'  => $booking->id,
            'cleaners_id' => $user->id,
            'clean_date'  => $request->date,
            'amount'      => $price ?? 0,
            'car_name'    => $car_type ?? 'N/A',
            'earning_id'  => $earningid ?? null,
        ]);
    }

    return response()->json([
        'status'  => true,
        'message' => 'Status Updated Successfully',
        'data'    => [],
    ], 200);
}


//     public function update_status(Request $request): JsonResponse
// {
//     $user = Auth::user();

//     $validated = Validator::make($request->all(), [
//         'id'    => 'required',
//         'date'  => 'required',
//         'image' => 'nullable|image|mimes:jpg,png,jpeg|max:5048',
//         'reason'=> 'nullable',
//         'status'=> 'required', 
//     ]);

//     if ($validated->fails()) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Validation Error',
//             'errors' => $validated->errors(),
//             'data' => [],
//         ], 422);
//     }

//     $Booking = BookingAdd::where('id', $request->id)
//         ->where('cleaners_id', $user->id)
//         ->first();

//     if (!$Booking) {
//         return response()->json([
//             'status' => false,
//             'message' => 'No Booking Found',
//             'data' => [],
//         ], 404);
//     }

//     $bookingData = CleanerEarning::where('unit', $Booking->unit)->first();
//     if ($bookingData) {
//         $price = $bookingData->price;
//         $car_type = $bookingData->car_type;
//         $earningid = $bookingData->unit;
//     }


//     if ($request->file('image')) {
//         $path = Helper::saveFile($request->file('image'), 'image');
//     }

//     $Booking->image = $path;
//     $Booking->reason = $request->input('reason') ?? null;
//     $Booking->status = $request->status;
//     $Booking->save();

//     if ($request->status === '1') {
//         CleanerEarn::create([
//             'booking_id' => $request->id,
//             'cleaners_id' => $user->id,
//             'clean_date' => $request->date,
//             'amount' => $price,
//             'car_name' => $car_type,
//             'earning_id' => $earningid,
//         ]);
//     }

//     return response()->json([
//         'status' => true,
//         'message' => 'Status Updated Successfully',
//         'data' => [],
//     ], 200);
// }

    
    

    public function transaction(): JsonResponse
    {
        try {
            // Get all transactions with created_at
            $transactions = Transaction::select('id', 'cleaner_id', 'amount', 'type', 'created_at')->get(); 
    
            // Calculate total paid amount (type = 2)
            $totalPaidAmount = $transactions->where('type', 1)->sum('amount');
              
            // Format each transaction
            $formatted = $transactions->map(function ($item) {
                return [
                    'id' => $item->id,
                    'cleaner_id' => $item->cleaner_id,
                    'amount' => $item->amount,
                    'type' => $item->type,
                    'created_at' => Carbon::parse($item->created_at)->format('d-m-Y')
                ];
            });
    
            // Final response
            return response()->json([
                'status' => true, 
                'message' => $transactions->isEmpty() ? 'No Transaction Found' : 'Transaction History',
                'data' => $formatted,
                'total_paid_amount' => number_format($totalPaidAmount, 2)
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve bookings',
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }



    
    


}
