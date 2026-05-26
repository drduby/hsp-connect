<?php

use App\Models\Post;
use App\Services\PostService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination {
        setPage as traitSetPage;
    }

    public int $latestPostId = 0;

    public int $newPostCount = 0;

    public string $search = '';

    public string $type = 'Alle';

    public array $tags = [];

    public string $view = 'all';

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
        $this->resetPage();
    }

    public function setPage($page, $pageName = 'page'): void
    {
        $this->traitSetPage($page, $pageName);
        $this->dispatch('page-changed');
    }

    public function setType(string $type): void
    {
        $this->type = $type;
        $this->resetPage();
        $this->dispatch('type-synced', type: $type);
    }

    #[On('post-created')]
    public function onPostCreated(): void
    {
        $this->loadNewPosts();
    }

    #[On('post-deleted')]
    public function onPostDeleted(): void
    {
        $this->resetPage();
    }

    #[On('livewire-filter-updated')]
    public function onFilterUpdated(string $search, string $type, array $tags, string $view = 'all'): void
    {
        $this->search = $search;
        $this->type = $type;
        $this->tags = $tags;
        $this->view = $view;
        $this->resetPage();
    }

    #[Computed]
    public function tabCounts(): array
    {
        return app(PostService::class)->filteredCounts($this->search, $this->tags, $this->view);
    }

    #[Computed]
    public function posts(): LengthAwarePaginator
    {
        return app(PostService::class)->publishedPostsPaginated(
            search: $this->search,
            type: $this->type,
            tags: $this->tags,
            view: $this->view,
        );
    }
};
?>

<div x-data x-on:page-changed.window="$nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }))">

    {{-- Feed tabs --}}
    <div class="ftabs">
        <button class="ft {{ $type === 'Alle' ? 'on' : '' }}" wire:click="setType('Alle')">
            Alle ({{ $this->tabCounts['all'] }})
        </button>
        <button class="ft {{ $type === 'Erfahrung' ? 'on' : '' }}" wire:click="setType('Erfahrung')">
            ✨ Erfahrungen ({{ $this->tabCounts['experiences'] }})
        </button>
        <button class="ft {{ $type === 'Frage' ? 'on' : '' }}" wire:click="setType('Frage')">
            ❓ Fragen ({{ $this->tabCounts['questions'] }})
        </button>
    </div>

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
        @forelse($this->posts as $post)
            <livewire:post-card :post="$post" wire:key="post-{{ $post->id }}"/>
        @empty
            <div class="empty">
                <div class="empty-i">🌊</div>
                <div class="empty-t">Keine Beiträge gefunden</div>
                <p>Andere Filter oder neuen Beitrag erstellen!</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        @php
            $lastPage    = $this->posts->lastPage();
            $currentPage = $this->posts->currentPage();
            $window      = 2; // pages on each side of current

            $pages = collect();
            for ($i = 1; $i <= $lastPage; $i++) {
                if (
                    $i === 1 ||
                    $i === $lastPage ||
                    ($i >= $currentPage - $window && $i <= $currentPage + $window)
                ) {
                    $pages->push($i);
                }
            }

            // Insert null as ellipsis marker where gaps exist
            $withEllipsis = collect();
            $prev = null;
            foreach ($pages as $page) {
                if ($prev !== null && $page - $prev > 1) {
                    $withEllipsis->push(null);
                }
                $withEllipsis->push($page);
                $prev = $page;
            }
        @endphp
        @if($lastPage > 1)
            <div class="pag">
                <button class="pgb" wire:click="previousPage" @disabled($currentPage <= 1)>&#x2039;</button>

                @foreach($withEllipsis as $page)
                    @if($page === null)
                        <span class="pgb" style="pointer-events:none;opacity:.4;cursor:default">…</span>
                    @else
                        <button class="pgb {{ $page === $currentPage ? 'on' : '' }}" wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                    @endif
                @endforeach

                <button class="pgb" wire:click="nextPage" @disabled($currentPage >= $lastPage)>&#x203A;</button>

                <span x-data="{ p: '' }"
                    style="display:flex;align-items:center;gap:5px;margin-left:6px;font-size:12px;color:var(--muted);font-family:var(--body)">
                    Gehe zu
                    <input type="number" min="1" max="{{ $lastPage }}"
                        x-model.number="p"
                        x-on:keydown.enter="if(p >= 1 && p <= {{ $lastPage }}) { $wire.gotoPage(p); p = ''; }"
                        style="width:44px;height:32px;border-radius:16px;border:1.5px solid rgba(10,110,122,.15);background:var(--surf);font-family:var(--body);font-size:12.5px;font-weight:700;color:var(--muted);text-align:center;outline:none;padding:0 4px">
                </span>
            </div>
        @endif

    </div>

</div>
