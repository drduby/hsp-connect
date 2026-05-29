<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('ui.auth.page_title_verify') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=Mulish:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body>
<div class="reset-page">
    <div class="modal">
        <a class="logo" href="{{ url('/') }}" style="margin-bottom:22px">
            <x-logo />
            <div class="logo-text">
                <div class="logo-name">HSP<span>Connect</span></div>
                <div class="logo-sub">Community</div>
            </div>
        </a>

        <div class="mttl">{{ __('ui.auth.verify_title') }}</div>
        <div class="msub">{{ __('ui.auth.verify_sub') }}</div>

        <div style="font-size:13px;color:var(--muted);line-height:1.65;margin:16px 0 20px">
            {{ __('ui.auth.verify_text') }}
        </div>

        @if(session('status') === 'verification-link-sent')
            <div style="font-size:12.5px;margin-bottom:16px;padding:10px 14px;border-radius:10px;line-height:1.5;color:var(--t);background:var(--t3)">
                {{ __('ui.auth.verify_sent') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" style="margin-bottom:12px">
            @csrf
            <button class="mbtn" type="submit">{{ __('ui.auth.resend') }}</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="width:100%;padding:11px;border-radius:20px;border:1.5px solid var(--br);background:none;font-family:var(--body);font-size:14px;font-weight:600;cursor:pointer;color:var(--muted)">
                {{ __('ui.auth.logout') }}
            </button>
        </form>
    </div>
</div>
</body>
</html>
