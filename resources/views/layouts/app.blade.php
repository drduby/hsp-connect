<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HSPConnect — Community für Spastik')</title>
    <meta name="description" content="@yield('description', 'HSPConnect ist die deutschsprachige Online-Community für Menschen mit Hereditärer Spastischer Paraplegie (HSP). Erfahrungen teilen, Fragen stellen, einander unterstützen.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="HSPConnect">
    <meta property="og:title" content="@yield('title', 'HSPConnect — Community für Spastik')">
    <meta property="og:description" content="@yield('description', 'HSPConnect ist die deutschsprachige Online-Community für Menschen mit Hereditärer Spastischer Paraplegie (HSP). Erfahrungen teilen, Fragen stellen, einander unterstützen.')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:locale" content="{{ app()->getLocale() === 'en' ? 'en_US' : 'de_DE' }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="@yield('title', 'HSPConnect — Community für Spastik')">
    <meta name="twitter:description" content="@yield('description', 'HSPConnect ist die deutschsprachige Online-Community für Menschen mit Hereditärer Spastischer Paraplegie (HSP). Erfahrungen teilen, Fragen stellen, einander unterstützen.')">

    @yield('structured-data')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=Mulish:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>

@php
    $authUser = auth()->check() && auth()->user()->hasVerifiedEmail() ? auth()->user() : null;
    $authState = $authUser
        ? ['name' => $authUser->nickname, 'ava' => strtoupper(mb_substr($authUser->nickname, 0, 1))]
        : null;
@endphp
<script>
window.__AUTH__ = @json($authState);
window.__NOTIF_COUNT__ = {{ $authUser ? $authUser->unreadNotifications()->count() : 0 }};
window.__LOCALE__ = '{{ app()->getLocale() }}';
@php
$legalContent = [
    'impressum' => ['title' => __('ui.legal.impressum.title'), 'body' => __('ui.legal.impressum.body')],
    'datenschutz' => ['title' => __('ui.legal.datenschutz.title'), 'body' => __('ui.legal.datenschutz.body')],
    'nutzung' => ['title' => __('ui.legal.nutzung.title'), 'body' => __('ui.legal.nutzung.body')],
    'regeln' => ['title' => __('ui.legal.regeln.title'), 'body' => __('ui.legal.regeln.body')],
    'faq' => ['title' => __('ui.legal.faq.title'), 'body' => __('ui.legal.faq.body')],
    'kontakt' => ['title' => __('ui.legal.kontakt.title'), 'body' => __('ui.legal.kontakt.body')],
];
@endphp
window.__LEGAL__ = @json($legalContent);
window.__TRANS__ = {
    notificationsTitle: @json(__('ui.notifications.title')),
    noNotifications: @json(__('ui.notifications.none')),
    unread: @json(__('ui.notifications.unread')),
    close: @json(__('ui.notifications.close')),
    filterSaved: @json(__('ui.account.saved_posts')),
    filterMine: @json(__('ui.account.my_posts')),
    filter: 'Filter',
    requireLogin: @json(__('ui.layout.require_login')),
    socialLoginSoon: @json(__('ui.layout.social_login_soon')),
    welcomeBack: @json(__('ui.auth.welcome_back')),
};
</script>
@persist('hexbg')
<canvas id="hexbg"></canvas>
@endpersist
<div class="toasts" id="toasts"></div>

<x-notification-panel />
<x-profile-menu />

@if(session('impersonating_admin_id'))
    <div style="background:#1a1a2e;color:#fff;text-align:center;padding:10px 16px;font-size:13.5px;font-family:var(--body);display:flex;align-items:center;justify-content:center;gap:12px;position:sticky;top:0;z-index:999">
        <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" style="flex-shrink:0;opacity:.8"><path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z"/></svg>
        <span>You are logged in as <strong>{{ auth()->user()->nickname }}</strong></span>
        <form method="POST" action="{{ route('impersonate.stop') }}" style="margin:0">
            @csrf
            <button type="submit" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);color:#fff;padding:4px 14px;border-radius:20px;font-size:12.5px;cursor:pointer;font-weight:600;font-family:var(--body);transition:background .15s" onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">
                ← Back to Admin
            </button>
        </form>
    </div>
@endif

@if(auth()->check() && ! auth()->user()->hasVerifiedEmail())
    <div style="background:#d97706;color:#fff;text-align:center;padding:10px 16px;font-size:13.5px;font-family:var(--body);display:flex;align-items:center;justify-content:center;gap:12px">
        <span>{{ __('ui.layout.verify_notice') }}</span>
        <form method="POST" action="/email/verification-notification" style="display:inline">
            @csrf
            <button type="submit" style="background:rgba(255,255,255,.25);border:none;color:#fff;padding:4px 12px;border-radius:20px;font-size:12.5px;cursor:pointer;font-weight:600">{{ __('ui.layout.resend') }}</button>
        </form>
    </div>
@endif

@yield('content')

@include('partials.account-page')
<x-modals.beta />
<x-modals.info />
<livewire:feedback-modal />
<livewire:auth-modal />
<x-modals.post />
<x-modals.verified />

<div id="confirm-modal-bg"
     onclick="if(event.target===this)closeConfirm()"
     style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center">
    <div style="background:var(--surf);border-radius:18px;padding:28px 24px;max-width:340px;width:88%;box-shadow:0 8px 40px rgba(0,0,0,.18)">
        <div id="confirm-modal-title" style="font-family:var(--disp);font-size:16px;font-weight:700;color:var(--ink);margin-bottom:6px"></div>
        <div id="confirm-modal-msg" style="font-size:13.5px;color:var(--muted);line-height:1.6;margin-bottom:22px"></div>
        <div style="display:flex;gap:10px;justify-content:flex-end">
            <button onclick="closeConfirm()"
                style="padding:9px 20px;border-radius:20px;border:1.5px solid var(--br);background:none;font-family:var(--body);font-size:13.5px;cursor:pointer;color:var(--muted);font-weight:500">
                {{ __('ui.layout.cancel') }}
            </button>
            <button onclick="doConfirm()"
                style="padding:9px 20px;border-radius:20px;border:none;background:#c04040;color:#fff;font-family:var(--body);font-size:13.5px;font-weight:600;cursor:pointer">
                {{ __('ui.layout.delete') }}
            </button>
        </div>
    </div>
</div>
<livewire:report-post />

@include('partials.cookie-banner')

@livewireScripts
<script data-navigate-once>
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('open-login', function () { Livewire.dispatch('open-auth-modal', { tab: 'login' }); });

        Livewire.on('close-post-modal', function () { if (typeof closePM === 'function') closePM(); });

        Livewire.on('post-created', function () {
            var mineEl = document.getElementById('nav-mine-count');
            var accPostsEl = document.getElementById('acc-posts');
            if (mineEl || accPostsEl) {
                var cur = parseInt((mineEl ? mineEl.textContent.replace(/\D/g, '') : '') || (accPostsEl ? accPostsEl.textContent : '0')) || 0;
                var next = cur + 1;
                if (mineEl) mineEl.textContent = ' (' + next + ')';
                if (accPostsEl) accPostsEl.textContent = next;
            }
        });

        Livewire.on('post-deleted', function () {
            if (typeof render === 'function') render();
            var mineEl = document.getElementById('nav-mine-count');
            var accPostsEl = document.getElementById('acc-posts');
            if (mineEl || accPostsEl) {
                var cur = parseInt((mineEl ? mineEl.textContent.replace(/\D/g, '') : '') || (accPostsEl ? accPostsEl.textContent : '0')) || 0;
                var next = Math.max(0, cur - 1);
                if (mineEl) mineEl.textContent = next > 0 ? ' (' + next + ')' : '';
                if (accPostsEl) accPostsEl.textContent = next;
            }
        });

        Livewire.on('saved-count-updated', function (event) {
            var count = event && event.count != null ? event.count : (Array.isArray(event) && event[0] ? event[0].count : 0);
            var el = document.getElementById('nav-saved-count');
            if (el) el.textContent = count > 0 ? ' (' + count + ')' : '';
            var accEl = document.getElementById('acc-saved-cnt');
            if (accEl) accEl.textContent = count;
        });
    });
</script>
</body>
</html>
