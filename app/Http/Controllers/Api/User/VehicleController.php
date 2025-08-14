<?php

namespace App\Http\Controllers\Api\User;


use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\UserVehicle;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
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

        $vehicles = UserVehicle::with('vehicle', 'brands')->where('user_id', $this->userId)
            ->when(!empty($request->type), function ($query) use ($request) {
                $query->where('vehicle_type', $request->type);
            })
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        // dd($vehicles);
        if (count($vehicles) > 0) {

            $data = [];
            $body_types = [1 => 'Hatchback', 2 => 'Sedan', 3 => 'Suv', 4 => 'Bike', 5 => 'Scooty'];
            $vehicle_types = [1 => 'Car', 2 => 'Bike', 3 => 'Scooty'];
            foreach ($vehicles as $vehicle) {
                $data[] = [
                    'id' => $vehicle['id'],
                    'user_id' => $vehicle['user_id'] ?? 'N/A',
                    'vehicle_id' => $vehicle['vehicle_id'],
                    'vehicle_name' => $vehicle['vehicle']['name'] ?? 'N/A',
                    'vehicle_image' => Helper::getVehicleImage($vehicle['body_type']) ?? 'N/A',
                    'vehicle_type' => $vehicle['vehicle_type'],
                    'vehicle_type_name' => $vehicle_types[$vehicle['vehicle_type']] ?? null,
                    'brand_id' => $vehicle['brand_id'],
                    'brand_name' => $vehicle['brands']['name'] ?? 'N/A',
                    'body_type' => $vehicle['body_type'],
                    'body_type_name' => $body_types[$vehicle['body_type']] ?? null,
                    // 'address' => $vehicle['address'],
                    'vehicle_number' => $vehicle['vehicle_number'],
                    'parking_number' => $vehicle['parking_number'],
                    'color' => $vehicle['color'],
                    'is_default' => $vehicle['is_default'],

                ];
            }

            return response()->json([
                'status'    => true,
                'message'   => 'Vehicle List',
                'data'      => $data,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Vehicle Found',
                'data' => [],
            ], 200);
        }
    }


    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id'      => 'required|exists:vehicles,id',
            'brand_id'        => 'required|exists:brands,id',
            'vehicle_type'    => 'required',
            'body_type'       => 'required',
            // 'addre ss'         => 'required',
            'vehicle_number'  => 'required',
            'parking_number'  => 'required',
            'color'           => 'required',
        ], [
            'vehicle_id.exists'  => 'The selected car does not exist.',
            'brand_id.exists' => 'The selected brand does not exist.',
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
        $data['is_default'] = 1;
        UserVehicle::where('user_id', $this->userId)->update(['is_default' => 0]);
        $vehicle = UserVehicle::create($data);
        return response()->json([
            'status' => true,
            'message' => 'Vehicle Added Successfully.',
            'data' => $vehicle,
        ], 200);
    }

    public function show($slug): JsonResponse
    {
        $vehicle = UserVehicle::where('id', $slug)
            ->where('user_id', $this->userId)
            ->firstOrFail();
        if (!$vehicle) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle Not Found~!!!!',
                'data' => [],
            ], 404);
        }
        $data = [
            'id' => $vehicle['id'],
            'user_id' => $vehicle['user_id'] ?? 'N/A',
            'vehicle_id' => $vehicle['vehicle_id'],
            'brand_id' => $vehicle['brand_id'],
            'vehicle_type' => $vehicle['vehicle_type'],
            'body_type' => $vehicle['body_type'],
            // 'address' => $vehicle['address'],
            'vehicle_number' => $vehicle['vehicle_number'],
            'parking_number' => $vehicle['parking_number'],
            'color' => $vehicle['color'],
            'is_default' => $vehicle['is_default'],
        ];
        return response()->json([
            'status' => true,
            'message' => 'Vehicle Found!!!!',
            'data' => $data,
        ], 200);
    }
    public function update(Request $request, $slug): JsonResponse
    {
        $vehicle = UserVehicle::where('id', $slug)
            ->where('user_id', $this->userId)
            ->firstOrFail();
        if (!$vehicle) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle Not Found',
                'data' => [],
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'vehicle_id'      => 'required|exists:vehicles,id',
            'brand_id'        => 'required|exists:brands,id',
            'vehicle_type'    => 'required',
            'body_type'       => 'required',
            // 'address'         => 'required',
            'vehicle_number'  => 'required',
            'parking_number'  => 'required',
            'color'           => 'required',
        ], [
            'vehicle_id.exists'  => 'The selected car does not exist.',
            'brand_id.exists' => 'The selected brand does not exist.',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation Error",
                'errors' => $validator->errors(),
            ], 422);
        };
        $data = $validator->validated();



        $vehicle->update($data);
        return response()->json([
            'status' => true,
            'message' => 'Vehicle Updated',
            'data' => $vehicle,
        ], 200);
    }

    public function destroy(Request $request, $slug): JsonResponse
    {
        $vehicle = UserVehicle::where('id', $slug)
            ->where('user_id', $this->userId)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle Not Found',
                'data' => [],
            ], 404);
        }

        Helper::deleteRecord(new UserVehicle, $slug);

        $vehicle = UserVehicle::where('user_id', $this->userId)->first();

        if ($vehicle) {
            $vehicle->update(['is_default' => 1]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Vehicle Deleted',
            'data' => ['id' => $slug],
        ], 200);
    }

    public function update_default(Request $request, $id): JsonResponse
    {
        $vehicle = UserVehicle::where('id', $id)
            ->where('user_id', $this->userId)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle Not Found',
                'data' => [],
            ], 404);
        }

        if ($vehicle->is_default) {
            return response()->json([
                'status' => true,
                'message' => 'Vehicle is already default',
                'data' => ['id' => $id],
            ], 200);
        }

        DB::transaction(function () use ($vehicle) {
            UserVehicle::where('user_id', $this->userId)->update(['is_default' => 0]);
            $vehicle->update(['is_default' => 1]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Default Vehicle Updated',
            'data' => ['id' => $vehicle->id],
        ], 200);
    }
}
