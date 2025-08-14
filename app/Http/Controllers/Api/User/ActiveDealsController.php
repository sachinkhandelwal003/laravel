<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\ActiveDeals;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helper\Helper;
use Carbon\Carbon; // ✅ Correct import

use Illuminate\Support\Facades\Auth;

class ActiveDealsController extends Controller
{
    protected $userId;
    protected $user;

    public function __construct()
    {
        $this->middleware(['auth:userApi']);
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::guard('userApi')->id();
            $this->user = Auth::guard('userApi')->user();
            return $next($request);
        });
    }

 
 public function index(): JsonResponse
    {
        $today = Carbon::today();

        $activeDealsCount = ActiveDeals::whereDate('valid_date', '>=', $today)->count();

        if ($activeDealsCount === 0) {
            return response()->json([
                'success' => true,
                'message' => [
                    'message' => 'This active deal is expired'
                ]
            ], 200);
        }

        $activeDeals = ActiveDeals::whereDate('valid_date', '>=', $today)->get()->map(function ($deal) {
            return [
                'id' => $deal->id,
                'offer_type' => $deal->offer_type,
                'valid_date' => $deal->valid_date,
                'discount' => $deal->discount,
                'price' => $deal->price,
                'code' => $deal->code,
                'description' => strip_tags($deal->description),
                'status' => $deal->status,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $activeDeals
        ], 200);
    }




    /**
     * Store a new booking
     */

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'start_date'         => 'required',
            'selected_date'      => 'required',
            'selectedtime_slots' => 'required',
            'plain_id'           => 'required',
            'car_id'             => 'required',
            'address_id'         => 'required',
            'payment_status'     => 'nullable',
        ]);

        $bookingData = $request->all();

        if (!is_array($request->selected_date)) {
            $request->merge(['selected_date' => explode(',', $request->selected_date)]);
        }

        $bookingData['selected_date'] = implode(',', $request->selected_date);

        $booking = Booking::create($bookingData);

        $responseData = [
            'status'  => true,
            'message' => 'Booking Created Successfully',
            'data'    => $booking,
        ];

        if ($booking->payment_status == 1) {
            $responseData['payment_status'] = 'Success';
        } elseif ($booking->payment_status == 2) {
            $responseData['payment_status'] = 'Failed';
        }

        return response()->json($responseData, 200);
    }



    /**
     * Show a single booking
     */
    public function show($id): JsonResponse
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'status'  => false,
                'message' => 'Booking Not Found',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Booking Details',
            'data'    => $booking,
        ], 200);
    }

    /**
     * Update a booking
     */
    public function update(Request $request, $id): JsonResponse
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'status'  => false,
                'message' => 'Booking Not Found',
            ], 404);
        }

        $request->validate([
            'start_date'        => 'sometimes',
            'selected_date'     => 'sometimes',
            'selectedtime_slots' => 'sometimes',
            'plain_id'          => 'sometimes',
            'car_id'            => 'sometimes',
            'address_id'        => 'sometimes',
            'payment_status'    => 'sometimes',
        ]);

        $booking->update($request->all());

        return response()->json([
            'status'  => true,
            'message' => 'Booking Updated Successfully',
            'data'    => $booking,
        ], 200);
    }

    /**
     * Delete a booking
     */
    public function destroy($id): JsonResponse
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'status'  => false,
                'message' => 'Booking Not Found',
            ], 404);
        }

        $booking->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Booking Deleted Successfully',
        ], 200);
    }

    public function subscriptiondetails($id): JsonResponse
    {
        $booking = Booking::with(['plan', 'vehicle', 'uservehicle', 'cleaners'])->find($id);
    
        if (!$booking) {
            return response()->json([
                'status'  => false,
                'message' => 'Booking not found',
                'data'    => null,
            ], 404);
        }
    
        $startDate = Carbon::parse($booking->start_date);
        $planExpireDate = $startDate->copy()->addMonth()->endOfMonth()->format('Y-m-d');
    
        $interiorDays = is_string($booking->selected_date)
            ? array_map(fn($date) => Carbon::parse(trim($date))->format('Y-m-d'), explode(',', $booking->selected_date))
            : ($booking->selected_date ?? []);
    
        $exteriorDays = [];
        for ($i = 3; $i < 6; $i++) {
            $exteriorDays[] = $startDate->copy()->addDays($i)->format('Y-m-d');
        }
    
     
        $notAtHome = [];
        if (is_array($booking->not_at_home)) {
            $notAtHome = array_map(fn($date) => Carbon::parse($date)->format('Y-m-d'), $booking->not_at_home);
        } elseif (is_string($booking->not_at_home)) {
            $decoded = json_decode($booking->not_at_home, true);
            if (is_array($decoded)) {
                $notAtHome = array_map(fn($date) => Carbon::parse($date)->format('Y-m-d'), $decoded);
            }
        }
    
        $formattedBooking = [
            'id'              => $booking->id,
            'start_date'      => $startDate->format('Y-m-d'),
            'plan_expire'     => $planExpireDate,
            'vehicle_name'    => $booking->vehicle ? $booking->vehicle->name : null,
            'interior_days'   => $interiorDays,
            'exterior_days'   => $exteriorDays,
            'not_at_home'     => $notAtHome, 
            'cleaners_name'   => $booking->cleaners ? $booking->cleaners->name : null,
            'cleaners_number' => $booking->cleaners ? $booking->cleaners->mobile : null,
            'vehicle_number'  => $booking->uservehicle ? $booking->uservehicle->vehicle_number : null,
            'vehicle_image'   => $booking->vehicle ? Helper::showImage($booking->vehicle->image) : null,
        ];
    
        return response()->json([
            'status'  => true,
            'message' => 'Booking details retrieved successfully',
            'data'    => $formattedBooking,
        ], 200);
    }




   public function updateNotAtHome(Request $request, $id)
{
    $request->validate([
        'not_at_home' => 'required', // Ensure it's a single date in Y-m-d format
    ]);

    $booking = Booking::findOrFail($id);

  
    $existingNotAtHome = is_array($booking->not_at_home)
        ? $booking->not_at_home
        : (is_string($booking->not_at_home) ? json_decode($booking->not_at_home, true) : []);

    if (!is_array($existingNotAtHome)) {
        $existingNotAtHome = [];
    }

    if (!in_array($request->not_at_home, $existingNotAtHome)) {
        $existingNotAtHome[] = $request->not_at_home;
    }

    // Store as a JSON string in the database
    $booking->not_at_home = json_encode($existingNotAtHome);
    $booking->save();

    return response()->json([
        'status'  => true,
        'message' => 'Not at home data updated successfully',
        'data'    => [
            'id'          => $booking->id,
            'not_at_home' => $existingNotAtHome, // Returns an array of dates
        ]
    ]);
}



    public function orderhistory(): JsonResponse
    {
        $bookings = Booking::with(['plan', 'vehicle', 'uservehicle'])->orderBy('id', 'desc')->get();

        $formattedBookings = $bookings->map(function ($booking) {

            $startDate = Carbon::parse($booking->start_date);


            $planExpireDate = $startDate->addMonth()->endOfMonth()->toDateString();

            return [
                'id'         => $booking->id,
                'start_date'         => $booking->start_date,
                'payment_status'     => $booking->payment_status == 1 ? 'Success' : 'Failed',
                'plan_name'      => $booking->plan ? $booking->plan->name : null,
                'price'      => $booking->plan ? $booking->plan->price : null,
                'vehicle_name'   => $booking->vehicle ? $booking->vehicle->name : null,
                'vehicle_number' => $booking->uservehicle ? $booking->uservehicle->vehicle_number : null,
                'vehicle_image'  => $booking->vehicle ? Helper::showImage($booking->vehicle->image) : null,
            ];
        });

        return response()->json([
            'status'  => $formattedBookings->isNotEmpty(),
            'message' => $formattedBookings->isNotEmpty() ? 'Booking List' : 'No Bookings Found',
            'data'    => $formattedBookings,
        ], 200);
    }
}
