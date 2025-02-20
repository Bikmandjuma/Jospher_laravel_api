<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{
    public function home(){
        $count_users = collect(User::all())->count();
    	
        $today_count_users = User::whereDate('created_at', now()->toDateString())->count();
        
        $onlineUsersCount = User::where('last_active_at', '>=', now()->subMinutes(5))->count();

        $percent_online_user_count = ( $onlineUsersCount * 100 ) / $count_users;
        $percent_today_count_users = ( $today_count_users * 100 ) / $count_users; 


        return  view('admin.user.home',[
            'all_users_count' => $count_users,
            'todays_users_joined_count' => $today_count_users,
            'online_users_count' => $onlineUsersCount,
            'percent_online_user_count' => $percent_online_user_count,
            'percent_today_count_users' => $percent_today_count_users
    	]);
    }

    public function online_users(){
        $count_users = collect(User::all())->count();
        
        $onlineUsersCount = User::where('last_active_at', '>=', now()->subMinutes(5))->count();

        $percent_online_user_count = ( $onlineUsersCount * 100 ) / $count_users;


        return response()->json([
            'count' => $onlineUsersCount,
            'percent_online_user_count' => $percent_online_user_count.'%',

        ]);

    }

}
