<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    public function View_information(){
        $admin = Auth::guard('admin')->user();

        if ($admin) {

            return response()->json([
                'status' => 'Auth admin info',
                'user_info' => $admin
            ], 200);

        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function count_seekers(){
        $seeker_count = collect(User::all())->count();
        return response()->json([
            'seeker_counts' => $seeker_count,
        ]);
    }

    public function count_seekers_today()
    {
        $today_count = User::whereDate('created_at', now()->toDateString())->count();
        
        return response()->json([
            'seeker_counts_today' => $today_count,
        ]);
    }

   public function count_online_users()
    {
        $online_users = User::where('last_active_at', '>=', now()->subMinutes(5))->count();

        return response()->json([
            'online_users' => $online_users,
        ]);
    }


    public function update_password(Request $request){

        try {
            
            $request->merge([
                'current_password' => trim($request->current_password),
                'new_password' => trim($request->new_password),
                'confirm_new_password' => trim($request->confirm_new_password),
            ]);

            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|between:8,32|same:confirm_new_password',
                'confirm_new_password' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new \Illuminate\Validation\ValidationException($validator);
            }

            $user = auth()->guard('admin')->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Current password does not match.',
                ], 401);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Password changed successfully.',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors occurred.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Password update failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Update failed. ' . $e->getMessage(),
            ], 500);
        }
        
    }


    public function edit_info(Request $request){

        try {
        
            $userId = Auth::guard('admin')->user()->id;

            $validatedData = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'gender' => 'required|string|max:255',
                'phone' => [
                    'required',
                    'numeric',
                    'digits:10',
                    'regex:/^(072|078|073|079)\d{7}$/',
                    'unique:users,phone,' . $userId,
                    'unique:admins,phone,'. $userId,
                ],
                    'email' => [
                    'required',
                    'email',
                    'unique:users,email,' . $userId,
                    'unique:admins,email,' . $userId,
                ],
                    'dob' => [
                    'required',
                    'date',
                ],

            ]);

            $user = Admin::find($userId);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found.'
                ], 404);
            }

            $user->update($validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Data updated successfully!',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
        
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors occurred.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            
            \Log::error('Error occurred while editing user info: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        
        }
        
    }


}