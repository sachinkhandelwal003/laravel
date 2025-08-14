<?php

namespace App\Http\Controllers\Api\User;


use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
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

  $addresses = Address::where('user_id', $this->userId)
    ->orderBy('id', 'desc')
    ->get()
    ->toArray();

        if (count($addresses) > 0) {

            $data = [];
            foreach ($addresses as $address) {
                $data[] = [
                    'id' => $address['id'],
                    'user_id' => $address['user_id'] ?? 'N/A',
                    // 'city_id'    => $address['city_id'] ?? 'N/A',
                    // 'city_name'    => $address['city']['name'] ?? 'N/A',
                      // 'city_name'    => $address['city']['name'] ?? 'N/A',
                    'label' => $address['label'],
                    'address_type' => $address['address_type'] ?? 'N/A',

                    'address' => $address['address'],
                    'latitude' => $address['latitude'],
                    'longitude' => $address['longitude'],
                    'is_default' => $address['is_default'],
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
            'label'      => 'required|string|max:255',
            'address'    => 'required|string|max:500',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'address_type'    => 'required',
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

        Address::where('user_id', $this->userId)->update(['is_default' => 0]);

        $address = Address::create($data);
        $address->is_default = 1;
        $address->save();
        return response()->json([
            'status' => true,
            'message' => 'Address Added Successfully.',
            'data' => $address,
        ], 200);
    }

    public function show($slug): JsonResponse
    {
        $address = Address::with('city')->where('id', $slug)
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
            'city_id'    => $address['city_id'] ?? 'N/A',
            'city_name'    => $address['city']['name'] ?? 'N/A',
            'label'      => $address['label'],
            'address'    => $address['address'],
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
    public function update(Request $request, $slug): JsonResponse
    {
        $address = Address::where('id', $slug)
            ->where('user_id', $this->userId)
            ->firstOrFail();
        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address Not Found',
                'data' => [],
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'label'    => 'required|string|max:255',
            'address'  => 'required|string|max:500',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'city_id'    => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation Error",
                'errors' => $validator->errors(),
            ], 422);
        };
        $data = $validator->validated();



        $address->update($data);
        return response()->json([
            'status' => true,
            'message' => 'Address Updated',
            'data' => $address,
        ], 200);
    }

    public function destroy(Request $request, $slug): JsonResponse
    {
        $address = Address::where('id', $slug)
            ->where('user_id', $this->userId)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address Not Found',
                'data' => [],
            ], 404);
        }

        Helper::deleteRecord(new Address, $slug);

        $address = Address::where('user_id', $this->userId)->first();

        if ($address) {
            $address->update(['is_default' => 1]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Address Deleted',
            'data' => ['id' => $slug],
        ], 200);
    }

    public function update_default(Request $request, $id): JsonResponse
    {
        $address = Address::where('id', $id)
            ->where('user_id', $this->userId)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address Not Found',
                'data' => [],
            ], 404);
        }

        if ($address->is_default) {
            return response()->json([
                'status' => true,
                'message' => 'Address is already default',
                'data' => ['id' => $id],
            ], 200);
        }

        DB::transaction(function () use ($address) {
            Address::where('user_id', $this->userId)->update(['is_default' => 0]);
            $address->update(['is_default' => 1]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Default Address Updated',
            'data' => ['id' => $address->id],
        ], 200);
    }
}
