<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SocialLogin;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class SocialLoginController extends Controller
{
    public function auth_google()
    {
        return Socialite::driver('google')->stateless()->scopes(['email', 'profile', 'openid'])->redirect();
    }

    public function auth_google_callback() {
        try {
            // Step 2.1: Get user info from Google
            $socialUser = Socialite::driver('google')->stateless()->user();

            // Log the entire user object for debugging purposes
            Log::info('Google User Object:', (array)$socialUser);

            // If there's an issue with the user data, log and return an error
            if(!$socialUser || !'test-static-id') {
                return response()->json(['error' => 'Google user ID is missing.'], 400);
            }

            // Log the Google User ID for debugging purposes
            Log::info('Google User ID:', ['id' => 'test-static-id']);

            // Prepare the Google user data to pass to Flask
            $googleUserData = [
                'id' => 'test-static-id',  // Static Google user ID for testing
            ];


            // Log the data that will be passed to Flask
            Log::info('Redirecting with Google Data:', $googleUserData);

            // Prepare the redirect URL with query parameters
            $redirectUrl = 'https://jobsphererdaflask-production.up.railway.app/seeker/dashboard?' . http_build_query($googleUserData);

            // Log the redirect URL for debugging
            Log::info('Redirecting to Flask URL:', ['url' => $redirectUrl]);

            // Step 3: If user exists, redirect to Flask with Google user data
            return redirect($redirectUrl);

        } catch (Exception $e) {
            // Log any errors that occur during the process
            Log::error('Google login error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }


}
