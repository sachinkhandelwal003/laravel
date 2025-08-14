<?php



namespace App\Http\Controllers\Api\User;



use App\Http\Controllers\Controller;

use App\Models\Referral;

use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;

use App\Helper\Helper;

use Carbon\Carbon;





use Illuminate\Support\Facades\Auth;



class ReferralCodeController extends Controller

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





    public function generateReferralCode()

    {

        do {

            $randomNumber = mt_rand(10000, 99999);

            $referralCode = 'REF' . $randomNumber;

        } while (Referral::where('referral_code', $referralCode)->exists());



   

        $referral = Referral::create(['referral_code' => $referralCode]);



        return response()->json([

            'success' => true,

            'referral_code' => $referral->referral_code

        ]);

    }



    public function joinWithReferralCode(Request $request)

    {



        $user = Auth::guard('userApi')->user();



        if (!$user) {

            return response()->json([

                'success' => false,

                'message' => 'Unauthorized. Invalid token.',

            ], 401);

        }



        $request->validate([

            'referral_code' => 'required|string|exists:referrals,referral_code',

        ]);



        $referral = Referral::where('referral_code', $request->referral_code)->first();



        if ($referral->user_id) {

            return response()->json([

                'success' => false,

                'message' => 'This referral code has already been used.',

            ], 400);

        }





        $referral->user_id = $user->id;

        $referral->save();



        return response()->json([

            'success' => true,

            'message' => 'Referral code applied successfully.',

            'referral_code' => $referral->referral_code,

            'user_id' => $user->id,

        ]);

    }





    public function getTotalJoined()

    {



        $totalJoined = Referral::whereNotNull('user_id')->count();



        return response()->json([

            'success' => true,

            'total_joined' => $totalJoined

        ]);

    }



    public function getTotalEarned()

    {

        $user = Auth::guard('userApi')->user();



        if (!$user) {

            return response()->json([

                'success' => false,

                'message' => 'Unauthorized. Invalid token.',

            ], 401);

        }





        $totalEarned = Referral::where('user_id', $user->id)->sum('reward');



        return response()->json([

            'success' => true,

            'total_earned' => $totalEarned

        ]);

    }



    public function getReferralStatistics()
{
    $user = Auth::guard('userApi')->user();

    // Check if user is authenticated
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Invalid token.',
        ], 401);
    }


    $referral = Referral::where('user_id', $user->id)->first();

    if (!$referral) {
        return response()->json([
            'success' => true,
            'referral_code' => null,
            'total_joined_count' => 0,
            'total_earned_count' => 0
        ]);
    }

    $referralCode = $referral->referral_code; 
    $useCode = $referral->use_code;        

 
    $referredCount = Referral::where('use_code', $referralCode)->count();
    $referrerCount = Referral::where('referral_code', $useCode)->count();

   
    $earningsFromMyCode = $referredCount * 100;
    $earningsFromUseCode = $referrerCount * 50;

    $totalEarned = $earningsFromMyCode + $earningsFromUseCode;

    return response()->json([
        'success' => true,
        'referral_code' => $referralCode,
        'total_joined_count' => $referredCount,
        'total_earned_count' => $totalEarned
    ]);
}



    // public function getReferralStatistics()
    // {
    //     $user = Auth::guard('userApi')->user();
    //     if (!$user) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthorized. Invalid token.',
    //         ], 401);
    //     }


    //     $referral = Referral::where('user_id', $user->id)->first();
    //     $referralCode = $referral ? $referral->referral_code : null;

    //     $useCode = $referral ? $referral->use_code : null;
    //     $usecodetotalJoinedCount = Referral::where('referral_code', $useCode)->count();
    //     $totalusecodeearn = $usecodetotalJoinedCount * 50;

    //     $totalJoinedCount = Referral::where('use_code', $referralCode)->count();

    //     $usemycode = Referral::where('use_code', $referralCode)->count();
    //     $totalEarnedSumus = $totalJoinedCount * 100;

    //     $totalEarnedSum = $totalEarnedSumus + $totalusecodeearn;
    //     return response()->json([
    //         'success' => true,
    //         'referral_code' => $referralCode,
    //         'total_joined_count' => $totalJoinedCount,
    //         'total_earned_count' => $totalEarnedSum
    //     ]);
    // }


    // public function getReferralStatistics()
    // {
    //     $user = Auth::guard('userApi')->user();
    //     if (!$user) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthorized. Invalid token.',
    //         ], 401);
    //     }
    //     $referral = Referral::where('user_id', $user->id)->first();
    //     $referralCode = $referral ? $referral->referral_code : null;

    //     $totalJoinedCount = Referral::whereNotNull('user_id')->count();
    //     $totalEarnedSum = Referral::where('user_id', $user->id)->sum('reward');
    //     return response()->json([
    //         'success' => true,
    //         'referral_code' => $referralCode,
    //         'total_joined_count' => $totalJoinedCount,
    //         'total_earned_count' => $totalEarnedSum
    //     ]);
    // }

}
