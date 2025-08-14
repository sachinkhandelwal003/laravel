<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\WalletHistories;

class WalletHistoryController extends Controller
{
    public function getTotalAmount(): JsonResponse
    {
        $totalAmount = WalletHistories::sum('points');

        return response()->json([
            'status' => true,
            'message' => 'Total Wallet Amount Calculated',
            'total_amount' => $totalAmount,
        ], 200);
    }
    
 public function getWalletHistory(): JsonResponse
{
    $walletHistory = WalletHistories::with('user') // Eager load user data
        ->select('user_id', 'type', 'points', 'points_type', 'created_at')
        ->get()
        ->map(function ($item) {
            return [
                'user_name' => $item->user ? $item->user->name : 'Unknown', // Get name from relation
                'type' => $item->type ?? 0,
                'points' => $item->points ?? 0,
                'points_type' => $item->points_type ?? 0,
                'created_at' => $item->created_at ? $item->created_at->format('d-m-Y') : '00-00-0000'
            ];
        });

    return response()->json([
        'status' => true,
        'message' => 'Wallet History Fetched Successfully',
        'data' => $walletHistory,
    ], 200);
}


}
