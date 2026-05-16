<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class PostService
{
    public function publishedPosts(): Collection
    {
        $userId = auth()->id();

        return Post::where('is_published', true)
            ->with([
                'tags',
                'user',
                'comments.user',
                'likes',
                'saves',
                'ratings',
            ])
            ->withCount(['likes', 'comments'])
            ->latest('published_at')
            ->get()
            ->each(function (Post $post) use ($userId): void {
                $post->user_liked = $userId
                    ? $post->likes->contains('id', $userId)
                    : false;

                $post->user_saved = $userId
                    ? $post->saves->contains('id', $userId)
                    : false;

                $post->user_rating = $userId
                    ? ($post->ratings->where('id', $userId)->first()?->pivot->rating ?? 0)
                    : 0;

                $post->rating_average = $post->ratings->count() > 0
                    ? round($post->ratings->sum(fn ($u) => $u->pivot->rating) / $post->ratings->count(), 1)
                    : null;

                $post->is_mine = $userId && $post->user_id === $userId;
            });
    }
}
