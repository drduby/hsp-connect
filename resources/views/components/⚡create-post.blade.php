<?php

use App\Models\Tag;
use App\Models\User;
use App\Notifications\UserMentioned;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public string $title = '';

    public string $content = '';

    public array $selectedTagIds = [];

    public string $type = 'experience';

    #[On('open-create-post')]
    public function open(string $type = 'experience'): void
    {
        $this->type = $type;
        $this->reset(['title', 'content', 'selectedTagIds']);
        $this->resetValidation();
    }

    public function toggleTag(int $id): void
    {
        if (in_array($id, $this->selectedTagIds, strict: true)) {
            $this->selectedTagIds = array_values(
                array_filter($this->selectedTagIds, fn(int $t) => $t !== $id)
            );
        } else {
            $this->selectedTagIds[] = $id;
        }
    }

    public function save(): void
    {
        if (! auth()->check() || ! auth()->user()->hasVerifiedEmail()) {
            $this->dispatch('open-login');

            return;
        }

        $key = 'create-post:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, maxAttempts: 3)) {
            $this->addError('title', __('ui.create_post.too_many'));

            return;
        }
        RateLimiter::hit($key, decaySeconds: 60);

        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'selectedTagIds' => ['array', 'min:1'],
            'selectedTagIds.*' => [Rule::exists('tags', 'id')->where('is_active', true)],
        ]);

        $post = auth()->user()->posts()->create([
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $post->tags()->sync($this->selectedTagIds);
        $this->notifyMentions($this->content, $post);

        $this->dispatch('post-created');
        $this->dispatch('close-post-modal');
        $this->reset(['title', 'content', 'selectedTagIds']);
    }

    private function notifyMentions(string $text, \App\Models\Post $post): void
    {
        preg_match_all('/@([a-zA-Z0-9_]+)/', $text, $matches);
        foreach (array_unique($matches[1]) as $nickname) {
            $user = User::where('nickname', $nickname)->first();
            if ($user && $user->id !== auth()->id()) {
                $user->notify(new UserMentioned(auth()->user(), $post));
            }
        }
    }

    #[Computed]
    public function tags(): Collection
    {
        return Tag::where('is_active', true)->orderBy('id')->get();
    }
};
?>

<div>
    <div class="ptypes">
        <button type="button" class="ptyp {{ $type === 'experience' ? 'on' : '' }}"
                wire:click="$set('type', 'experience')">
            {{ __('ui.create_post.type_experience') }}
        </button>
        <button type="button" class="ptyp {{ $type === 'question' ? 'on' : '' }}"
                wire:click="$set('type', 'question')">
            {{ __('ui.create_post.type_question') }}
        </button>
    </div>

    <label>{{ __('ui.create_post.topic') }}</label>
    <div class="tag-picker">
        @foreach($this->tags as $tag)
            <button type="button"
                    wire:key="tag-{{ $tag->id }}"
                    class="tag-pill {{ in_array($tag->id, $selectedTagIds) ? 'on' : '' }}"
                    wire:click="toggleTag({{ $tag->id }})">
                <span class="tag-pill-dot" style="background:{{ $tag->color ?? 'var(--t)' }}"></span>
                # {{ $tag->localizedName }}
            </button>
        @endforeach
    </div>
    @error('selectedTagIds')
        <p class="form-err">{{ __('ui.create_post.topic_required') }}</p>
    @enderror

    <label>{{ __('ui.create_post.title') }}</label>
    <input type="text" wire:model="title" placeholder="{{ __('ui.create_post.title_placeholder') }}">
    @error('title')
        <p class="form-err">{{ $message }}</p>
    @enderror

    <label>{{ __('ui.create_post.content') }}</label>
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
            const el = this.$refs.ta;
            const before = el.value.slice(0, this.mentionStart);
            const after = el.value.slice(el.selectionStart);
            const val = before + '@' + nick + ' ' + after;
            $wire.set('content', val);
            this.open = false;
            this.$nextTick(() => { el.focus(); const p = before.length + nick.length + 2; el.setSelectionRange(p, p); });
        }
    }" @click.outside="open = false" style="position:relative">
        <textarea x-ref="ta"
            wire:model="content"
            x-on:input="detect($el)"
            x-on:keydown.arrow-down.prevent="if(open) active = (active+1) % suggestions.length"
            x-on:keydown.arrow-up.prevent="if(open) active = (active-1+suggestions.length) % suggestions.length"
            x-on:keydown.enter="if(open) { $event.preventDefault(); pick(suggestions[active]); }"
            x-on:keydown.escape="open = false"
            placeholder="{{ __('ui.create_post.content_placeholder') }}"></textarea>
        <div x-show="open" x-cloak class="mention-drop" style="top:calc(100% - 8px);bottom:auto">
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
    @error('content')
        <p class="form-err">{{ $message }}</p>
    @enderror

    <button class="mbtn" wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove>{{ __('ui.create_post.publish') }}</span>
        <span wire:loading>{{ __('ui.create_post.saving') }}</span>
    </button>
</div>
