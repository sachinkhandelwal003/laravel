<?php



namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helper\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;
use App\Models\BookingAdd;


class BookingController extends Controller

{

    protected $userId;

    protected $user;



    public function __construct()

    {

        $this->middleware(['auth:userApi']);
    }




    

    public function index(): JsonResponse

    {

        $user = Auth::user();

        // dd($user);

        $bookings = Booking::where('user_id', $user->id)

            ->with(['plan', 'vehicle', 'cleaners', 'uservehicle','uservehicleid'])

            ->orderBy('id', 'desc')

            ->get();

        // dd($bookings);



        $today = Carbon::today();



        $formattedBookings = $bookings->map(function ($booking) use ($today) {

            $parsedAddOns = [];



            if ($booking->add_on) {

                // Match all { ... } blocks in string

                preg_match_all('/\{.*?\}/', $booking->add_on, $matches);



                foreach ($matches[0] as $addOnStr) {

                    // Clean braces and split into key-value pairs

                    $fixedStr = str_replace(['{', '}'], '', $addOnStr);

                    $parts = explode(',', $fixedStr);



                    $addOn = [];

                    foreach ($parts as $part) {

                        if (strpos($part, ':') !== false) {

                            [$key, $value] = explode(':', $part, 2);

                            $addOn[trim($key)] = trim($value, " \t\n\r\0\x0B\"");
                        }
                    }



                    // Parse date and determine status

                    $date = isset($addOn['date']) ? Carbon::parse($addOn['date']) : null;

                    $addOn['status'] = $date && $date->lessThanOrEqualTo($today) ? 2 : 1;



                    $parsedAddOns[] = $addOn;
                }
            }



            return [

                'id'              => $booking->id,

                'active_status'   => $booking->active_status,

                'body_type'  => $booking->unit,

                'start_date'      => $booking->start_date,

                'selectedtime_slots'   => $booking->selectedtime_slots,


                'add_on'          => $parsedAddOns,

                'total_price'     => $booking->total_price,

                'payment_status'  => $booking->payment_status == 1 ? 'Success' : 'Failed',

                'plan_name'       => optional($booking->plan)->name,

                'cleaner_name'    => optional($booking->cleaners)->name,

                'vehicle_name'    => optional($booking->vehicle)->name,

                'vehicle_number'  => optional($booking->uservehicleid)->vehicle_number,

                'vehicle_image' => optional($booking->vehicle) ? Helper::showImage($booking->vehicle->image) : null,
                'service_type'  => $booking->service_type,



                // 'vehicle_image'   => optional($booking->vehicle) ? Helper::showImage($booking->vehicle->image) : null,

            ];
        });



        return response()->json([

            'status'  => $formattedBookings->isNotEmpty(),

            'message' => $formattedBookings->isNotEmpty() ? 'Booking List' : 'No Bookings Found',

            'data'    => $formattedBookings,

        ], 200);
    }



    public function store(Request $request): JsonResponse
{
    $user = Auth::user();

    $validatedData = $request->validate([
        'start_date'         => 'required|date',
        'selected_date'      => 'nullable', // Interior dates
        'add_on'             => 'nullable',
        'selectedtime_slots' => 'nullable',
        'plain_id'           => 'required|integer',
        'car_id'             => 'required|integer',
        'address_id'         => 'required|integer',
        'total_price'        => 'nullable|numeric',
        'payment_status'     => 'nullable|integer',
        'cupon'              => 'nullable|string',
        'unit'               => 'nullable|integer',
        'user_vehicle_id'               => 'nullable',
        'service_type'               => 'nullable',


    ]);

    try {
        $plan = Plan::withTrashed()->find($validatedData['plain_id']);
        if (!$plan) {
            return response()->json(['status' => false, 'message' => 'Plan not found.'], 404);
        }

        $startDate = \Carbon\Carbon::parse($validatedData['start_date']);
        $endDate = $startDate->copy()->addDays(29); // 30 days total

        $interiorDaysCount = (int) ($plan->interior_days ?? 0);
        $exteriorDaysPerWeek = (int) ($plan->exterior_days ?? 0);
        $exteriorDaysCount = $exteriorDaysPerWeek * 4;

        // Handle selected interior dates
        $selectedInteriorDatesRaw = is_array($request->selected_date)
            ? $request->selected_date
            : explode(',', $request->selected_date ?? '');

        $interiorDates = array_map(function ($date) {
            return ['date' => trim($date), 'status' => '0'];
        }, $selectedInteriorDatesRaw);

        $interiorDateStrings = array_map(
            fn($d) => \Carbon\Carbon::parse($d['date'])->format('Y-m-d'),
            $interiorDates
        );

        // Weekday map (0=Sun .. 6=Sat)
        $weekdayMap = [
            1 => [6],
            2 => [1, 6],
            3 => [1, 4, 6],
            4 => [1, 3, 0, 6],
            5 => [0, 1, 3, 5, 6],
            6 => [0, 1, 2, 4, 5, 6],
        ];

        $allowedWeekdays = $weekdayMap[$exteriorDaysPerWeek] ?? [];

        $exteriorDates = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dayOfWeek = $currentDate->dayOfWeek;

            if ($dayOfWeek === 3) {
                $currentDate->addDay(); // skip Wednesday
                continue;
            }

            if (
                in_array($dayOfWeek, $allowedWeekdays) &&
                !in_array($currentDate->toDateString(), $interiorDateStrings)
            ) {
                $exteriorDates[] = [
                    'date' => $currentDate->toDateString(),
                    'status' => '0',
                ];
            }

            if (count($exteriorDates) >= ($exteriorDaysCount - count($interiorDates))) {
                break;
            }

            $currentDate->addDay();
        }

        $addOns = is_array($request->add_on)
            ? $request->add_on
            : explode(',', $request->add_on ?? '');

        $booking = Booking::create([
            'user_id'             => $user->id,
            'cupon'               => $validatedData['cupon'],
            'start_date'          => $validatedData['start_date'],
            'selected_date'       => json_encode($interiorDates),
            'selected_date_2'     => json_encode($exteriorDates),
            'add_on'              => implode(',', $addOns),
            'selectedtime_slots'  => $validatedData['selectedtime_slots'],
            'plain_id'            => $validatedData['plain_id'],
            'car_id'              => $validatedData['car_id'],
            'address_id'          => $validatedData['address_id'],
            'total_price'         => $validatedData['total_price'],
            'payment_status'      => $validatedData['payment_status'] ?? 0,
            'unit'                => $validatedData['unit'] ?? 0,
            'user_vehicle_id'     => $validatedData['user_vehicle_id'],
            'service_type'     => $validatedData['service_type'],


        ]);

        foreach ($interiorDates as $interiorDate) {
            BookingAdd::create([
                'booking_id'         => $booking->id,
                'user_id'            => $user->id,
                'day_type'           => 'interior',
                'interior_days'      => $interiorDate['date'],
                'cleaners_id'        => null,
                'unit'               => $validatedData['unit'] ?? null,
                'image'              => null,
                'reason'             => null,
                'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
                'plain_id'           => $validatedData['plain_id'],
                'add_on'             => implode(',', $addOns),
            ]);
        }

        foreach ($exteriorDates as $exteriorDate) {
            BookingAdd::create([
                'booking_id'         => $booking->id,
                'user_id'            => $user->id,
                'day_type'           => 'exterior',
                'exterior_days'      => $exteriorDate['date'],
                'cleaners_id'        => null,
                'unit'               => $validatedData['unit'] ?? null,
                'image'              => null,
                'reason'             => null,
                'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
                'plain_id'           => $validatedData['plain_id'],
                'add_on'             => implode(',', $addOns),
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Booking Created Successfully',
            'data'    => [
                ...$booking->toArray(),
                'interior_dates' => $interiorDates,
                'exterior_dates' => $exteriorDates,
                'add_on_array'   => $addOns,
            ],
            'payment_status' => match ($booking->payment_status) {
                1 => 'Success',
                2 => 'Failed',
                default => 'Pending',
            },
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Failed to create booking: ' . $e->getMessage()
        ], 400);
    }
}



//     public function store(Request $request): JsonResponse
// {
//     $user = Auth::user();

//     $validatedData = $request->validate([
//         'start_date'         => 'required|date',
//         'selected_date'      => 'nullable', // Interior dates
//         'add_on'             => 'nullable',
//         'selectedtime_slots' => 'nullable',
//         'plain_id'           => 'required|integer',
//         'car_id'             => 'required|integer',
//         'address_id'         => 'required|integer',
//         'total_price'        => 'nullable|numeric',
//         'payment_status'     => 'nullable|integer',
//         'cupon'              => 'nullable|string',
//         'unit'               => 'nullable|integer',
//     ]);

//     try {
//         $plan = Plan::withTrashed()->find($validatedData['plain_id']);

//         if (!$plan) {
//             return response()->json(['status' => false, 'message' => 'Plan not found.'], 404);
//         }

//         $startDate = \Carbon\Carbon::parse($validatedData['start_date']);
//         $endDate = $startDate->copy()->addDays(29); // 30 days
//         // $interiorDaysCount = (int) $plan->interior_days;
//         // $exteriorDaysPerWeek = (int) $plan->exterior_days;
//         $interiorDaysCount = (int) ($plan->interior_days ?? 0);
// $exteriorDaysPerWeek = (int) ($plan->exterior_days ?? 0);

//         $exteriorDaysCount = $exteriorDaysPerWeek * 4;

//         // Handle interior dates
//         $selectedInteriorDatesRaw = is_array($request->selected_date)
//             ? $request->selected_date
//             : explode(',', $request->selected_date ?? '');

//         $interiorDates = array_map(function ($date) {
//             return ['date' => trim($date), 'status' => '0'];
//         }, $selectedInteriorDatesRaw);

//         $interiorDateStrings = array_map(
//             fn($d) => \Carbon\Carbon::parse($d['date'])->format('Y-m-d'),
//             $interiorDates
//         );

//         // Weekday mapping (0=Sun ... 6=Sat)
//         $weekdayMap = [
//             1 => [6],             // Saturday
//             2 => [1, 6],          // Monday, Saturday
//             3 => [1, 4, 6],       // Monday, Friday, Saturday
//             4 => [1, 3, 0, 6],    // Monday, Thursday, Friday, Saturday
//             5 => [0, 1, 3, 5, 6], // Sunday, Monday, Thursday, Friday, Saturday
//             6 => [0, 1, 2, 4, 5, 6], // Sun, Mon, Tue, Thu, Fri, Sat
//         ];




//         $allowedWeekdays = $weekdayMap[$exteriorDaysPerWeek];

//         $exteriorDates = [];
//         $currentDate = $startDate->copy();

//         while ($currentDate->lte($endDate)) {
//             $dayOfWeek = $currentDate->dayOfWeek;

//             if ($dayOfWeek === 3) { // Wednesday skip
//                 $currentDate->addDay();
//                 continue;
//             }

//             if (
//                 in_array($dayOfWeek, $allowedWeekdays) &&
//                 !in_array($currentDate->toDateString(), $interiorDateStrings)
//             ) {
//                 $exteriorDates[] = [
//                     'date' => $currentDate->toDateString(),
//                     'status' => '0',
//                 ];
//             }

//             if (count($exteriorDates) >= ($exteriorDaysCount - count($interiorDates))) {
//                 break;
//             }

//             $currentDate->addDay();
//         }

//         $addOns = is_array($request->add_on)
//             ? $request->add_on
//             : explode(',', $request->add_on ?? '');

//         $booking = Booking::create([
//             'user_id'             => $user->id,
//             'cupon'               => $validatedData['cupon'],
//             'start_date'          => $validatedData['start_date'],
//             'selected_date'       => json_encode($interiorDates),
//             'selected_date_2'     => json_encode($exteriorDates),
//             'add_on'              => implode(',', $addOns),
//             'selectedtime_slots'  => $validatedData['selectedtime_slots'],
//             'plain_id'            => $validatedData['plain_id'],
//             'car_id'              => $validatedData['car_id'],
//             'address_id'          => $validatedData['address_id'],
//             'total_price'         => $validatedData['total_price'],
//             'payment_status'      => $validatedData['payment_status'] ?? 0,
//             'unit'                => $validatedData['unit'] ?? 0,
//         ]);

//         foreach ($interiorDates as $interiorDate) {
//             BookingAdd::create([
//                 'booking_id'         => $booking->id,
//                 'user_id'            => $user->id,
//                 'day_type'           => 'interior',
//                 'interior_days'      => $interiorDate['date'],
//                 'cleaners_id'        => null,
//                 'unit'               => $validatedData['unit'] ?? null,
//                 'image'              => null,
//                 'reason'             => null,
//                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
//                 'plain_id'           => $validatedData['plain_id'],
//                 'add_on'             => implode(',', $addOns),
//             ]);
//         }

//         foreach ($exteriorDates as $exteriorDate) {
//             BookingAdd::create([
//                 'booking_id'         => $booking->id,
//                 'user_id'            => $user->id,
//                 'day_type'           => 'exterior',
//                 'exterior_days'      => $exteriorDate['date'],
//                 'cleaners_id'        => null,
//                 'unit'               => $validatedData['unit'] ?? null,
//                 'image'              => null,
//                 'reason'             => null,
//                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
//                 'plain_id'           => $validatedData['plain_id'],
//                 'add_on'             => implode(',', $addOns),
//             ]);
//         }

//         return response()->json([
//             'status'  => true,
//             'message' => 'Booking Created Successfully',
//             'data'    => [
//                 ...$booking->toArray(),
//                 'interior_dates' => $interiorDates,
//                 'exterior_dates' => $exteriorDates,
//                 'add_on_array'   => $addOns,
//             ],
//             'payment_status' => match ($booking->payment_status) {
//                 1 => 'Success',
//                 2 => 'Failed',
//                 default => 'Pending',
//             },
//         ], 200);
//     } catch (\Exception $e) {
//         return response()->json([
//             'status'  => false,
//             'message' => 'Failed to create booking: ' . $e->getMessage()
//         ], 400);
//     }
// }


    
    // public function store(Request $request): JsonResponse
    // {
    //     $user = Auth::user();
    
    //     $validatedData = $request->validate([
    //         'start_date'         => 'nullable|date',
    //         'selected_date'      => 'nullable', // Interior dates
    //         'add_on'             => 'nullable',
    //         'selectedtime_slots' => 'nullable',
    //         'plain_id'           => 'nullable|integer',
    //         'car_id'             => 'nullable|integer',
    //         'address_id'         => 'nullable|integer',
    //         'total_price'        => 'nullable|numeric',
    //         'payment_status'     => 'nullable|integer',
    //         'cupon'              => 'nullable|string',
    //         'unit'               => 'nullable|integer',
    //     ]);
    
    //     try {
    //         $plan = Plan::withTrashed()->find($validatedData['plain_id']);
    
    //         if (!$plan) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Plan not found.'
    //             ], 404);
    //         }
    
    //         $startDate = \Carbon\Carbon::parse($validatedData['start_date']);
    //         $endDate = $startDate->copy()->addDays(30);
    
    //         // Interior Dates
    //         $selectedInteriorDatesRaw = is_array($request->selected_date)
    //             ? $request->selected_date
    //             : explode(',', $request->selected_date ?? '');
    
    //         $interiorDates = array_map(function ($date) {
    //             return ['date' => trim($date), 'status' => '0'];
    //         }, $selectedInteriorDatesRaw);
    
    //         $interiorDateStrings = array_map(
    //             fn($d) => \Carbon\Carbon::parse($d['date'])->format('Y-m-d'),
    //             $interiorDates
    //         );
    
    //         $interiorDaysCount = count($interiorDateStrings);
    //         $exteriorDaysCount = ($plan->exterior_days * 4) - $interiorDaysCount;
    
    //         // Generate exterior dates
    //         $exteriorDates = [];
    //         $currentDate = $startDate->copy();
    
    //         while ($currentDate->lte($endDate) && count($exteriorDates) < $exteriorDaysCount) {
    //             // Skip Wednesday
    //             if ($currentDate->dayOfWeek === 3) {
    //                 $currentDate->addDay();
    //                 continue;
    //             }
    
    //             // Skip if date already in interior
    //             if (in_array($currentDate->format('Y-m-d'), $interiorDateStrings)) {
    //                 $currentDate->addDay();
    //                 continue;
    //             }
    
    //             $exteriorDates[] = [
    //                 'date' => $currentDate->toDateString(),
    //                 'status' => '0'
    //             ];
    
    //             $currentDate->addDay();
    //         }
    
    //         $addOns = is_array($request->add_on)
    //             ? $request->add_on
    //             : explode(',', $request->add_on ?? '');
    
    //         // Create Booking
    //         $booking = Booking::create([
    //             'user_id'             => $user->id,
    //             'cupon'               => $validatedData['cupon'],
    //             'start_date'          => $validatedData['start_date'],
    //             'selected_date'       => json_encode($interiorDates),
    //             'selected_date_2'     => json_encode($exteriorDates),
    //             'add_on'              => implode(',', $addOns),
    //             'selectedtime_slots'  => $validatedData['selectedtime_slots'],
    //             'plain_id'            => $validatedData['plain_id'],
    //             'car_id'              => $validatedData['car_id'],
    //             'address_id'          => $validatedData['address_id'],
    //             'total_price'         => $validatedData['total_price'],
    //             'payment_status'      => $validatedData['payment_status'] ?? 0,
    //             'unit'                => $validatedData['unit'] ?? 0,
    //         ]);
    
    //         // Save interior bookings
    //         foreach ($interiorDates as $interiorDate) {
    //             BookingAdd::create([
    //                 'booking_id'         => $booking->id,
    //                 'user_id'            => $user->id,
    //                 'day_type'           => 'interior',
    //                 'interior_days'      => $interiorDate['date'],
    //                 'cleaners_id'        => null,
    //                 'unit'               => $validatedData['unit'] ?? null,
    //                 'image'              => null,
    //                 'reason'             => null,
    //                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
    //                 'plain_id'           => $validatedData['plain_id'],
    //                 'add_on'             => implode(',', $addOns),
    //             ]);
    //         }
    
    //         // Save exterior bookings
    //         foreach ($exteriorDates as $exteriorDate) {
    //             BookingAdd::create([
    //                 'booking_id'         => $booking->id,
    //                 'user_id'            => $user->id,
    //                 'day_type'           => 'exterior',
    //                 'exterior_days'      => $exteriorDate['date'],
    //                 'cleaners_id'        => null,
    //                 'unit'               => $validatedData['unit'] ?? null,
    //                 'image'              => null,
    //                 'reason'             => null,
    //                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
    //                 'plain_id'           => $validatedData['plain_id'],
    //                 'add_on'             => implode(',', $addOns),
    //             ]);
    //         }
    
    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Booking Created Successfully',
    //             'data'    => [
    //                 ...$booking->toArray(),
    //                 'interior_dates' => $interiorDates,
    //                 'exterior_dates' => $exteriorDates,
    //                 'add_on_array'   => $addOns,
    //             ],
    //             'payment_status' => match ($booking->payment_status) {
    //                 1 => 'Success',
    //                 2 => 'Failed',
    //                 default => 'Pending',
    //             },
    //         ], 200);
    
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Failed to create booking: ' . $e->getMessage()
    //         ], 400);
    //     }
    // }
    




        public function storetodayaajka (Request $request): JsonResponse
{
    $user = Auth::user();

    $validatedData = $request->validate([
        'start_date'         => 'nullable|date',
        'selected_date'      => 'nullable', // Interior dates
        'add_on'             => 'nullable',
        'selectedtime_slots' => 'nullable',
        'plain_id'           => 'nullable|integer',
        'car_id'             => 'nullable|integer',
        'address_id'         => 'nullable|integer',
        'total_price'        => 'nullable|numeric',
        'payment_status'     => 'nullable|integer',
        'cupon'              => 'nullable|string',
        'unit'               => 'nullable|integer',
    ]);

    try {
        $plan = Plan::withTrashed()->find($validatedData['plain_id']);

        if (!$plan) {
            return response()->json([
                'status' => false,
                'message' => 'Plan not found.'
            ], 404);
        }

        $startDate = \Carbon\Carbon::parse($validatedData['start_date']);
        $endDate = $startDate->copy()->addMonth();
        $exteriorDaysPerWeek = $plan->exterior_days;

        // Prepare interior dates (selected manually by user)
        $selectedInteriorDatesRaw = is_array($request->selected_date)
            ? $request->selected_date
            : explode(',', $request->selected_date);

        $interiorDates = array_map(function ($date) {
            return ['date' => trim($date), 'status' => '0'];
        }, $selectedInteriorDatesRaw);

        // Convert interior to string dates
        $interiorDateStrings = array_map(
            fn($d) => \Carbon\Carbon::parse($d['date'])->format('Y-m-d'),
            $interiorDates
        );

        // Group interior dates by week (oW format)
        $interiorByWeek = [];
        foreach ($interiorDateStrings as $intDate) {
            $weekKey = \Carbon\Carbon::parse($intDate)->format('oW');
            $interiorByWeek[$weekKey] = ($interiorByWeek[$weekKey] ?? 0) + 1;
        }

        // Generate exterior dates excluding Wednesdays and limiting by interior count
        $exteriorDates = [];
        $currentDate = $startDate->copy();
        $weekCounter = [];

        while ($currentDate->lte($endDate)) {
            // Skip Wednesday
            if ($currentDate->dayOfWeek === 3) {
                $currentDate->addDay();
                continue;
            }

            $weekKey = $currentDate->format('oW');
            $exteriorLimitThisWeek = $exteriorDaysPerWeek - ($interiorByWeek[$weekKey] ?? 0);
            $weekCounter[$weekKey] = $weekCounter[$weekKey] ?? 0;

            // Skip if already in interior
            if (in_array($currentDate->format('Y-m-d'), $interiorDateStrings)) {
                $currentDate->addDay();
                continue;
            }

            if ($weekCounter[$weekKey] < $exteriorLimitThisWeek) {
                $exteriorDates[] = [
                    'date' => $currentDate->toDateString(),
                    'status' => '0'
                ];
                $weekCounter[$weekKey]++;
            }

            $currentDate->addDay();
        }

        $addOns = is_array($request->add_on)
            ? $request->add_on
            : explode(',', $request->add_on ?? '');

        // Create Booking
        $booking = Booking::create([
            'user_id'             => $user->id,
            'cupon'               => $validatedData['cupon'],
            'start_date'          => $validatedData['start_date'],
            'selected_date'       => json_encode($interiorDates),
            'selected_date_2'     => json_encode($exteriorDates),
            'add_on'              => implode(',', $addOns),
            'selectedtime_slots'  => $validatedData['selectedtime_slots'],
            'plain_id'            => $validatedData['plain_id'],
            'car_id'              => $validatedData['car_id'],
            'address_id'          => $validatedData['address_id'],
            'total_price'         => $validatedData['total_price'],
            'payment_status'      => $validatedData['payment_status'] ?? 0,
            'unit'                => $validatedData['unit'] ?? 0,
        ]);

        // Save interior dates
        foreach ($interiorDates as $interiorDate) {
            BookingAdd::create([
                'booking_id'         => $booking->id,
                'user_id'            => $user->id,
                'day_type'           => 'interior',
                'interior_days'      => $interiorDate['date'],
                'cleaners_id'        => null,
                'unit'               => $validatedData['unit'] ?? null,
                'image'              => null,
                'reason'             => null,
                'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
                'plain_id'           => $validatedData['plain_id'],
                'add_on'             => implode(',', $addOns),
            ]);
        }

        // Save exterior dates
        foreach ($exteriorDates as $exteriorDate) {
            BookingAdd::create([
                'booking_id'         => $booking->id,
                'user_id'            => $user->id,
                'day_type'           => 'exterior',
                'exterior_days'      => $exteriorDate['date'],
                'cleaners_id'        => null,
                'unit'               => $validatedData['unit'] ?? null,
                'image'              => null,
                'reason'             => null,
                'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
                'plain_id'           => $validatedData['plain_id'],
                'add_on'             => implode(',', $addOns),
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Booking Created Successfully',
            'data'    => [
                ...$booking->toArray(),
                'interior_dates' => $interiorDates,
                'exterior_dates' => $exteriorDates,
                'add_on_array'   => $addOns,
            ],
            'payment_status' => match ($booking->payment_status) {
                1 => 'Success',
                2 => 'Failed',
                default => 'Pending',
            },
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Failed to create booking: ' . $e->getMessage()
        ], 400);
    }
}


    // public function store(Request $request): JsonResponse
    // {
    //     $user = Auth::user();
    
    //     $validatedData = $request->validate([
    //         'start_date'         => 'nullable|date',
    //         'selected_date'      => 'nullable', // Interior dates
    //         'add_on'             => 'nullable',
    //         'selectedtime_slots' => 'nullable',
    //         'plain_id'           => 'nullable|integer',
    //         'car_id'             => 'nullable|integer',
    //         'address_id'         => 'nullable|integer',
    //         'total_price'        => 'nullable|numeric',
    //         'payment_status'     => 'nullable|integer',
    //         'cupon'              => 'nullable|string',
    //         'unit'               => 'nullable|integer',
    //     ]);
    
    //     try {
    //         $plan = Plan::withTrashed()->find($validatedData['plain_id']);
    
    //         if (!$plan) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Plan not found.'
    //             ], 404);
    //         }
    
    //         $startDate = \Carbon\Carbon::parse($validatedData['start_date']);
    //         $endDate = $startDate->copy()->addMonth();
    //         $exteriorDaysPerWeek = $plan->exterior_days;
    
    //         // Interior Dates
    //         $selectedInteriorDatesRaw = is_array($request->selected_date)
    //             ? $request->selected_date
    //             : explode(',', $request->selected_date);
    
    //         $interiorDates = array_map(function ($date) {
    //             return ['date' => trim($date), 'status' => '0'];
    //         }, $selectedInteriorDatesRaw);
    
    //         $interiorDateStrings = array_map(
    //             fn($d) => \Carbon\Carbon::parse($d['date'])->format('Y-m-d'),
    //             $interiorDates
    //         );
    
    //         // Count interior dates per week
    //         $interiorByWeek = [];
    //         foreach ($interiorDateStrings as $intDate) {
    //             $weekKey = \Carbon\Carbon::parse($intDate)->format('oW');
    //             $interiorByWeek[$weekKey] = ($interiorByWeek[$weekKey] ?? 0) + 1;
    //         }
    
    //         // Generate exterior dates excluding interior dates and Wednesdays
    //         $exteriorDates = [];
    //         $currentDate = $startDate->copy();
    //         $weekCounter = [];
    
    //         while ($currentDate->lte($endDate)) {
    //             if ($currentDate->dayOfWeek === 3) { // Skip Wednesday
    //                 $currentDate->addDay();
    //                 continue;
    //             }
    
    //             $currentDateStr = $currentDate->format('Y-m-d');
    //             $weekKey = $currentDate->format('oW');
    
    //             $exteriorLimitThisWeek = $exteriorDaysPerWeek;
    
    //             // Interior already booked on this day? Count it as exterior too, skip making exterior entry
    //             if (in_array($currentDateStr, $interiorDateStrings)) {
    //                 $weekCounter[$weekKey] = ($weekCounter[$weekKey] ?? 0) + 1;
    //                 $currentDate->addDay();
    //                 continue;
    //             }
    
    //             // Create exterior entry only if within limit
    //             $weekCounter[$weekKey] = $weekCounter[$weekKey] ?? 0;
    //             if ($weekCounter[$weekKey] < $exteriorLimitThisWeek) {
    //                 $exteriorDates[] = [
    //                     'date' => $currentDateStr,
    //                     'status' => '0'
    //                 ];
    //                 $weekCounter[$weekKey]++;
    //             }
    
    //             $currentDate->addDay();
    //         }
    
    //         $addOns = is_array($request->add_on)
    //             ? $request->add_on
    //             : explode(',', $request->add_on ?? '');
    
    //         // Create Booking
    //         $booking = Booking::create([
    //             'user_id'             => $user->id,
    //             'cupon'               => $validatedData['cupon'],
    //             'start_date'          => $validatedData['start_date'],
    //             'selected_date'       => json_encode($interiorDates),
    //             'selected_date_2'     => json_encode($exteriorDates),
    //             'add_on'              => implode(',', $addOns),
    //             'selectedtime_slots'  => $validatedData['selectedtime_slots'],
    //             'plain_id'            => $validatedData['plain_id'],
    //             'car_id'              => $validatedData['car_id'],
    //             'address_id'          => $validatedData['address_id'],
    //             'total_price'         => $validatedData['total_price'],
    //             'payment_status'      => $validatedData['payment_status'] ?? 0,
    //             'unit'                => $validatedData['unit'] ?? 0,
    //         ]);
    
    //         // Save Interior Dates
    //         foreach ($interiorDates as $interiorDate) {
    //             BookingAdd::create([
    //                 'booking_id'         => $booking->id,
    //                 'user_id'            => $user->id,
    //                 'day_type'           => 'interior',
    //                 'interior_days'      => $interiorDate['date'],
    //                 'cleaners_id'        => null,
    //                 'unit'               => $validatedData['unit'] ?? null,
    //                 'image'              => null,
    //                 'reason'             => null,
    //                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
    //                 'plain_id'           => $validatedData['plain_id'],
    //                 'add_on'             => implode(',', $addOns),
    //             ]);
    //         }
    
    //         // Save Exterior Dates
    //         foreach ($exteriorDates as $exteriorDate) {
    //             BookingAdd::create([
    //                 'booking_id'         => $booking->id,
    //                 'user_id'            => $user->id,
    //                 'day_type'           => 'exterior',
    //                 'exterior_days'      => $exteriorDate['date'],
    //                 'cleaners_id'        => null,
    //                 'unit'               => $validatedData['unit'] ?? null,
    //                 'image'              => null,
    //                 'reason'             => null,
    //                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
    //                 'plain_id'           => $validatedData['plain_id'],
    //                 'add_on'             => implode(',', $addOns),
    //             ]);
    //         }
    
    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Booking Created Successfully',
    //             'data'    => [
    //                 ...$booking->toArray(),
    //                 'interior_dates' => $interiorDates,
    //                 'exterior_dates' => $exteriorDates,
    //                 'add_on_array'   => $addOns,
    //             ],
    //             'payment_status' => match ($booking->payment_status) {
    //                 1 => 'Success',
    //                 2 => 'Failed',
    //                 default => 'Pending',
    //             },
    //         ], 200);
    
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Failed to create booking: ' . $e->getMessage()
    //         ], 400);
    //     }
    // }
    


//     public function storeye shi h (Request $request): JsonResponse
// {
//     $user = Auth::user();

//     $validatedData = $request->validate([
//         'start_date'         => 'nullable|date',
//         'selected_date'      => 'nullable', // Interior dates
//         'add_on'             => 'nullable',
//         'selectedtime_slots' => 'nullable',
//         'plain_id'           => 'nullable|integer',
//         'car_id'             => 'nullable|integer',
//         'address_id'         => 'nullable|integer',
//         'total_price'        => 'nullable|numeric',
//         'payment_status'     => 'nullable|integer',
//         'cupon'              => 'nullable|string',
//         'unit'               => 'nullable|integer',
//     ]);

//     try {
//         $plan = Plan::withTrashed()->find($validatedData['plain_id']);

//         if (!$plan) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Plan not found.'
//             ], 404);
//         }

//         $startDate = \Carbon\Carbon::parse($validatedData['start_date']);
//         $endDate = $startDate->copy()->addMonth();
//         $exteriorDaysPerWeek = $plan->exterior_days;

//         // Prepare interior dates (selected manually by user)
//         $selectedInteriorDatesRaw = is_array($request->selected_date)
//             ? $request->selected_date
//             : explode(',', $request->selected_date);

//         $interiorDates = array_map(function ($date) {
//             return ['date' => trim($date), 'status' => '0'];
//         }, $selectedInteriorDatesRaw);

//         // Convert interior to string dates
//         $interiorDateStrings = array_map(
//             fn($d) => \Carbon\Carbon::parse($d['date'])->format('Y-m-d'),
//             $interiorDates
//         );

//         // Group interior dates by week (oW format)
//         $interiorByWeek = [];
//         foreach ($interiorDateStrings as $intDate) {
//             $weekKey = \Carbon\Carbon::parse($intDate)->format('oW');
//             $interiorByWeek[$weekKey] = ($interiorByWeek[$weekKey] ?? 0) + 1;
//         }

//         // Generate exterior dates excluding Wednesdays and limiting by interior count
//         $exteriorDates = [];
//         $currentDate = $startDate->copy();
//         $weekCounter = [];

//         while ($currentDate->lte($endDate)) {
//             // Skip Wednesday
//             if ($currentDate->dayOfWeek === 3) {
//                 $currentDate->addDay();
//                 continue;
//             }

//             $weekKey = $currentDate->format('oW');
//             $exteriorLimitThisWeek = $exteriorDaysPerWeek - ($interiorByWeek[$weekKey] ?? 0);
//             $weekCounter[$weekKey] = $weekCounter[$weekKey] ?? 0;

//             // Skip if already in interior
//             if (in_array($currentDate->format('Y-m-d'), $interiorDateStrings)) {
//                 $currentDate->addDay();
//                 continue;
//             }

//             if ($weekCounter[$weekKey] < $exteriorLimitThisWeek) {
//                 $exteriorDates[] = [
//                     'date' => $currentDate->toDateString(),
//                     'status' => '0'
//                 ];
//                 $weekCounter[$weekKey]++;
//             }

//             $currentDate->addDay();
//         }

//         $addOns = is_array($request->add_on)
//             ? $request->add_on
//             : explode(',', $request->add_on ?? '');

//         // Create Booking
//         $booking = Booking::create([
//             'user_id'             => $user->id,
//             'cupon'               => $validatedData['cupon'],
//             'start_date'          => $validatedData['start_date'],
//             'selected_date'       => json_encode($interiorDates),
//             'selected_date_2'     => json_encode($exteriorDates),
//             'add_on'              => implode(',', $addOns),
//             'selectedtime_slots'  => $validatedData['selectedtime_slots'],
//             'plain_id'            => $validatedData['plain_id'],
//             'car_id'              => $validatedData['car_id'],
//             'address_id'          => $validatedData['address_id'],
//             'total_price'         => $validatedData['total_price'],
//             'payment_status'      => $validatedData['payment_status'] ?? 0,
//             'unit'                => $validatedData['unit'] ?? 0,
//         ]);

//         // Save interior dates
//         foreach ($interiorDates as $interiorDate) {
//             BookingAdd::create([
//                 'booking_id'         => $booking->id,
//                 'user_id'            => $user->id,
//                 'day_type'           => 'interior',
//                 'interior_days'      => $interiorDate['date'],
//                 'cleaners_id'        => null,
//                 'unit'               => $validatedData['unit'] ?? null,
//                 'image'              => null,
//                 'reason'             => null,
//                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
//                 'plain_id'           => $validatedData['plain_id'],
//                 'add_on'             => implode(',', $addOns),
//             ]);
//         }

//         // Save exterior dates
//         foreach ($exteriorDates as $exteriorDate) {
//             BookingAdd::create([
//                 'booking_id'         => $booking->id,
//                 'user_id'            => $user->id,
//                 'day_type'           => 'exterior',
//                 'exterior_days'      => $exteriorDate['date'],
//                 'cleaners_id'        => null,
//                 'unit'               => $validatedData['unit'] ?? null,
//                 'image'              => null,
//                 'reason'             => null,
//                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
//                 'plain_id'           => $validatedData['plain_id'],
//                 'add_on'             => implode(',', $addOns),
//             ]);
//         }

//         return response()->json([
//             'status'  => true,
//             'message' => 'Booking Created Successfully',
//             'data'    => [
//                 ...$booking->toArray(),
//                 'interior_dates' => $interiorDates,
//                 'exterior_dates' => $exteriorDates,
//                 'add_on_array'   => $addOns,
//             ],
//             'payment_status' => match ($booking->payment_status) {
//                 1 => 'Success',
//                 2 => 'Failed',
//                 default => 'Pending',
//             },
//         ], 200);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status'  => false,
//             'message' => 'Failed to create booking: ' . $e->getMessage()
//         ], 400);
//     }
// }




//     public function store(Request $request): JsonResponse
// {
//     $user = Auth::user();

//     $validatedData = $request->validate([
//         'start_date'         => 'nullable|date',
//         'selected_date'      => 'nullable', // Interior dates
//         'add_on'             => 'nullable',
//         'selectedtime_slots' => 'nullable',
//         'plain_id'           => 'nullable|integer',
//         'car_id'             => 'nullable|integer',
//         'address_id'         => 'nullable|integer',
//         'total_price'        => 'nullable|numeric',
//         'payment_status'     => 'nullable|integer',
//         'cupon'              => 'nullable|string',
//         'unit'               => 'nullable|integer',
//     ]);

//     try {
//         $plan = Plan::withTrashed()->find($validatedData['plain_id']);

//         if (!$plan) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Plan not found.'
//             ], 404);
//         }

//         $startDate = \Carbon\Carbon::parse($validatedData['start_date']);
//         $endDate = $startDate->copy()->addMonth();
//         $exteriorDaysPerWeek = $plan->exterior_days;

//         // Prepare interior dates (selected manually by user)
//         $selectedInteriorDatesRaw = is_array($request->selected_date)
//             ? $request->selected_date
//             : explode(',', $request->selected_date);

//         $interiorDates = array_map(function ($date) {
//             return ['date' => trim($date), 'status' => '0'];
//         }, $selectedInteriorDatesRaw);

//         // Interior date strings for comparison
//         $interiorDateStrings = array_map(fn($d) => \Carbon\Carbon::parse($d['date'])->format('Y-m-d'), $interiorDates);

//         // Generate exterior dates based on weekly limit, skipping Wednesdays and counting interiors
//         $exteriorDates = [];
//         $currentDate = $startDate->copy();
//         $weekCounter = [];

//         while ($currentDate->lte($endDate)) {
//             // Skip Wednesday
//             if ($currentDate->dayOfWeek === 3) {
//                 $currentDate->addDay();
//                 continue;
//             }

//             $weekKey = $currentDate->format('oW'); // year+week e.g., 202519
//             $weekCounter[$weekKey] = $weekCounter[$weekKey] ?? 0;

//             // If it's an interior date, count it but don't add to exteriorDates
//             if (in_array($currentDate->format('Y-m-d'), $interiorDateStrings)) {
//                 $weekCounter[$weekKey]++;
//                 $currentDate->addDay();
//                 continue;
//             }

//             // If limit not reached, add to exterior
//             if ($weekCounter[$weekKey] < $exteriorDaysPerWeek) {
//                 $exteriorDates[] = [
//                     'date' => $currentDate->toDateString(),
//                     'status' => '0'
//                 ];
//                 $weekCounter[$weekKey]++;
//             }

//             $currentDate->addDay();
//         }

//         $addOns = is_array($request->add_on)
//             ? $request->add_on
//             : explode(',', $request->add_on ?? '');

//         // Create Booking
//         $booking = Booking::create([
//             'user_id'             => $user->id,
//             'cupon'               => $validatedData['cupon'],
//             'start_date'          => $validatedData['start_date'],
//             'selected_date'       => json_encode($interiorDates),
//             'selected_date_2'     => json_encode($exteriorDates),
//             'add_on'              => implode(',', $addOns),
//             'selectedtime_slots'  => $validatedData['selectedtime_slots'],
//             'plain_id'            => $validatedData['plain_id'],
//             'car_id'              => $validatedData['car_id'],
//             'address_id'          => $validatedData['address_id'],
//             'total_price'         => $validatedData['total_price'],
//             'payment_status'      => $validatedData['payment_status'] ?? 0,
//             'unit'                => $validatedData['unit'] ?? 0,
//         ]);

//         // Save interior dates to BookingAdd
//         foreach ($interiorDates as $interiorDate) {
//             BookingAdd::create([
//                 'booking_id'         => $booking->id,
//                 'user_id'            => $user->id,
//                 'day_type'           => 'interior',
//                 'interior_days'      => $interiorDate['date'],
//                 'cleaners_id'        => null,
//                 'unit'               => $validatedData['unit'] ?? null,
//                 'image'              => null,
//                 'reason'             => null,
//                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
//                 'plain_id'           => $validatedData['plain_id'],
//                 'add_on'             => implode(',', $addOns),
//             ]);
//         }

//         // Save exterior dates to BookingAdd
//         foreach ($exteriorDates as $exteriorDate) {
//             BookingAdd::create([
//                 'booking_id'         => $booking->id,
//                 'user_id'            => $user->id,
//                 'day_type'           => 'exterior',
//                 'exterior_days'      => $exteriorDate['date'],
//                 'cleaners_id'        => null,
//                 'unit'               => $validatedData['unit'] ?? null,
//                 'image'              => null,
//                 'reason'             => null,
//                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
//                 'plain_id'           => $validatedData['plain_id'],
//                 'add_on'             => implode(',', $addOns),
//             ]);
//         }

//         return response()->json([
//             'status'  => true,
//             'message' => 'Booking Created Successfully',
//             'data'    => [
//                 ...$booking->toArray(),
//                 'interior_dates' => $interiorDates,
//                 'exterior_dates' => $exteriorDates,
//                 'add_on_array'   => $addOns,
//             ],
//             'payment_status' => match ($booking->payment_status) {
//                 1 => 'Success',
//                 2 => 'Failed',
//                 default => 'Pending',
//             },
//         ], 200);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status'  => false,
//             'message' => 'Failed to create booking: ' . $e->getMessage()
//         ], 400);
//     }
// }



//     public function store(Request $request): JsonResponse
// {
//     $user = Auth::user();

//     $validatedData = $request->validate([
//         'start_date'         => 'nullable|date',
//         'selected_date'      => 'nullable', // Interior dates
//         'add_on'             => 'nullable',
//         'selectedtime_slots' => 'nullable',
//         'plain_id'           => 'nullable|integer',
//         'car_id'             => 'nullable|integer',
//         'address_id'         => 'nullable|integer',
//         'total_price'        => 'nullable|numeric',
//         'payment_status'     => 'nullable|integer',
//         'cupon'              => 'nullable|string',
//         'unit'               => 'nullable|integer',
//     ]);

//     try {
//         $plan = Plan::withTrashed()->find($validatedData['plain_id']);

//         if (!$plan) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Plan not found.'
//             ], 404);
//         }

//         $startDate = \Carbon\Carbon::parse($validatedData['start_date']);
//         $endDate = $startDate->copy()->addMonth();
//         $exteriorDaysPerWeek = $plan->exterior_days;

//         // Prepare interior dates (selected manually by user)
//         $selectedInteriorDatesRaw = is_array($request->selected_date)
//             ? $request->selected_date
//             : explode(',', $request->selected_date);

//         $interiorDates = array_map(function ($date) {
//             return ['date' => trim($date), 'status' => '0'];
//         }, $selectedInteriorDatesRaw);

//         // Generate exterior dates based on weekly limit
//         $exteriorDates = [];
//         $currentDate = $startDate->copy();
//         $weekCounter = [];

//         while ($currentDate->lte($endDate)) {
//             $weekKey = $currentDate->format('oW'); // year+week e.g., 202519
//             $weekCounter[$weekKey] = $weekCounter[$weekKey] ?? 0;

//             if ($weekCounter[$weekKey] < $exteriorDaysPerWeek) {
//                 $exteriorDates[] = [
//                     'date' => $currentDate->toDateString(),
//                     'status' => '0'
//                 ];
//                 $weekCounter[$weekKey]++;
//             }

//             $currentDate->addDay();
//         }

//         $addOns = is_array($request->add_on)
//             ? $request->add_on
//             : explode(',', $request->add_on ?? '');

//         // Create Booking
//         $booking = Booking::create([
//             'user_id'             => $user->id,
//             'cupon'               => $validatedData['cupon'],
//             'start_date'          => $validatedData['start_date'],
//             'selected_date'       => json_encode($interiorDates),
//             'selected_date_2'     => json_encode($exteriorDates),
//             'add_on'              => implode(',', $addOns),
//             'selectedtime_slots'  => $validatedData['selectedtime_slots'],
//             'plain_id'            => $validatedData['plain_id'],
//             'car_id'              => $validatedData['car_id'],
//             'address_id'          => $validatedData['address_id'],
//             'total_price'         => $validatedData['total_price'],
//             'payment_status'      => $validatedData['payment_status'] ?? 0,
//             'unit'                => $validatedData['unit'] ?? 0,
//         ]);

//         // Insert Interior (user-selected) dates
//         foreach ($interiorDates as $interiorDate) {
//             BookingAdd::create([
//                 'booking_id'         => $booking->id,
//                 'user_id'            => $user->id,
//                 'day_type'           => 'interior',
//                 'interior_days'      => $interiorDate['date'],
//                 'cleaners_id'        => null,
//                 'unit'               => $validatedData['unit'] ?? null,
//                 'image'              => null,
//                 'reason'             => null,
//                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
//                 'plain_id'           => $validatedData['plain_id'],
//                 'add_on'             => implode(',', $addOns),
//             ]);
//         }

//         // Insert Exterior (auto-generated) dates
//         foreach ($exteriorDates as $exteriorDate) {
//             BookingAdd::create([
//                 'booking_id'         => $booking->id,
//                 'user_id'            => $user->id,
//                 'day_type'           => 'exterior',
//                 'exterior_days'      => $exteriorDate['date'],
//                 'cleaners_id'        => null,
//                 'unit'               => $validatedData['unit'] ?? null,
//                 'image'              => null,
//                 'reason'             => null,
//                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
//                 'plain_id'           => $validatedData['plain_id'],
//                 'add_on'             => implode(',', $addOns),
//             ]);
//         }

//         return response()->json([
//             'status'  => true,
//             'message' => 'Booking Created Successfully',
//             'data'    => [
//                 ...$booking->toArray(),
//                 'interior_dates' => $interiorDates,
//                 'exterior_dates' => $exteriorDates,
//                 'add_on_array'   => $addOns,
//             ],
//             'payment_status' => match ($booking->payment_status) {
//                 1 => 'Success',
//                 2 => 'Failed',
//                 default => 'Pending',
//             },
//         ], 200);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status'  => false,
//             'message' => 'Failed to create booking: ' . $e->getMessage()
//         ], 400);
//     }
// }





//     public function store(Request $request): JsonResponse
// {
//     $user = Auth::user();

//     $validatedData = $request->validate([
//         'start_date'         => 'nullable',
//         'selected_date'      => 'nullable',
//         'add_on'             => 'nullable',
//         'selectedtime_slots' => 'nullable',
//         'plain_id'           => 'nullable',
//         'car_id'             => 'nullable',
//         'address_id'         => 'nullable',
//         'total_price'        => 'nullable',
//         'payment_status'     => 'nullable',
//         'cupon'              => 'nullable',
//         'day_name'           => 'nullable',
//         'unit'               => 'nullable',
//     ]);

//     try {
//         $plan = Plan::withTrashed()->where('id', $request->plain_id)->first();

//         if (!$plan) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Plan not found.'
//             ], 404);
//         }

//         $exterior_days = $plan->exterior_days - $plan->interior_days;

//         $selectedDatesRaw = is_array($request->selected_date)
//             ? $request->selected_date
//             : explode(',', $request->selected_date);

//         $selectedDates = array_map(function ($date) {
//             return ['date' => $date, 'status' => '0'];
//         }, $selectedDatesRaw);

//         $maxDate = collect($selectedDatesRaw)->max();

//         // Interior date generation
//         $interiorDates = [];
//         $currentDate = \Carbon\Carbon::parse($maxDate)->addDay();
//         $startDateLimit = \Carbon\Carbon::parse($validatedData['start_date'])->copy()->addMonth();

//         while (count($interiorDates) < $exterior_days && $currentDate->lte($startDateLimit)) {
//             if (
//                 $currentDate->format('l') === $request->day_name &&
//                 !in_array($currentDate->toDateString(), array_column($selectedDates, 'date'))
//             ) {
//                 $interiorDates[] = [
//                     'date' => $currentDate->toDateString(),
//                     'status' => '0'
//                 ];
//             }
//             $currentDate->addDay();
//         }

//         // Fill remaining interior days using alternate days before selected dates
//         $remaining = $exterior_days - count($interiorDates);

//         if ($remaining > 0) {
//             $altStart = \Carbon\Carbon::parse($validatedData['start_date']);
//             $altEnd = \Carbon\Carbon::parse($maxDate)->subDay();
//             $altDate = $altEnd;

//             while ($remaining > 0 && $altDate->gte($altStart)) {
//                 $formatted = $altDate->toDateString();
//                 if (
//                     !in_array($formatted, array_column($selectedDates, 'date')) &&
//                     !in_array($formatted, array_column($interiorDates, 'date'))
//                 ) {
//                     $interiorDates[] = [
//                         'date' => $formatted,
//                         'status' => '0'
//                     ];
//                     $remaining--;
//                     $altDate->subDays(2); // Skip one day
//                 } else {
//                     $altDate->subDay();
//                 }
//             }
//         }

//         $addOns = is_array($request->add_on)
//             ? $request->add_on
//             : explode(',', $request->add_on);

//         // Merge all dates to check conflict
//         $allDatesToCheck = array_merge(
//             array_column($selectedDates, 'date'),
//             array_column($interiorDates, 'date')
//         );

//         // Create Booking
//         $booking = Booking::create([
//             'user_id'             => $user->id,
//             'cupon'               => $validatedData['cupon'],
//             'start_date'          => $validatedData['start_date'],
//             'selected_date'       => json_encode($selectedDates),
//             'selected_date_2'     => json_encode($interiorDates),
//             'add_on'              => implode(',', $addOns),
//             'selectedtime_slots'  => $validatedData['selectedtime_slots'],
//             'plain_id'            => $validatedData['plain_id'],
//             'car_id'              => $validatedData['car_id'],
//             'address_id'          => $validatedData['address_id'],
//             'total_price'         => $validatedData['total_price'],
//             'payment_status'      => $validatedData['payment_status'] ?? 0,
//             'unit'      => $validatedData['unit'] ?? 0,

//         ]);

//         // Insert exterior BookingAdd records
//         foreach ($selectedDates as $exteriorDate) {
//             BookingAdd::create([
//                 'booking_id'         => $booking->id,
//                 'user_id'            => $user->id,
//                 'day_type'           => 'interior',
//                 'interior_days'      => $exteriorDate['date'],
//                 'cleaners_id'        => null,
//                 'unit'               => $validatedData['unit'] ?? null,
//                 'image'              => null,
//                 'reason'             => null,
//                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
//                 'plain_id'            => $validatedData['plain_id'],
//                 'add_on'              => implode(',', $addOns),

//             ]);
//         }

//         // Insert interior BookingAdd records
//         foreach ($interiorDates as $interiorDate) {
//             BookingAdd::create([
//                 'booking_id'         => $booking->id,
//                 'user_id'            => $user->id,
//                 'day_type'           => 'exterior',
//                 'exterior_days'      => $interiorDate['date'],
//                 'cleaners_id'        => null,
//                 'unit'               => $validatedData['unit'] ?? null,
//                 'image'              => null,
//                 'reason'             => null,
//                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
//                 'plain_id'            => $validatedData['plain_id'],
//                 'add_on'              => implode(',', $addOns),

//             ]);
//         }

//         return response()->json([
//             'status'  => true,
//             'message' => 'Booking Created Successfully',
//             'data'    => [
//                 ...$booking->toArray(),
//                 'selected_date_array'   => $selectedDates,
//                 'selected_date_2_array' => $interiorDates,
//                 'add_on_array'          => $addOns
//             ],
//             'payment_status' => match ($booking->payment_status) {
//                 1 => 'Success',
//                 2 => 'Failed',
//                 default => 'Pending',
//             },
//         ], 200);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status'  => false,
//             'message' => 'Failed to create booking: ' . $e->getMessage()
//         ], 400);
//     }
// }




    public function storetoday(Request $request): JsonResponse
{
    $user = Auth::user();

    $validatedData = $request->validate([
        'start_date'         => 'nullable',
        'selected_date'      => 'nullable',
        'add_on'             => 'nullable',
        'selectedtime_slots' => 'nullable',
        'plain_id'           => 'nullable',
        'car_id'             => 'nullable',
        'address_id'         => 'nullable',
        'total_price'        => 'nullable',
        'payment_status'     => 'nullable',
        'cupon'              => 'nullable',
        'day_name'           => 'nullable',
        'unit'               => 'nullable',
    ]);

    try {
        $plan = Plan::withTrashed()->where('id', $request->plain_id)->first();

        if (!$plan) {
            return response()->json([
                'status' => false,
                'message' => 'Plan not found.'
            ], 404);
        }

        $exterior_days = $plan->exterior_days - $plan->interior_days;

        $selectedDatesRaw = is_array($request->selected_date)
            ? $request->selected_date
            : explode(',', $request->selected_date);

        $selectedDates = array_map(function ($date) {
            return ['date' => $date, 'status' => '0'];
        }, $selectedDatesRaw);

        $maxDate = collect($selectedDatesRaw)->max();

        // Interior date generation
        $interiorDates = [];
        $currentDate = \Carbon\Carbon::parse($maxDate)->addDay();
        $startDateLimit = \Carbon\Carbon::parse($validatedData['start_date'])->copy()->addMonth();

        while (count($interiorDates) < $exterior_days && $currentDate->lte($startDateLimit)) {
            if (
                $currentDate->format('l') === $request->day_name &&
                !in_array($currentDate->toDateString(), array_column($selectedDates, 'date'))
            ) {
                $interiorDates[] = [
                    'date' => $currentDate->toDateString(),
                    'status' => '0'
                ];
            }
            $currentDate->addDay();
        }

        // Fill remaining interior days using alternate days before selected dates
        $remaining = $exterior_days - count($interiorDates);

        if ($remaining > 0) {
            $altStart = \Carbon\Carbon::parse($validatedData['start_date']);
            $altEnd = \Carbon\Carbon::parse($maxDate)->subDay();
            $altDate = $altEnd;

            while ($remaining > 0 && $altDate->gte($altStart)) {
                $formatted = $altDate->toDateString();
                if (
                    !in_array($formatted, array_column($selectedDates, 'date')) &&
                    !in_array($formatted, array_column($interiorDates, 'date'))
                ) {
                    $interiorDates[] = [
                        'date' => $formatted,
                        'status' => '0'
                    ];
                    $remaining--;
                    $altDate->subDays(2); // Skip one day
                } else {
                    $altDate->subDay();
                }
            }
        }

        $addOns = is_array($request->add_on)
            ? $request->add_on
            : explode(',', $request->add_on);

        // Merge all dates to check conflict
        $allDatesToCheck = array_merge(
            array_column($selectedDates, 'date'),
            array_column($interiorDates, 'date')
        );

        // $conflict = BookingAdd::whereIn('interior_days', $allDatesToCheck)
        //     ->orWhereIn('exterior_days', $allDatesToCheck)
        //     ->whereIn('interior_days', $allDatesToCheck)
        //     ->exists();

        // if ($conflict) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Some selected dates are already booked. Please choose different dates.',
        //     ], 409);
        // }

        // Create Booking
        $booking = Booking::create([
            'user_id'             => $user->id,
            'cupon'               => $validatedData['cupon'],
            'start_date'          => $validatedData['start_date'],
            'selected_date'       => json_encode($selectedDates),
            'selected_date_2'     => json_encode($interiorDates),
            'add_on'              => implode(',', $addOns),
            'selectedtime_slots'  => $validatedData['selectedtime_slots'],
            'plain_id'            => $validatedData['plain_id'],
            'car_id'              => $validatedData['car_id'],
            'address_id'          => $validatedData['address_id'],
            'total_price'         => $validatedData['total_price'],
            'payment_status'      => $validatedData['payment_status'] ?? 0,
            'unit'      => $validatedData['unit'] ?? 0,

        ]);

        // Insert exterior BookingAdd records
        foreach ($selectedDates as $exteriorDate) {
            BookingAdd::create([
                'booking_id'         => $booking->id,
                'user_id'            => $user->id,
                'day_type'           => 'interior',
                'interior_days'      => $exteriorDate['date'],
                'cleaners_id'        => null,
                'unit'               => $validatedData['unit'] ?? null,
                'image'              => null,
                'reason'             => null,
                'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
                'plain_id'            => $validatedData['plain_id'],
                'add_on'              => implode(',', $addOns),

            ]);
        }

        // Insert interior BookingAdd records
        foreach ($interiorDates as $interiorDate) {
            BookingAdd::create([
                'booking_id'         => $booking->id,
                'user_id'            => $user->id,
                'day_type'           => 'exterior',
                'exterior_days'      => $interiorDate['date'],
                'cleaners_id'        => null,
                'unit'               => $validatedData['unit'] ?? null,
                'image'              => null,
                'reason'             => null,
                'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
                'plain_id'            => $validatedData['plain_id'],
                'add_on'              => implode(',', $addOns),

            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Booking Created Successfully',
            'data'    => [
                ...$booking->toArray(),
                'selected_date_array'   => $selectedDates,
                'selected_date_2_array' => $interiorDates,
                'add_on_array'          => $addOns
            ],
            'payment_status' => match ($booking->payment_status) {
                1 => 'Success',
                2 => 'Failed',
                default => 'Pending',
            },
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Failed to create booking: ' . $e->getMessage()
        ], 400);
    }
}




    // public function store(Request $request): JsonResponse
    // {
    //     $user = Auth::user();
    
    //     $validatedData = $request->validate([
    //         'start_date'         => 'nullable',
    //         'selected_date'      => 'nullable',
    //         'add_on'             => 'nullable',
    //         'selectedtime_slots' => 'nullable',
    //         'plain_id'           => 'nullable',
    //         'car_id'             => 'nullable',
    //         'address_id'         => 'nullable',
    //         'total_price'        => 'nullable',
    //         'payment_status'     => 'nullable',
    //         'cupon'              => 'nullable',
    //         'day_name'           => 'nullable',
    //         'unit'               => 'nullable',
    //     ]);
    
    //     try {
    //         $plan = Plan::withTrashed()->where('id', $request->plain_id)->first();
    
    //         if (!$plan) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Plan not found.'
    //             ], 404);
    //         }
    
    //         $exterior_days = $plan->exterior_days - $plan->interior_days;
    
    //         $selectedDatesRaw = is_array($request->selected_date)
    //             ? $request->selected_date
    //             : explode(',', $request->selected_date);
    
    //         $selectedDates = array_map(function ($date) {
    //             return ['date' => $date, 'status' => '0'];
    //         }, $selectedDatesRaw);
    
    //         $maxDate = collect($selectedDatesRaw)->max();
    
    //         // Interior date generation
    //         $interiorDates = [];
    //         $currentDate = \Carbon\Carbon::parse($maxDate)->addDay();
    //         $startDateLimit = \Carbon\Carbon::parse($validatedData['start_date'])->copy()->addMonth();
    
    //         while (count($interiorDates) < $exterior_days && $currentDate->lte($startDateLimit)) {
    //             if (
    //                 $currentDate->format('l') === $request->day_name &&
    //                 !in_array($currentDate->toDateString(), array_column($selectedDates, 'date'))
    //             ) {
    //                 $interiorDates[] = [
    //                     'date' => $currentDate->toDateString(),
    //                     'status' => '0'
    //                 ];
    //             }
    //             $currentDate->addDay();
    //         }
    
    //         // Fill remaining interior days from month-end using alternate days
    //         $remaining = $exterior_days - count($interiorDates);
    
    //         if ($remaining > 0) {
    //             $monthEnd = $startDateLimit->copy()->endOfMonth();
    //             $altDate = $monthEnd;
    
    //             while ($remaining > 0 && $altDate->gte($startDateLimit)) {
    //                 $formatted = $altDate->toDateString();
    //                 if (
    //                     !in_array($formatted, array_column($selectedDates, 'date')) &&
    //                     !in_array($formatted, array_column($interiorDates, 'date'))
    //                 ) {
    //                     $interiorDates[] = [
    //                         'date' => $formatted,
    //                         'status' => '0'
    //                     ];
    //                     $remaining--;
    //                     $altDate->subDays(2);
    //                 } else {
    //                     $altDate->subDay();
    //                 }
    //             }
    //         }
    
    //         $addOns = is_array($request->add_on)
    //             ? $request->add_on
    //             : explode(',', $request->add_on);
    
    //         // Merge all dates to check conflict
    //         $allDatesToCheck = array_merge(
    //             array_column($selectedDates, 'date'),
    //             array_column($interiorDates, 'date')
    //         );
    
    //         $conflict = BookingAdd::whereIn('interior_days', $allDatesToCheck)
    //             ->orWhereIn('exterior_days', $allDatesToCheck)
    //             ->exists();
    
    //         if ($conflict) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Some selected dates are already booked. Please choose different dates.',
    //             ], 409);
    //         }
    
    //         // Create Booking
    //         $booking = Booking::create([
    //             'user_id'             => $user->id,
    //             'cupon'               => $validatedData['cupon'],
    //             'start_date'          => $validatedData['start_date'],
    //             'selected_date'       => json_encode($selectedDates),
    //             'selected_date_2'     => json_encode($interiorDates),
    //             'add_on'              => implode(',', $addOns),
    //             'selectedtime_slots'  => $validatedData['selectedtime_slots'],
    //             'plain_id'            => $validatedData['plain_id'],
    //             'car_id'              => $validatedData['car_id'],
    //             'address_id'          => $validatedData['address_id'],
    //             'total_price'         => $validatedData['total_price'],
    //             'payment_status'      => $validatedData['payment_status'] ?? 0,
    //         ]);
    
    //         // Insert exterior BookingAdd records
    //         foreach ($selectedDates as $exteriorDate) {
    //             BookingAdd::create([
    //                 'booking_id'         => $booking->id,
    //                 'user_id'            => $user->id,
    //                 'day_type'           => 'exterior',
    //                 'interior_days'      => $exteriorDate['date'],
    //                 'cleaners_id'        => null,
    //                 'unit'               => $validatedData['unit'] ?? null,
    //                 'image'              => null,
    //                 'reason'             => null,
    //                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
    //             ]);
    //         }
    
    //         // Insert interior BookingAdd records
    //         foreach ($interiorDates as $interiorDate) {
    //             BookingAdd::create([
    //                 'booking_id'         => $booking->id,
    //                 'user_id'            => $user->id,
    //                 'day_type'           => 'interior',
    //                 'exterior_days'      => $interiorDate['date'],
    //                 'cleaners_id'        => null,
    //                 'unit'               => $validatedData['unit'] ?? null,
    //                 'image'              => null,
    //                 'reason'             => null,
    //                 'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
    //             ]);
    //         }
    
    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Booking Created Successfully',
    //             'data'    => [
    //                 ...$booking->toArray(),
    //                 'selected_date_array'   => $selectedDates,
    //                 'selected_date_2_array' => $interiorDates,
    //                 'add_on_array'          => $addOns
    //             ],
    //             'payment_status' => match ($booking->payment_status) {
    //                 1 => 'Success',
    //                 2 => 'Failed',
    //                 default => 'Pending',
    //             },
    //         ], 200);
    
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Failed to create booking: ' . $e->getMessage()
    //         ], 400);
    //     }
    // }
    
    

    public function storessss(Request $request): JsonResponse
    {
        $user = Auth::user();
    
        $validatedData = $request->validate([
            'start_date'         => 'nullable|date',
            'selected_date'      => 'nullable',
            'add_on'             => 'nullable',
            'selectedtime_slots' => 'nullable',
            'plain_id'           => 'nullable|integer',
            'car_id'             => 'nullable|integer',
            'address_id'         => 'nullable|integer',
            'total_price'        => 'nullable',
            'payment_status'     => 'nullable|integer',
            'cupon'              => 'nullable|string',
            'day_name'           => 'nullable|string',
            'unit'               => 'nullable|integer',
        ]);
    
        try {
            $plan = Plan::withTrashed()->where('id', $request->plain_id)->first();
    
            if (!$plan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Plan not found.'
                ], 404);
            }
    
            $exterior_days = $plan->exterior_days - $plan->interior_days;
    
            $selectedDatesRaw = is_array($request->selected_date)
                ? $request->selected_date
                : explode(',', $request->selected_date);
    
            $selectedDates = array_map(function ($date) {
                return ['date' => $date, 'status' => '0'];
            }, $selectedDatesRaw);
    
            $maxDate = collect($selectedDatesRaw)->max();
    
            // Generate interior_dates from next matching weekdays
            $futureMatchingDates = [];
            $currentDate = \Carbon\Carbon::parse($maxDate)->addDay();
            $startDateLimit = \Carbon\Carbon::parse($validatedData['start_date'])->copy()->addMonth();
    
            while (count($futureMatchingDates) < $exterior_days) {
                if ($currentDate->gt($startDateLimit)) {
                    break; // Stop if dates exceed 1 month from start_date
                }
    
                if ($currentDate->format('l') === $request->day_name) {
                    $futureMatchingDates[] = [
                        'date' => $currentDate->toDateString(),
                        'status' => '0'
                    ];
                }
                $currentDate->addDay();
            }
    
            $addOns = is_array($request->add_on)
                ? $request->add_on
                : explode(',', $request->add_on);
    
            // Merge all dates to check conflict
            $allDatesToCheck = array_merge(
                array_column($selectedDates, 'date'),
                array_column($futureMatchingDates, 'date')
            );
    
            $conflict = BookingAdd::whereIn('interior_days', $allDatesToCheck)
                ->orWhereIn('exterior_days', $allDatesToCheck)
                ->exists();
    
            if ($conflict) {
                return response()->json([
                    'status' => false,
                    'message' => 'Some selected dates are already booked. Please choose different dates.',
                ], 409);
            }
    
            // Create Booking
            $booking = Booking::create([
                'user_id'             => $user->id,
                'cupon'               => $validatedData['cupon'],
                'start_date'          => $validatedData['start_date'],
                'selected_date'       => json_encode($selectedDates),
                'selected_date_2'     => json_encode($futureMatchingDates),
                'add_on'              => implode(',', $addOns),
                'selectedtime_slots'  => $validatedData['selectedtime_slots'],
                'plain_id'            => $validatedData['plain_id'],
                'car_id'              => $validatedData['car_id'],
                'address_id'          => $validatedData['address_id'],
                'total_price'         => $validatedData['total_price'],
                'payment_status'      => $validatedData['payment_status'] ?? 0,
            ]);
    
            // Insert exterior BookingAdd records
            foreach ($selectedDates as $exteriorDate) {
                BookingAdd::create([
                    'booking_id'         => $booking->id,
                    'user_id'            => $user->id,
                    'day_type'           => 'exterior',
                    'interior_days'      => $exteriorDate['date'],
                    'cleaners_id'        => null,
                    'unit'               => $validatedData['unit'] ?? null,
                    'image'              => null,
                    'reason'             => null,
                    'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
                ]);
            }
    
            // Insert interior BookingAdd records
            foreach ($futureMatchingDates as $interiorDate) {
                BookingAdd::create([
                    'booking_id'         => $booking->id,
                    'user_id'            => $user->id,
                    'day_type'           => 'interior',
                    'exterior_days'      => $interiorDate['date'],
                    'cleaners_id'        => null,
                    'unit'               => $validatedData['unit'] ?? null,
                    'image'              => null,
                    'reason'             => null,
                    'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
                ]);
            }
    
            return response()->json([
                'status'  => true,
                'message' => 'Booking Created Successfully',
                'data'    => [
                    ...$booking->toArray(),
                    'selected_date_array'   => $selectedDates,
                    'selected_date_2_array' => $futureMatchingDates,
                    'add_on_array'          => $addOns
                ],
                'payment_status' => match ($booking->payment_status) {
                    1 => 'Success',
                    2 => 'Failed',
                    default => 'Pending',
                },
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to create booking: ' . $e->getMessage()
            ], 400);
        }
    }
    

    public function storeooooo(Request $request): JsonResponse
    {
        // dd($request);
        $user = Auth::user();
    
        $validatedData = $request->validate([
            'start_date'         => 'nullable',
            'selected_date'      => 'nullable',
            'add_on'             => 'nullable',
            'selectedtime_slots' => 'nullable',
            'plain_id'           => 'nullable',
            'car_id'             => 'nullable',
            'address_id'         => 'nullable',
            'total_price'        => 'nullable',
            'payment_status'     => 'nullable',
            'cupon'              => 'nullable',
            'day_name'           => 'nullable',
            'unit'               => 'nullable',
        ]);
    
        try {
            $plan = Plan::withTrashed()->where('id', $request->plain_id)->first();
    
            if (!$plan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Plan not found.'
                ], 404);
            }
    
            $exterior_days = $plan->exterior_days - $plan->interior_days;
    
            $selectedDatesRaw = is_array($request->selected_date)
                ? $request->selected_date
                : explode(',', $request->selected_date);
    
            $selectedDates = array_map(function ($date) {
                return ['date' => $date, 'status' => '0'];
            }, $selectedDatesRaw);
    
            $maxDate = collect($selectedDatesRaw)->max();
    
            $futureMatchingDates = [];
            $currentDate = \Carbon\Carbon::parse($maxDate)->addDay();
    
            while (count($futureMatchingDates) < $exterior_days) {
                if ($currentDate->format('l') === $request->day_name) {
                    $futureMatchingDates[] = [
                        'date' => $currentDate->toDateString(),
                        'status' => '0'
                    ];
                }
                $currentDate->addDay();
            }
    
            $addOns = is_array($request->add_on)
                ? $request->add_on
                : explode(',', $request->add_on);

                // echo 'cscsc';die;
            // Create Booking
            $booking = Booking::create([
                'user_id'             => $user->id,
                'cupon'               => $validatedData['cupon'],
                'start_date'          => $validatedData['start_date'],
                'selected_date'       => json_encode($selectedDates),
                'selected_date_2'     => json_encode($futureMatchingDates),
                'add_on'              => implode(',', $addOns),
                'selectedtime_slots'  => $validatedData['selectedtime_slots'],
                'plain_id'            => $validatedData['plain_id'],
                'car_id'              => $validatedData['car_id'],
                'address_id'          => $validatedData['address_id'],
                'total_price'         => $validatedData['total_price'],
                'payment_status'      => $validatedData['payment_status'] ?? 0,
            ]);
            // dd($booking);
    
            // Insert multiple BookingAdd rows
            foreach ($selectedDates as $exteriorDate) {
                BookingAdd::create([
                    'booking_id'         => $booking->id,
                    'user_id'            => $user->id,
                    'day_type'           => 'exterior',
                    'interior_days'         => $exteriorDate['date'],
                    // 'status'             => $exteriorDate['status'],
                    'cleaners_id'        => null,
                    'unit'               => $validatedData['unit'] ?? null,
                    'image'              => null,
                    'reason'             => null,
                    'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
                ]);
            }
    
            foreach ($futureMatchingDates as $interiorDate) {
                BookingAdd::create([
                    'booking_id'         => $booking->id,
                    'user_id'            => $user->id,
                    'day_type'           => 'interior',
                    'exterior_days'      => $interiorDate['date'],
                    // 'status'             => $interiorDate['status'],
                    'cleaners_id'        => null,
                    'unit'               => $validatedData['unit'] ?? null,
                    'image'              => null,
                    'reason'             => null,
                    'selectedtime_slots' => $validatedData['selectedtime_slots'] ?? null,
                ]);
            }
    
            $responseData = [
                'status'  => true,
                'message' => 'Booking Created Successfully',
                'data'    => [
                    ...$booking->toArray(),
                    'selected_date_array'   => $selectedDates,
                    'selected_date_2_array' => $futureMatchingDates,
                    'add_on_array'          => $addOns
                ],
                'payment_status' => match ($booking->payment_status) {
                    1 => 'Success',
                    2 => 'Failed',
                    default => 'Pending',
                },
            ];
    




            return response()->json($responseData, 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to create booking: ' . $e->getMessage()
            ], 400);
        }
    }
    
    


    public function storeold(Request $request): JsonResponse

    {

        $user = Auth::user();



        $validatedData = $request->validate([

            'start_date'         => 'nullable',

            'selected_date'      => 'nullable',

            'add_on'             => 'nullable',

            'selectedtime_slots' => 'nullable',

            'plain_id'           => 'nullable',

            'car_id'             => 'nullable',

            'address_id'         => 'nullable',

            'total_price'        => 'nullable',

            'payment_status'     => 'nullable',

            'cupon'              => 'nullable',

            'day_name'           => 'nullable',
            'unit'           => 'nullable',


        ]);



        try {



            if ($request->start_date) {



                $plan = Plan::withTrashed()->where('id', $request->plain_id)->first();



                if (!$plan) {

                    return response()->json([

                        'status' => false,

                        'message' => 'Plan not found.'

                    ], 404);
                }





                $exterior_days = $plan->exterior_days - $plan->interior_days;

                // $exterior_days = $plan->exterior_days;

                $selectedDatesRaw = is_array($request->selected_date)

                    ? $request->selected_date

                    : explode(',', $request->selected_date);



                $selectedDates = array_map(function ($date) {

                    return ['date' => $date, 'status' => '0'];
                }, $selectedDatesRaw);



                // Find max date from selected_date

                $maxDate = collect($selectedDatesRaw)->max();



                // Collect next $exterior_days dates from maxDate that match the day_name

                $futureMatchingDates = [];

                $currentDate = \Carbon\Carbon::parse($maxDate)->addDay(); // Start from next day



                while (count($futureMatchingDates) < $exterior_days) {

                    if ($currentDate->format('l') === $request->day_name) {

                        $futureMatchingDates[] = [

                            'date' => $currentDate->toDateString(),

                            'status' => '0'

                        ];
                    }

                    $currentDate->addDay();
                }



                $addOns = is_array($request->add_on)

                    ? $request->add_on

                    : explode(',', $request->add_on);



                $booking = Booking::create([

                    'user_id'             => $user->id,

                    'cupon'               => $validatedData['cupon'],

                    'start_date'          => $validatedData['start_date'],

                    'selected_date'       => json_encode($selectedDates),

                    'selected_date_2'     => json_encode($futureMatchingDates),

                    'add_on'              => implode(',', $addOns),

                    'selectedtime_slots'  => $validatedData['selectedtime_slots'],

                    'plain_id'            => $validatedData['plain_id'],

                    'car_id'              => $validatedData['car_id'],

                    'address_id'          => $validatedData['address_id'],

                    'total_price'         => $validatedData['total_price'],

                    'payment_status'      => $validatedData['payment_status'] ?? 0,

                ]);



                $responseData = [

                    'status'  => true,

                    'message' => 'Booking Created Successfully',

                    'data'    => [

                        ...$booking->toArray(),

                        'selected_date_array'  => $selectedDates,

                        'selected_date_2_array' => $futureMatchingDates,

                        'add_on_array'         => $addOns

                    ],

                    'payment_status' => match ($booking->payment_status) {

                        1 => 'Success',

                        2 => 'Failed',

                        default => 'Pending',
                    },

                ];



                return response()->json($responseData, 200);
            } else {



                $plan = Plan::withTrashed()->where('id', $request->plain_id)->first();



                if (!$plan) {

                    return response()->json([

                        'status' => false,

                        'message' => 'Plan not found.'

                    ], 404);
                }



                $exterior_days = $plan->exterior_days - $plan->interior_days;



                $selectedDatesRaw = is_array($request->selected_date)

                    ? $request->selected_date

                    : explode(',', $request->selected_date);



                $selectedDates = array_map(function ($date) {

                    return ['date' => $date, 'status' => '0'];
                }, $selectedDatesRaw);



                // Find max date from selected_date

                $maxDate = collect($selectedDatesRaw)->max();



                // Collect next $exterior_days dates from maxDate that match the day_name

                $futureMatchingDates = [];

                $currentDate = \Carbon\Carbon::parse($maxDate)->addDay(); // Start from next day



                while (count($futureMatchingDates) < $exterior_days) {

                    if ($currentDate->format('l') === $request->day_name) {

                        $futureMatchingDates[] = [

                            'date' => $currentDate->toDateString(),

                            'status' => '0'

                        ];
                    }

                    $currentDate->addDay();
                }



                $addOns = is_array($request->add_on)

                    ? $request->add_on

                    : explode(',', $request->add_on);



                $booking = Booking::create([

                    'user_id'             => $user->id,

                    'cupon'               => $validatedData['cupon'],

                    'start_date'          => $validatedData['start_date'],

                    'selected_date'       => json_encode($selectedDates),

                    'selected_date_2'     => json_encode($futureMatchingDates),

                    'add_on'              => implode(',', $addOns),

                    'selectedtime_slots'  => $validatedData['selectedtime_slots'],

                    'plain_id'            => $validatedData['plain_id'],

                    'car_id'              => $validatedData['car_id'],

                    'address_id'          => $validatedData['address_id'],

                    'total_price'         => $validatedData['total_price'],

                    'payment_status'      => $validatedData['payment_status'] ?? 0,

                ]);



                $responseData = [

                    'status'  => true,

                    'message' => 'Booking Created Successfully',

                    'data'    => [

                        ...$booking->toArray(),

                        'selected_date_array'  => $selectedDates,

                        'selected_date_2_array' => $futureMatchingDates,

                        'add_on_array'         => $addOns

                    ],

                    'payment_status' => match ($booking->payment_status) {

                        1 => 'Success',

                        2 => 'Failed',

                        default => 'Pending',
                    },

                ];



                return response()->json($responseData, 200);
            }
        } catch (\Exception $e) {

            return response()->json([

                'status'  => false,

                'message' => 'Failed to create booking: ' . $e->getMessage()

            ], 400);
        }
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
    $booking = Booking::with(['plan', 'vehicle', 'uservehicle', 'cleaners', 'uservehicleid', 'bookingAdd'])->find($id);

    if (!$booking) {
        return response()->json([
            'status'  => false,
            'message' => 'Booking not found',
            'data'    => null,
        ], 404);
    }

    $startDate = Carbon::parse($booking->start_date);
    $planExpireDate = $startDate->copy()->addMonth()->endOfMonth()->format('Y-m-d');

    $interiorDays = [];
    $exteriorDays = [];

    $bookingAdds = BookingAdd::where('booking_id', $id)->get();

    foreach ($bookingAdds as $add) {
        \Log::info('interior_days raw:', ['data' => $add->interior_days]);
        \Log::info('exterior_days raw:', ['data' => $add->exterior_days]);

        $interiorDays = array_merge($interiorDays, $this->processDays($add->interior_days));
        $exteriorDays = array_merge($exteriorDays, $this->processDays($add->exterior_days));
    }

    // Unique values
    $interiorDays = array_values(array_unique($interiorDays));
    $exteriorDays = array_values(array_unique($exteriorDays));

    // Process not at home
    $notAtHome = $this->processDays($booking->not_at_home);

    $formattedBooking = [
        'id'              => $booking->id,
        'start_date'      => $startDate->format('Y-m-d'),
        'plan_expire'     => $planExpireDate,
        'vehicle_name'    => $booking->vehicle?->name,
        'interior_days'   => !empty($interiorDays) ? $interiorDays : ['N/A'],
        'exterior_days'   => !empty($exteriorDays) ? $exteriorDays : ['N/A'],
        'not_at_home'     => $notAtHome,
        'cleaners_name'   => $booking->cleaners?->name,
        'cleaners_number' => $booking->cleaners?->mobile,
        'vehicle_number'  => $booking->uservehicleid?->vehicle_number,
        'body_type'       => $booking->uservehicle?->body_type,
        'vehicle_image'   => $booking->vehicle ? Helper::showImage($booking->vehicle->image) : null,
    ];

    return response()->json([
        'status'  => true,
        'message' => 'Booking details retrieved successfully',
        'data'    => $formattedBooking,
    ], 200);
}

private function processDays($daysData): array
{
    if (empty($daysData)) {
        return [];
    }

    // Handle JSON or comma-separated string
    if (is_string($daysData)) {
        $decoded = json_decode($daysData, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $daysData = $decoded;
        } else {
            $daysData = explode(',', $daysData);
        }
    }

    if (!is_array($daysData)) {
        return [];
    }

    return array_values(array_filter(array_map(function ($item) {
        try {
            $date = is_array($item) ? ($item['date'] ?? $item) : $item;
            return \Carbon\Carbon::parse(trim($date))->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }, $daysData)));
}




    //     public function subscriptiondetails($id): JsonResponse

    // {

    //     $booking = Booking::with(['plan', 'vehicle', 'uservehicle', 'cleaners'])->find($id);



    //     if (!$booking) {

    //         return response()->json([

    //             'status'  => false,

    //             'message' => 'Booking not found',

    //             'data'    => null,

    //         ], 404);

    //     }



    //     $startDate = Carbon::parse($booking->start_date);

    //     $planExpireDate = $startDate->copy()->addMonth()->endOfMonth()->format('Y-m-d');



    //     // ✅ FIXED: Properly decode JSON string or fallback to empty array

    //     $interiorDaysRaw = $booking->selected_date;

    //     $interiorDays = [];



    //     if (is_string($interiorDaysRaw)) {

    //         $decodedInterior = json_decode($interiorDaysRaw, true);

    //         if (is_array($decodedInterior)) {

    //             // Extract 'date' from each item and format it

    //             $interiorDays = array_map(fn($item) => Carbon::parse($item['date'])->format('Y-m-d'), $decodedInterior);

    //         }

    //     } elseif (is_array($interiorDaysRaw)) {

    //         // Same logic for raw array data if it is already an array

    //         $interiorDays = array_map(fn($date) => Carbon::parse($date)->format('Y-m-d'), $interiorDaysRaw);

    //     }



    //     $exteriorDays = [];

    //     for ($i = 3; $i < 6; $i++) {

    //         $exteriorDays[] = $startDate->copy()->addDays($i)->format('Y-m-d');

    //     }



    //     // ✅ FIXED: Decode not_at_home correctly

    //     $notAtHomeRaw = $booking->not_at_home;

    //     $notAtHome = [];



    //     if (is_string($notAtHomeRaw)) {

    //         $decoded = json_decode($notAtHomeRaw, true);

    //         if (is_array($decoded)) {

    //             $notAtHome = array_map(fn($date) => Carbon::parse($date)->format('Y-m-d'), $decoded);

    //         }

    //     } elseif (is_array($notAtHomeRaw)) {

    //         $notAtHome = array_map(fn($date) => Carbon::parse($date)->format('Y-m-d'), $notAtHomeRaw);

    //     }



    //     $formattedBooking = [

    //         'id'              => $booking->id,

    //         'start_date'      => $startDate->format('Y-m-d'),

    //         'plan_expire'     => $planExpireDate,

    //         'vehicle_name'    => $booking->vehicle ? $booking->vehicle->name : null,

    //         'interior_days'   => $interiorDays,

    //         'exterior_days'   => $exteriorDays,

    //         'not_at_home'     => $notAtHome,

    //         'cleaners_name'   => $booking->cleaners ? $booking->cleaners->name : null,

    //         'cleaners_number' => $booking->cleaners ? $booking->cleaners->mobile : null,

    //         'vehicle_number'  => $booking->uservehicle ? $booking->uservehicle->vehicle_number : null,



    //         'body_type'  => $booking->uservehicle ? $booking->uservehicle->body_type : null,



    //         'vehicle_image'   => $booking->vehicle ? Helper::showImage($booking->vehicle->image) : null,

    //     ];



    //     return response()->json([

    //         'status'  => true,

    //         'message' => 'Booking details retrieved successfully',

    //         'data'    => $formattedBooking,

    //     ], 200);

    // }











    public function updateNotAtHome(Request $request, $id)

    {

        $request->validate([
            'not_at_home' => 'required',

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
        $booking->not_at_home = json_encode($existingNotAtHome);
        $booking->save();
        return response()->json([
            'status'  => true,
            'message' => 'Not at home data updated successfully',
            'data'    => [
                'id'          => $booking->id,
                'not_at_home' => $existingNotAtHome,
            ]

        ]);
    }







    public function orderhistory(): JsonResponse

    {

        $user = Auth::user();

        $bookings = Booking::where('user_id', $user->id)->with(['plan', 'vehicle', 'uservehicle'])->orderBy('id', 'desc')->get();



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

                'body_type' => $booking->unit,



                'vehicle_image'  => $booking->vehicle ? Helper::showImage($booking->vehicle->image) : null,

            ];
        });



        return response()->json([

            'status'  => $formattedBookings->isNotEmpty(),

            'message' => $formattedBookings->isNotEmpty() ? 'Booking List' : 'No Bookings Found',

            'data'    => $formattedBookings,

        ], 200);
    }

    public function bookingorderhistory(Request $request): JsonResponse
    {
        $user = Auth::user();
    
        $bookings = BookingAdd::where([
            'user_id'    => $user->id,
            'booking_id' => $request->id
        ])->with('plan')->get();
    
        if ($bookings->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'Booking Not Found',
                'data'    => [],
            ], 404);
        }
    
        $planData = $bookings->first()?->plan ?? null;

        // $planData = optional($bookings->first()->plan);
    
        $bookingData = $bookings->map(function ($booking) {
            return [
                'id'             => $booking->id,
                'reason'         => $booking->reason ?? null,
                'image'          => $booking->image ? asset($booking->image) : null,
                'interior_days'  => $booking->interior_days ?? null,
                'exterior_days'  => $booking->exterior_days ?? null,
                'status'         => $booking->status ?? null,
            ];
        });
    
        return response()->json([
            'status'  => true,
            'message' => 'Booking Found',
            'data'    => [
                'plan'    => $planData,
                'details' => $bookingData,
            ],
        ], 200);
    }
    



    // public function bookingorderhistory(Request $request): JsonResponse
    // {
    //     $user = Auth::user();
    
    //     $bookings = BookingAdd::where([
    //         'user_id'    => $user->id,
    //         'booking_id' => $request->id
    //     ])->with('plan')->get();
    
    //     if ($bookings->isEmpty()) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Booking Not Found',
    //             'data'    => [],
    //         ], 404);
    //     }
    
    //     $bookingData = $bookings->map(function ($booking) {
    //         return [
    //             'id'     => $booking->id,
    //             'reason' => $booking->reason ?? null,
    //             'image'  => $booking->image ? asset($booking->image) : null,
    //             'interior_days' => $booking->interior_days ?? null,
    //             'exterior_days' => $booking->exterior_days ?? null,
    //             'status' => $booking->status ?? null,
    //             'plan'   => $booking->plan ?? null,


    //         ];
    //     });
    
    //     return response()->json([
    //         'status'  => true,
    //         'message' => 'Booking Found',
    //         'data'    => $bookingData,
    //     ], 200);
    // }
    





    // public function bookingorderhistory(Request $request): JsonResponse

    // {

    //     $user = Auth::user();
    //     $booking = BookingAdd::where([
    //         'user_id' => $user->id,
    //         'booking_id' => $request->id
    //     ])->with(['plan'])->get();

    //     if (!$booking) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Booking Not Found',
    //             'data' => [],
    //         ], 404);
    //     }

    //     $bookingData = [
    //         'id' => $booking->id,
    //         'reason' => $booking->reason,
    //         'plan' => $booking->plan,
    //         'image' => $booking->image,

    //     ];
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Booking Found',
    //         'data' => $bookingData,
    //     ], 200);
    // }
}
