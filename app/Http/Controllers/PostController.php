<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function index(): View
    {
        $counts = [
            'all' => Post::where('is_published', true)->count(),
            'experiences' => Post::where('is_published', true)->where('type', 'experience')->count(),
            'questions' => Post::where('is_published', true)->where('type', 'question')->count(),
        ];

        $tags = Tag::query()
            ->where('is_active', true)
            ->withCount(['posts' => fn ($q) => $q->where('is_published', true)])
            ->orderBy('id')
            ->get(['id', 'name', 'color']);

        $savedCount = auth()->check()
            ? Post::whereHas('saves', fn ($q) => $q->where('user_id', auth()->id()))->count()
            : 0;

        $myPostCount = auth()->check()
            ? Post::where('user_id', auth()->id())->where('is_published', true)->count()
            : 0;

        $likesGivenCount = auth()->check()
            ? DB::table('post_likes')->where('user_id', auth()->id())->count()
            : 0;

        $memberCount = User::whereNotNull('email_verified_at')->where('is_admin', false)->count();
        $onlineCount = User::where('last_seen_at', '>=', now()->subMinutes(5))->where('is_admin', false)->count();

        return view('home', compact('counts', 'tags', 'savedCount', 'myPostCount', 'likesGivenCount', 'memberCount', 'onlineCount'));
    }
}
