<?php

use App\Models\FaqQuestion;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public bool $showForm = false;

    public bool $submitted = false;

    #[Validate('nullable|string|max:200')]
    public string $topic = '';

    #[Validate('required|string|min:5|max:2000')]
    public string $question = '';

    public function submit(): void
    {
        if (! auth()->check() || ! auth()->user()->hasVerifiedEmail()) {
            $this->dispatch('open-login');

            return;
        }

        $key = 'faq-question:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, maxAttempts: 5)) {
            $this->addError('question', __('ui.faq_question.too_many'));

            return;
        }
        RateLimiter::hit($key, decaySeconds: 3600);

        $this->validate();

        FaqQuestion::create([
            'user_id'  => auth()->id(),
            'topic'    => $this->topic ?: null,
            'question' => $this->question,
        ]);

        $this->submitted = true;
    }

    public function resetForm(): void
    {
        $this->showForm = false;
        $this->submitted = false;
        $this->topic = '';
        $this->question = '';
        $this->resetValidation();
    }
};
?>

<div class="sb-card">
    <div class="sb-hdg">{{ __('ui.faq_question.heading') }}</div>

    @if($submitted)
        <div style="text-align:center;padding:12px 0">
            <div style="font-size:22px;margin-bottom:8px">🙏</div>
            <div style="font-family:var(--disp);font-size:14px;font-weight:700;color:var(--t);margin-bottom:6px">{{ __('ui.faq_question.thank_you') }}</div>
            <div style="font-size:12.5px;color:var(--muted);line-height:1.65;font-weight:300">{{ __('ui.faq_question.hope_help') }}</div>
            <button wire:click="resetForm"
                style="margin-top:12px;background:none;border:1.5px solid var(--bord2);padding:6px 16px;border-radius:20px;font-family:var(--body);font-size:12px;color:var(--muted);cursor:pointer">
                {{ __('ui.faq_question.new_question') }}
            </button>
        </div>
    @elseif($showForm)
        <div style="display:flex;flex-direction:column;gap:8px">
            <div>
                <label style="font-size:11.5px;font-weight:700;color:var(--ink2);display:block;margin-bottom:4px">{{ __('ui.faq_question.topic_label') }} <span style="font-weight:400;color:var(--light)">{{ __('ui.faq_question.optional') }}</span></label>
                <input wire:model="topic"
                    type="text"
                    placeholder="{{ __('ui.faq_question.topic_placeholder') }}"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--bord2);border-radius:10px;font-family:var(--body);font-size:13px;outline:none;color:var(--ink);background:var(--surf2)">
                @error('topic')
                    <p class="form-err" style="margin-top:3px">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label style="font-size:11.5px;font-weight:700;color:var(--ink2);display:block;margin-bottom:4px">{{ __('ui.faq_question.question_label') }}</label>
                <textarea wire:model="question"
                    placeholder="{{ __('ui.faq_question.question_placeholder') }}"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--bord2);border-radius:10px;font-family:var(--body);font-size:13px;outline:none;color:var(--ink);background:var(--surf2);resize:none;height:80px"></textarea>
                @error('question')
                    <p class="form-err" style="margin-top:3px">{{ $message }}</p>
                @enderror
            </div>
            <button wire:click="submit" wire:loading.attr="disabled"
                style="width:100%;background:var(--t);color:#fff;border:none;padding:9px;border-radius:40px;font-family:var(--body);font-size:13px;font-weight:700;cursor:pointer">
                <span wire:loading.remove>{{ __('ui.faq_question.submit') }}</span>
                <span wire:loading>{{ __('ui.faq_question.submitting') }}</span>
            </button>
        </div>
    @else
        <p style="font-size:12.5px;color:var(--muted);line-height:1.65;font-weight:300;margin-bottom:12px">
            {{ __('ui.faq_question.intro') }}
        </p>
        <button wire:click="$set('showForm', true)"
            style="width:100%;background:var(--t);color:#fff;border:none;padding:9px;border-radius:40px;font-family:var(--body);font-size:13px;font-weight:700;cursor:pointer">
            {{ __('ui.faq_question.submit') }}
        </button>
    @endif
</div>
