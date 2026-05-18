<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;

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

        return view('home', compact('counts', 'tags', 'savedCount', 'myPostCount'));
    }
}
