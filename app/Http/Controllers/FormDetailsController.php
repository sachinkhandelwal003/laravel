<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FormDetailsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'description' => 'required|string',
        ]);

        try {
            Inquiry::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Your inquiry has been submitted successfully!'
            ]);
        } catch (\Exception $e) {
            // Log the actual error for debugging
            Log::error('Inquiry submission failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again later.',
                'error' => $e->getMessage() // Only for development
            ], 500);
        }
    }
}