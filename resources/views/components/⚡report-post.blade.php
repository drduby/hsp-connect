<?php

use App\Models\Post;
use App\Models\PostReport;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\RateLimiter;
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
        if (! auth()->check() || ! auth()->user()->hasVerifiedEmail()) {
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
        if (! auth()->check() || ! auth()->user()->hasVerifiedEmail()) {
            $this->dispatch('open-login');

            return;
        }

        $key = 'report-post:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, maxAttempts: 10)) {
            $this->submitted = true;

            return;
        }
        RateLimiter::hit($key, decaySeconds: 3600);

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

        ActivityLogger::log('report.submitted', 'Post #'.$this->postId.' reported for: '.$this->reason);

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
                    <div class="mttl">{{ __('ui.report_post.received') }}</div>
                    <p style="font-size:13.5px;color:var(--muted);margin-top:8px;line-height:1.6">
                        {{ __('ui.report_post.thank_you') }}
                    </p>
                    <button class="mbtn" style="margin-top:20px" wire:click="close">{{ __('ui.report_post.close') }}</button>
                </div>
            @else
                <div class="mttl">{{ __('ui.report_post.title') }}</div>
                <div class="msub">{{ __('ui.report_post.sub') }}</div>

                <div style="display:flex;flex-direction:column;gap:8px;margin:16px 0">
                    @foreach(PostReport::translatedReasons() as $key => $label)
                        <label style="display:flex;align-items:center;gap:10px;padding:10px 13px;border-radius:10px;border:1.5px solid {{ $reason === $key ? 'var(--t)' : 'rgba(10,110,122,.13)' }};background:{{ $reason === $key ? 'var(--t3)' : 'var(--surf2)' }};cursor:pointer;transition:all .15s">
                            <input type="radio" wire:model="reason" value="{{ $key }}"
                                style="accent-color:var(--t);width:15px;height:15px;flex-shrink:0">
                            <span style="font-size:13.5px;color:var(--ink);font-weight:{{ $reason === $key ? '600' : '400' }}">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('reason')
                    <p class="form-err" style="margin-bottom:10px">{{ __('ui.report_post.reason_required') }}</p>
                @enderror

                <label>{{ __('ui.report_post.details_label') }}</label>
                <textarea wire:model="description"
                    placeholder="{{ __('ui.report_post.placeholder') }}"
                    style="width:100%;padding:10px 13px;border:1.5px solid rgba(10,110,122,.15);border-radius:10px;font-family:var(--body);font-size:13.5px;outline:none;color:var(--ink);background:var(--surf2);resize:vertical;min-height:80px;margin-bottom:4px;transition:border-color .18s"></textarea>
                @error('description')
                    <p class="form-err" style="margin-bottom:8px">{{ $message }}</p>
                @enderror

                <div style="display:flex;gap:10px;margin-top:16px">
                    <button class="mbtn" wire:click="submit" wire:loading.attr="disabled" style="flex:1">
                        <span wire:loading.remove>{{ __('ui.report_post.submit') }}</span>
                        <span wire:loading>{{ __('ui.report_post.submitting') }}</span>
                    </button>
                    <button wire:click="close"
                        style="padding:11px 18px;border-radius:10px;border:1.5px solid var(--bord2);background:transparent;color:var(--muted);font-size:14px;font-family:var(--body);cursor:pointer">
                        {{ __('ui.report_post.cancel') }}
                    </button>
                </div>
            @endif
        </div>
    </div>
@endif
</div>
