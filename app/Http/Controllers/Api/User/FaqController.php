<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:userApi']);
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::guard('userApi')->id();
            $this->user = Auth::guard('userApi')->user();
            return $next($request);
        });
    }

    /**
     * Display a listing of the FAQs.
     */
    public function index(): JsonResponse
    {
        $faqs = Faq::all();
        return response()->json(['success' => true, 'data' => $faqs], 200);
    }

    /**
     * Store a newly created FAQ.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:255',
        ]);

        $faq = Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        return response()->json(['success' => true, 'message' => 'FAQ created successfully', 'data' => $faq], 201);
    }

    /**
     * Display the specified FAQ.
     */
    public function show($id): JsonResponse
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json(['success' => false, 'message' => 'FAQ not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $faq], 200);
    }

    /**
     * Update the specified FAQ.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:255',
        ]);

        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json(['success' => false, 'message' => 'FAQ not found'], 404);
        }

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        return response()->json(['success' => true, 'message' => 'FAQ updated successfully', 'data' => $faq], 200);
    }

    /**
     * Remove the specified FAQ.
     */
    public function destroy($id): JsonResponse
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json(['success' => false, 'message' => 'FAQ not found'], 404);
        }

        $faq->delete();
        return response()->json(['success' => true, 'message' => 'FAQ deleted successfully'], 200);
    }
}
