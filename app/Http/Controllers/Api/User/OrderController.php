<?php

namespace App\Http\Controllers\Api\User;


use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\EmployeeLead;
use App\Models\EmployeeLeave;
use App\Models\Order;
use App\Models\UserVehicle;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
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

    public function index(Request $request): View|JsonResponse
    {

        $orders = Order::with(['plan', 'user_vehicle', 'user_vehicle.vehicle'])->where('user_id', $this->userId)
            ->orderBy('id', 'desc')
            ->get()->toArray();
        // dd($orders);

        if (count($orders) > 0) {

            $data = [];
            $payment_status_names = ['0' => 'Pending', '1' => 'Paid', '2' => 'Failed'];
            $status_names = ['0' => 'Pending', '1' => 'Completed', '2' => 'Cancelled'];
            foreach ($orders as $order) {
                $data[] = [
                    'id'             => $order['id'],
                    'start_date'     => $order['start_date'],
                    'plan_name'      => $order['plan']['name'] ?? 'N/A',
                    'plan_price'     => $order['plan']['price'] ?? 'N/A',
                    'vehicle_number' =>  $order['user_vehicle']['vehicle_number'] ?? null,
                    'vehicle_name'   => $order['user_vehicle']['vehicle']['name'] ?? null,
                    'vehicle_image'   => Helper::showImage($order['user_vehicle']['vehicle']['image']) ?? null,
                    'status'         => $status_names[$order['status']],
                ];
            }

            return response()->json([
                'status'    => true,
                'message'   => 'Address List',
                'data'      => $data,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Address Found',
                'data' => [],
            ], 200);
        }
    }


    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'plan_id'              => ['required'],
            'vehicle_id'           => ['required'],
            'address_id'           => ['required'],
            'start_date'           => ['required'],
            'end_date'             => ['required'],
            'start_time'           => ['required'],
            'end_time'             => ['required'],
            'payment_type'         => ['required'],
            'payment_status'       => ['required'],
            'status'               => ['required'],
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation Error",
                'errors' => $validator->errors(),
            ], 422);
        };


        $data = [...$validator->validated()];
        
        $data['user_id'] = $this->userId;
        
        if (!empty($request->address_id)) {
            $address = Address::find($request->address_id);
            $data['address_json'] = !empty($address) ? json_encode($address) : null;
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Address Not Found..!!',
                'data' => [],
            ], 404);
        }
        
        if (!empty($request->vehicle_id)) {
            $user_vehicle = UserVehicle::find($request->vehicle_id);
            $data['vehicle_json'] = !empty($user_vehicle) ? json_encode($user_vehicle) : null;
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Vehicle Not Found..!!',
                'data' => [],
            ], 404);
        }
        $address = Order::create($data);
        return response()->json([
            'status' => true,
            'message' => 'Order Placed Successfully.',
            'data' => $address,
        ], 200);
    }

    public function show($slug): JsonResponse
    {
        $address = Address::where('id', $slug)
            ->where('user_id', $this->userId)
            ->firstOrFail();
        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address Not Found~!!!!',
                'data' => [],
            ], 404);
        }
        $data = [
            'id'         => $address['id'],
            'user_id'    => $address['user_id'] ?? 'N/A',
            'label'      => $address['label'],
            'address'    => $address['address'],
            'city_id'    => $address['city_id'],
            'state_id'   => $address['state_id'],
            'country'    => $address['country'],
            'latitude'   => $address['latitude'],
            'longitude'  => $address['longitude'],
            'is_default' => $address['is_default'],
        ];
        return response()->json([
            'status' => true,
            'message' => 'Address Found!!!!',
            'data' => $data,
        ], 200);
    }
   
}
