<?php

use App\Actions\Fortify\CreateNewUser;
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
            'email.required'    => 'Bitte E-Mail eingeben.',
            'email.email'       => 'Bitte eine gültige E-Mail-Adresse eingeben.',
            'password.required' => 'Bitte Passwort eingeben.',
        ]);

        $key = 'login:'.strtolower($this->email).'|'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', 'Zu viele Versuche. Bitte warte einen Moment.');

            return;
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            RateLimiter::hit($key, 60);
            $this->addError('email', 'E-Mail oder Passwort ist falsch.');

            return;
        }

        RateLimiter::clear($key);
        $user = Auth::user();

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
            'firstName.required'               => 'Bitte Vorname eingeben.',
            'lastName.required'                => 'Bitte Nachname eingeben.',
            'nickname.required'                => 'Bitte Nickname eingeben.',
            'nickname.regex'                   => 'Der Nickname darf nur Buchstaben, Zahlen und _ enthalten.',
            'nickname.unique'                  => 'Dieser Nickname ist bereits vergeben.',
            'regEmail.required'                => 'Bitte E-Mail eingeben.',
            'regEmail.email'                   => 'Bitte eine gültige E-Mail-Adresse eingeben.',
            'regEmail.unique'                  => 'Diese E-Mail-Adresse ist bereits registriert.',
            'regPassword.required'             => 'Bitte Passwort eingeben.',
            'regPassword.min'                  => 'Passwort mind. 8 Zeichen.',
            'regPassword.letters'              => 'Passwort muss mind. 1 Buchstaben enthalten.',
            'regPassword.numbers'              => 'Passwort muss mind. 1 Zahl enthalten.',
            'regPasswordConfirmation.required' => 'Bitte Passwort wiederholen.',
            'regPasswordConfirmation.same'     => 'Passwörter stimmen nicht überein.',
        ]);

        $key = 'register:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->addError('regEmail', 'Zu viele Registrierungsversuche. Bitte warte einen Moment.');

            return;
        }
        RateLimiter::hit($key, 60);

        $user = app(CreateNewUser::class)->create([
            'first_name'            => $this->firstName,
            'last_name'             => $this->lastName,
            'nickname'              => $this->nickname,
            'email'                 => $this->regEmail,
            'password'              => $this->regPassword,
            'password_confirmation' => $this->regPasswordConfirmation,
        ]);

        event(new Registered($user));
        $this->tab = 'verify';
    }

    public function sendResetLink(): void
    {
        $this->validate([
            'resetEmail' => ['required', 'email'],
        ], [
            'resetEmail.required' => 'Bitte E-Mail eingeben.',
            'resetEmail.email'    => 'Bitte eine gültige E-Mail-Adresse eingeben.',
        ]);

        $status = Password::sendResetLink(['email' => $this->resetEmail]);

        $this->resetMessage = $status === Password::RESET_LINK_SENT
            ? 'Wenn ein Konto existiert, senden wir dir einen Link zum Zurücksetzen.'
            : 'Fehler beim Senden. Bitte versuche es erneut.';
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
            @if($tab === 'login') Willkommen zurück
            @elseif($tab === 'register') Konto erstellen
            @elseif($tab === 'reset') Passwort zurücksetzen
            @else Fast geschafft!
            @endif
        </div>
        <div class="msub">
            @if($tab === 'login') Schön, dass du wieder da bist!
            @elseif($tab === 'register') Werde Teil der Community
            @elseif($tab === 'reset') Wir senden dir einen Link per E-Mail
            @endif
        </div>

        {{-- Login --}}
        @if($tab === 'login')
        <div>
            <label>E-Mail</label>
            <input type="email" wire:model="email" placeholder="deine@email.at" wire:keydown.enter="login">
            @error('email') <div class="field-error" style="margin:-8px 0 10px">{{ $message }}</div> @enderror

            <label>Passwort</label>
            <div x-data="{ show: false }" style="position:relative;margin-bottom:12px">
                <input wire:model="password" :type="show ? 'text' : 'password'"
                    placeholder="••••••••" wire:keydown.enter="login"
                    style="padding-right:42px;width:100%;margin-bottom:0">
                <button type="button" x-on:click="show = !show" tabindex="-1"
                    style="position:absolute;right:12px;top:0;bottom:0;margin:auto 0;height:18px;background:none;border:none;cursor:pointer;padding:0;display:flex;align-items:center"
                    :style="show ? 'color:var(--t)' : 'color:var(--light)'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            @error('password') <div class="field-error" style="margin:-8px 0 10px">{{ $message }}</div> @enderror

            <div class="mlink mlink-tight">Passwort vergessen? <a wire:click="switchTab('reset')" style="cursor:pointer">Reset password</a></div>

            <button class="mbtn" wire:click="login" wire:loading.attr="disabled" wire:target="login">
                <span wire:loading.remove wire:target="login">Anmelden</span>
                <span wire:loading wire:target="login">Wird angemeldet…</span>
            </button>
            <div class="mlink">Noch kein Konto? <a wire:click="switchTab('register')" style="cursor:pointer">Registrieren</a></div>
        </div>
        @endif

        {{-- Register --}}
        @if($tab === 'register')
        <div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 12px">
                <div>
                    <label>Vorname</label>
                    <input type="text" wire:model="firstName" placeholder="z.B. Maria">
                    @error('firstName') <div class="field-error" style="margin:-8px 0 10px">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label>Nachname</label>
                    <input type="text" wire:model="lastName" placeholder="z.B. Schmidt">
                    @error('lastName') <div class="field-error" style="margin:-8px 0 10px">{{ $message }}</div> @enderror
                </div>
            </div>

            <label>Nickname <span style="font-size:11px;color:var(--light);font-weight:400">(sichtbar für alle)</span></label>
            <input type="text" wire:model="nickname" placeholder="z.B. spastik_warrior" maxlength="30">
            <div style="font-size:11px;color:var(--light);margin:-8px 0 4px">Nur Buchstaben, Zahlen und _ erlaubt.</div>
            @error('nickname') <div class="field-error" style="margin:0 0 10px">{{ $message }}</div> @enderror

            <label>E-Mail</label>
            <input type="email" wire:model="regEmail" placeholder="deine@email.at">
            @error('regEmail') <div class="field-error" style="margin:-8px 0 10px">{{ $message }}</div> @enderror

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 12px;align-items:start">
                <div>
                    <label>Passwort</label>
                    <div x-data="{ pw: '', show: false }" style="margin-bottom:4px">
                        <div style="position:relative;margin-bottom:0">
                            <input wire:model="regPassword" :type="show ? 'text' : 'password'"
                                x-on:input="pw = $event.target.value"
                                placeholder="Min. 8 Zeichen"
                                style="padding-right:36px;width:100%;margin-bottom:6px">
                            <button type="button" x-on:click="show = !show" tabindex="-1"
                                style="position:absolute;right:10px;top:13px;height:18px;background:none;border:none;cursor:pointer;padding:0;display:flex;align-items:center"
                                :style="show ? 'color:var(--t)' : 'color:var(--light)'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <div x-show="pw.length > 0" style="font-size:11px;display:flex;flex-direction:column;gap:2px;margin-bottom:6px">
                            <span :style="pw.length >= 8 ? 'color:var(--t)' : 'color:var(--light)'" x-text="(pw.length >= 8 ? '✓' : '✗') + ' Mindestens 8 Zeichen'"></span>
                            <span :style="/[a-zA-ZäöüÄÖÜß]/.test(pw) ? 'color:var(--t)' : 'color:var(--light)'" x-text="(/[a-zA-ZäöüÄÖÜß]/.test(pw) ? '✓' : '✗') + ' Mindestens 1 Buchstabe'"></span>
                            <span :style="/[0-9]/.test(pw) ? 'color:var(--t)' : 'color:var(--light)'" x-text="(/[0-9]/.test(pw) ? '✓' : '✗') + ' Mindestens 1 Zahl'"></span>
                        </div>
                    </div>
                    @error('regPassword') <div class="field-error" style="margin:0 0 10px">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label>Wiederholen</label>
                    <div x-data="{ show: false }" style="position:relative;margin-bottom:12px">
                        <input wire:model="regPasswordConfirmation" :type="show ? 'text' : 'password'"
                            placeholder="Min. 8 Zeichen"
                            style="padding-right:36px;width:100%;margin-bottom:0">
                        <button type="button" x-on:click="show = !show" tabindex="-1"
                            style="position:absolute;right:10px;top:13px;height:18px;background:none;border:none;cursor:pointer;padding:0;display:flex;align-items:center"
                            :style="show ? 'color:var(--t)' : 'color:var(--light)'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('regPasswordConfirmation') <div class="field-error" style="margin:-8px 0 10px">{{ $message }}</div> @enderror
                </div>
            </div>

            <button class="mbtn" wire:click="register" wire:loading.attr="disabled" wire:target="register">
                <span wire:loading.remove wire:target="register">Konto erstellen</span>
                <span wire:loading wire:target="register">Wird erstellt…</span>
            </button>
            <div class="mlink">Bereits registriert? <a wire:click="switchTab('login')" style="cursor:pointer">Anmelden</a></div>
        </div>
        @endif

        {{-- Password reset --}}
        @if($tab === 'reset')
        <div>
            <label>E-Mail</label>
            <input type="email" wire:model="resetEmail" placeholder="deine@email.at" wire:keydown.enter="sendResetLink">
            @error('resetEmail') <div class="field-error" style="margin:-8px 0 10px">{{ $message }}</div> @enderror

            @if($resetMessage)
            <div style="font-size:12.5px;margin:-4px 0 10px;padding:8px 12px;border-radius:8px;line-height:1.5;color:{{ $resetMessageType === 'success' ? 'var(--t)' : '#c04040' }};background:{{ $resetMessageType === 'success' ? 'var(--t3)' : 'rgba(192,64,64,.08)' }}">
                {{ $resetMessage }}
            </div>
            @endif

            <button class="mbtn" wire:click="sendResetLink" wire:loading.attr="disabled" wire:target="sendResetLink">
                <span wire:loading.remove wire:target="sendResetLink">Reset password</span>
                <span wire:loading wire:target="sendResetLink">Wird gesendet…</span>
            </button>
            <div class="mlink">Zurück zum Login? <a wire:click="switchTab('login')" style="cursor:pointer">Anmelden</a></div>
        </div>
        @endif

        {{-- Verify email --}}
        @if($tab === 'verify')
        <div style="text-align:center;padding:8px 0 4px">
            <div style="font-size:48px;margin-bottom:16px">&#x2709;&#xFE0F;</div>
            <div style="font-size:15px;font-weight:700;color:var(--ink);margin-bottom:10px">Bitte bestätige deine E-Mail-Adresse</div>
            <div style="font-size:13.5px;color:var(--muted);line-height:1.65;margin-bottom:24px">
                Wir haben dir einen Bestätigungslink gesendet.<br>
                Bitte klicke auf den Link in der E-Mail, damit dein Konto aktiviert werden kann.
            </div>
            <button class="mbtn" wire:click="closeModal">Alles klar</button>
        </div>
        @endif
    </div>
    @endif
</div>
