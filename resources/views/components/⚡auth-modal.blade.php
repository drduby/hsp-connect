<?php

use App\Actions\Fortify\CreateNewUser;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public bool $open = false;

    public string $tab = 'login';

    // Login
    public string $email = '';

    public string $password = '';

    // Register
    public string $firstName = '';

    public string $lastName = '';

    public string $nickname = '';

    public string $regEmail = '';

    public string $regPassword = '';

    public string $regPasswordConfirmation = '';

    // Reset
    public string $resetEmail = '';

    public ?string $resetMessage = null;

    public string $resetMessageType = 'success';

    #[On('open-auth-modal')]
    public function openModal(string $tab = 'login'): void
    {
        $this->open = true;
        $this->tab = $tab;
        $this->resetErrorBag();
        $this->resetMessage = null;
    }

    public function closeModal(): void
    {
        $this->open = false;
        $this->reset([
            'tab', 'email', 'password',
            'firstName', 'lastName', 'nickname', 'regEmail', 'regPassword', 'regPasswordConfirmation',
            'resetEmail', 'resetMessage',
        ]);
        $this->resetErrorBag();
    }

    public function switchTab(string $tab): void
    {
        if ($tab === 'reset') {
            $this->resetEmail = $this->email;
            $this->email = '';
            $this->password = '';
        }
        if ($tab === 'login') {
            $this->email = $this->resetEmail ?: $this->regEmail;
            $this->resetEmail = '';
            $this->regEmail = '';
        }
        $this->tab = $tab;
        $this->resetErrorBag();
        $this->resetMessage = null;
    }

    public function login(): void
    {
        $this->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => __('ui.auth.err_email_required'),
            'email.email'       => __('ui.auth.err_email_invalid'),
            'password.required' => __('ui.auth.err_password_required'),
        ]);

        $key = 'login:'.strtolower($this->email).'|'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', __('ui.auth.err_too_many_login'));

            return;
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            RateLimiter::hit($key, 60);
            $this->addError('email', __('ui.auth.err_credentials'));

            return;
        }

        RateLimiter::clear($key);
        $user = Auth::user();

        ActivityLogger::log('login.success', 'User logged in: '.$user->nickname, $user->id);

        if (! $user->hasVerifiedEmail()) {
            $resendKey = 'resend-verification:'.$user->id;
            if (! RateLimiter::tooManyAttempts($resendKey, 2)) {
                $user->sendEmailVerificationNotification();
                RateLimiter::hit($resendKey, 300);
            }
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            $this->tab = 'verify';

            return;
        }

        request()->session()->regenerate();
        $this->redirect('/', navigate: false);
    }

    public function register(): void
    {
        $this->validate([
            'firstName'               => ['required', 'string', 'max:255'],
            'lastName'                => ['required', 'string', 'max:255'],
            'nickname'                => ['required', 'string', 'max:30', 'regex:/^[a-zA-Z0-9_]+$/', Rule::unique('users', 'nickname')],
            'regEmail'                => ['required', 'string', 'email:rfc', 'max:255', Rule::unique('users', 'email')],
            'regPassword'             => ['required', PasswordRule::min(8)->letters()->numbers()],
            'regPasswordConfirmation' => ['required', 'same:regPassword'],
        ], [
            'firstName.required'               => __('ui.auth.err_first_name'),
            'lastName.required'                => __('ui.auth.err_last_name'),
            'nickname.required'                => __('ui.auth.err_nickname_required'),
            'nickname.regex'                   => __('ui.auth.err_nickname_format'),
            'nickname.unique'                  => __('ui.auth.err_nickname_taken'),
            'regEmail.required'                => __('ui.auth.err_email_required'),
            'regEmail.email'                   => __('ui.auth.err_email_invalid'),
            'regEmail.unique'                  => __('ui.auth.err_email_taken'),
            'regPassword.required'             => __('ui.auth.err_password_required'),
            'regPassword.min'                  => __('ui.auth.err_password_min'),
            'regPassword.letters'              => __('ui.auth.err_password_letters'),
            'regPassword.numbers'              => __('ui.auth.err_password_numbers'),
            'regPasswordConfirmation.required' => __('ui.auth.err_confirm_required'),
            'regPasswordConfirmation.same'     => __('ui.auth.err_confirm_mismatch'),
        ]);

        $key = 'register:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->addError('regEmail', __('ui.auth.err_too_many_register'));

            return;
        }

        $user = app(CreateNewUser::class)->create([
            'first_name'            => $this->firstName,
            'last_name'             => $this->lastName,
            'nickname'              => $this->nickname,
            'email'                 => $this->regEmail,
            'password'              => $this->regPassword,
            'password_confirmation' => $this->regPasswordConfirmation,
        ]);

        RateLimiter::hit($key, 60);

        try {
            event(new Registered($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Registration email failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }

        $this->tab = 'verify';
    }

    public function sendResetLink(): void
    {
        $this->validate([
            'resetEmail' => ['required', 'email'],
        ], [
            'resetEmail.required' => __('ui.auth.err_email_required'),
            'resetEmail.email'    => __('ui.auth.err_email_invalid'),
        ]);

        $status = Password::sendResetLink(['email' => $this->resetEmail]);

        $this->resetMessage = $status === Password::RESET_LINK_SENT
            ? __('ui.auth.reset_sent')
            : __('ui.auth.reset_error');
        $this->resetMessageType = $status === Password::RESET_LINK_SENT ? 'success' : 'error';
    }
};
?>

<div class="mbg {{ $open ? 'on' : '' }}" wire:click.self="closeModal">
    @if($open)
    <div class="modal">
        <button class="mc" wire:click="closeModal">&#x2715;</button>

        {{-- Title --}}
        <div class="mttl">
            @if($tab === 'login') {{ __('ui.auth.welcome_back') }}
            @elseif($tab === 'register') {{ __('ui.auth.create_account') }}
            @elseif($tab === 'reset') {{ __('ui.auth.reset_title') }}
            @else {{ __('ui.auth.almost_done') }}
            @endif
        </div>
        <div class="msub">
            @if($tab === 'login') {{ __('ui.auth.sub_login') }}
            @elseif($tab === 'register') {{ __('ui.auth.sub_register') }}
            @elseif($tab === 'reset') {{ __('ui.auth.sub_reset') }}
            @endif
        </div>

        {{-- Login --}}
        @if($tab === 'login')
        <div>
            <label>{{ __('ui.auth.email') }}</label>
            <input type="email" wire:model="email" placeholder="deine@email.at" wire:keydown.enter="login">
            @error('email') <div class="field-error" style="margin:-8px 0 10px">{{ $message }}</div> @enderror

            <label>{{ __('ui.auth.password') }}</label>
            <div x-data="{ show: false }" style="display:flex;align-items:center;border:1.5px solid rgba(10,110,122,.15);border-radius:10px;background:var(--surf2);margin-bottom:12px;transition:border-color .18s;padding-right:12px" @focusin="$el.style.borderColor='var(--t)'" @focusout="$el.style.borderColor='rgba(10,110,122,.15)'">
                <input wire:model="password" :type="show ? 'text' : 'password'"
                    placeholder="••••••••" wire:keydown.enter="login"
                    style="flex:1;padding:10px 8px 10px 13px;border:none;background:transparent;outline:none;font-family:var(--body);font-size:14px;color:var(--ink);margin-bottom:0;min-width:0">
                <button type="button" x-on:click="show = !show" tabindex="-1"
                    style="background:none;border:none;cursor:pointer;display:flex;align-items:center;flex-shrink:0;padding:0"
                    :style="show ? 'color:var(--t)' : 'color:var(--light)'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            @error('password') <div class="field-error" style="margin:-8px 0 10px">{{ $message }}</div> @enderror

            <div class="mlink mlink-tight">{{ __('ui.auth.forgot_password') }} <a wire:click="switchTab('reset')" style="cursor:pointer">{{ __('ui.auth.reset_title') }}</a></div>

            <button class="mbtn" wire:click="login" wire:loading.attr="disabled" wire:target="login">
                <span wire:loading.remove wire:target="login">{{ __('ui.auth.login_btn') }}</span>
                <span wire:loading wire:target="login">{{ __('ui.auth.logging_in') }}</span>
            </button>
            <div class="mlink">{{ __('ui.auth.no_account') }} <a wire:click="switchTab('register')" style="cursor:pointer">{{ __('ui.auth.register_link') }}</a></div>
        </div>
        @endif

        {{-- Register --}}
        @if($tab === 'register')
        <div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 12px">
                <div>
                    <label>{{ __('ui.auth.first_name') }}</label>
                    <input type="text" wire:model="firstName" placeholder="z.B. Maria">
                    @error('firstName') <div class="field-error" style="margin:-8px 0 10px">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label>{{ __('ui.auth.last_name') }}</label>
                    <input type="text" wire:model="lastName" placeholder="z.B. Schmidt">
                    @error('lastName') <div class="field-error" style="margin:-8px 0 10px">{{ $message }}</div> @enderror
                </div>
            </div>

            <label>{{ __('ui.auth.nickname') }} <span style="font-size:11px;color:var(--light);font-weight:400">{{ __('ui.auth.nickname_visible') }}</span></label>
            <input type="text" wire:model="nickname" placeholder="z.B. spastik_warrior" maxlength="30">
            <div style="font-size:11px;color:var(--light);margin:-8px 0 4px">{{ __('ui.auth.nickname_format') }}</div>
            @error('nickname') <div class="field-error" style="margin:0 0 10px">{{ $message }}</div> @enderror

            <label>{{ __('ui.auth.email') }}</label>
            <input type="email" wire:model="regEmail" placeholder="deine@email.at">
            @error('regEmail') <div class="field-error" style="margin:-8px 0 10px">{{ $message }}</div> @enderror

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 12px;align-items:start">
                <div>
                    <label>{{ __('ui.auth.password') }}</label>
                    <div x-data="{ pw: '', show: false }">
                        <div style="display:flex;align-items:center;border:1.5px solid rgba(10,110,122,.15);border-radius:10px;background:var(--surf2);margin-bottom:6px;transition:border-color .18s;padding-right:10px" @focusin="$el.style.borderColor='var(--t)'" @focusout="$el.style.borderColor='rgba(10,110,122,.15)'">
                            <input wire:model="regPassword" :type="show ? 'text' : 'password'"
                                x-on:input="pw = $event.target.value"
                                placeholder="{{ __('ui.auth.password_min') }}"
                                style="flex:1;padding:10px 0 10px 13px;border:none;background:transparent;outline:none;font-family:var(--body);font-size:14px;color:var(--ink);margin-bottom:0;min-width:0">
                            <button type="button" x-on:click="show = !show" tabindex="-1"
                                style="padding:0 12px 0 8px;background:none;border:none;cursor:pointer;display:flex;align-items:center;flex-shrink:0"
                                :style="show ? 'color:var(--t)' : 'color:var(--light)'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <div x-show="pw.length > 0" style="font-size:11px;display:flex;flex-direction:column;gap:2px;margin-bottom:6px">
                            <span :style="pw.length >= 8 ? 'color:var(--t)' : 'color:var(--light)'" x-text="(pw.length >= 8 ? '✓' : '✗') + ' {{ __('ui.auth.pw_check_length') }}'"></span>
                            <span :style="/[a-zA-ZäöüÄÖÜß]/.test(pw) ? 'color:var(--t)' : 'color:var(--light)'" x-text="(/[a-zA-ZäöüÄÖÜß]/.test(pw) ? '✓' : '✗') + ' {{ __('ui.auth.pw_check_letter') }}'"></span>
                            <span :style="/[0-9]/.test(pw) ? 'color:var(--t)' : 'color:var(--light)'" x-text="(/[0-9]/.test(pw) ? '✓' : '✗') + ' {{ __('ui.auth.pw_check_number') }}'"></span>
                        </div>
                    </div>
                    @error('regPassword') <div class="field-error" style="margin:0 0 10px">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label>{{ __('ui.auth.repeat_pwd') }}</label>
                    <div x-data="{ show: false }" style="display:flex;align-items:center;border:1.5px solid rgba(10,110,122,.15);border-radius:10px;background:var(--surf2);margin-bottom:12px;transition:border-color .18s;padding-right:10px" @focusin="$el.style.borderColor='var(--t)'" @focusout="$el.style.borderColor='rgba(10,110,122,.15)'">
                        <input wire:model="regPasswordConfirmation" :type="show ? 'text' : 'password'"
                            placeholder="{{ __('ui.auth.password_min') }}"
                            style="flex:1;padding:10px 0 10px 13px;border:none;background:transparent;outline:none;font-family:var(--body);font-size:14px;color:var(--ink);margin-bottom:0;min-width:0">
                        <button type="button" x-on:click="show = !show" tabindex="-1"
                            style="padding:0 12px 0 8px;background:none;border:none;cursor:pointer;display:flex;align-items:center;flex-shrink:0"
                            :style="show ? 'color:var(--t)' : 'color:var(--light)'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('regPasswordConfirmation') <div class="field-error" style="margin:-8px 0 10px">{{ $message }}</div> @enderror
                </div>
            </div>

            <button class="mbtn" wire:click="register" wire:loading.attr="disabled" wire:target="register">
                <span wire:loading.remove wire:target="register">{{ __('ui.auth.create_account') }}</span>
                <span wire:loading wire:target="register">{{ __('ui.auth.creating') }}</span>
            </button>
            <div class="mlink">{{ __('ui.auth.already_registered') }} <a wire:click="switchTab('login')" style="cursor:pointer">{{ __('ui.auth.login_link') }}</a></div>
        </div>
        @endif

        {{-- Password reset --}}
        @if($tab === 'reset')
        <div>
            <label>{{ __('ui.auth.email') }}</label>
            <input type="email" wire:model="resetEmail" placeholder="deine@email.at" wire:keydown.enter="sendResetLink">
            @error('resetEmail') <div class="field-error" style="margin:-8px 0 10px">{{ $message }}</div> @enderror

            @if($resetMessage)
            <div style="font-size:12.5px;margin:-4px 0 10px;padding:8px 12px;border-radius:8px;line-height:1.5;color:{{ $resetMessageType === 'success' ? 'var(--t)' : '#c04040' }};background:{{ $resetMessageType === 'success' ? 'var(--t3)' : 'rgba(192,64,64,.08)' }}">
                {{ $resetMessage }}
            </div>
            @endif

            <button class="mbtn" wire:click="sendResetLink" wire:loading.attr="disabled" wire:target="sendResetLink">
                <span wire:loading.remove wire:target="sendResetLink">{{ __('ui.auth.reset_button') }}</span>
                <span wire:loading wire:target="sendResetLink">{{ __('ui.auth.sending') }}</span>
            </button>
            <div class="mlink">{{ __('ui.auth.back_to_login') }} <a wire:click="switchTab('login')" style="cursor:pointer">{{ __('ui.auth.login_link') }}</a></div>
        </div>
        @endif

        {{-- Verify email --}}
        @if($tab === 'verify')
        <div style="text-align:center;padding:8px 0 4px">
            <div style="font-size:48px;margin-bottom:16px">&#x2709;&#xFE0F;</div>
            <div style="font-size:15px;font-weight:700;color:var(--ink);margin-bottom:10px">{{ __('ui.auth.verify_confirm_title') }}</div>
            <div style="font-size:13.5px;color:var(--muted);line-height:1.65;margin-bottom:24px">
                {{ __('ui.auth.verify_confirm_text1') }}<br>
                {{ __('ui.auth.verify_confirm_text2') }}
            </div>
            <button class="mbtn" wire:click="closeModal">{{ __('ui.auth.ok') }}</button>
        </div>
        @endif
    </div>
    @endif
</div>
