<?php

use App\Models\Post;
use App\Models\PostReport;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public bool $open = false;

    public ?int $postId = null;

    #[Validate('required|string|in:spam,harassment,misinformation,medical,offtopic,other')]
    public string $reason = '';

    #[Validate('required|string|max:500')]
    public string $description = '';

    public bool $submitted = false;

    #[On('open-report')]
    public function openModal(int $postId): void
    {
        if (! auth()->check()) {
            $this->dispatch('open-login');

            return;
        }

        $post = Post::find($postId);

        if (! $post || $post->user_id === auth()->id()) {
            return;
        }

        $this->postId = $postId;
        $this->reason = '';
        $this->description = '';
        $this->submitted = false;
        $this->resetValidation();
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function submit(): void
    {
        if (! auth()->check()) {
            $this->dispatch('open-login');

            return;
        }

        $this->validate();

        $alreadyReported = PostReport::where('post_id', $this->postId)
            ->where('user_id', auth()->id())
            ->exists();

        if ($alreadyReported) {
            $this->submitted = true;

            return;
        }

        PostReport::create([
            'post_id'     => $this->postId,
            'user_id'     => auth()->id(),
            'reason'      => $this->reason,
            'description' => $this->description,
            'status'      => 'pending',
        ]);

        $this->submitted = true;
        $this->dispatch('post-reported', postId: $this->postId);
    }
};
?>

<div>
@if($open)
    <div class="mbg on" wire:click.self="close">
        <div class="modal">
            <button class="mc" wire:click="close">&#x2715;</button>

            @if($submitted)
                <div style="text-align:center;padding:16px 0 8px">
                    <div style="font-size:36px;margin-bottom:12px">✅</div>
                    <div class="mttl">Meldung eingegangen</div>
                    <p style="font-size:13.5px;color:var(--muted);margin-top:8px;line-height:1.6">
                        Danke für deine Meldung. Wir prüfen den Beitrag so bald wie möglich.
                    </p>
                    <button class="mbtn" style="margin-top:20px" wire:click="close">Schließen</button>
                </div>
            @else
                <div class="mttl">Beitrag melden</div>
                <div class="msub">Was stimmt mit diesem Beitrag nicht?</div>

                <div style="display:flex;flex-direction:column;gap:8px;margin:16px 0">
                    @foreach(\App\Models\PostReport::REASONS as $key => $label)
                        <label style="display:flex;align-items:center;gap:10px;padding:10px 13px;border-radius:10px;border:1.5px solid {{ $reason === $key ? 'var(--t)' : 'rgba(10,110,122,.13)' }};background:{{ $reason === $key ? 'var(--t3)' : 'var(--surf2)' }};cursor:pointer;transition:all .15s">
                            <input type="radio" wire:model="reason" value="{{ $key }}"
                                style="accent-color:var(--t);width:15px;height:15px;flex-shrink:0">
                            <span style="font-size:13.5px;color:var(--ink);font-weight:{{ $reason === $key ? '600' : '400' }}">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('reason')
                    <p class="form-err" style="margin-bottom:10px">Bitte einen Grund auswählen.</p>
                @enderror

                <label>Weitere Details</label>
                <textarea wire:model="description"
                    placeholder="Beschreibe kurz, was das Problem ist…"
                    style="width:100%;padding:10px 13px;border:1.5px solid rgba(10,110,122,.15);border-radius:10px;font-family:var(--body);font-size:13.5px;outline:none;color:var(--ink);background:var(--surf2);resize:vertical;min-height:80px;margin-bottom:4px;transition:border-color .18s"></textarea>
                @error('description')
                    <p class="form-err" style="margin-bottom:8px">{{ $message }}</p>
                @enderror

                <div style="display:flex;gap:10px;margin-top:16px">
                    <button class="mbtn" wire:click="submit" wire:loading.attr="disabled" style="flex:1">
                        <span wire:loading.remove>Meldung absenden</span>
                        <span wire:loading>Wird gesendet…</span>
                    </button>
                    <button wire:click="close"
                        style="padding:11px 18px;border-radius:10px;border:1.5px solid var(--bord2);background:transparent;color:var(--muted);font-size:14px;font-family:var(--body);cursor:pointer">
                        Abbrechen
                    </button>
                </div>
            @endif
        </div>
    </div>
@endif
</div>
