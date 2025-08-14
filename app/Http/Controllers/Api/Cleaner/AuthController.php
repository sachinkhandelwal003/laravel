<?php


namespace App\Http\Controllers\Api\Cleaner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppUser;
use App\Helper\Helper;
use App\Models\Cleaner;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function login(Request $request): JsonResponse   
{
    $validator = Validator::make($request->all(), [
        "mobile"    => "required|exists:cleaners,mobile",
        "password"  => "required",
        "fcm_token" => "nullable" 
    ]);

    if ($validator->fails()) {
        return response()->json([
            "status"  => false,
            "message" => "Validation Error.",
            'errors'  => $validator->errors()
        ], 422);
    }

    $cleaner = Cleaner::where('mobile', $request->mobile)->first();
   
    if (!$cleaner || !Hash::check($request->password, $cleaner->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $isNewUser = is_null($cleaner->last_login_at); 

    $cleaner->update([
        'fcm_token' => $request->fcm_token,
        'last_login_at' => now()
    ]);

    $token = $cleaner->createToken('CleanerToken', ['cleanerApi'])->accessToken;

    return response()->json([
        'token'       => $token,
        'cleaner'     => [
            'id'             => $cleaner->id,
            'user_id'        => $cleaner->user_id,
            'name'           => $cleaner->name,
            'wallet_balance' => $cleaner->wallet_balance,
            'image'          => $cleaner->image,
            'mobile'         => $cleaner->mobile,
            'email'          => $cleaner->email,
            'address'        => $cleaner->address,
            'area'           => $cleaner->area,
            'status'         => $cleaner->status,
            'last_login_at'  => optional($cleaner->last_login_at)->format('d-m-Y, H:i'),
        ],
        'is_new_user' => $isNewUser  
    ], 200);
}
    public function getProfile(Request $request): JsonResponse
    {
        $cleaner = $request->user();
    
        if (!$cleaner) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
    
        return response()->json([
            'status' => true,
            'cleaner' => [
                'id'             => $cleaner->id,
                'user_id'        => $cleaner->user_id,
                'name'           => $cleaner->name,
                'wallet_balance' => $cleaner->wallet_balance,
                'image' => Helper::showImage($cleaner->image, true),
                'mobile'         => $cleaner->mobile,
                'email'          => $cleaner->email,
                'address'        => $cleaner->address,
                'area'           => $cleaner->area,
                'bank_name'      => $cleaner->bank_name,
                'account_no'     => $cleaner->account_no,
                'ifsc_code'      => $cleaner->ifsc_code,
                'id_card'        => $cleaner->id_card,
                'superviser'     => $cleaner->superviser,
                'id_proof'       => $cleaner->id_proof,
                'performance'    => $cleaner->performance,
                'status'         => $cleaner->status,
                
            ]
        ], 200);
    }
    
   public function updateProfile(Request $request): JsonResponse
{
    $cleaner = $request->user();

    if (!$cleaner) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized',
        ], 401);
    }

    $validator = Validator::make($request->all(), [
        'name'       => 'nullable|string',
        'email'      => 'nullable|email',
        'mobile'     => 'nullable|string',
        'address'    => 'nullable|string',
        'area'       => 'nullable|string',
        'image'      => 'nullable|image',
        'bank_name'  => 'nullable|string',
        'account_no' => 'nullable|string',
        'ifsc_code'  => 'nullable|string',
        'id_card'    => 'nullable|string',
        'id_proof'   => 'nullable|in:1,2', // Ensure it's either 1 or 2
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation Error.',
            'errors' => $validator->errors()
        ], 422);
    }

    // Image upload using Helper
    if ($request->hasFile('image')) {
        $cleaner->image = Helper::saveFile($request->file('image'), 'cleaners');
    }

    // Update all fields including id_card and id_proof
    $cleaner->fill($request->only([
        'name',
        'email',
        'mobile',
        'address',
        'area',
        'bank_name',
        'account_no',
        'ifsc_code',
        'id_card',
        'id_proof'
    ]));

    $cleaner->save();

    return response()->json([
        'status' => true,
        'message' => 'Profile updated successfully.',
        'cleaner' => [
            'id'             => $cleaner->id,
            'name'           => $cleaner->name,
            'email'          => $cleaner->email,
            'mobile'         => $cleaner->mobile,
            'address'        => $cleaner->address,
            'area'           => $cleaner->area,
            'image'          => Helper::showImage($cleaner->image, true),
            'bank_name'      => $cleaner->bank_name,
            'account_no'     => $cleaner->account_no,
            'ifsc_code'      => $cleaner->ifsc_code,
            'id_card'        => $cleaner->id_card,
            'id_proof'       => $cleaner->id_proof,
        ]
    ], 200);
}



}

