<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use App\Helper\Helper;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AddonController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:userApi']);
    }

    /**
     * Display a listing of the coupons.
     */
     
    
  public function index(): JsonResponse
    {
        $addon = AddOn::all()->map(function ($addon) {
            return [
                'id' => $addon->id,
                'name' => $addon->name,
                'image' => Helper::showImage($addon->image), 
                'price' => $addon->price,
                'description' => $addon->description,
            ];
        });
        
        return response()->json(['success' => true, 'data' => $addon], 200);
    }




    /**
     * Store a newly created coupon in storage.
     */
       public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reward_type' => 'required',
            'amount' => 'required',
            'status' => 'required'
        ]);
    
      
        $validated['code'] = $this->generateUniqueCode();
    
        $reward = Rewards::create($validated);
    
        return response()->json(['success' => true, 'data' => $reward], 201);
    }
    
    /**
     * Generate a unique coupon code.
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8)); // Example: 8-character unique code
        } while (Rewards::where('code', $code)->exists());
    
        return $code;
    }

    /**
     * Display the specified coupon.
     */
    public function show($id): JsonResponse
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $coupon], 200);
    }

    /**
     * Update the specified coupon in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found'], 404);
        }

        $validated = $request->validate([
            'code' => 'sometimes|string|unique:coupons,code,' . $id,
            'discount_type' => 'sometimes|string',
            'discount_value' => 'sometimes|numeric',
            'min_order_value' => 'sometimes|numeric',
            'expiry_date' => 'sometimes|date',
            'is_active' => 'sometimes|boolean'
        ]);

        $coupon->update($validated);

        return response()->json(['success' => true, 'data' => $coupon], 200);
    }

    /**
     * Remove the specified coupon from storage.
     */
    public function destroy($id): JsonResponse
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found'], 404);
        }

        $coupon->delete();

        return response()->json(['success' => true, 'message' => 'Coupon deleted successfully'], 200);
    }
}
