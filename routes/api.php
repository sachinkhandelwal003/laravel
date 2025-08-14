<?php



use App\Http\Controllers\Api\Cleaner\AuthController as CleanerAuthController;

use App\Http\Controllers\Api\Cleaner\HomeController as CleanerHomeController;

use App\Http\Controllers\Api\Cleaner\UserController as CleanerUserController;

use App\Http\Controllers\Api\Cleaner\LeaveController;

use App\Http\Controllers\Api\User\AddOnBookController;

use App\Http\Controllers\Api\User\AddressController;

use App\Http\Controllers\Api\User\AuthController;

use App\Http\Controllers\Api\User\BookingController;

use App\Http\Controllers\Api\User\ContactController;

use App\Http\Controllers\Api\User\DiscountController;

use App\Http\Controllers\Api\User\FaqController;

use App\Http\Controllers\Api\User\HelpSupportController;

use App\Http\Controllers\Api\User\ReferralCodeController;

use App\Http\Controllers\Api\User\HomeController;

use App\Http\Controllers\Api\User\OrderController;

use App\Http\Controllers\Api\User\RewardController;

use App\Http\Controllers\Api\User\UserController;

use App\Http\Controllers\Api\User\VehicleController;

use App\Http\Controllers\Api\User\ActiveDealsController;

use App\Http\Controllers\Api\User\DiscountController as DiscountsController;

use App\Http\Controllers\Api\User\AddonController as AddonsController;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\Api\User\WalletHistoryController;

use App\Http\Controllers\Api\Cleaner\CleanerEarningController;

use App\Http\Controllers\Api\Cleaner\CleanerReferralCodeController;

use App\Http\Controllers\Api\Cleaner\TermsConditionController;

/*

|--------------------------------------------------------------------------

| API Routes

|--------------------------------------------------------------------------

|

| Here is where you can register API routes for your application. These

| routes are loaded by the RouteServiceProvider and all of them will

| be assigned to the "api" middleware group. Make something great!

|

*/

Route::get('/get-razorpay-key', function (Request $request) {
    return response()->json([
        'key' => env('RAZORPAY_API_KEY')
    ]);
});

Route::get('/', function () {

    return response()->json([

        'message'   => "Api Working Fine."

    ]);
});

// Route::get('home', 'index');
// Route::get('/home', [CleanerHomeController::class, 'index']);


Route::get('clear-all', function () {

    Artisan::call('cache:clear');

    Artisan::call('config:clear');

    Artisan::call('route:clear');

    Artisan::call('view:clear');

    Artisan::call('storage:link');

    return '<h1>Clear All</h1>';
});



Route::post('/price-details', [DiscountController::class, 'pricedetails']);



Route::middleware(['checkVerifykey',])->group(function () {



    Route::get('home', [HomeController::class, 'home']);

    Route::post('login-or-register', [AuthController::class, 'loginOrRegister']);

    Route::post('cleaners/login', [CleanerAuthController::class, 'login']);

    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);

    Route::get('get-cities/{state_id}', [HomeController::class, 'get_cities']);

    Route::get('get-cities', [HomeController::class, 'get_all_cities']);

    Route::get('get-states', [HomeController::class, 'get_states']);

    Route::get('get-brands', [HomeController::class, 'get_brands']);

    Route::get('get-cars/{id}', [HomeController::class, 'get_cars']);

    Route::get('get-plans/{body_type}', [HomeController::class, 'get_plans']);




    Route::get('get-add-ons', [HomeController::class, 'get_add_ons']);

    Route::get('get-popular-plans', [HomeController::class, 'get_popular_plans']);

    Route::get('get-banners', [HomeController::class, 'get_banners']);

    Route::get('get-blogs', [HomeController::class, 'get_blogs']);

    Route::get('get-blogs/{id}', [HomeController::class, 'get_blogs_details']);

    Route::get('get-testimonials', [HomeController::class, 'get_testimonials']);



    Route::get('/bookings', [BookingController::class, 'index']);

    Route::post('/bookings', [BookingController::class, 'store']);

    Route::get('/bookings/{id}', [BookingController::class, 'show']);

    Route::put('/bookings/{id}', [BookingController::class, 'update']);

    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);



    Route::get('subscription-details/{id}', [BookingController::class, 'subscriptiondetails']);

    Route::post('not-at-home/{id}', [BookingController::class, 'updateNotAtHome']);

    Route::get('order-history', [BookingController::class, 'orderhistory']);



    Route::get('booking-order-history', [BookingController::class, 'bookingorderhistory']);



    Route::get('/contacts', [ContactController::class, 'index']);

    Route::post('/contacts', [ContactController::class, 'store']);

    Route::get('/contacts/{id}', [ContactController::class, 'show']);

    Route::put('/contacts/{id}', [ContactController::class, 'update']);

    Route::delete('/contacts/{id}', [ContactController::class, 'destroy']);

    Route::get('/faqs', [FaqController::class, 'index']);

    Route::post('/faqs', [FaqController::class, 'store']);

    Route::get('/faqs/{id}', [FaqController::class, 'show']);

    Route::put('/faqs/{id}', [FaqController::class, 'update']);

    Route::delete('/faqs/{id}', [FaqController::class, 'destroy']);

    Route::get('/help-support', [HelpSupportController::class, 'index']);

    Route::post('/help-support', [HelpSupportController::class, 'store']);

    Route::get('/help-support/{id}', [HelpSupportController::class, 'show']);

    Route::put('/help-support/{id}', [HelpSupportController::class, 'update']);

    Route::delete('/help-support/{id}', [HelpSupportController::class, 'destroy']);

    Route::get('/generate-referral-code', [ReferralCodeController::class, 'generateReferralCode']);

    Route::post('/join-with-referral', [ReferralCodeController::class, 'joinWithReferralCode']);

    Route::get('/total-joined', [ReferralCodeController::class, 'getTotalJoined']);

    Route::get('/total-earned', [ReferralCodeController::class, 'getTotalEarned']);

    Route::get('/total-earning-data', [ReferralCodeController::class, 'getReferralStatistics']);

    Route::get('/rewards', [RewardController::class, 'index']);

    Route::post('/rewards', [RewardController::class, 'store']);

    Route::get('/coupons/{id}', [CouponController::class, 'show']);

    Route::put('/coupons/{id}', [CouponController::class, 'update']);

    Route::delete('/coupons/{id}', [CouponController::class, 'destroy']);

    Route::post('/coupons-apply', [RewardController::class, 'applyCode']);

    Route::get('/active-deals', [ActiveDealsController::class, 'index']);

    Route::get('/discounts', [DiscountsController::class, 'index']);

    Route::get('/add-on', [AddonsController::class, 'index']);

    Route::post('/discount', [DiscountsController::class, 'store']);

    Route::get('/wallet/total', [WalletHistoryController::class, 'getTotalAmount']);

    Route::get('/wallet/history', [WalletHistoryController::class, 'getWalletHistory']);

    Route::get('/add-on-book', [AddOnBookController::class, 'index']);

    // Route::post('/price-details', [DiscountController::class, 'pricedetails']);



    // Route::get('/getAuthUser', [ReferralCodeController::class, 'getAuthUser']);



    Route::middleware(['auth:userApi'])->group(function () {



        Route::get('/user', [UserController::class, 'getUserDetails'])->name('user.details');

        Route::post('/user/update', [UserController::class, 'updateUserProfile'])->name('user.update');

        Route::post('/logout', [UserController::class, 'logout'])->name('user.logout');

        Route::post('/select-vehicle', [UserController::class, 'selectVehicle'])->name('user.select-vehicle');



        Route::controller(AddressController::class)->group(function () {

            Route::get('addresses', 'index');

            Route::get('addresses/update-default/{id}', 'update_default');

            Route::post('addresses', 'store');

            Route::get('addresses/{id}', 'show');

            Route::put('addresses/{id}', 'update');

            Route::delete('addresses/{id}', 'destroy');
        });





        Route::controller(OrderController::class)->group(function () {

            Route::get('orders', 'index');

            Route::post('orders', 'store');

            Route::get('orders/{id}', 'show');
        });





        Route::controller(VehicleController::class)->group(function () {

            Route::get('user-vehicles', 'index');

            Route::get('user-vehicles/update-default/{id}', 'update_default');

            Route::post('user-vehicles', 'store');

            Route::get('user-vehicles/{id}', 'show');

            Route::put('user-vehicles/{id}', 'update');

            Route::delete('user-vehicles/{id}', 'destroy');
        });
    });







    Route::middleware(['auth:cleanerApi'])->prefix('cleaners')->group(function () {



        Route::get('/user-profile', [CleanerAuthController::class, 'getProfile']);

        Route::post('/user-profile', [CleanerAuthController::class, 'getProfile']);

        Route::post('/user-profile', [CleanerAuthController::class, 'updateProfile']);

        Route::post('/logout', [CleanerUserController::class, 'logout']);

        Route::put('/bank-details', [CleanerUserController::class, 'updateBankDetails']);

        Route::get('/cleaner-earning', [CleanerEarningController::class, 'index']);

        Route::get('/transaction-history', [CleanerHomeController::class, 'transaction']);

        Route::get('generate-referral', [CleanerReferralCodeController::class, 'generateReferralCode']);

        Route::post('join-referral', [CleanerReferralCodeController::class, 'joinWithReferralCode']);

        Route::get('referral/total-joined', [CleanerReferralCodeController::class, 'getTotalJoined']);

        Route::get('referral/total-earned', [CleanerReferralCodeController::class, 'getTotalEarned']);

        // Route::get('referral/statistics', [CleanerReferralCodeController::class, 'getReferralStatistics']);

        Route::get('terms-condition', [TermsConditionController::class, 'index']);

        Route::get('details', [CleanerReferralCodeController::class, 'getCleanerDetails']);



        Route::get('getAuthUser', [CleanerReferralCodeController::class, 'getAuthUser']);



        Route::controller(CleanerHomeController::class)->group(function () {

            Route::get('home', 'index');

            Route::post('update-status', 'update_status');

            Route::post('addresses', 'store');

            Route::get('addresses/{id}', 'show');

            Route::put('addresses/{id}', 'update');

            Route::delete('addresses/{id}', 'destroy');
        });



        Route::controller(LeaveController::class)->group(function () {

            Route::get('leaves', 'index');

            Route::post('leaves', 'store');

            Route::get('leaves/{id}', 'show');

            Route::delete('leaves/{id}', 'destroy');
        });
    });
});





Route::any('{path}', function () {

    return response()->json([

        'status'    => false,

        'message'   => 'Api not found..!!'

    ], 404);
})->where('path', '.*');
