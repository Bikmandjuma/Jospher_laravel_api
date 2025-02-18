<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class WebAuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            // Validate input fields
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ], [
                'username.required' => 'The username is required.',
                'password.required' => 'The password is required.',
            ]);

            // Determine if the username is an email or phone number
            $loginField = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

            // Attempt to authenticate with the 'admin' guard
            if (Auth::guard('admin')->attempt([
                $loginField => $request->input('username'),
                'password' => $request->input('password'),
            ])) {
                $request->session()->regenerate(); // Prevent session fixation attacks
                
                // return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
                return 'Login successful!';
            }

            return back()->withErrors([
                'username' => 'Invalid Username or Password, try again!',
            ])->withInput();

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Login failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'An unexpected error occurred. Please try again later.',
            ])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        // Invalidate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // return redirect()->route('admin.login')->with('success', 'You have been logged out.');
        return 'logout system !';
    }

}
