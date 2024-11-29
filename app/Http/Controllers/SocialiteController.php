<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SocialiteController extends Controller
{
    public function login_by_google()
    {
        // Initiating Google login process
        return Socialite::driver('google')->stateless()->scopes(['email', 'profile', 'openid'])->redirect();
    }

    public function login_by_google_callback(Request $request)
    {
        try {
            // Get the user data from Google
            $user = Socialite::driver('google')->stateless()->user();

            // Check if the user already exists in the database
            $existingUser = User::where('provider_id', $user->id)->first();

            if ($existingUser) {

                return redirect('https://jobsphererdaflask-production.up.railway.app/user/dashboard');
                
            } else {

                $profilePicture = $user->avatar ?? 'default_image_url';
                $birthdate = $user->user['birthday'] ?? null;
                $phone = $user->phone ?? 'N/A';
                $gender = $user->user['gender'] ?? null;

                // Create a new user in the database
                $newUser = User::create([
                    'names' => $user->name,
                    'provider_name' => 'Google',
                    'provider_id' => $user->id,
                    'email' => $user->email,
                    'phone' => $phone,
                    'image' => $profilePicture,
                    'dob' => $birthdate,
                    'gender' => $gender,
                ]);

                return redirect('https://jobsphererdaflask-production.up.railway.app/user/dashboard');
            }

        } catch (Exception $e) {
            // Return a response indicating something went wrong
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }

        
    }
}
