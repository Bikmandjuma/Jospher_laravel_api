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
        return Socialite::driver('google')->stateless()->scopes(['email', 'profile', 'openid'])->redirect();
    }

    public function login_by_google_callback(Request $request)
    {
        try {

            $user = Socialite::driver('google')->stateless()->user();

            Log::info('Google user data:', $user->toArray());

            $existingUser = User::where('provider_id', $user->id)->first();

            if ($existingUser) {
                $token = Auth::guard('user')->login($existingUser);

                // return redirect('https://jobsphererdaflask-production.up.railway.app/user/dashboard?token=' . $token);
                return redirect()->json([
                    'massage' => "Existing user's google data login !"
                ]);

            }else{

                $profilePicture = $user->avatar;
                $birthdate = isset($user->user['birthday']) ? $user->user['birthday'] : null;
                $phone =$user->phone ?? 'N/A';
                $image = $user->avatar ?? 'default_image_url';

                $newUser = User::create([
                    'names' => $user->name,
                    'provider_name' => 'Google',
                    'provider_id' => $user->id,
                    'email' => $user->email,
                    'phone' => $phone,
                    'image' => $image,
                    'dob' => $birthdate,
                    'gender' => $user->gender,
                ]);

                $token = Auth::guard('user')->login($newUser);

                // return redirect('https://jobsphererdaflask-production.up.railway.app/user/dashboard?token=' . $token);
                return redirect()->json([
                    'massage' => "new user's google data added !"
                ]);
            }

        } catch (Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }

    }

}