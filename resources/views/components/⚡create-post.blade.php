<?php

use App\Models\Tag;
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

        $this->dispatch('post-created');
        $this->dispatch('close-post-modal');
        $this->reset(['title', 'content', 'selectedTagIds']);
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
                # {{ $tag->name }}
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
    <textarea wire:model="content" placeholder="{{ __('ui.create_post.content_placeholder') }}"></textarea>
    @error('content')
        <p class="form-err">{{ $message }}</p>
    @enderror

    <button class="mbtn" wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove>{{ __('ui.create_post.publish') }}</span>
        <span wire:loading>{{ __('ui.create_post.saving') }}</span>
    </button>
</div>
