<?php

use App\Models\Feedback;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public bool $open = false;

    public string $type = 'idea';

    #[Validate('nullable|string|max:200')]
    public string $topic = '';

    #[Validate('required|string|min:5|max:2000')]
    public string $description = '';

    #[Validate('nullable|email|max:255')]
    public string $email = '';

    public bool $submitted = false;

    #[On('open-feedback')]
    public function openModal(string $type = 'idea'): void
    {
        $this->type = in_array($type, ['idea', 'bug']) ? $type : 'idea';
        $this->topic = '';
        $this->description = '';
        $this->email = '';
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
        $this->validate();

        Feedback::create([
            'user_id'     => auth()->id(),
            'type'        => $this->type,
            'topic'       => $this->topic ?: null,
            'description' => $this->description,
            'email'       => auth()->check() ? null : ($this->email ?: null),
        ]);

        $this->submitted = true;
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
                    <div style="font-size:36px;margin-bottom:12px">{{ $type === 'idea' ? '💡' : '✅' }}</div>
                    <div class="mttl">{{ $type === 'idea' ? 'Danke für deine Idee!' : 'Problem gemeldet — danke!' }}</div>
                    <p style="font-size:13.5px;color:var(--muted);margin-top:8px;line-height:1.6">
                        Dein Feedback hilft uns, HSPConnect zu verbessern.
                    </p>
                    <button class="mbtn" style="margin-top:20px" wire:click="close">Schließen</button>
                </div>
            @else
                <div class="mttl">{{ $type === 'idea' ? '💡 Idee oder Wunsch' : '🐛 Technisches Problem' }}</div>
                <div class="msub">{{ $type === 'idea' ? 'Was würdest du dir wünschen?' : 'Was funktioniert nicht?' }}</div>

                <div style="display:flex;flex-direction:column;gap:12px;margin-top:16px">
                    <div>
                        <label>Thema <span style="font-size:10.5px;color:var(--light);font-weight:400">(optional)</span></label>
                        <input wire:model="topic"
                            type="text"
                            placeholder="z.B. Suche, Beiträge, Profil…"
                            style="width:100%;padding:10px 13px;border:1.5px solid rgba(10,110,122,.15);border-radius:10px;font-family:var(--body);font-size:14px;outline:none;color:var(--ink);background:var(--surf2);transition:border-color .18s">
                        @error('topic')
                            <p class="form-err" style="margin-top:4px">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label>Beschreibung</label>
                        <textarea wire:model="description"
                            placeholder="{{ $type === 'idea' ? 'Beschreibe deine Idee oder deinen Wunsch…' : 'Beschreibe das Problem so genau wie möglich…' }}"
                            style="width:100%;padding:10px 13px;border:1.5px solid rgba(10,110,122,.15);border-radius:10px;font-family:var(--body);font-size:13.5px;outline:none;color:var(--ink);background:var(--surf2);resize:vertical;min-height:100px;transition:border-color .18s"></textarea>
                        @error('description')
                            <p class="form-err" style="margin-top:4px">{{ $message }}</p>
                        @enderror
                    </div>

                    @guest
                        <div>
                            <label>E-Mail <span style="font-size:10.5px;color:var(--light);font-weight:400">(optional, für Rückfragen)</span></label>
                            <input wire:model="email"
                                type="email"
                                placeholder="deine@email.at"
                                style="width:100%;padding:10px 13px;border:1.5px solid rgba(10,110,122,.15);border-radius:10px;font-family:var(--body);font-size:14px;outline:none;color:var(--ink);background:var(--surf2);transition:border-color .18s">
                            @error('email')
                                <p class="form-err" style="margin-top:4px">{{ $message }}</p>
                            @enderror
                        </div>
                    @endguest

                    @auth
                        <div style="font-size:12px;color:var(--t);background:var(--t3);border-radius:8px;padding:8px 12px;font-weight:500">
                            Antwort geht an: {{ auth()->user()->nickname }}.
                        </div>
                    @endauth
                </div>

                <div style="display:flex;gap:10px;margin-top:18px">
                    <button class="mbtn" wire:click="submit" wire:loading.attr="disabled" style="flex:1">
                        <span wire:loading.remove>Feedback senden</span>
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
