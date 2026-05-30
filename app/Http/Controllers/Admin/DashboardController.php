<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostReport;
use App\Models\User;
use Carbon\Carbon;
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

        $activity = [
            'today' => $this->activityFrom(now()->startOfDay()),
            'month' => $this->activityFrom(now()->startOfMonth()),
            'year' => $this->activityFrom(now()->startOfYear()),
        ];

        $recentUsers = User::orderByDesc('created_at')->limit(5)->get();
        $recentPosts = Post::with('user')->where('is_published', true)->orderByDesc('created_at')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'activity', 'recentUsers', 'recentPosts'));
    }

    /** @return array<string, int> */
    private function activityFrom(Carbon $from): array
    {
        return [
            'registrations' => ActivityLog::where('action', 'user.registered')->where('created_at', '>=', $from)->count(),
            'logins' => ActivityLog::where('action', 'login.success')->where('created_at', '>=', $from)->count(),
            'failed_logins' => ActivityLog::whereIn('action', ['login.failed', 'admin.login.failed'])->where('created_at', '>=', $from)->count(),
            'posts' => Post::where('is_published', true)->where('created_at', '>=', $from)->count(),
            'comments' => Comment::where('created_at', '>=', $from)->count(),
            'reports' => ActivityLog::where('action', 'report.submitted')->where('created_at', '>=', $from)->count(),
            'feedback' => ActivityLog::whereIn('action', ['feedback.idea', 'feedback.bug'])->where('created_at', '>=', $from)->count(),
        ];
    }
}
