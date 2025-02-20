<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Visit;

class AdminController extends Controller
{
    public function home(){
        $all_count_users = collect(User::all())->count();
        $count_users = collect(User::all()->where('firstname','!=',null))->count();
        $partial_count_users = collect(User::all()->where('firstname',null))->count();
    	
        $user_joined_today_count = User::whereDate('created_at', now()->toDateString())->count();
        
        $onlineUsersCount = User::where('last_active_at', '>=', now()->subMinutes(5))->count();

        $percent_online_user_count = ( $onlineUsersCount * 100 ) / $count_users;
        $percent_today_count_users = ( $user_joined_today_count * 100 ) / $count_users; 

        #start of visit count
        $todaysVisitCount = Visit::whereDate('date', Carbon::today())->sum('count');    
        $yesterdaysVisitCount = Visit::whereDate('date', Carbon::yesterday())->sum('count');
        $allVisitCount = Visit::all()->sum('count');
        #end of visit count

        return  view('admin.user.home',[
            'allUsersCount' => $all_count_users,
            'partialCountUsers' => $partial_count_users,
            'user_joined_today_count' => $user_joined_today_count,
            'online_user_count' => $onlineUsersCount,
            'percent_online_user_count' => substr(number_format($percent_online_user_count, 2, '.', ''), 0, -1).'%',
            'percent_user_joined_today' => substr(number_format($percent_today_count_users, 2, '.', ''), 0, -1).'%',
            'todaysVisitCount' => $todaysVisitCount,
            'yesterdaysVisitCount' => $yesterdaysVisitCount,
            'allVisitCount' => $allVisitCount,
    	]);
    }

    public function refresh_counts(){
        $all_count_users = collect(User::all())->count();
        $count_users = collect(User::all()->where('firstname','!=',null))->count();
        $partial_count_users = collect(User::all()->where('firstname',null))->count();
        
        $onlineUsersCount = User::where('last_active_at', '>=', now()->subMinutes(5))->count();

        $percent_online_user_count = ( $onlineUsersCount * 100 ) / $count_users;

        #start User_joined_today
        $user_joined_today_count = User::whereDate('created_at', now()->toDateString())->count();
        $percent_user_joined_today = ( $user_joined_today_count * 100 ) / $count_users;
        #end User_joined_today

        #start visit count
        $todaysVisitCount = Visit::whereDate('date', Carbon::today())->sum('count');    
        $yesterdaysVisitCount = Visit::whereDate('date', Carbon::yesterday())->sum('count');

        $allVisitCount = Visit::all()->sum('count');
        #end of visit count

        function todaysVisitCountFN($todaysVisitCount) {
            if ($todaysVisitCount >= 1000000000) {
                // For billions
                return number_format($todaysVisitCount / 1000000000, 1) . 'B';
            } elseif ($todaysVisitCount >= 1000000) {
                // For millions
                return number_format($todaysVisitCount / 1000000, 1) . 'M';
            } elseif ($todaysVisitCount >= 1000) {
                // For thousands
                return number_format($todaysVisitCount / 1000, 1) . 'K';
            } else {
                // Return the number as is if it's less than 1000
                return $todaysVisitCount;
            }
        }

        function yesterdaysVisitCountFN($yesterdaysVisitCount) {
            if ($yesterdaysVisitCount >= 1000000000) {
                // For billions
                return number_format($yesterdaysVisitCount / 1000000000, 1) . 'B';
            } elseif ($yesterdaysVisitCount >= 1000000) {
                // For millions
                return number_format($yesterdaysVisitCount / 1000000, 1) . 'M';
            } elseif ($yesterdaysVisitCount >= 1000) {
                // For thousands
                return number_format($yesterdaysVisitCount / 1000, 1) . 'K';
            } else {
                // Return the number as is if it's less than 1000
                return $yesterdaysVisitCount;
            }
        }

        function allVisitCountFN($allVisitCount) {
            if ($allVisitCount >= 1000000000) {
                // For billions
                return number_format($allVisitCount / 1000000000, 1) . 'B';
            } elseif ($allVisitCount >= 1000000) {
                // For millions
                return number_format($allVisitCount / 1000000, 1) . 'M';
            } elseif ($allVisitCount >= 1000) {
                // For thousands
                return number_format($allVisitCount / 1000, 1) . 'K';
            } else {
                // Return the number as is if it's less than 1000
                return $allVisitCount;
            }
        }

        return response()->json([
            'allUsersCount' => $all_count_users,
            'partialCountUsers' => $partial_count_users,
            'online_user_count' => $onlineUsersCount,
            'percent_online_user_count' => substr(number_format($percent_online_user_count, 2, '.', ''), 0, -1).'%',
            'user_joined_today_count' => $user_joined_today_count,
            'percent_user_joined_today' => substr(number_format($percent_user_joined_today, 2, '.', ''), 0, -1).'%',
            'todaysVisitCount' => todaysVisitCountFN($todaysVisitCount),
            'yesterdaysVisitCount' => yesterdaysVisitCountFN($yesterdaysVisitCount),
            'allVisitCount' => allVisitCountFN($allVisitCount),
        ]);

    }

}
