<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Facades\Validator;
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
            // Get user info from Google
            $user = Socialite::driver('google')->stateless()->user();

            // Log the user info for debugging
            Log::info('Google user data:', $user->toArray());

            // Find the user by email (if it exists)
            $existingUser = User::where('email', $user->email)->first();

            if ($existingUser) {
                // Attempt to authenticate the user
                Auth::login($existingUser);
                return redirect()->json(['message' => 'User logged in successfully']);
            } else {
                // Create a new user
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'provider_name' => 'Google',
                    'provider_id' => $user->id,
                ]);
                Auth::login($newUser);
                return redirect()->json(['message' => 'New user created and logged in']);
            }
        } catch (Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

}