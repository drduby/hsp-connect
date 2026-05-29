<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostReport;
use App\Models\User;
use App\Notifications\CommentPosted;
use App\Notifications\PostLiked;
use App\Notifications\PostRated;
use App\Notifications\UserMentioned;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    public Post $post;

    public bool $showComments = false;

    public bool $reported = false;

    #[Validate('required|string|max:1000')]
    public string $newComment = '';

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->reported = auth()->check() && PostReport::where('post_id', $post->id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();
    }

    #[On('post-reported')]
    public function onPostReported(int $postId): void
    {
        if ((int) $postId === (int) $this->post->id) {
            $this->reported = true;
        }
    }

    public function toggleLike(): void
    {
        if (! auth()->check() || ! auth()->user()->hasVerifiedEmail()) {
            $this->dispatch('open-login');

            return;
        }

        if ($this->isMine) {
            return;
        }

        $userId = auth()->id();

        if ($this->post->likes->contains('id', $userId)) {
            $this->post->likes()->detach($userId);
        } else {
            $this->post->likes()->attach($userId);
            $this->post->user->notify(new PostLiked(auth()->user(), $this->post));
        }

        $this->post->unsetRelation('likes');
        $this->post->load('likes');
    }

    public function toggleSave(): void
    {
        if (! auth()->check() || ! auth()->user()->hasVerifiedEmail()) {
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

        $savedCount = Post::whereHas('saves', fn ($q) => $q->where('user_id', auth()->id()))->count();
        $this->dispatch('saved-count-updated', count: $savedCount);
    }

    public function rate(int $rating): void
    {
        if (! auth()->check() || ! auth()->user()->hasVerifiedEmail()) {
            $this->dispatch('open-login');

            return;
        }

        if ($this->isMine || $rating < 1 || $rating > 5) {
            return;
        }

        $userId = auth()->id();
        $current = (int) ($this->post->ratings->where('id', $userId)->first()?->pivot->rating ?? 0);

        $this->post->ratings()->detach($userId);

        if ($current !== $rating) {
            $this->post->ratings()->attach($userId, ['rating' => $rating]);
            $this->post->user->notify(new PostRated(auth()->user(), $this->post, $rating));
        }

        $this->post->unsetRelation('ratings');
        $this->post->load('ratings');
    }

    public function addComment(): void
    {
        if (! auth()->check() || ! auth()->user()->hasVerifiedEmail()) {
            $this->dispatch('open-login');

            return;
        }

        $key = 'add-comment:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, maxAttempts: 10)) {
            $this->addError('newComment', __('ui.post.too_many_comments'));

            return;
        }
        RateLimiter::hit($key, decaySeconds: 60);

        $dailyKey = 'add-comment-daily:'.auth()->id();
        if (RateLimiter::tooManyAttempts($dailyKey, maxAttempts: 100)) {
            $this->addError('newComment', 'Tägliches Kommentarlimit erreicht.');

            return;
        }
        RateLimiter::hit($dailyKey, decaySeconds: 86400);

        $this->validate();

        Comment::create([
            'post_id' => $this->post->id,
            'user_id' => auth()->id(),
            'content' => $this->newComment,
        ]);

        if (! $this->isMine) {
            $this->post->user->notify(new CommentPosted(auth()->user(), $this->post));
        }

        $this->notifyMentions($this->newComment);

        $this->newComment = '';
        $this->showComments = true;

        $this->post->unsetRelation('comments');
        $this->post->load('comments.user');
    }

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);

        if ((int) $comment->user_id !== (int) auth()->id()) {
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

    private function notifyMentions(string $text): void
    {
        preg_match_all('/@([a-zA-Z0-9_]+)/', $text, $matches);
        foreach (array_unique($matches[1]) as $nickname) {
            $user = User::where('nickname', $nickname)->first();
            if ($user && $user->id !== auth()->id()) {
                $user->notify(new UserMentioned(auth()->user(), $this->post));
            }
        }
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
    $typeLabel  = $typeRaw === 'experience' ? __('ui.post.experience') : __('ui.post.question');
    $typeInternal = $typeRaw === 'experience' ? 'Erfahrung' : 'Frage';
    $author     = $post->user->nickname;
    $ava        = strtoupper(mb_substr($author, 0, 1));
    $time       = $post->published_at->diffForHumans();
    $avg        = $this->averageRating ? number_format($this->averageRating, 1) : '—';
@endphp

<div class="post"
    id="post-{{ $post->id }}"
    data-type="{{ $typeLabel }}"
    data-tag="{{ $tagName }}"
    style="border-left:3px solid {{ $tc }}">

    {{-- Post header & body --}}
    <div class="post-inner">
        <div class="pmeta">
            <div class="pava">{{ $ava }}</div>
            <div>
                <div class="pname">{{ $author }}</div>
                <div class="ptime">{{ $time }}</div>
            </div>
            <span class="pbadge {{ $typeInternal === 'Erfahrung' ? 'pb-e' : 'pb-f' }}">
                {{ $typeLabel }}
            </span>
            @foreach($post->tags as $tag)
                <span class="ptag"
                    style="border-color:{{ $tag->color ?? 'var(--t)' }}30;color:{{ $tag->color ?? 'var(--t)' }}"
                    data-tag="{{ $tag->name }}"
                    onclick="toggleTag(this.dataset.tag)">
                    # {{ $tag->localizedName }}
                </span>
            @endforeach
        </div>

        <div class="ptitle">{{ $post->title }}</div>
        <div x-data="{ expanded: false, clamped: false }" x-init="$nextTick(() => { clamped = $refs.body.scrollHeight > $refs.body.clientHeight })">
            <div class="pbody" :class="expanded ? '' : 'cl'" x-ref="body">{!! preg_replace('/@([a-zA-Z0-9_]+)/', '<span class="mention">@$1</span>', e($post->content)) !!}</div>
            <button x-show="clamped && !expanded" class="readmore" x-on:click="expanded = true">{{ __('ui.post.read_more') }}</button>
            <button x-show="expanded" class="readmore" x-on:click="expanded = false">{{ __('ui.post.show_less') }}</button>
        </div>
    </div>

    {{-- Action bar --}}
    <div class="pacts">
        @if($this->isMine)
            <span class="pab" style="opacity:.3;cursor:default">🤍 {{ $post->likes->count() }}</span>
        @else
            <button class="pab {{ $this->isLiked ? 'lk' : '' }}"
                wire:click="toggleLike"
                wire:loading.attr="disabled">
                {{ $this->isLiked ? '❤️' : '🤍' }}
                {{ $post->likes->count() }}
            </button>
        @endif

        <button class="pab" wire:click="$toggle('showComments')">
            💬 {{ $post->comments->count() }}
        </button>

        @if($this->isMine)
            <button class="pab"
                x-on:click="openConfirm('{{ __('ui.post.delete_title') }}', '{{ __('ui.post.delete_confirm') }}', () => $wire.deletePost())"
                style="color:#c04040;font-size:12px">
                🗑 {{ __('ui.post.delete') }}
            </button>
        @else
            <button class="pab {{ $this->isSaved ? 'sv' : '' }}"
                wire:click="toggleSave"
                wire:loading.attr="disabled">
                {{ $this->isSaved ? '🔖' : '🏷️' }}
                {{ $this->isSaved ? __('ui.post.saved') : __('ui.post.save') }}
            </button>
        @endif

        @if(! $this->isMine)
            @if($this->reported)
                <button class="pab" disabled
                    style="margin-left:auto;font-size:11px;color:#c04040;cursor:default;opacity:1">
                    ⚠ {{ __('ui.post.reported') }}
                </button>
            @else
                <button class="pab"
                    wire:click="$dispatch('open-report', { postId: {{ $post->id }} })"
                    style="margin-left:auto;color:var(--light);font-size:11px">
                    ⚠ {{ __('ui.post.report') }}
                </button>
            @endif
        @endif

        <span class="sepv" @if($this->isMine) style="margin-left:auto" @endif></span>

        @php
            $avgRounded = (int) round($this->averageRating ?? 0);
            $displayStars = $this->isMine ? $avgRounded : ($this->userRating ?: $avgRounded);
        @endphp
        @if($this->isMine)
            <div class="stars" style="pointer-events:none">
                @for($s = 1; $s <= 5; $s++)
                    <span class="star {{ $displayStars >= $s ? 'on' : '' }}" style="opacity:.6">★</span>
                @endfor
            </div>
            <span class="avgr" style="opacity:.5">ø {{ $avg }}</span>
        @else
            <div class="stars">
                @for($s = 1; $s <= 5; $s++)
                    <span class="star {{ $displayStars >= $s ? 'on' : '' }}"
                        wire:key="star-{{ $post->id }}-{{ $s }}"
                        wire:click="rate({{ $s }})"
                        title="{{ $this->userRating === $s ? __('ui.post.remove_rating') : ($s . ' ' . ($s > 1 ? __('ui.post.stars') : __('ui.post.star'))) }}"
                        style="cursor:pointer">★</span>
                @endfor
            </div>
            <span class="avgr">ø {{ $avg }}</span>
        @endif
    </div>

    {{-- Comments toggle --}}
    @if($post->comments->count() > 0 && ! $showComments)
        <button class="read-cmts-btn" wire:click="$set('showComments', true)">
            {{ __('ui.post.show_comments', ['count' => $post->comments->count()]) }}
        </button>
    @endif

    {{-- Comments section --}}
    @if($showComments)
        <div class="cmts">
            <button class="togcmt" wire:click="$set('showComments', false)">{{ __('ui.post.hide_comments') }}</button>

            <div>
                @foreach($post->comments as $comment)
                    @php $commentIsMine = auth()->check() && (int) auth()->id() === (int) $comment->user_id; @endphp
                    <div class="cmt" wire:key="comment-{{ $comment->id }}">
                        <div class="cava">{{ strtoupper(mb_substr($comment->user->nickname, 0, 1)) }}</div>
                        <div class="cbub">
                            <div class="cname" style="display:flex;justify-content:space-between;align-items:center">
                                {{ $comment->user->nickname }}
                                @if($commentIsMine)
                                    <button
                                        x-on:click="openConfirm('{{ __('ui.post.delete_comment_title') }}', '{{ __('ui.post.delete_comment_confirm') }}', () => $wire.deleteComment({{ $comment->id }}))"
                                        style="background:none;border:none;color:var(--light);font-size:11px;cursor:pointer;padding:2px 6px;line-height:1;display:flex;align-items:center;gap:4px;border-radius:6px;transition:color .15s" onmouseover="this.style.color='#c04040'" onmouseout="this.style.color='var(--light)'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                        {{ __('ui.post.delete') }}
                                    </button>
                                @endif
                            </div>
                            <div class="ctext">{!! preg_replace('/@([a-zA-Z0-9_]+)/', '<span class="mention">@$1</span>', e($comment->content)) !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="cmt-write-label">{{ __('ui.post.write_comment') }}</div>
            <div class="cform">
                @auth
                    <div class="cava">{{ strtoupper(mb_substr(auth()->user()->nickname, 0, 1)) }}</div>
                    <div x-data="{
                        open: false, suggestions: [], active: 0, mentionStart: 0, timer: null,
                        detect(el) {
                            const m = el.value.slice(0, el.selectionStart).match(/@([a-zA-Z0-9_]{1,30})$/);
                            if (!m) { this.open = false; return; }
                            clearTimeout(this.timer);
                            this.timer = setTimeout(async () => {
                                const r = await fetch('/users/search?q=' + encodeURIComponent(m[1]));
                                this.suggestions = await r.json();
                                this.mentionStart = el.selectionStart - m[1].length - 1;
                                this.active = 0;
                                this.open = this.suggestions.length > 0;
                            }, 200);
                        },
                        pick(nick) {
                            const el = this.$refs.cinp;
                            const before = el.value.slice(0, this.mentionStart);
                            const after = el.value.slice(el.selectionStart);
                            const val = before + '@' + nick + ' ' + after;
                            $wire.set('newComment', val);
                            this.open = false;
                            this.$nextTick(() => { el.focus(); const p = before.length + nick.length + 2; el.setSelectionRange(p, p); });
                        }
                    }" @click.outside="open = false" style="position:relative;flex:1;display:flex">
                        <input class="cinp"
                            x-ref="cinp"
                            wire:model="newComment"
                            x-on:input="detect($el)"
                            x-on:keydown.arrow-down.prevent="if(open) active = (active+1) % suggestions.length"
                            x-on:keydown.arrow-up.prevent="if(open) active = (active-1+suggestions.length) % suggestions.length"
                            x-on:keydown.enter.prevent="open ? pick(suggestions[active]) : $wire.addComment()"
                            x-on:keydown.escape="open = false"
                            placeholder="{{ __('ui.post.comment_placeholder') }}">
                        <div x-show="open" x-cloak class="mention-drop">
                            <template x-for="(u, i) in suggestions" :key="u">
                                <button type="button"
                                        @mousedown.prevent="pick(u)"
                                        :class="{ 'mention-opt': true, 'sel': i === active }"
                                        @mouseenter="active = i">
                                    <span class="mention-ava" x-text="u[0].toUpperCase()"></span>
                                    <span>@<span x-text="u"></span></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <button class="csend" wire:click="addComment" wire:loading.attr="disabled">{{ __('ui.post.send') }}</button>
                @else
                    <div class="cava">?</div>
                    <input class="cinp"
                        placeholder="{{ __('ui.post.login_to_comment') }}"
                        readonly
                        style="cursor:pointer"
                        onclick="openLg()">
                    <button class="csend" onclick="openLg()">{{ __('ui.post.login') }}</button>
                @endauth
            </div>
            @error('newComment')
                <p class="form-err" style="margin-top:4px">{{ $message }}</p>
            @enderror
        </div>
    @endif


</div>
