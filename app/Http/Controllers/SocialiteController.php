<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\SocialiteUser;
use App\Models\User;

class SocialiteController extends Controller
{
    public function login_by_google()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    
    public function login_by_google_callback(){
        try {
            $user = Socialite::driver('google')->stateless()->user();

            // Check if user exists
            $existingUser = SocialiteUser::where('provider_id', $user->id)->first();

            if ($existingUser) {
                // Generate JWT token
                $token = Auth::guard('api')->login($existingUser);

                // Redirect to Flask app with token
                return redirect('http://192.168.0.82:8000/user/dashboard?token=' . $token);
            }

            // Get profile picture (avatar)
            $profilePicture = $user->avatar; 
            $birthdate = isset($user->user['birthday']) ? $user->user['birthday'] : null;

            // Create new user with profile picture
            $newUser = SocialiteUser::create([
                'names' => $user->name,
                'provider_name' => 'Google',
                'provider_id' => $user->id,
                'provider_email' => $user->email,
            ]);

            User::create([
                'socialite_user_id' => $newUser->id,
                'names' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'dob' => $birthdate,
                'image' => $profilePicture,
            ]);

            // Generate JWT token for the new user
            $token = Auth::guard('api')->login($newUser);

            // Redirect to Flask app with token
            return redirect('http://192.168.0.82:8000/user/dashboard?token=' . $token);

        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }


    }

    

}
