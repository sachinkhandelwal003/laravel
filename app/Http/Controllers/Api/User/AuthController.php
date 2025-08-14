<?php


namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Referral;

use Illuminate\Http\Request;
use App\Models\AppUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{




    public function loginOrRegister(Request $request): JsonResponse
{
    $validator = Validator::make($request->all(), [
        'phone' => 'required|string|max:15',
    ], [
        'phone.required' => 'Phone number is required.',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation Failed',
            'errors' => $validator->errors()
        ], 422);
    }

    $otp = rand(100000, 999999);
    $otpExpiry = Carbon::now()->addMinutes(10);

    try {
        try {
            $sendUrl = "https://cpaas.messagecentral.com/verification/v3/send?countryCode=91&customerId=C-E497A6AEF8294FA&flowType=SMS&mobileNumber=$request->phone";
        
            $ch2 = curl_init($sendUrl);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch2, CURLOPT_POST, true);
            curl_setopt($ch2, CURLOPT_POSTFIELDS, ""); // No body
            curl_setopt($ch2, CURLOPT_HTTPHEADER, [
                "authToken: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJDLUU0OTdBNkFFRjgyOTRGQSIsImlhdCI6MTc0NjAxMzE5MiwiZXhwIjoxOTAzNjkzMTkyfQ.4KtmkMg4ji9OO1a1YmVzGPVcd5dki66SFdWk7_bRJo62Yb1xtbOYPcmfYBwqxPWHXO6PBOcRR60S00xlF0mKpQ",
                "Content-Type: application/json"
            ]);
            
            $otpResponse = curl_exec($ch2);
        
            if (curl_errno($ch2)) {
                throw new \Exception('cURL Error: ' . curl_error($ch2));
            }
        
            $otpStatus = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
            curl_close($ch2);
        
            if ($otpStatus == 200) {

                $otpid = json_decode($otpResponse, true);
               

                $verificationId = $otpid['data']['verificationId'];

                $user = AppUser::updateOrCreate(
                    ['phone' => $request->phone],
                    ['otp' => $verificationId, 'otp_expires_at' => $otpExpiry]
                );
       

                return response()->json([
                    'status' => true,
                    'message' => 'OTP sent successfully',
                    'otp_response' => json_decode($otpResponse, true)
                ]);



            } else {
                throw new \Exception("OTP sending failed. Status Code: $otpStatus. Response: $otpResponse");
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Exception while sending OTP',
                'error' => $e->getMessage()
            ], 500);
        }

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Exception occurred while sending OTP',
            'error' => $e->getMessage()
        ], 500);
    }
}




//     public function loginOrRegister(Request $request): JsonResponse
// {
//     $validator = Validator::make($request->all(), [
//         'phone' => 'required|string|max:15',
//     ], [
//         'phone.required' => 'Phone number is required.',
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Validation Failed',
//             'errors' => $validator->errors()
//         ], 422);
//     }

//     // Generate OTP and Save
//     $otp = rand(100000, 999999);
//     $otpExpiry = Carbon::now()->addMinutes(10);

//     $user = AppUser::updateOrCreate(
//         ['phone' => $request->phone],
//         ['otp' => $otp, 'otp_expires_at' => $otpExpiry]
//     );

//     try {
//         // Step 1: Get token using cURL
//         // $tokenUrl = "https://cpaas.messagecentral.com/auth/v1/authentication/token";
//         // $tokenPostData = http_build_query([
//         //     'customerId' => 'C-E497A6AEF8294FA',
//         //     'key'        => 'V2FzaC4xMjM0',
//         //     'scope'      => 'NEW',
//         //     'country'    => '91',
//         //     'email'      => 'magicwash29@gmail.com',
//         // ]);

//         // $ch = curl_init($tokenUrl);
//         // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//         // curl_setopt($ch, CURLOPT_POST, true);
//         // curl_setopt($ch, CURLOPT_POSTFIELDS, $tokenPostData);
//         // curl_setopt($ch, CURLOPT_HTTPHEADER, [
//         //     'Content-Type: application/x-www-form-urlencoded',
//         // ]);
//         // $tokenResponse = curl_exec($ch);
//         // $tokenStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//         // curl_close($ch);



//         try {
//             // Step 2: Send OTP using cURL with token in header
//             $sendUrl = "https://cpaas.messagecentral.com/verification/v3/send?countryCode=91&customerId=C-E497A6AEF8294FA&flowType=SMS&mobileNumber=$request->phone";
        
//             $ch2 = curl_init($sendUrl);
//             curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($ch2, CURLOPT_POST, true);
//             curl_setopt($ch2, CURLOPT_POSTFIELDS, ""); // No body
//             curl_setopt($ch2, CURLOPT_HTTPHEADER, [
//                 "authToken: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJDLUU0OTdBNkFFRjgyOTRGQSIsImlhdCI6MTc0NjAxMzE5MiwiZXhwIjoxOTAzNjkzMTkyfQ.4KtmkMg4ji9OO1a1YmVzGPVcd5dki66SFdWk7_bRJo62Yb1xtbOYPcmfYBwqxPWHXO6PBOcRR60S00xlF0mKpQ",
//                 "Content-Type: application/json"
//             ]);
            
//             $otpResponse = curl_exec($ch2);
        
//             if (curl_errno($ch2)) {
//                 throw new \Exception('cURL Error: ' . curl_error($ch2));
//             }
        
//             $otpStatus = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
//             curl_close($ch2);
        
//             if ($otpStatus == 200) {
//                 return response()->json([
//                     'status' => true,
//                     'message' => 'OTP sent successfully',
//                     'otp_response' => json_decode($otpResponse, true)
//                 ]);
//             } else {
//                 throw new \Exception("OTP sending failed. Status Code: $otpStatus. Response: $otpResponse");
//             }
            
//         } catch (\Exception $e) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Exception while sending OTP',
//                 'error' => $e->getMessage()
//             ], 500);
//         }

//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Exception occurred while sending OTP',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }






    public function loginOrRegister666(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:15',
        ], [
            'phone.required' => 'Phone number is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Failed',
                'errors' => $validator->errors()
            ], 422);
        };

        // Check if the user already exists
        $user = AppUser::where('phone', $request->phone)->first();

        // Generate a random OTP
        $otp = rand(100000, 999999);
        $otpExpiry = Carbon::now()->addMinutes(10);

        if (!$user) {
            // Register a new user if they don't exist
            $user = AppUser::create([
                'phone' => $request->phone,
                'otp' => $otp,
                'otp_expires_at' => $otpExpiry,
            ]);
        } else {
            // Update OTP and expiration for an existing user
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => $otpExpiry,
            ]);
        }


        return response()->json(['message' => 'OTP sent successfully', 'otp' => $otp]);
    }


public function verifyOtp(Request $request)
{
    $request->validate([
        'phone' => 'required|max:15',
        'otp' => 'required',
        'fcm_token' => 'nullable' // Add FCM token validation
    ]);

    $user = AppUser::where('phone', $request->phone)->first();
    if (!$user) {
        return response()->json(['message' => 'Invalid or expired OTP'], 401);
    }

    $verificationId = $user->otp;
    $code = $request->otp;

    $url = "https://cpaas.messagecentral.com/verification/v3/validateOtp?verificationId={$verificationId}&code={$code}";
    try {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "authToken: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJDLUU0OTdBNkFFRjgyOTRGQSIsImlhdCI6MTc0NjAxMzE5MiwiZXhwIjoxOTAzNjkzMTkyfQ.4KtmkMg4ji9OO1a1YmVzGPVcd5dki66SFdWk7_bRJo62Yb1xtbOYPcmfYBwqxPWHXO6PBOcRR60S00xlF0mKpQ",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('cURL Error: ' . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode == 200 && isset($responseData['responseCode']) && $responseData['responseCode'] == '200') {
            // Update FCM token
            $user->update([
                'fcm_token' => $request->fcm_token,
                'is_new' => 2
            ]);

            $token = $user->createToken('UserToken', ['userApi'])->accessToken;

            $referral = Referral::where('user_id', $user->id)->first();

            if (!$referral) {
                $referralCode = 'REF' . strtoupper(substr(md5(uniqid($user->id, true)), 0, 8));
                
                Referral::create([
                    'user_id' => $user->id,
                    'referral_code' => $referralCode,
                    'use_code' => $request->use_code,
                    'reward' => 100,
                ]);
            } else {
                $referralCode = $referral->referral_code;
            }

            return response()->json([
                'status' => true,
                'message' => 'OTP verified successfully',
                'data' => $responseData,
                'token' => $token,
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'OTP verification failed',
                'data' => $responseData
            ], 400);
        }

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Exception during OTP verification',
            'error' => $e->getMessage()
        ], 500);
    }
}


}
