<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;

class RazorpayController extends Controller
{
    public function createOrder(Request $request)
    {
        $api = new Api(env('RAZORPAY_API_KEY'), env('RAZORPAY_API_SECRET'));
        
        $orderData = [
            'amount' => $request->amount * 100, // Razorpay expects amount in paise
            'currency' => 'INR',
            'receipt' => 'order_rcptid_'.time(),
            'payment_capture' => 1 // auto-capture payment
        ];
        
        try {
            $order = $api->order->create($orderData);
            
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'amount' => $order->amount,
                'currency' => $order->currency,
                'key' => env('RAZORPAY_API_KEY')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function verifyPayment(Request $request)
    {
        $api = new Api(env('RAZORPAY_API_KEY'), env('RAZORPAY_API_SECRET'));
        
        try {
            $attributes = [
                'razorpay_order_id' => $request->order_id,
                'razorpay_payment_id' => $request->payment_id,
                'razorpay_signature' => $request->signature
            ];
            
            $api->utility->verifyPaymentSignature($attributes);
            
            // Payment is successful
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}