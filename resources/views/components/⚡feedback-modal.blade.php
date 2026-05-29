<?php

use App\Models\Feedback;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public bool $open = false;

    public string $type = 'idea';

    public string $topic = '';

    public string $description = '';

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

    public function rules(): array
    {
        return [
            'topic'       => $this->type === 'idea' ? 'required|string|max:200' : 'nullable|string|max:200',
            'description' => 'required|string|min:5|max:2000',
            'email'       => 'nullable|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'topic.required'       => __('ui.feedback.error_topic'),
            'topic.max'            => __('ui.feedback.error_topic_max'),
            'description.required' => __('ui.feedback.error_desc'),
            'description.min'      => __('ui.feedback.error_desc_min'),
            'description.max'      => __('ui.feedback.error_desc_max'),
            'email.email'          => __('ui.feedback.error_email'),
        ];
    }

    public function submit(): void
    {
        $key = 'feedback:' . (auth()->id() ?? request()->ip());
        if (RateLimiter::tooManyAttempts($key, maxAttempts: 5)) {
            $this->addError('description', __('ui.feedback.too_many'));

            return;
        }
        RateLimiter::hit($key, decaySeconds: 3600);

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
                    <div class="mttl">{{ $type === 'idea' ? __('ui.feedback.idea_success') : __('ui.feedback.bug_success') }}</div>
                    <p style="font-size:13.5px;color:var(--muted);margin-top:8px;line-height:1.6">
                        {{ __('ui.feedback.success_text') }}
                    </p>
                    <button class="mbtn" style="margin-top:20px" wire:click="close">{{ __('ui.feedback.close') }}</button>
                </div>
            @else
                <div class="mttl">{{ $type === 'idea' ? __('ui.feedback.idea_title') : __('ui.feedback.bug_title') }}</div>
                <div class="msub">{{ $type === 'idea' ? __('ui.feedback.idea_sub') : __('ui.feedback.bug_sub') }}</div>

                <div style="display:flex;flex-direction:column;gap:12px;margin-top:16px">
                    <div>
                        <label>{{ __('ui.feedback.topic') }} @if($type !== 'idea')<span style="font-size:10.5px;color:var(--light);font-weight:400">{{ __('ui.feedback.optional') }}</span>@endif</label>
                        <input wire:model="topic"
                            type="text"
                            placeholder="z.B. Suche, Beiträge, Profil…"
                            style="width:100%;padding:10px 13px;border:1.5px solid rgba(10,110,122,.15);border-radius:10px;font-family:var(--body);font-size:14px;outline:none;color:var(--ink);background:var(--surf2);transition:border-color .18s">
                        @error('topic')
                            <p class="form-err" style="margin-top:4px">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label>{{ __('ui.feedback.description') }}</label>
                        <textarea wire:model="description"
                            placeholder="{{ $type === 'idea' ? __('ui.feedback.idea_placeholder') : __('ui.feedback.bug_placeholder') }}"
                            style="width:100%;padding:10px 13px;border:1.5px solid rgba(10,110,122,.15);border-radius:10px;font-family:var(--body);font-size:13.5px;outline:none;color:var(--ink);background:var(--surf2);resize:vertical;min-height:100px;transition:border-color .18s"></textarea>
                        @error('description')
                            <p class="form-err" style="margin-top:4px">{{ $message }}</p>
                        @enderror
                    </div>

                    @guest
                        <div>
                            <label>{{ __('ui.feedback.email_label') }} <span style="font-size:10.5px;color:var(--light);font-weight:400">{{ __('ui.feedback.email_hint') }}</span></label>
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
                            {{ __('ui.feedback.reply_to', ['name' => auth()->user()->nickname]) }}
                        </div>
                    @endauth
                </div>

                <div style="display:flex;gap:10px;margin-top:18px">
                    <button class="mbtn" wire:click="submit" wire:loading.attr="disabled" style="flex:1">
                        <span wire:loading.remove>{{ __('ui.feedback.submit') }}</span>
                        <span wire:loading>{{ __('ui.feedback.submitting') }}</span>
                    </button>
                    <button wire:click="close"
                        style="padding:11px 18px;border-radius:10px;border:1.5px solid var(--bord2);background:transparent;color:var(--muted);font-size:14px;font-family:var(--body);cursor:pointer">
                        {{ __('ui.feedback.cancel') }}
                    </button>
                </div>
            @endif
        </div>
    </div>
@endif
</div>
