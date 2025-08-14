<?php

namespace App\Http\Controllers\Api\User;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\City;
use App\Models\Plan;
use App\Models\Service;
use App\Models\State;
use App\Models\Testimonial;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function __construct()
    {
        //    $this->middleware(["auth:api"]);
    }
    public function get_cities(Request $request, $state_id): JsonResponse
    {


        if (empty($state_id)) {
            return response()->json([
                'status'    => false,
                'message'   => 'State id is required.',
            ], 400);
        }

        $cities = City::where('state_id', $state_id)->get()->toArray();
        if (empty($cities)) {
            return response()->json([
                'status'    => false,
                'message'   => 'State not found.',
            ], 400);
        }

        return response()->json([
            'status'    => true,
            'cities'   => $cities,
        ], 200);
    }
    public function get_all_cities(Request $request): JsonResponse
    {

        $data = [];
        $cities = City::select('id', 'name', 'state_id', 'status', 'is_popular')
            ->where('status', 1)
            ->where('is_popular', 0)
            ->get()->toArray();

        $popular_cities = City::select('id', 'name', 'state_id', 'status', 'is_popular')
            ->where('status', 1)
            ->where('is_popular', 1)
            ->get()->toArray();



        if (empty($cities)) {
            return response()->json([
                'status'    => false,
                'message'   => 'Cities not found.',
            ], 400);
        }

        $data['cities'] = $cities;
        $data['popular_cities'] = $popular_cities;

        return response()->json([
            'status'    => true,
            'data'   => $data,
        ], 200);
    }


    public function get_states(Request $request): JsonResponse
    {

        $states = State::whereNull('deleted_at')->get()->toArray();

        if (empty($states)) {
            return response()->json([
                'status'    => false,
                'message'   => 'States not found.',
            ], 400);
        }

        return response()->json([
            'status'    => true,
            'cities'   => $states,
        ], 200);
    }

    public function home(Request $request): JsonResponse
    {
        $data = [];

        $popular_plans = Plan::with(['base_plan'])->where('is_popular', 1)->whereNull('deleted_at')->get()->toArray();

        $popular_plans_data = [];

        if (!empty($popular_plans)) {
            $categories = ['1' => 'Car Subscription', '2' => 'Bike Subscription', '3' => 'Scooty Subscription', '4' => 'Other Subscription'];
            foreach ($popular_plans as $plan) {
                $serviceIds = !empty($plan['services']) ? explode(',', $plan['services']) : [];
                $services = [];
                if ($serviceIds) {
                    $services = Service::whereIn('id', $serviceIds)->get()->map(function ($service) {
                        $service->image = Helper::showImage($service->image); // Modify the image
                        return $service; // Return the modified service
                    });
                }
                $popular_plans_data[] = [

                    'id' => $plan['id'],
                    'name' => $plan['name'],
                    // 'category_id' => $plan['category_id'],
                    // 'category_name' => $categories[$plan['category_id']],
                    // 'base_plan_id' => $plan['base_plan_id'],
                    // 'base_plan_name' => $plan['base_plan']['name'] ?? 'N/A',
                    // 'interior_days' => $plan['interior_days'],
                    // 'exterior_days' => $plan['exterior_days'],
                    'image' => Helper::showImage($plan['image'], true),



                    'price' => $plan['price'],
                    // 'offer_price' => $plan['offer_price'],
                    // 'discount' => $plan['discount'],
                    'rating' => $plan['rating'],
                    // 'rating_count' => $plan['rating_count'],
                    // 'duration' => $plan['duration'],
                    // 'description' => $plan['description'],
                    // 'recommendation' => $plan['recommendation'],
                    // 'is_recommended' => $plan['is_recommended'],
                    // 'services' => $plan['services'],
                    // 'is_popular' => $plan['is_popular'],
                    // 'status' => $plan['status'],
                    // 'all_services' => $services,

                ];
            }
        }

        $banners = Banner::where(['status' => 1, 'banner_type' => 'up'])->whereNull('deleted_at')->get()->toArray();

        $banners_data = [];

        if (!empty($banners)) {
            foreach ($banners as $banner) {

                $banners_data[] = [
                    'id' => $banner['id'],
                    'name' => $banner['name'],
                    'image' => !empty($banner['image']) ? Helper::showImage($banner['image']) : null,

                ];
            }
        }


        $banners = Banner::where(['status' => 1, 'banner_type' => 'down'])->whereNull('deleted_at')->get()->toArray();

        $banners_data2 = [];

        if (!empty($banners)) {
            foreach ($banners as $banner) {

                $banners_data2[] = [
                    'id' => $banner['id'],
                    'name' => $banner['name'],
                    'image' => !empty($banner['image']) ? Helper::showImage($banner['image']) : null,

                ];
            }
        }




        $blogs = Blog::where('status', 1)->whereNull('deleted_at')->get()->toArray();

        $blogs_data = [];

        if (!empty($blogs)) {
            foreach ($blogs as $blog) {

                $blogs_data[] = [
                    'id' => $blog['id'],
                    'title' => $blog['title'],
                    'icon' => !empty($blog['icon']) ? Helper::showImage($blog['icon']) : null,
                ];
            }
        }

        $testimonials = Testimonial::where('status', 1)->whereNull('deleted_at')->get()->toArray();

        $testimonials_data = [];

        if (!empty($testimonials)) {
            foreach ($testimonials as $testimonial) {

                $testimonials_data[] = [
                    'id' => $testimonial['id'],
                    'name' => $testimonial['name'],
                    'video' => !empty($testimonial['video']) ? Helper::showImage($testimonial['video']) : null,
                ];
            }
        }


        $data['popular_plans'] = $popular_plans_data;
        $data['banners'] = $banners_data;
        $data['blogs'] = $blogs_data;
        $data['testimonials'] = $testimonials_data;

        return response()->json([
            'status'    => true,
            'popular_plans'   => $popular_plans_data ?? null,
            'banners'   => $banners_data ?? null,
            'banners2'   => $banners_data2 ?? null,

            'blogs'   => $blogs_data ?? null,
            'testimonials'   => $testimonials_data ?? null,
        ], 200);
    }

    public function get_brands(Request $request): JsonResponse
    {

        $brands = Brand::where('brand_type', $request->id)->select('id', 'name')->whereNull('deleted_at')->get()->toArray();
        // $brands = Brand::select('id', 'name')->whereNull('deleted_at')->get()->toArray();

        if (empty($brands)) {
            return response()->json([
                'status'    => false,
                'message'   => 'Brands not found.',
                'data'      => [],
            ], 400);
        }

        return response()->json([
            'status'    => true,
            'data'   => $brands,
        ], 200);
    }
    public function get_cars(Request $request, $id): JsonResponse
    {

        $cars = Vehicle::select('id', 'name')->where('brand_id', $id)->whereNull('deleted_at')->get()->toArray();

        if (empty($cars)) {
            return response()->json([
                'status'    => false,
                'message'   => 'No cars found.',
                'data'      => [],
            ], 400);
        }

        return response()->json([
            'status'    => true,
            'data'   => $cars,
        ], 200);
    }

  public function get_plans($body_type): JsonResponse
{
    
    if (!is_numeric($body_type)) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid category_id.',
        ], 400);
    }

    $plans = Plan::with(['base_plan'])
        ->where('body_type', $body_type)
        ->when(!empty($body_type) && $body_type == 6, function ($query) use ($body_type) {
            $query->where('base_plan_id', $body_type);
        })
        ->whereNull('deleted_at')
        ->get()
        ->toArray();

    if (empty($plans)) {
        return response()->json([
            'status'  => false,
            'message' => 'No plans found.',
            'data'    => [],
        ], 200);
    }

    $data = [];
    $categories = ['1' => 'Car Subscription', '2' => 'Bike Subscription', '3' => 'Scooty Subscription', '4' => 'Other Subscription'];

    foreach ($plans as $plan) {
        $serviceIds = !empty($plan['services']) ? explode(',', $plan['services']) : [];
        $services = [];

        if ($serviceIds) {
            $services = Service::whereIn('id', $serviceIds)->get()->map(function ($service) {
                $service->image = Helper::showImage($service->image);
                return $service;
            });
        }

        $data[] = [
            'id' => $plan['id'],
            'name' => $plan['name'],
            'category_id' => $plan['category_id'],
            'category_name' => $categories[$plan['category_id']] ?? 'Unknown',
            'base_plan_id' => $plan['base_plan_id'],
            'base_plan_name' => $plan['base_plan']['name'] ?? 'N/A',
            'interior_days' => $plan['interior_days'],
            'exterior_days' => $plan['exterior_days'],
            'image' => !empty($plan['image']) ? asset('storage/' . $plan['image']) : null,
            'price' => $plan['price'],
            'offer_price' => $plan['offer_price'],
            'recommendation' => $plan['recommendation'],
            'is_recommended' => $plan['is_recommended'],
            'services' => $plan['services'],
            'is_popular' => $plan['is_popular'],
            'status' => $plan['status'],
            'all_services' => $services,
        ];
    }

    return response()->json([
        'status'  => true,
        'message' => 'Plans found.',
        'data'    => $data,
    ], 200);
}



    public function get_popular_plans(Request $request): JsonResponse
    {

        $plans = Plan::with(['base_plan'])->where('is_popular', 1)->whereNull('deleted_at')->get()->toArray();



        if (empty($plans)) {
            return response()->json([
                'status'    => false,
                'message'   => 'No popular plans found.',
                'data'      => [],
            ], 200);
        }

        $data = [];
        $categories = ['1' => 'Car Subscription', '2' => 'Bike Subscription', '3' => 'Scooty Subscription', '4' => 'Other Subscription'];
        foreach ($plans as $plan) {
            $serviceIds = !empty($plan['services']) ? explode(',', $plan['services']) : [];
            $services = [];
            if ($serviceIds) {
                $services = Service::whereIn('id', $serviceIds)->get()->map(function ($service) {
                    $service->image = Helper::showImage($service->image); // Modify the image
                    return $service; // Return the modified service
                });
            }
            $data[] = [

                'id' => $plan['id'],
                'name' => $plan['name'],
                'category_id' => $plan['category_id'],
                'category_name' => $categories[$plan['category_id']],
                'base_plan_id' => $plan['base_plan_id'],
                'base_plan_name' => $plan['base_plan']['name'] ?? 'N/A',
                'interior_days' => $plan['interior_days'],
                'exterior_days' => $plan['exterior_days'],
                'image' => Helper::showImage($plan['image'], true),


                'price' => $plan['price'],
                // 'offer_price' => $plan['offer_price'],
                // 'discount' => $plan['discount'],
                // 'rating' => $plan['rating'],
                // 'rating_count' => $plan['rating_count'],
                // 'duration' => $plan['duration'],
                // 'description' => $plan['description'],
                'recommendation' => $plan['recommendation'],
                'is_recommended' => $plan['is_recommended'],
                'services' => $plan['services'],
                'is_popular' => $plan['is_popular'],
                'status' => $plan['status'],
                'all_services' => $services,

            ];
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Popular plans found.',
            'data'   => $data,
        ], 200);
    }

    public function get_banners(Request $request): JsonResponse
    {

        $banners = Banner::where('status', 1)->whereNull('deleted_at')->get()->toArray();
        $data = [];
        if (empty($banners)) {
            return response()->json([
                'status'    => false,
                'message'   => 'No banners found.',
                'data'      => $data,
            ], 200);
        }

        foreach ($banners as $banner) {

            $data[] = [
                'id' => $banner['id'],
                'name' => $banner['name'],
                'image' => !empty($banner['image']) ? Helper::showImage($banner['image']) : null,
            ];
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Banners found.',
            'data'   => $data,
        ], 200);
    }


    public function get_blogs(Request $request): JsonResponse
    {

        $blogs = Blog::where('status', 1)->whereNull('deleted_at')->get()->toArray();
        $data = [];
        if (empty($blogs)) {
            return response()->json([
                'status'    => false,
                'message'   => 'No blogs found.',
                'data'      => $data,
            ], 200);
        }

        foreach ($blogs as $blog) {

            $data[] = [
                'id' => $blog['id'],
                'title' => $blog['title'],
                'icon' => !empty($blog['icon']) ? Helper::showImage($blog['icon']) : null,
            ];
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Blogs found.',
            'data'   => $data,
        ], 200);
    }


    public function get_testimonials(Request $request): JsonResponse
    {

        $testimonials = Testimonial::where('status', 1)->whereNull('deleted_at')->get()->toArray();
        $data = [];
        if (empty($testimonials)) {
            return response()->json([
                'status'    => false,
                'message'   => 'No Testimonials found.',
                'data'      => $data,
            ], 200);
        }

        foreach ($testimonials as $testimonial) {

            $data[] = [
                'id' => $testimonial['id'],
                'name' => $testimonial['name'],
                'video' => !empty($testimonial['video']) ? Helper::showImage($testimonial['video']) : null,
            ];
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Testimonials found.',
            'data'   => $data,
        ], 200);
    }


    public function get_blogs_details(Request $request, $id): JsonResponse
    {

        $banner = Blog::where('id', $id)->where('status', 1)->whereNull('deleted_at')->first();
        $data = [];
        if (empty($banner)) {
            return response()->json([
                'status'    => false,
                'message'   => 'No blog found.',
                'data'      => $data,
            ], 404);
        }
        $keys_details = !empty($banner['key_details']) ? json_decode($banner['key_details'], 1) : null;

        $data = [
            'id' => $banner['id'],
            'title' => $banner['title'],
            'description' => $banner['description'],
            'key_details' => $keys_details,
            'icon' => !empty($banner['icon']) ? Helper::showImage($banner['icon']) : null,
            'image' => !empty($banner['image']) ? Helper::showImage($banner['image']) : null,
        ];


        return response()->json([
            'status'    => true,
            'message'   => 'Blog found.',
            'data'   => $data,
        ], 200);
    }
}
