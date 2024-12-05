<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
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
