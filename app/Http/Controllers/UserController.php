<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserController extends Controller
{

    public function register(Request $request){
            try {
                // Validate request data
                $validatedData = $request->validate([
                    'user_name' => 'required|string|max:255',
                    'email' => 'required|string|max:255|unique:users,email|unique:admins,email',
                    'phone' => 'required|string|max:255|unique:users,phone|unique:admins,phone',
                ]);

                // Create seeker_code logic...
                $lastUserCode = DB::table('users')->max('user_code');
                if ($lastUserCode) {
                    preg_match('/\d+$/', $lastUserCode, $matches);
                    $sequenceNumber = isset($matches[0]) ? (int)$matches[0] + 1 : 1;
                } else {
                    $sequenceNumber = 1;
                }

                $prefix = date('y');
                $middle = 'JSR';
                $formattedNumber = str_pad($sequenceNumber, 5, '0', STR_PAD_LEFT);
                $seeker_code = $prefix . $middle . $formattedNumber;

                // Create the user
                $user = User::create([
                    'user_code' => $seeker_code,
                    'provider_name' => 'Usual_reg',
                    'user_name' => $request->user_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ]);

                // Log the user creation for debugging
                \Log::info('User created: ' . $user->id);

                // Log the successful token creation
                $token = Auth::login($user);
                \Log::info('Auth token generated: ' . $token);

                // Return response
                return response()->json([
                    'status' => 'success',
                    'message' => 'User created successfully',
                    'user' => $user,
                    'authorisation' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
                
            } catch (\Exception $e) {
                // Log the error to Laravel logs
                \Log::error('Registration failed: ' . $e->getMessage());

                return response()->json([
                    'status' => 'error',
                    'message' => 'Registration failed. ' . $e->getMessage()
                ], 500);
            }



    }

	public function View_information(){
        $user = Auth::user();

        if ($user) {

            return response()->json([
                'status' => 'Auth user info',
                'user_info' => $user
            ], 200);

        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function edit_info(Request $request){
        $auth_user_id = Auth::user()->id;

        if ($auth_user_id) {

            $user = User::find($auth_user_id);

            if ($user) {
                // Validate the incoming request data
                $validatedData = $request->validate([
                    'user_name' => 'required|string|max:255',
                    'firstname' => 'required|string|max:255',
                    'lastname' => 'required|string|max:255',
                    'gender' => 'required|string|max:255',
                    'phone' => 'required|string|max:255|unique:users,phone,' . $auth_user_id.'|unique:admins,phone,' . $auth_user_id,
                    'birthdate' => 'required|string|max:255',
                ]);

                // Update the user's information based on the request
                $user->update($validatedData);

                // Return a success response
                return response()->json([
                    'message' => 'Data updated successfully!',
                ], 200);
            }

            return response()->json(['error' => 'User not found'], 404);
        
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    
    }


    public function profile_picture(){
        return 'profile public';
    }
    
}
