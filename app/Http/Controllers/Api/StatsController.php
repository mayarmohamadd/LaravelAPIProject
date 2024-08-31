<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Cache;


class StatsController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('stats', 60*60, function () {
            $allUsersCount = User::count();
            $allPostsCount = Post::count();
            $usersWithZeroPostsCount = User::doesntHave('posts')->count();
            return [
                'all_users_count' => $allUsersCount,
                'all_posts_count' => $allPostsCount,
                'users_with_zero_posts_count' => $usersWithZeroPostsCount,
            ];
        });
        return response()->json($stats, 200);
    }
}
