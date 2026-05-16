<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Services\PostService;
use Illuminate\Contracts\View\View;

class PostController extends Controller
{
    public function __construct(private PostService $postService) {}

    public function index(): View
    {
        $posts = $this->postService->publishedPosts();
        $tags = Tag::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name', 'color']);

        return view('home', compact('posts', 'tags'));
    }
}
