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
        return Socialite::driver('google')->stateless()->scopes(['email', 'profile', 'openid'])->redirect();
    }

    public function login_by_google_callback(Request $request)
    {
        try {
            // Retrieve user from Google
            $user = Socialite::driver('google')->stateless()->user();

            // Check if user exists in SocialiteUser model
            $existingUser = SocialiteUser::where('provider_id', $user->id)->first();

            if ($existingUser) {
                // Generate JWT token for existing user
                $token = Auth::guard('api')->login($existingUser);

                // Redirect to Flask app with token
                return redirect('https://jobsphererdaflask-production.up.railway.app/user/dashboard?token=' . $token);
            }

            // Get additional profile information from Google
            $profilePicture = $user->avatar; 
            $birthdate = isset($user->user['birthday']) ? $user->user['birthday'] : null;

            // Create new user in SocialiteUser model
            $newUser = SocialiteUser::create([
                'names' => $user->name,
                'provider_name' => 'Google',
                'provider_id' => $user->id,
                'provider_email' => $user->email,
            ]);

            // Create corresponding user in the main User model
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
            return redirect('https://jobsphererdaflask-production.up.railway.app/user/dashboard?token=' . $token);
        } catch (Exception $e) {
            // Log and return error message
            \Log::error('Google login error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }
}
