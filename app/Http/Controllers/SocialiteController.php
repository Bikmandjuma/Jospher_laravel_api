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
        return Socialite::driver('google')->redirect();
    }

    public function login_by_google_callback(Request $request)
    {
        return response()->json([
            'message' => "Google testing !",
        ]);
    }
}
