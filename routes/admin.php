<?php



use App\Http\Controllers\Admin\AddOnController;
use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Admin\AppUserController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CleanerEarnController;
use App\Http\Controllers\Admin\ClenerEarnController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\PriceDetailsController;
use App\Routes\Profile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CleanerController;
use App\Http\Controllers\Admin\CleaningLogController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderStatusLogController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceDayController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserVehicleController;
use App\Http\Controllers\Admin\BankDetailController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\CleanerEarningController;
use App\Http\Controllers\Admin\BookingController as BookController;
use App\Http\Controllers\Admin\RewardController as RewardsController;
use App\Http\Controllers\Admin\ActiveDealsController as ActiveDealController;
use App\Http\Controllers\Admin\DiscountController as DiscountsController;
/*

|--------------------------------------------------------------------------

| Web Routes For Admin

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/



// Admin & Sub-Admin Routes

Route::prefix('admin')->name('admin.')->middleware(['auth', 'permission', 'authCheck', 'verified'])->group(function () {

    Profile::routes();

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');



    // ----------------------- Role Routes ----------------------------------------------------

    Route::controller(RolesController::class)->name('roles')->group(function () {

        Route::get('roles', 'index')->middleware('isAllow:102,can_view');

        Route::post('roles', 'save')->middleware('isAllow:102,can_add');

        Route::put('roles', 'update')->middleware('isAllow:102,can_edit');

        Route::delete('roles', 'delete')->middleware('isAllow:102,can_delete');

        Route::get('roles/permission/{id}', 'permission')->name('.permission.view')->middleware('isAllow:102,can_edit');

        Route::put('roles/permission', 'permission_update')->name('.permission.update')->middleware('isAllow:102,can_edit');

    });
    Route::post('/send-notification', [NotificationController::class, 'sendNotification'])->name('send.web-notification');
    Route::post('/store-token', [NotificationController::class, 'saveToken'])->name('store.token');


    // ----------------------- Admin and Sub Admin Routes ----------------------------------------------------

    Route::controller(UsersController::class)->group(function () {

        Route::get('users', 'index')->name('users')->middleware('isAllow:103,can_view');

        Route::get('users/add', 'add')->name('users.add')->middleware('isAllow:103,can_add');

        Route::post('users/add', 'save')->name('users.add')->middleware('isAllow:103,can_add');

        Route::get('users/{slug}', 'edit')->name('users.edit')->middleware('isAllow:103,can_edit');

        Route::post('users/{slug}', 'update')->name('users.edit')->middleware('isAllow:103,can_edit');

        Route::delete('users', 'delete')->name('users')->middleware('isAllow:103,can_delete');

        Route::get('users/permission/{id}', 'permission')->name('users.permission.view')->middleware('isAllow:103,can_edit');

        Route::put('users/permission', 'permission_update')->name('users.permission.update')->middleware('isAllow:103,can_edit');

    });



    // ----------------------- States Routes ----------------------------------------------------

    Route::controller(StateController::class)->name('states')->group(function () {

        Route::get('states', 'index')->middleware('isAllow:105,can_view');

        Route::post('states', 'save')->middleware('isAllow:105,can_add');

        Route::put('states', 'update')->middleware('isAllow:105,can_edit');

        Route::delete('states', 'delete')->middleware('isAllow:105,can_delete');

    });



    // ----------------------- City Routes ----------------------------------------------------

    Route::controller(CityController::class)->name('cities')->group(function () {

        Route::get('cities', 'index')->middleware('isAllow:106,can_view');

        Route::post('cities', 'save')->middleware('isAllow:106,can_add');

        Route::put('cities', 'update')->middleware('isAllow:106,can_edit');

        Route::delete('cities', 'delete')->middleware('isAllow:106,can_delete');

    });



    // ----------------------- CMS Routes ----------------------------------------------------

    Route::controller(CmsController::class)->group(function () {

        Route::get('cms', 'index')->name('cms')->middleware('isAllow:115,can_view');

        Route::get('cms/add', 'add')->name('cms.add')->middleware('isAllow:115,can_add');

        Route::post('cms/add', 'save')->name('cms.add')->middleware('isAllow:115,can_add');

        Route::get('cms/{id}', 'edit')->name('cms.edit')->middleware('isAllow:115,can_edit');

        Route::post('cms', 'slug')->name('cms.slug')->middleware('isAllow:115,can_edit');

        Route::post('cms/{id}', 'update')->name('cms.edit')->middleware('isAllow:115,can_edit');

        Route::delete('cms', 'delete')->name('cms')->middleware('isAllow:115,can_delete');

    });
    Route::get('admin/cleaner-earning', [CleanerEarningController::class, 'index'])->name('admin.cleaner-earning');
    Route::post('cleaner-earning/update-status', [CleanerEarnController::class, 'updateStatus'])
        ->name('cleanerearn.updateStatus');
    // ----------------------- CMS Routes ----------------------------------------------------

    Route::controller(CleanerEarnController::class)->group(function () {

        Route::get('cleaner-earn', 'index')->name('cleaner-earn')->middleware('isAllow:116,can_view');

        Route::get('cleaner-earn/add', 'add')->name('cleaner-earn.add')->middleware('isAllow:116,can_add');

        Route::post('cleaner-earn/add', 'save')->name('cleaner-earn.add')->middleware('isAllow:116,can_add');

        Route::get('cleaner-earn/{id}', 'edit')->name('cleaner-earn.edit')->middleware('isAllow:116,can_edit');

        Route::post('cleaner-earn', 'slug')->name('cleaner-earn.slug')->middleware('isAllow:116,can_edit');

        Route::post('cleaner-earn/{id}', 'update')->name('cleaner-earn.edit')->middleware('isAllow:116,can_edit');

        Route::delete('cleaner-earn', 'delete')->name('cleaner-earn')->middleware('isAllow:116,can_delete');

    });




    // ----------------------- Brands Routes ----------------------------------------------------

    Route::controller(BrandController::class)->group(function () {

        Route::get('brands', 'index')->name('brands')->middleware('isAllow:111,can_view');

        Route::get('brands/add', 'add')->name('brands.add')->middleware('isAllow:111,can_add');

        Route::post('brands/add', 'save')->name('brands.add')->middleware('isAllow:111,can_add');

        Route::get('brands/{id}', 'edit')->name('brands.edit')->middleware('isAllow:111,can_edit');

        Route::post('brands', 'slug')->name('brands.slug')->middleware('isAllow:111,can_edit');

        Route::post('brands/{id}', 'update')->name('brands.edit')->middleware('isAllow:111,can_edit');

        Route::delete('brands', 'delete')->name('brands')->middleware('isAllow:111,can_delete');

    });





    // ----------------------- Vehicles Routes ----------------------------------------------------

    Route::controller(VehicleController::class)->group(function () {

        Route::get('vehicles', 'index')->name('vehicles')->middleware('isAllow:110,can_view');

        Route::get('vehicles/add', 'add')->name('vehicles.add')->middleware('isAllow:110,can_add');

        Route::post('vehicles/add', 'save')->name('vehicles.add')->middleware('isAllow:110,can_add');

        Route::get('vehicles/{id}', 'edit')->name('vehicles.edit')->middleware('isAllow:110,can_edit');

        Route::post('vehicles', 'slug')->name('vehicles.slug')->middleware('isAllow:110,can_edit');

        Route::post('vehicles/{id}', 'update')->name('vehicles.edit')->middleware('isAllow:110,can_edit');

        Route::delete('vehicles', 'delete')->name('vehicles')->middleware('isAllow:110,can_delete');

    });





    // ----------------------- Banners Routes ----------------------------------------------------

    Route::controller(BannerController::class)->group(function () {

        Route::get('banners', 'index')->name('banners')->middleware('isAllow:115,can_view');

        Route::get('banners/add', 'add')->name('banners.add')->middleware('isAllow:115,can_add');

        Route::post('banners/add', 'save')->name('banners.add')->middleware('isAllow:115,can_add');

        Route::get('banners/{id}', 'edit')->name('banners.edit')->middleware('isAllow:115,can_edit');

        Route::post('banners', 'slug')->name('banners.slug')->middleware('isAllow:115,can_edit');

        Route::post('banners/{id}', 'update')->name('banners.edit')->middleware('isAllow:115,can_edit');

        Route::delete('banners', 'delete')->name('banners')->middleware('isAllow:115,can_delete');

    });





    // ----------------------- Add-on Routes ----------------------------------------------------

    Route::controller(AddOnController::class)->group(function () {

        Route::get('add-ons', 'index')->name('add-ons')->middleware('isAllow:112,can_view');

        Route::get('add-ons/add', 'add')->name('add-ons.add')->middleware('isAllow:112,can_add');

        Route::post('add-ons/add', 'save')->name('add-ons.add')->middleware('isAllow:112,can_add');

        Route::get('add-ons/{id}', 'edit')->name('add-ons.edit')->middleware('isAllow:112,can_edit');

        Route::post('add-ons', 'slug')->name('add-ons.slug')->middleware('isAllow:112,can_edit');

        Route::post('add-ons/{id}', 'update')->name('add-ons.edit')->middleware('isAllow:112,can_edit');

        Route::delete('add-ons', 'delete')->name('add-ons')->middleware('isAllow:112,can_delete');

    });





    // ----------------------- Testimonials Routes ----------------------------------------------------

    Route::controller(TestimonialController::class)->group(function () {

        Route::get('testimonials', 'index')->name('testimonials')->middleware('isAllow:109,can_view');

        Route::get('testimonials/add', 'add')->name('testimonials.add')->middleware('isAllow:109,can_add');

        Route::post('testimonials/add', 'save')->name('testimonials.add')->middleware('isAllow:109,can_add');

        Route::get('testimonials/{id}', 'edit')->name('testimonials.edit')->middleware('isAllow:109,can_edit');

        Route::post('testimonials', 'slug')->name('testimonials.slug')->middleware('isAllow:109,can_edit');

        Route::put('testimonials/{id}', 'update')->name('testimonials.update')->middleware('isAllow:109,can_edit');


        Route::delete('testimonials', 'delete')->name('testimonials')->middleware('isAllow:109,can_delete');

    });





    // ----------------------- app-users Routes ----------------------------------------------------

    Route::controller(AppUserController::class)->group(function () {

        Route::get('app-users', 'index')->name('app-users')->middleware('isAllow:117,can_view');

        Route::get('app-users/add', 'add')->name('app-users.add')->middleware('isAllow:117,can_add');

        Route::post('app-users/add', 'save')->name('app-users.add')->middleware('isAllow:117,can_add');

        Route::get('app-users/{id}', 'edit')->name('app-users.edit')->middleware('isAllow:117,can_edit');

        Route::post('app-users', 'slug')->name('app-users.slug')->middleware('isAllow:117,can_edit');

        Route::post('app-users/{id}', 'update')->name('app-users.edit')->middleware('isAllow:117,can_edit');

        Route::delete('app-users', 'delete')->name('app-users')->middleware('isAllow:117,can_delete');

    });





    // ----------------------- Cleaners Routes ----------------------------------------------------

    Route::controller(CleanerController::class)->group(function () {

        Route::get('cleaners', 'index')->name('cleaners')->middleware('isAllow:116,can_view');

        Route::get('cleaners/add', 'add')->name('cleaners.add')->middleware('isAllow:116,can_add');

        Route::post('cleaners/add', 'save')->name('cleaners.add')->middleware('isAllow:116,can_add');

        Route::get('cleaners/{id}', 'edit')->name('cleaners.edit')->middleware('isAllow:116,can_edit');

        Route::post('cleaners', 'slug')->name('cleaners.slug')->middleware('isAllow:116,can_edit');

        Route::post('cleaners/{id}', 'update')->name('cleaners.edit')->middleware('isAllow:116,can_edit');

        Route::delete('cleaners', 'delete')->name('cleaners')->middleware('isAllow:116,can_delete');

    });





    // ----------------------- addresses Routes ----------------------------------------------------

    Route::controller(AddressController::class)->group(function () {

        Route::get('addresses', 'index')->name('addresses')->middleware('isAllow:117,can_view');

        Route::get('addresses/add', 'add')->name('addresses.add')->middleware('isAllow:117,can_add');

        Route::post('addresses/add', 'save')->name('addresses.add')->middleware('isAllow:117,can_add');

        Route::get('addresses/{id}', 'edit')->name('addresses.edit')->middleware('isAllow:117,can_edit');

        Route::post('addresses', 'slug')->name('addresses.slug')->middleware('isAllow:117,can_edit');

        Route::post('addresses/{id}', 'update')->name('addresses.edit')->middleware('isAllow:117,can_edit');

        Route::delete('addresses', 'delete')->name('addresses')->middleware('isAllow:117,can_delete');

        Route::post('get-address', 'get_address')->name('get-address');



    });





    // ----------------------- Leave Routes ----------------------------------------------------

    Route::controller(LeaveController::class)->group(function () {

        Route::get('leaves', 'index')->name('leaves')->middleware('isAllow:116,can_view');

        Route::get('leaves/add', 'add')->name('leaves.add')->middleware('isAllow:116,can_add');

        Route::post('leaves/add', 'save')->name('leaves.add')->middleware('isAllow:116,can_add');

        Route::get('leaves/{id}', 'edit')->name('leaves.edit')->middleware('isAllow:116,can_edit');

        Route::post('leaves', 'updateStatus')->name('leaves.status')->middleware('isAllow:116,can_edit');

        Route::post('leaves/{id}', 'update')->name('leaves.edit')->middleware('isAllow:116,can_edit');

        Route::delete('leaves', 'delete')->name('leaves')->middleware('isAllow:116,can_delete');

    });





    // ----------------------- user_vehicles Routes ----------------------------------------------------

    Route::controller(UserVehicleController::class)->group(function () {

        Route::get('user-vehicles', 'index')->name('user-vehicles')->middleware('isAllow:117,can_view');

        Route::get('user-vehicles/add', 'add')->name('user-vehicles.add')->middleware('isAllow:117,can_add');

        Route::post('user-vehicles/add', 'save')->name('user-vehicles.add')->middleware('isAllow:117,can_add');

        Route::get('user-vehicles/{id}', 'edit')->name('user-vehicles.edit')->middleware('isAllow:117,can_edit');

        Route::post('user-vehicles', 'slug')->name('user-vehicles.slug')->middleware('isAllow:117,can_edit');

        Route::post('user-vehicles/{id}', 'update')->name('user-vehicles.edit')->middleware('isAllow:117,can_edit');

        Route::delete('user-vehicles', 'delete')->name('user-vehicles')->middleware('isAllow:117,can_delete');

    });





    // ----------------------- Plan Routes ----------------------------------------------------

    Route::controller(PlanController::class)->group(function () {

        Route::get('plans', 'index')->name('plans')->middleware('isAllow:113,can_view');

        Route::get('plans/add', 'add')->name('plans.add')->middleware('isAllow:113,can_add');

        Route::post('plans/add', 'save')->name('plans.add')->middleware('isAllow:113,can_add');

        Route::get('plans/{id}', 'edit')->name('plans.edit')->middleware('isAllow:113,can_edit');

        Route::post('plans', 'slug')->name('plans.slug')->middleware('isAllow:113,can_edit');

        Route::post('plans/{id}', 'update')->name('plans.edit')->middleware('isAllow:113,can_edit');

        Route::delete('plans', 'delete')->name('plans')->middleware('isAllow:113,can_delete');

    });





    // ----------------------- Tags Routes ----------------------------------------------------

    Route::controller(TagController::class)->group(function () {

        Route::get('tags', 'index')->name('tags')->middleware('isAllow:104,can_view');

        Route::get('tags/add', 'add')->name('tags.add')->middleware('isAllow:104,can_add');

        Route::post('tags/add', 'save')->name('tags.add')->middleware('isAllow:104,can_add');

        Route::get('tags/{id}', 'edit')->name('tags.edit')->middleware('isAllow:104,can_edit');

        Route::post('tags', 'slug')->name('tags.slug')->middleware('isAllow:104,can_edit');

        Route::post('tags/{id}', 'update')->name('tags.edit')->middleware('isAllow:104,can_edit');

        Route::delete('tags', 'delete')->name('tags')->middleware('isAllow:104,can_delete');

    });



    // ----------------------- Services Routes ----------------------------------------------------

    Route::controller(ServiceController::class)->group(function () {

        Route::get('services', 'index')->name('services')->middleware('isAllow:104,can_view');

        Route::get('services/add', 'add')->name('services.add')->middleware('isAllow:104,can_add');

        Route::post('services/add', 'save')->name('services.add')->middleware('isAllow:104,can_add');

        Route::get('services/{id}', 'edit')->name('services.edit')->middleware('isAllow:104,can_edit');

        Route::post('services', 'slug')->name('services.slug')->middleware('isAllow:104,can_edit');

        Route::post('services/{id}', 'update')->name('services.edit')->middleware('isAllow:104,can_edit');

        Route::delete('services', 'delete')->name('services')->middleware('isAllow:104,can_delete');

    });



    // ----------------------- Blogs Routes ----------------------------------------------------

    Route::controller(BlogController::class)->group(function () {

        Route::get('blogs', 'index')->name('blogs')->middleware('isAllow:118,can_view');

        Route::get('blogs/add', 'add')->name('blogs.add')->middleware('isAllow:118,can_add');

        Route::post('blogs/add', 'save')->name('blogs.add')->middleware('isAllow:118,can_add');

        Route::get('blogs/{id}', 'edit')->name('blogs.edit')->middleware('isAllow:118,can_edit');

        Route::post('blogs', 'slug')->name('blogs.slug')->middleware('isAllow:118,can_edit');

        Route::post('blogs/{id}', 'update')->name('blogs.edit')->middleware('isAllow:118,can_edit');

        Route::delete('blogs', 'delete')->name('blogs')->middleware('isAllow:118,can_delete');

    });



    // ----------------------- Reviews Routes ----------------------------------------------------

    Route::controller(ReviewController::class)->group(function () {

        Route::get('reviews', 'index')->name('reviews')->middleware('isAllow:114,can_view');

        Route::get('reviews/add', 'add')->name('reviews.add')->middleware('isAllow:114,can_add');

        Route::post('reviews/add', 'save')->name('reviews.add')->middleware('isAllow:114,can_add');

        Route::get('reviews/{id}', 'edit')->name('reviews.edit')->middleware('isAllow:114,can_edit');

        Route::post('reviews', 'slug')->name('reviews.slug')->middleware('isAllow:114,can_edit');

        Route::post('reviews/{id}', 'update')->name('reviews.edit')->middleware('isAllow:114,can_edit');

        Route::delete('reviews', 'delete')->name('reviews')->middleware('isAllow:114,can_delete');

    });





    // ----------------------- Orders Routes ----------------------------------------------------

    Route::controller(OrderController::class)->group(function () {

        Route::get('orders', 'index')->name('orders')->middleware('isAllow:104,can_view');

        Route::get('orders/add', 'add')->name('orders.add')->middleware('isAllow:104,can_add');

        Route::post('orders/add', 'save')->name('orders.add')->middleware('isAllow:104,can_add');

        Route::post('orders/assign-cleaner', 'assign_cleaner')->name('orders.assign-cleaner');

        Route::get('orders/{id}', 'edit')->name('orders.edit')->middleware('isAllow:104,can_edit');

        Route::post('orders', 'slug')->name('orders.slug')->middleware('isAllow:104,can_edit');

        Route::post('orders/{id}', 'update')->name('orders.edit')->middleware('isAllow:104,can_edit');

        Route::delete('orders', 'delete')->name('orders')->middleware('isAllow:104,can_delete');

    });





    // ----------------------- Transactions Routes ----------------------------------------------------

    Route::controller(TransactionController::class)->group(function () {

        Route::get('transactions', 'index')->name('transactions')->middleware('isAllow:116,can_view');

        Route::get('transactions/add', 'add')->name('transactions.add')->middleware('isAllow:116,can_add');

        Route::post('transactions/add', 'save')->name('transactions.add')->middleware('isAllow:116,can_add');

        // Route::post('transactions/assign-cleaner', 'assign_cleaner')->name('orders.assign-cleaner');

        Route::get('transactions/{id}', 'edit')->name('transactions.edit')->middleware('isAllow:116,can_edit');

        Route::post('transactions', 'slug')->name('transactions.slug')->middleware('isAllow:116,can_edit');

        Route::post('transactions/{id}', 'update')->name('transactions.edit')->middleware('isAllow:116,can_edit');

        Route::delete('transactions', 'delete')->name('transactions')->middleware('isAllow:116,can_delete');

    });





    // ----------------------- Cleaning Logs Routes ----------------------------------------------------

    Route::controller(CleaningLogController::class)->group(function () {

        Route::get('cleaning-logs', 'index')->name('cleaning-logs')->middleware('isAllow:104,can_view');

        Route::get('cleaning-logs/add', 'add')->name('cleaning-logs.add')->middleware('isAllow:104,can_add');

        Route::post('cleaning-logs/add', 'save')->name('cleaning-logs.add')->middleware('isAllow:104,can_add');

        // Route::post('transactions/assign-cleaner', 'assign_cleaner')->name('orders.assign-cleaner');

        Route::get('cleaning-logs/{id}', 'edit')->name('cleaning-logs.edit')->middleware('isAllow:104,can_edit');

        Route::post('cleaning-logs', 'slug')->name('cleaning-logs.slug')->middleware('isAllow:104,can_edit');

        Route::post('cleaning-logs/{id}', 'update')->name('cleaning-logs.edit')->middleware('isAllow:104,can_edit');

        Route::delete('cleaning-logs', 'delete')->name('cleaning-logs')->middleware('isAllow:104,can_delete');

    });





    // ----------------------- Cleaning Logs Routes ----------------------------------------------------

    Route::controller(ServiceDayController::class)->group(function () {

        Route::get('service-days', 'index')->name('service-days')->middleware('isAllow:104,can_view');

        Route::get('service-days/add', 'add')->name('service-days.add')->middleware('isAllow:104,can_add');

        Route::post('service-days/add', 'save')->name('service-days.add')->middleware('isAllow:104,can_add');

        // Route::post('transactions/assign-cleaner', 'assign_cleaner')->name('orders.assign-cleaner');

        Route::get('service-days/{id}', 'edit')->name('service-days.edit')->middleware('isAllow:104,can_edit');

        Route::post('service-days', 'slug')->name('service-days.slug')->middleware('isAllow:104,can_edit');

        Route::post('service-days/{id}', 'update')->name('service-days.edit')->middleware('isAllow:104,can_edit');

        Route::delete('service-days', 'delete')->name('service-days')->middleware('isAllow:104,can_delete');

    });





    // ----------------------- Order Status Logs Routes ----------------------------------------------------

    Route::controller(OrderStatusLogController::class)->group(function () {

        Route::get('order-status-logs', 'index')->name('order-status-logs')->middleware('isAllow:104,can_view');

        Route::get('order-status-logs/add', 'add')->name('order-status-logs.add')->middleware('isAllow:104,can_add');

        Route::post('order-status-logs/add', 'save')->name('order-status-logs.add')->middleware('isAllow:104,can_add');

        // Route::post('transactions/assign-cleaner', 'assign_cleaner')->name('orders.assign-cleaner');

        Route::get('order-status-logs/{id}', 'edit')->name('order-status-logs.edit')->middleware('isAllow:104,can_edit');

        Route::post('order-status-logs', 'slug')->name('order-status-logs.slug')->middleware('isAllow:104,can_edit');

        Route::post('order-status-logs/{id}', 'update')->name('order-status-logs.edit')->middleware('isAllow:104,can_edit');

        Route::delete('order-status-logs', 'delete')->name('order-status-logs')->middleware('isAllow:104,can_delete');

    });





    // ----------------------- Subscriptions Routes ----------------------------------------------------

    Route::controller(SubscriptionController::class)->group(function () {

        Route::get('subscriptions', 'index')->name('subscriptions')->middleware('isAllow:104,can_view');

        Route::get('subscriptions/add', 'add')->name('subscriptions.add')->middleware('isAllow:104,can_add');

        Route::post('subscriptions/add', 'save')->name('subscriptions.add')->middleware('isAllow:104,can_add');

        Route::get('subscriptions/{id}', 'edit')->name('subscriptions.edit')->middleware('isAllow:104,can_edit');

        Route::post('subscriptions', 'slug')->name('subscriptions.slug')->middleware('isAllow:104,can_edit');

        Route::post('subscriptions/{id}', 'update')->name('subscriptions.edit')->middleware('isAllow:104,can_edit');

        Route::delete('subscriptions', 'delete')->name('subscriptions')->middleware('isAllow:104,can_delete');

    });



    // ----------------------- BookingController Routes ----------------------------------------------------

    Route::controller(BookController::class)->group(function () {

        Route::get('booking-index/{id}', 'booking_index')->name('booking.view')->middleware('isAllow:108,can_view');


        Route::get('booking', 'index')->name('booking')->middleware('isAllow:108,can_view');

        Route::get('booking/add', 'add')->name('booking.add')->middleware('isAllow:108,can_add');

        Route::post('booking/add', 'save')->name('booking.add')->middleware('isAllow:108,can_add');

        Route::get('booking/{id}', 'edit')->name('booking.edit')->middleware('isAllow:108,can_edit');
        Route::post('booking', 'slug')->name('booking.slug')->middleware('isAllow:108,can_edit');
        Route::post('booking/{id}', 'update')->name('booking.edit')->middleware('isAllow:108,can_edit');
        Route::delete('booking', 'delete')->name('booking')->middleware('isAllow:108,can_delete');
        Route::post('booking-update', 'booking_update')->name('booking.update')->middleware('isAllow:108 ,can_view');
        Route::post('add-booking-update', 'add_booking_update')->name('add.booking.update')->middleware('isAllow:108,can_view');


    });



    // ----------------------- Rewards Routes ----------------------------------------------------

    Route::controller(RewardsController::class)->group(function () {

        Route::get('rewards', 'index')->name('rewards')->middleware('isAllow:107,can_view');

        Route::get('rewards/add', 'add')->name('rewards.add')->middleware('isAllow:107,can_add');

        Route::post('rewards/add', 'save')->name('rewards.add')->middleware('isAllow:107,can_add');

        Route::get('rewards/{id}', 'edit')->name('rewards.edit')->middleware('isAllow:107,can_edit');

        Route::post('rewards', 'slug')->name('rewards.slug')->middleware('isAllow:107,can_edit');

        Route::post('rewards/{id}', 'update')->name('rewards.edit')->middleware('isAllow:107,can_edit');

        Route::delete('rewards', 'delete')->name('rewards')->middleware('isAllow:107,can_delete');
        //  Route::post('rewards/status-toggle', 'toggleStatus')->name('admin.rewards.toggleStatus')->middleware('isAllow:104,can_edit');



    });



    // ----------------------- Active Deals Routes ----------------------------------------------------

    Route::controller(ActiveDealController::class)->group(function () {

        Route::get('active-deals', 'index')->name('active-deals')->middleware('isAllow:104,can_view');

        Route::get('active-deals/add', 'add')->name('active-deals.add')->middleware('isAllow:104,can_add');

        Route::post('active-deals/add', 'save')->name('active-deals.add')->middleware('isAllow:104,can_add');

        Route::get('active-deals/{id}', 'edit')->name('active-deals.edit')->middleware('isAllow:104,can_edit');

        Route::post('active-deals', 'slug')->name('active-deals.slug')->middleware('isAllow:104,can_edit');

        Route::post('active-deals/{id}', 'update')->name('active-deals.edit')->middleware('isAllow:104,can_edit');

        Route::delete('active-deals', 'delete')->name('active-deals')->middleware('isAllow:104,can_delete');

    });



    // ----------------------- Discount Routes ----------------------------------------------------

    Route::controller(DiscountsController::class)->group(function () {

        Route::get('discount', 'index')->name('discount')->middleware('isAllow:115,can_view');

        Route::get('discount/add', 'add')->name('discount.add')->middleware('isAllow:115,can_add');

        Route::post('discount/add', 'save')->name('discount.add')->middleware('isAllow:115,can_add');

        Route::get('discount/{id}', 'edit')->name('discount.edit')->middleware('isAllow:115,can_edit');

        Route::post('discount', 'slug')->name('discount.slug')->middleware('isAllow:115,can_edit');

        Route::post('discount/{id}', 'update')->name('discount.edit')->middleware('isAllow:115,can_edit');

        Route::delete('discount', 'delete')->name('discount')->middleware('isAllow:115,can_delete');

    });





    // ----------------------- CleanerEarning Routes ----------------------------------------------------

    Route::controller(CleanerEarningController::class)->group(function () {

        Route::get('cleaner-earning', 'index')->name('cleaner-earning')->middleware('isAllow:116,can_view');

        Route::get('cleaner-earning/add', 'add')->name('cleaner-earning.add')->middleware('isAllow:116,can_add');

        Route::post('cleaner-earning/add', 'save')->name('cleaner-earning.add')->middleware('isAllow:116,can_add');

        Route::get('cleaner-earning/{id}', 'edit')->name('cleaner-earning.edit')->middleware('isAllow:116,can_edit');

        Route::post('cleaner-earning', 'slug')->name('cleaner-earning.slug')->middleware('isAllow:116,can_edit');

        Route::post('cleaner-earning/{id}', 'update')->name('cleaner-earning.edit')->middleware('isAllow:116,can_edit');

        Route::delete('cleaner-earning', 'delete')->name('cleaner-earning')->middleware('isAllow:116,can_delete');

    });



    // ----------------------- Bank Details Routes ----------------------------------------------------

    Route::controller(BankDetailController::class)->group(function () {

        Route::get('bank-details', 'index')->name('bank-details')->middleware('isAllow:116,can_view');

        Route::get('bank-details/add', 'add')->name('bank-details.add')->middleware('isAllow:116,can_add');

        Route::post('bank-details/add', 'save')->name('bank-details.add')->middleware('isAllow:116,can_add');

        Route::get('bank-details/{id}', 'edit')->name('bank-details.edit')->middleware('isAllow:116,can_edit');

        Route::post('bank-details', 'slug')->name('bank-details.slug')->middleware('isAllow:116,can_edit');

        Route::post('bank-details/{id}', 'update')->name('bank-details.edit')->middleware('isAllow:116,can_edit');

        Route::delete('bank-details', 'delete')->name('bank-details')->middleware('isAllow:116,can_delete');

    });

    // ----------------------- Price Details Routes ----------------------------------------------------

    Route::controller(PriceDetailsController::class)->group(function () {

        Route::get('price-details', 'index')->name('price-details')->middleware('isAllow:115,can_view');

        Route::get('price-details/add', 'add')->name('price-details.add')->middleware('isAllow:115,can_add');

        Route::post('price-details/add', 'save')->name('price-details.add')->middleware('isAllow:115,can_add');

        Route::get('price-details/{id}', 'edit')->name('price-details.edit')->middleware('isAllow:115,can_edit');

        Route::post('price-details', 'slug')->name('price-details.slug')->middleware('isAllow:115,can_edit');

        Route::post('price-details/{id}', 'update')->name('price-details.edit')->middleware('isAllow:115,can_edit');

        Route::delete('price-details', 'delete')->name('price-details')->middleware('isAllow:115,can_delete');

    });

    Route::controller(ReportsController::class)->group(function () {
        Route::get('reports/daily', 'index')->name('reports.index')->middleware('isAllow:104,can_view');
        Route::get('reports/weakly', 'weekly')->name('reports.weekly')->middleware('isAllow:104,can_view');
        Route::get('reports/monthly', 'monthly')->name('reports.monthly')->middleware('isAllow:104,can_view');
    });



    Route::any('setting/{id}', [SettingController::class, 'setting'])->name('setting')->middleware('isAllow:101,can_view');

    Route::get('database-backup', [SettingController::class, 'database_backup'])->name('database_backup')->middleware('isAllow:101,can_view');

    Route::get('server-control', [SettingController::class, 'serverControl'])->name('server-control')->middleware('isAllow:101,can_view');

    Route::post('server-control', [SettingController::class, 'serverControlSave'])->name('server-control')->middleware('isAllow:101,can_view');

});

