<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('ui.auth.page_title_reset') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=Mulish:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body>
<div class="reset-page">
    <form class="modal" id="reset-password-form">
        <a class="logo" href="{{ url('/') }}" style="margin-bottom:22px">
            <x-logo />
            <div class="logo-text">
                <div class="logo-name">HSP<span>Connect</span></div>
                <div class="logo-sub">Community</div>
            </div>
        </a>

        <div class="mttl">{{ __('ui.auth.reset_title') }}</div>
        <div class="msub">{{ __('ui.auth.reset_sub') }}</div>

        <div id="reset-password-fields">
            <input type="hidden" id="reset-token" value="{{ $token }}">

            <label for="reset-email">{{ __('ui.auth.email') }}</label>
            <input type="email" id="reset-email" value="{{ $email }}" placeholder="deine@email.at" autocomplete="email" required>

            <label for="reset-password">{{ __('ui.auth.new_password') }}</label>
            <input type="password" id="reset-password" placeholder="{{ __('ui.auth.password_hint') }}" autocomplete="new-password" required>

            <label for="reset-password-confirmation">{{ __('ui.auth.repeat_password') }}</label>
            <input type="password" id="reset-password-confirmation" placeholder="{{ __('ui.auth.password_hint') }}" autocomplete="new-password" required>

            <div id="reset-password-message" style="display:none;font-size:12.5px;margin:-4px 0 10px;padding:8px 12px;border-radius:8px;line-height:1.5"></div>

            <button class="mbtn" type="submit">{{ __('ui.auth.reset_button') }}</button>
            <div class="mlink"><a href="{{ url('/') }}">{{ __('ui.auth.back_home') }}</a></div>
        </div>

        <div id="reset-password-success" style="display:none">
            <div style="font-size:12.5px;margin:14px 0 18px;padding:12px 14px;border-radius:10px;line-height:1.6;color:var(--t);background:var(--t3)">
                {{ __('ui.auth.reset_success') }}
            </div>
            <a class="mbtn" href="{{ url('/?login=1') }}" style="display:block;text-align:center;text-decoration:none">{{ __('ui.auth.back_login') }}</a>
        </div>
    </form>
</div>

<script>
const resetPasswordForm = document.getElementById('reset-password-form');
const resetPasswordMessage = document.getElementById('reset-password-message');
const resetPasswordFields = document.getElementById('reset-password-fields');
const resetPasswordSuccess = document.getElementById('reset-password-success');

function showResetPasswordMessage(message, type = 'error') {
    resetPasswordMessage.textContent = message;
    resetPasswordMessage.style.display = message ? '' : 'none';
    resetPasswordMessage.style.color = type === 'success' ? 'var(--t)' : '#c04040';
    resetPasswordMessage.style.background = type === 'success' ? 'var(--t3)' : 'rgba(192,64,64,.08)';
}

function firstResetPasswordError(data) {
    if (data.errors) {
        return Object.values(data.errors)[0]?.[0] || '{{ __('ui.auth.error_fallback') }}';
    }

    return data.message || '{{ __('ui.auth.error_fallback') }}';
}

resetPasswordForm.addEventListener('submit', async function (event) {
    event.preventDefault();
    showResetPasswordMessage('');

    try {
        const response = await fetch('{{ route('password.update') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                token: document.getElementById('reset-token').value,
                email: document.getElementById('reset-email').value.trim(),
                password: document.getElementById('reset-password').value,
                password_confirmation: document.getElementById('reset-password-confirmation').value,
            }),
        });
        const data = response.headers.get('content-type')?.includes('application/json') ? await response.json() : {};

        if (response.ok) {
            resetPasswordFields.style.display = 'none';
            resetPasswordSuccess.style.display = '';

            return;
        }

        showResetPasswordMessage(firstResetPasswordError(data));
    } catch (error) {
        showResetPasswordMessage('{{ __('ui.auth.conn_error') }}');
    }
});
</script>
</body>
</html>
