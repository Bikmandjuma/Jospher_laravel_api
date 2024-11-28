<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\SocialiteUser;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SocialiteController extends Controller
{
    public function login_by_google()
    {
        return Socialite::driver('google')->stateless()->scopes(['email', 'profile', 'openid'])->redirect();
    }

    public function login_by_google_callback(Request $request)
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();

            // Log the user data received from Google
            Log::info('Google user data:', $user->toArray());

            $existingUser = SocialiteUser::where('provider_id', $user->id)->first();

            if ($existingUser) {
                $token = Auth::guard('api')->login($existingUser);

                return redirect('https://jobsphererdaflask-production.up.railway.app/user/dashboard?token=' . $token);
            }

            $profilePicture = $user->avatar;
            $birthdate = isset($user->user['birthday']) ? $user->user['birthday'] : null;

            // Create a new SocialiteUser
            $newUser = SocialiteUser::create([
                'names' => $user->name,
                'provider_name' => 'Google',
                'provider_id' => $user->id,
                'provider_email' => $user->email,
            ]);

            // Create the corresponding User
            // User::create([
            //     'names' => $user->name,
            //     'email' => $user->email,
            //     'phone' => $user->phone,
            //     'gender' => $user->gender,
            //     'dob' => $birthdate,
            //     'image' => $profilePicture,
            // ]);

            $token = Auth::guard('api')->login($newUser);

            return redirect('https://jobsphererdaflask-production.up.railway.app/user/dashboard?token=' . $token);
        } catch (Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }


    
}
