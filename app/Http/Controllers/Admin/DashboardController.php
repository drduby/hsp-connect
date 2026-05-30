<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostReport;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $thirtyDaysAgo = now()->subDays(30);
        $sixtyDaysAgo = now()->subDays(60);

        $stats = [
            'users' => User::count(),
            'users_new' => User::where('created_at', '>=', $thirtyDaysAgo)->count(),
            'users_prev' => User::whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo])->count(),

            'online' => User::where('last_seen_at', '>=', now()->subMinutes(5))->where('is_admin', false)->count(),

            'posts' => Post::where('is_published', true)->count(),
            'posts_new' => Post::where('is_published', true)->where('created_at', '>=', $thirtyDaysAgo)->count(),
            'posts_prev' => Post::where('is_published', true)->whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo])->count(),

            'reports' => PostReport::where('status', 'pending')->count(),
            'reports_new' => PostReport::where('created_at', '>=', $thirtyDaysAgo)->count(),
            'reports_prev' => PostReport::whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo])->count(),
        ];

        $today = now()->startOfDay();

        $health = [
            'registrations_today' => ActivityLog::where('action', 'user.registered')->where('created_at', '>=', $today)->count(),
            'logins_today' => ActivityLog::where('action', 'login.success')->where('created_at', '>=', $today)->count(),
            'failed_logins_today' => ActivityLog::whereIn('action', ['login.failed', 'admin.login.failed'])->where('created_at', '>=', $today)->count(),
            'posts_today' => Post::where('is_published', true)->where('created_at', '>=', $today)->count(),
            'comments_today' => Comment::where('created_at', '>=', $today)->count(),
            'reports_today' => ActivityLog::where('action', 'report.submitted')->where('created_at', '>=', $today)->count(),
            'feedback_today' => ActivityLog::whereIn('action', ['feedback.idea', 'feedback.bug'])->where('created_at', '>=', $today)->count(),
        ];

        $recentUsers = User::orderByDesc('created_at')->limit(5)->get();
        $recentPosts = Post::with('user')->where('is_published', true)->orderByDesc('created_at')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'health', 'recentUsers', 'recentPosts'));
    }
}
