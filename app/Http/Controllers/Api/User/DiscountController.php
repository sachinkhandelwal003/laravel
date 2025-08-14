<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Discount;
use App\Models\PriceDetail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\ActiveDeals;
use App\Models\Rewards;
use App\Models\Booking;

class DiscountController extends Controller
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
        $discount = Discount::all()->map(function ($discount) {
            $ids = !empty($discount->plan_id) ? explode(',', $discount->plan_id) : [];
            $plan_names = [];
    
            if (!empty($ids)) {
                $plan_names = Plan::whereIn('id', $ids)->pluck('name')->toArray();
            }
    
            return [
                'id' => $discount->id,
                'door_step_fee' => $discount->door_step_fee,
                'magic_wash_discount' => $discount->magic_wash_discount,
                'plateform_fee' =>  $discount->plateform_fee,
                'gst' => $discount->gst,
                'coupon' => $discount->coupon,
                'plans_name' => $plan_names, 
                'status' =>  $discount->status, 
            ];
        });
    
        return response()->json(['success' => true, 'data' => $discount], 200);
    }

    public function pricedetails(Request $request)
    {
        // dd($request);
// echo 'ascasca';die;

        try {
            $user = Auth::user();

            $cupon = $request->use_cupon;

            $ActiveDeals = ActiveDeals::where('code',$cupon)->first();
            $Rewards = Rewards::where(['code'=> $cupon, 'user_id'=>$user->id])->first();

            $Booking = Booking::where(['user_id'=> $user->id, 'cupon'=> $cupon])->first();
            // dd($Booking);
            if($Booking){
                $Rewardsdelete = Rewards::where(['code'=> $cupon, 'user_id'=>$user->id])->delete();
            }

            if($ActiveDeals){
                $Booking = Booking::where(['user_id'=> $user->id, 'cupon'=> $cupon])->first();
                if($Booking){
                    $discount = 0;
                }else{
                    $discount = $ActiveDeals->discount;

                }
            }elseif($Rewards){

                $discount = $Rewards->amount;


            }else{
                $discount = 0;
            }

            // dd($user);
            $priceDetail = PriceDetail::where('status', 1) 
                ->first(); 
            
            if (!$priceDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active price details found'
                ], 404);
            }
    
            $formattedDetail = [
                'id' => $priceDetail->id,
                'magicwash_discount' => $priceDetail->magicwash_discount,
                'plateform_fee' => $priceDetail->plateform_fee,
                'tax' => $priceDetail->tax,
                'status' => $priceDetail->status,
                'discount' => $discount,

            ];
    
            return response()->json([
                'success' => true,
                'data' => $formattedDetail
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve price details',
                'dsdss' => $e,
            ], 500);
        }
    }



    /**
     * Store a newly created coupon in storage.
     */
   public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => 'required',
        ]);
    
        $discount = Discount::create($validated);
    
        return response()->json([
            'success' => true,
            'message' => 'Discount added successfully',
            'data' => $discount
        ], 200);
    }


    
   
}
