<?php

use App\Enums\PostType;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string')]
    public string $content = '';

    #[Validate('array|min:1')]
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
        $this->validate();

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
            ✨ Erfahrung
        </button>
        <button type="button" class="ptyp {{ $type === 'question' ? 'on' : '' }}"
                wire:click="$set('type', 'question')">
            ❓ Frage
        </button>
    </div>

    <label>Thema</label>
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
        <p class="form-err">Bitte mindestens ein Thema auswählen.</p>
    @enderror

    <label>Titel</label>
    <input type="text" wire:model="title" placeholder="Worum geht es?">
    @error('title')
        <p class="form-err">{{ $message }}</p>
    @enderror

    <label>Inhalt</label>
    <textarea wire:model="content" placeholder="Teile deine Gedanken..."></textarea>
    @error('content')
        <p class="form-err">{{ $message }}</p>
    @enderror

    <button class="mbtn" wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove>Ver&#xF6;ffentlichen</span>
        <span wire:loading>Wird gespeichert&#x2026;</span>
    </button>
</div>
