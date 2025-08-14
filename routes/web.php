<?php

use App\Http\Controllers\Admin\RewardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\FireController;
use App\Http\Controllers\FormDetailsController;
use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\Frontend\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something gsend.web-notificationsend.web-notificationreat!
|
*/

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return response()->json(['status' => 'Cache cleared']);
});
// Route::patch('fcm-token', [FireController::class, 'updateToken'])->name('fcmToken');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/privacy-policy', [HomeController::class, 'privacypolicy'])->name('privacy.policy');
Route::get('/terms-condition', [HomeController::class, 'termscondition'])->name('terms.condition');
Route::get('/genie-policies', [HomeController::class, 'geniepolicies'])->name('genie.policies');
Route::get('/delete-user', [HomeController::class, 'deleteuser'])->name('delete.user');
Route::post('/submit-inquiry', [FormDetailsController::class, 'store'])->name('inquiry.store');
Route::get('/push-notificaiton', [FireController::class, 'index'])->name('push-notificaiton');
Route::post('/store-token', [FireController::class, 'storeToken'])->name('store.token');
Route::post('/send-web-notification', [FireController::class, 'sendWebNotification'])->name('send.web-notification');
Route::get('test', [CommonController::class, 'test'])->name('test');
Route::get('{guard}', fn ($guard) => redirect($guard == 'admin' ?  url('/admin/login') : url("/$guard/login")))->whereIn('guard', ['admin']);
// Route::redirect('admin/dashboard', '/dashboard');
Route::get('/admin/check-new-booking', [\App\Http\Controllers\Admin\HomeController::class, 'checkNewBooking'])->name('admin.check-new-booking');


Route::middleware(['authCheck'])->group(function () {

    // Open Routes
    Route::post('get-cities', [CityController::class, 'get_cities'])->name('cities.list');
    Route::post('upload-image', [CommonController::class, 'upload_image'])->name('upload_image');
    Route::get('get-user-list-filter', [CommonController::class, 'get_user_list_filter'])->name('get_user_list_filter');
});
Route::post('/admin/rewards/status-toggle', [RewardController::class, 'toggleStatus'])->name('admin.rewards.toggleStatus');

Route::fallback(function () {
    abort(404);
});
