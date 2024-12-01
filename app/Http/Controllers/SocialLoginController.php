<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class SocialLoginController extends Controller
{
    public function auth_google()
    {
        return Socialite::driver('google')->stateless()->scopes(['email', 'profile', 'openid'])->redirect();
    }

    public function auth_google_callback()
    {
        try {
            // Step 1: Get user info from Google
            $socialUser = Socialite::driver('google')->stateless()->user();

            // Log the Google User ID for debugging purposes
            Log::info('Google User ID:', ['id' => $socialUser->id]);

            // Step 2: Check if the user already exists in our database
            $existingUser = User::where('email', $socialUser->email)->first();

            if ($existingUser) {
                // Step 3: If user exists, pass relevant data to Flask
                $googleUserData = [
                    'id' => $existingUser->id,
                    'name' => $existingUser->user_name,
                    'image' => $existingUser->image,
                    'token' => $existingUser->provider_token,
                ];

                // Log the data that will be passed to Flask
                Log::info('Redirecting with Existing User Data:', $googleUserData);

                return redirect('http://192.168.0.82:8000/seeker/dashboard?' . http_build_query($googleUserData));
            } else {
                // Step 4: If user is new, create user and save their data
                $newUser = User::create([
                    'user_name' => $socialUser->name,
                    'provider_name' => 'Google',
                    'provider_id' => $socialUser->id,
                    'email' => $socialUser->email,
                    'image' => $socialUser->avatar,
                    'provider_token' => $socialUser->token,  // Correct way to get the token
                ]);

                // Step 5: Pass relevant data to Flask
                $googleUserData = [
                    'id' => $newUser->id,
                    'name' => $newUser->user_name,
                    'image' => $newUser->image,
                    'token' => $newUser->provider_token,
                ];

                // Log the data that will be passed to Flask
                Log::info('Redirecting with New User Data:', $googleUserData);

                return redirect('http://192.168.0.82:8000/seeker/dashboard?' . http_build_query($googleUserData));
            }
        } catch (Exception $e) {
            // Log any errors that occur during the process
            Log::error('Google login error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }
}
