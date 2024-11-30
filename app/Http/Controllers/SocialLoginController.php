<?php

namespace App\Http\Controllers;

use App\Models\SocialLogin;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class SocialLoginController extends Controller
{
    public function auth_google()
    {
        // Redirect to Google authentication page
        return Socialite::driver('google')->stateless()->scopes(['email', 'profile', 'openid'])->redirect();
    }

    public function auth_google_callback(Request $request)
    {
        try {
            // Get user info from Google
            $socialUser = Socialite::driver('google')->stateless()->user();

            // Check if the user already exists in the SocialLogin model
            $existingUser = SocialLogin::where('provider_id', $socialUser->id)->first();

            if ($existingUser) {
                // User already exists, log them in
                Auth::login($existingUser);

                // Generate a token (if using JWT, Passport, or Sanctum)
                $token = $existingUser->createToken('Social Login')->accessToken;

                return response()->json([
                    'token' => $token,
                    'user' => $existingUser
                ]);
            } else {
                // Create a new user if they don't exist
                $newUser = SocialLogin::create([
                    'user_names' => $socialUser->name,
                    'provider_name' => 'Google',
                    'provider_id' => $socialUser->id,
                    'email' => $socialUser->email,
                    'image' => $socialUser->avatar,
                ]);

                // Log in the new user
                Auth::login($newUser);

                // Generate a token (if using JWT, Passport, or Sanctum)
                $token = $newUser->createToken('Social Login')->accessToken;

                return response()->json([
                    'token' => $token,
                    'user' => $newUser
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }
}
