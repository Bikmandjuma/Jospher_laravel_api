<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class WebAuthController extends Controller
{
    public function login_form(Request $request)
    {
        return view('admin.auth.login');
    }

    public function submit_login(Request $request)
    {

            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ], [
                'username.required' => 'The username is required.',
                'password.required' => 'The password is required.',
            ]);

            $loginField = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

            if (Auth::guard('admin')->attempt([
                $loginField => $request->input('username'),
                'password' => $request->input('password'),
            ])) {
                $request->session()->regenerate(); // Prevent session fixation attacks
                
                return redirect()->route('admin.dashboard')->with('info', 'Welcome '.Auth::guard('admin')->user()->firstname);
            }

            return back()->with([
                'error' => 'Invalid username/password, try again !',
            ]);
            // toastr()->success('Password changed successfully',['timeOut' => 5000]);

    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        // Invalidate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('info', 'You have been logged out.');
    }

}
