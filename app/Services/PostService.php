<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    /**
     * @return array{all: int, experiences: int, questions: int}
     */
    public function filteredCounts(string $search = '', array $tags = [], string $view = 'all'): array
    {
        $userId = auth()->id();

        $base = Post::where('is_published', true);

        if ($view === 'saved' && $userId) {
            $base->whereHas('saves', fn ($q) => $q->where('user_id', $userId));
        } elseif ($view === 'mine' && $userId) {
            $base->where('user_id', $userId);
        }

        if ($search !== '') {
            $base->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if (! empty($tags)) {
            $base->whereHas('tags', fn ($q) => $q->whereIn('name', $tags));
        }

        return [
            'all' => (clone $base)->count(),
            'experiences' => (clone $base)->where('type', 'experience')->count(),
            'questions' => (clone $base)->where('type', 'question')->count(),
        ];
    }

    public function publishedPostsPaginated(string $search = '', string $type = 'Alle', array $tags = [], string $view = 'all'): LengthAwarePaginator
    {
        $userId = auth()->id();

        $query = Post::where('is_published', true)
            ->with(['tags', 'user', 'comments.user', 'likes', 'saves', 'ratings'])
            ->withCount(['likes', 'comments'])
            ->latest('published_at');

        if ($view === 'saved' && $userId) {
            $query->whereHas('saves', fn ($q) => $q->where('user_id', $userId));
        } elseif ($view === 'mine' && $userId) {
            $query->where('user_id', $userId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($type !== 'Alle') {
            $query->where('type', $type === 'Erfahrung' ? 'experience' : 'question');
        }

        if (! empty($tags)) {
            $query->whereHas('tags', fn ($q) => $q->whereIn('name', $tags));
        }

        $paginator = $query->paginate(10);

        $paginator->getCollection()->each(function (Post $post) use ($userId): void {
            $post->user_liked = $userId ? $post->likes->contains('id', $userId) : false;
            $post->user_saved = $userId ? $post->saves->contains('id', $userId) : false;
            $post->user_rating = $userId
                ? ($post->ratings->where('id', $userId)->first()?->pivot->rating ?? 0)
                : 0;
            $post->rating_average = $post->ratings->count() > 0
                ? round($post->ratings->sum(fn ($u) => $u->pivot->rating) / $post->ratings->count(), 1)
                : null;
            $post->is_mine = $userId && $post->user_id === $userId;
        });

        return $paginator;
    }
}
