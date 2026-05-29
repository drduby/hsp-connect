<?php

namespace App\Http\Controllers;

use App\Models\FaqItem;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqItems = FaqItem::published()->orderBy('sort_order')->get(['question', 'question_en', 'answer', 'answer_en', 'tags']);

        $tags = Tag::where('is_active', true)
            ->withCount(['posts' => fn ($q) => $q->where('is_published', true)])
            ->orderBy('id')
            ->get(['id', 'name', 'name_en', 'slug', 'color']);

        $memberCount = User::whereNotNull('email_verified_at')->count();
        $onlineCount = User::where('last_seen_at', '>=', now()->subMinutes(5))->count();
        $postCount = Post::where('is_published', true)->count();
        $counts = ['all' => $postCount];

        return view('faq', compact('faqItems', 'tags', 'memberCount', 'onlineCount', 'postCount', 'counts'));
    }
}
