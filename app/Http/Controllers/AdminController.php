<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{
    public function View_information(){
        $admin = Auth::guard('admin')->user();

        if ($admin) {

            return response()->json([
                'status' => 'Auth admin info',
                'user_info' => $admin
            ], 200);

        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function count_seekers(){
        $seeker_count = collect(User::all())->count();
        return response()->json([
            'seeker_counts' => $seeker_count,
        ]);
    }

    public function count_seekers_today()
    {
        $today_count = User::whereDate('created_at', now()->toDateString())->count();
        
        return response()->json([
            'seeker_counts_today' => $today_count,
        ]);
    }

   public function count_online_users()
    {
        $online_users = User::where('last_active_at', '>=', now()->subMinutes(5))->count();

        return response()->json([
            'online_users' => $online_users,
        ]);
    }


}
