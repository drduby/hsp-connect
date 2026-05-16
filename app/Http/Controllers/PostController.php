<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Services\PostService;
use Illuminate\Contracts\View\View;

class PostController extends Controller
{
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(): View
    {
        $posts = $this->postService->publishedPosts();
        $tags = Tag::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name', 'color']);

        return view('home', [
            'posts' => $posts,
            'tags' => $tags,
        ]);
    }
}
