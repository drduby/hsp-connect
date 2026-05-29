<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostReport;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users' => User::count(),
            'online' => User::where('last_seen_at', '>=', now()->subMinutes(5))->where('is_admin', false)->count(),
            'posts' => Post::where('is_published', true)->count(),
            'reports' => PostReport::where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
