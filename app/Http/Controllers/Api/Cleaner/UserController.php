<?php

namespace App\Http\Controllers\Api\Cleaner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\BankDetail;

class UserController extends Controller
{
   public function __construct()
    {
       $this->middleware(["auth:cleanerApi"]);
   }
    public function getUserDetails()
    {
        return response()->json([
            'status' => true,
            'user'   => Auth::guard('cleanerApi')->user(),
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateUserProfile(Request $request)
    {
        $user = Auth::guard('cleanerApi')->user();

        $validatedData = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:app_users,email,' . $user->id,
            
        ],[
            'name'=> 'Name is required',
            'email'=> 'Email is required',
            'email.unique'=> 'Email already exists',
           
        ]);
        if ($validatedData->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validatedData->errors(),
            ],422);
        }

        $user->update($validatedData->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Profile updated successfully.',
            'user'    => $user,
        ]);
    }
    
    
       /**
     * Logout user by revoking token.
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully.',
        ]);
    }
    
   
}
