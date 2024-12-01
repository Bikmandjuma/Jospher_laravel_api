<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SocialLogin;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class SocialLoginController extends Controller
{
    // Step 1: Redirect to Google for authentication
    public function auth_google()
    {
        return Socialite::driver('google')->stateless()->scopes(['email', 'profile', 'openid'])->redirect();
    }

    // Step 2: Handle the callback from Google
    public function auth_google_callback()
    {
        try {
            // Step 2.1: Get user info from Google
            $socialUser = Socialite::driver('google')->stateless()->user();

            // Log the Google User ID for debugging purposes
            Log::info('Google User ID:', ['id' => $socialUser->id]);

            // Step 2.2: Check if the user already exists in our database
            $existingUser = SocialLogin::where('email', $socialUser->email)->first();

            // Prepare the Google user data to pass to Flask
            $googleUserData = [
                'id' => $socialUser->id,  // Google user ID
                'name' => $socialUser->name,  // Google user name
                'email' => $socialUser->email,  // Google user email
                'image' => $socialUser->avatar,  // Google user avatar
            ];

            // Log the data that will be passed to Flask
            Log::info('Redirecting with Google Data:', $googleUserData);

            if ($existingUser) {
                // Step 3: If user exists, redirect to Flask with Google user data
                return redirect('https://jobsphererdaflask-production.up.railway.app/seeker/dashboard?' . http_build_query($googleUserData));
            } else {
                // Step 4: If the user is new, save their information and then redirect
                $newUser = SocialLogin::create([
                    'user_names' => $socialUser->name,
                    'provider_name' => 'Google',
                    'provider_id' => $socialUser->id,
                    'email' => $socialUser->email,
                    'image' => $socialUser->avatar,
                ]);

                // Step 5: After saving the new user, redirect to Flask with the Google user data
                return redirect('https://jobsphererdaflask-production.up.railway.app/seeker/dashboard?' . http_build_query($googleUserData));
            }

        } catch (Exception $e) {
            // Log any errors that occur during the process
            Log::error('Google login error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }
}
