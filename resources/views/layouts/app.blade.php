<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HSPConnect — Community für Spastik')</title>
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
<script>window.__AUTH__ = @json($authState);</script>
@persist('hexbg')
<canvas id="hexbg"></canvas>
@endpersist
<div class="toasts" id="toasts"></div>

<x-notification-panel />
<x-profile-menu />

@if(auth()->check() && ! auth()->user()->hasVerifiedEmail())
    <div style="background:#d97706;color:#fff;text-align:center;padding:10px 16px;font-size:13.5px;font-family:var(--body);display:flex;align-items:center;justify-content:center;gap:12px">
        <span>Bitte bestätige deine E-Mail-Adresse, um HSPConnect nutzen zu können.</span>
        <form method="POST" action="/email/verification-notification" style="display:inline">
            @csrf
            <button type="submit" style="background:rgba(255,255,255,.25);border:none;color:#fff;padding:4px 12px;border-radius:20px;font-size:12.5px;cursor:pointer;font-weight:600">Erneut senden</button>
        </form>
    </div>
@endif

@yield('content')

@include('partials.account-page')
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
                Abbrechen
            </button>
            <button onclick="doConfirm()"
                style="padding:9px 20px;border-radius:20px;border:none;background:#c04040;color:#fff;font-family:var(--body);font-size:13.5px;font-weight:600;cursor:pointer">
                Löschen
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
