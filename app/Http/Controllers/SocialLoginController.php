<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SocialLogin;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function auth_google()
    {
        // return Socialite::driver('google')->redirect();
        return Socialite::driver('google')->stateless()->scopes(['email', 'profile', 'openid'])->redirect();
    }

    // Handle callback from provider
    public function auth_google_callback()
    {

        try {
            // Get user info from Google
            $socialUser = Socialite::driver('google')->stateless()->user();

            // Log the user info for debugging
            Log::info('Google user data:', $socialUser->toArray());

            // Find the user by email (if it exists)
            $existingUser = SocialLogin::where('email', $socialUser->email)->first();

            if ($existingUser) {

                Auth::login($existingUser);
                return redirect('https://jobsphererdaflask-production.up.railway.app/user/dashboard');

            } else {

                $newUser = SocialLogin::Create(
                    [   'user_names' =>  $socialUser->name,
                        'provider_name' => 'Google',
                        'provider_id' => $socialUser->id,
                        'email' =>  $socialUser->email,
                        'image' =>  $socialUser->avatar,
                    ]
                );
                
                Auth::login($newUser);
                return redirect('https://jobsphererdaflask-production.up.railway.app/user/dashboard');

            }

        } catch (Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }


    }

    // public function login_by_google()
    // {
    //     // Initiating Google login process
    //     return Socialite::driver('google')->stateless()->scopes(['email', 'profile', 'openid'])->redirect();
    // }


}

