<?php

use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component {
    public string $nickname = '';
    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';
    public string $currentPassword = '';
    public string $password = '';
    public string $passwordConfirmation = '';
    public string $profileSuccess = '';
    public string $passwordSuccess = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->nickname = $user->nickname ?? '';
        $this->firstName = $user->first_name ?? '';
        $this->lastName = $user->last_name ?? '';
        $this->email = $user->email ?? '';
    }

    public function saveProfile(): void
    {
        $user = auth()->user();

        $this->validate([
            'nickname' => ['required', 'string', 'max:30', 'regex:/^[a-zA-Z0-9_]+$/', Rule::unique('users', 'nickname')->ignore($user->id)],
            'firstName' => ['required', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $emailChanged = $this->email !== $user->email;

        $user->forceFill([
            'nickname' => $this->nickname,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            ...($emailChanged ? ['email_verified_at' => null] : []),
        ])->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        $this->profileSuccess = $emailChanged
            ? 'Profildaten gespeichert. Bitte bestätige deine neue E-Mail-Adresse.'
            : 'Profildaten gespeichert.';
        $this->passwordSuccess = '';
    }

    public function savePassword(): void
    {
        $this->validate([
            'currentPassword' => ['required', 'current_password:web'],
            'password' => ['required', 'string', 'min:8', 'same:passwordConfirmation'],
        ]);

        auth()->user()->forceFill([
            'password' => $this->password,
        ])->save();

        $this->currentPassword = '';
        $this->password = '';
        $this->passwordConfirmation = '';
        $this->passwordSuccess = 'Passwort erfolgreich geändert.';
        $this->profileSuccess = '';
    }
};
?>

<div style="display:flex;flex-direction:column;gap:14px">

    {{-- Profile info form --}}
    <div class="sb-card" style="padding:20px 22px">
        <div style="font-size:11px;font-weight:800;letter-spacing:.18em;text-transform:uppercase;color:var(--light);margin-bottom:16px">
            Profildaten
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
            <div>
                <label style="font-size:11.5px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px">Vorname</label>
                <input wire:model="firstName" type="text"
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--bord2);border-radius:10px;font-family:var(--body);font-size:13.5px;color:var(--ink);background:var(--bg);outline:none;box-sizing:border-box">
                @error('firstName') <span style="font-size:11px;color:#c04040">{{ $message }}</span> @enderror
            </div>
            <div>
                <label style="font-size:11.5px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px">Nachname</label>
                <input wire:model="lastName" type="text"
                    style="width:100%;padding:9px 12px;border:1.5px solid var(--bord2);border-radius:10px;font-family:var(--body);font-size:13.5px;color:var(--ink);background:var(--bg);outline:none;box-sizing:border-box">
                @error('lastName') <span style="font-size:11px;color:#c04040">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="margin-bottom:12px">
            <label style="font-size:11.5px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px">Benutzername</label>
            <input wire:model="nickname" type="text"
                style="width:100%;padding:9px 12px;border:1.5px solid var(--bord2);border-radius:10px;font-family:var(--body);font-size:13.5px;color:var(--ink);background:var(--bg);outline:none;box-sizing:border-box">
            @error('nickname') <span style="font-size:11px;color:#c04040">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom:16px">
            <label style="font-size:11.5px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px">E-Mail</label>
            <input wire:model="email" type="email"
                style="width:100%;padding:9px 12px;border:1.5px solid var(--bord2);border-radius:10px;font-family:var(--body);font-size:13.5px;color:var(--ink);background:var(--bg);outline:none;box-sizing:border-box">
            @error('email') <span style="font-size:11px;color:#c04040">{{ $message }}</span> @enderror
        </div>

        @if($profileSuccess)
            <div style="font-size:12.5px;color:#2a8a52;font-weight:600;margin-bottom:10px">✓ {{ $profileSuccess }}</div>
        @endif

        <button wire:click="saveProfile" wire:loading.attr="disabled"
            style="width:100%;padding:11px;background:var(--t);color:#fff;border:none;border-radius:10px;font-family:var(--body);font-size:13.5px;font-weight:700;cursor:pointer">
            <span wire:loading.remove>Speichern</span>
            <span wire:loading>Wird gespeichert…</span>
        </button>
    </div>

    {{-- Password form --}}
    <div class="sb-card" style="padding:20px 22px">
        <div style="font-size:11px;font-weight:800;letter-spacing:.18em;text-transform:uppercase;color:var(--light);margin-bottom:16px">
            Passwort ändern
        </div>

        <div style="margin-bottom:12px">
            <label style="font-size:11.5px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px">Aktuelles Passwort</label>
            <input wire:model="currentPassword" type="password"
                style="width:100%;padding:9px 12px;border:1.5px solid var(--bord2);border-radius:10px;font-family:var(--body);font-size:13.5px;color:var(--ink);background:var(--bg);outline:none;box-sizing:border-box">
            @error('currentPassword') <span style="font-size:11px;color:#c04040">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom:12px">
            <label style="font-size:11.5px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px">Neues Passwort</label>
            <input wire:model="password" type="password"
                style="width:100%;padding:9px 12px;border:1.5px solid var(--bord2);border-radius:10px;font-family:var(--body);font-size:13.5px;color:var(--ink);background:var(--bg);outline:none;box-sizing:border-box">
            @error('password') <span style="font-size:11px;color:#c04040">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom:16px">
            <label style="font-size:11.5px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px">Passwort bestätigen</label>
            <input wire:model="passwordConfirmation" type="password"
                style="width:100%;padding:9px 12px;border:1.5px solid var(--bord2);border-radius:10px;font-family:var(--body);font-size:13.5px;color:var(--ink);background:var(--bg);outline:none;box-sizing:border-box">
        </div>

        @if($passwordSuccess)
            <div style="font-size:12.5px;color:#2a8a52;font-weight:600;margin-bottom:10px">✓ {{ $passwordSuccess }}</div>
        @endif

        <button wire:click="savePassword" wire:loading.attr="disabled"
            style="width:100%;padding:11px;background:var(--t);color:#fff;border:none;border-radius:10px;font-family:var(--body);font-size:13.5px;font-weight:700;cursor:pointer">
            <span wire:loading.remove>Passwort ändern</span>
            <span wire:loading>Wird gespeichert…</span>
        </button>
    </div>

</div>
