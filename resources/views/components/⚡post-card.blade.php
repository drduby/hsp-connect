<?php

use App\Models\Comment;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public Post $post;

    public bool $showComments = false;

    #[Validate('required|string|max:1000')]
    public string $newComment = '';

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    public function toggleLike(): void
    {
        if (! auth()->check()) {
            $this->dispatch('open-login');

            return;
        }

        $userId = auth()->id();

        if ($this->post->likes->contains('id', $userId)) {
            $this->post->likes()->detach($userId);
        } else {
            $this->post->likes()->attach($userId);
        }

        $this->post->unsetRelation('likes');
        $this->post->load('likes');
    }

    public function toggleSave(): void
    {
        if (! auth()->check()) {
            $this->dispatch('open-login');

            return;
        }

        $userId = auth()->id();

        if ($this->post->saves->contains('id', $userId)) {
            $this->post->saves()->detach($userId);
        } else {
            $this->post->saves()->attach($userId);
        }

        $this->post->unsetRelation('saves');
        $this->post->load('saves');
    }

    public function rate(int $rating): void
    {
        if (! auth()->check()) {
            $this->dispatch('open-login');

            return;
        }

        if ($rating < 1 || $rating > 5) {
            return;
        }

        $this->post->ratings()->syncWithoutDetaching([
            auth()->id() => ['rating' => $rating],
        ]);

        $this->post->unsetRelation('ratings');
        $this->post->load('ratings');
    }

    public function addComment(): void
    {
        if (! auth()->check()) {
            $this->dispatch('open-login');

            return;
        }

        $this->validate();

        Comment::create([
            'post_id' => $this->post->id,
            'user_id' => auth()->id(),
            'content' => $this->newComment,
        ]);

        $this->newComment = '';
        $this->showComments = true;

        $this->post->unsetRelation('comments');
        $this->post->load('comments.user');
    }

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);

        if ($comment->user_id !== auth()->id()) {
            return;
        }

        $comment->delete();

        $this->post->unsetRelation('comments');
        $this->post->load('comments.user');
    }

    public function deletePost(): void
    {
        if ($this->post->user_id !== auth()->id()) {
            return;
        }

        $this->post->delete();
        $this->dispatch('post-deleted', postId: $this->post->id);
    }

    #[Computed]
    public function isLiked(): bool
    {
        return auth()->check() && $this->post->likes->contains('id', auth()->id());
    }

    #[Computed]
    public function isSaved(): bool
    {
        return auth()->check() && $this->post->saves->contains('id', auth()->id());
    }

    #[Computed]
    public function userRating(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        return $this->post->ratings->where('id', auth()->id())->first()?->pivot->rating ?? 0;
    }

    #[Computed]
    public function averageRating(): ?float
    {
        $ratings = $this->post->ratings;

        return $ratings->count() > 0
            ? round($ratings->sum(fn ($u) => $u->pivot->rating) / $ratings->count(), 1)
            : null;
    }

    #[Computed]
    public function isMine(): bool
    {
        return auth()->check() && $this->post->user_id === auth()->id();
    }
};
?>

@php
    $firstTag   = $post->tags->first();
    $tagName    = $firstTag?->name ?? '';
    $tc         = $firstTag?->color ?? 'var(--t)';
    $typeRaw    = $post->type->value;
    $typeLabel  = $typeRaw === 'experience' ? 'Erfahrung' : 'Frage';
    $author     = $post->user->nickname;
    $ava        = strtoupper(mb_substr($author, 0, 1));
    $time       = $post->published_at->diffForHumans();
    $avg        = $this->averageRating ? number_format($this->averageRating, 1) : '—';
@endphp

<div class="post"
    id="post-{{ $post->id }}"
    data-type="{{ $typeLabel }}"
    data-tag="{{ $tagName }}"
    data-search="{{ strtolower($post->title . ' ' . $post->content . ' ' . $tagName) }}"
    style="border-left:3px solid {{ $tc }}">

    {{-- Post header & body --}}
    <div class="post-inner">
        <div class="pmeta">
            <div class="pava">{{ $ava }}</div>
            <div>
                <div class="pname">{{ $author }}</div>
                <div class="ptime">{{ $time }}</div>
            </div>
            <span class="pbadge {{ $typeLabel === 'Erfahrung' ? 'pb-e' : 'pb-f' }}">
                {{ $typeLabel }}
            </span>
            @foreach($post->tags as $tag)
                <span class="ptag"
                    style="border-color:{{ $tag->color ?? 'var(--t)' }}30;color:{{ $tag->color ?? 'var(--t)' }}"
                    onclick="toggleTag('{{ $tag->name }}')">
                    # {{ $tag->name }}
                </span>
            @endforeach
        </div>

        <div class="ptitle">{{ $post->title }}</div>
        <div class="pbody cl" id="pbody-{{ $post->id }}">{{ $post->content }}</div>
        <button class="readmore" id="readmore-{{ $post->id }}" onclick="expand({{ $post->id }})">
            Weiterlesen →
        </button>
    </div>

    {{-- Action bar --}}
    <div class="pacts">
        <button class="pab {{ $this->isLiked ? 'lk' : '' }}"
            wire:click="toggleLike"
            wire:loading.attr="disabled">
            {{ $this->isLiked ? '❤️' : '🤍' }}
            {{ $post->likes->count() }}
        </button>

        <button class="pab" wire:click="$toggle('showComments')">
            💬 {{ $post->comments->count() }}
        </button>

        @if($this->isMine)
            <button class="pab"
                x-on:click="openConfirm('Beitrag löschen', 'Möchtest du diesen Beitrag wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.', () => $wire.deletePost())"
                style="color:#c04040;font-size:12px">
                🗑 Löschen
            </button>
        @else
            <button class="pab {{ $this->isSaved ? 'sv' : '' }}"
                wire:click="toggleSave"
                wire:loading.attr="disabled">
                {{ $this->isSaved ? '🔖' : '🏷️' }}
                {{ $this->isSaved ? 'Gespeichert' : 'Speichern' }}
            </button>
        @endif

        <button class="pab"
            wire:click="$dispatch('open-report', { postId: {{ $post->id }} })"
            style="margin-left:auto;color:var(--light);font-size:11px">
            ⚠ Melden
        </button>

        <span class="sepv"></span>

        <div class="stars">
            @for($s = 1; $s <= 5; $s++)
                <span class="star {{ $this->userRating >= $s ? 'on' : '' }}"
                    wire:key="star-{{ $post->id }}-{{ $s }}"
                    wire:click="rate({{ $s }})"
                    style="cursor:pointer">★</span>
            @endfor
        </div>
        <span class="avgr">ø {{ $avg }}</span>
    </div>

    {{-- Comments toggle --}}
    @if($post->comments->count() > 0 && ! $showComments)
        <button class="read-cmts-btn" wire:click="$set('showComments', true)">
            ▼ Kommentare anzeigen ({{ $post->comments->count() }})
        </button>
    @endif

    {{-- Comments section --}}
    @if($showComments)
        <div class="cmts">
            <button class="togcmt" wire:click="$set('showComments', false)">▲ Kommentare ausblenden</button>

            <div>
                @foreach($post->comments as $comment)
                    <div class="cmt" wire:key="comment-{{ $comment->id }}">
                        <div class="cava">{{ strtoupper(mb_substr($comment->user->nickname, 0, 1)) }}</div>
                        <div class="cbub">
                            <div class="cname" style="display:flex;justify-content:space-between;align-items:center">
                                {{ $comment->user->nickname }}
                                @if(auth()->id() === $comment->user_id)
                                    <button wire:click="deleteComment({{ $comment->id }})"
                                        style="background:none;border:none;color:var(--light);font-size:11px;cursor:pointer;padding:0;line-height:1">
                                        ✕
                                    </button>
                                @endif
                            </div>
                            <div class="ctext">{{ $comment->content }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="cmt-write-label">Kommentar schreiben</div>
            <div class="cform">
                @auth
                    <div class="cava">{{ strtoupper(mb_substr(auth()->user()->nickname, 0, 1)) }}</div>
                    <input class="cinp"
                        wire:model="newComment"
                        wire:keydown.enter="addComment"
                        placeholder="Kommentar eingeben…">
                    <button class="csend" wire:click="addComment" wire:loading.attr="disabled">Senden</button>
                @else
                    <div class="cava">?</div>
                    <input class="cinp"
                        placeholder="Anmelden zum Kommentieren"
                        readonly
                        style="cursor:pointer"
                        onclick="openLg()">
                    <button class="csend" onclick="openLg()">Anmelden</button>
                @endauth
            </div>
            @error('newComment')
                <p class="form-err" style="margin-top:4px">{{ $message }}</p>
            @enderror
        </div>
    @endif


</div>
