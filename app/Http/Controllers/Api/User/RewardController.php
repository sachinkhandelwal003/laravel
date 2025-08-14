<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Rewards;
use App\Models\WalletHistories;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RewardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:userApi']);
    }

    /**
     * Display a listing of the coupons.
     */
     
public function applyCode(Request $request): JsonResponse
{
    $validated = $request->validate([
        'code' => 'required',
    ]);

    // Find the reward using the provided code
    $reward = Rewards::where('code', $validated['code'])->first();

    // Check if the reward exists
    if (!$reward) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid reward code'
        ], 400);
    }

    // Check if the reward is expired
    if ($reward->valid_at < Carbon::today()) {
        return response()->json([
            'success' => false,
            'message' => 'This reward code has expired'
        ], 400);
    }

    // Check if the code is already used
    if ($reward->status == 2) {
        return response()->json([
            'success' => false,
            'message' => 'Code already used'
        ], 400);
    }

    // Mark the reward code as used
    $reward->update(['status' => 2]);

    // Store the transaction in WalletHistories
    WalletHistories::create([
        'user_id'     => auth()->id(),
        'type'        => 'Deposit',
        'points'      => $reward->amount,
        'points_type' => 'credit',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Code applied successfully',
        'data'    => $reward
    ], 200);
}


public function index(): JsonResponse
{
    $userId = Auth::id();

    $rewards = Rewards::where('user_id', $userId)
        ->whereDate('valid_at', '>=', Carbon::today())
        ->get()
        ->map(function ($reward) {
            return [
                'id'          => $reward->id,
                'user_id'     => (string) $reward->user_id,
                'reward_type' => $reward->reward_type,
                'amount'      => (string) $reward->amount,
                'code'        => $reward->code,
                'status'      => (int) $reward->status,
                'valid_at'    => $reward->valid_at->toDateString(), // optional
            ];
        });

    return response()->json([
        'success' => true,
        'data'    => $rewards,
    ], 200);
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
