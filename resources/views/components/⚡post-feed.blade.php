<?php

use App\Models\Post;
use App\Services\PostService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public int $latestPostId = 0;

    public int $newPostCount = 0;

    public function mount(): void
    {
        $this->latestPostId = Post::where('is_published', true)->max('id') ?? 0;
    }

    public function checkForNew(): void
    {
        $this->newPostCount = Post::where('is_published', true)
            ->where('id', '>', $this->latestPostId)
            ->count();
    }

    public function loadNewPosts(): void
    {
        $this->newPostCount = 0;
        $this->latestPostId = Post::where('is_published', true)->max('id') ?? 0;

        $this->dispatch('posts-refreshed', posts: $this->postsForJs());
    }

    #[On('post-created')]
    public function onPostCreated(): void
    {
        $this->loadNewPosts();
    }

    #[Computed]
    public function posts(): Collection
    {
        return app(PostService::class)->publishedPosts();
    }

    private function postsForJs(): array
    {
        return $this->posts->map(fn ($p) => [
            'id'      => $p->id,
            'type'    => $p->type->value === 'experience' ? 'Erfahrung' : 'Frage',
            'tags'    => $p->tags->pluck('name')->toArray(),
            'tag'     => $p->tags->first()?->name ?? '',
            'title'   => $p->title,
            'content' => $p->content,
            'mine'    => $p->is_mine,
            'saved'   => $p->user_saved,
            'liked'   => $p->user_liked,
        ])->values()->all();
    }
};
?>

<div wire:poll.30s="checkForNew">

    {{-- New posts banner --}}
    @if($newPostCount > 0)
        <button
            wire:click="loadNewPosts"
            wire:loading.attr="disabled"
            style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:11px 18px;margin-bottom:12px;background:var(--t);color:#fff;border:none;border-radius:12px;font-family:var(--body);font-size:13.5px;font-weight:600;cursor:pointer;box-shadow:var(--sh2);transition:background .15s">
            <span wire:loading.remove>
                ✨ {{ $newPostCount }} neue {{ $newPostCount === 1 ? 'Beitrag' : 'Beiträge' }} — jetzt laden
            </span>
            <span wire:loading>Wird geladen…</span>
        </button>
    @endif

    {{-- Post list --}}
    @foreach($this->posts as $post)
        <livewire:post-card :post="$post" wire:key="post-{{ $post->id }}"/>
    @endforeach

</div>
