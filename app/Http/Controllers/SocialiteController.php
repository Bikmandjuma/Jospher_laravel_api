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

            $user = Socialite::driver('google')->stateless()->user();

            User::create([
                    'names' => $user->name,
                    'provider_name' => 'Google',
                    'provider_id' => $user->id,
                    'email' => $user->email,
                   
            ]);

            return redirect('https://jobsphererdaflask-production.up.railway.app/user/dashboard');

        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }

        
    }
}
