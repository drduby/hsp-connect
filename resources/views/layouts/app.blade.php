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
    $authState = auth()->check()
        ? ['name' => auth()->user()->nickname, 'ava' => strtoupper(mb_substr(auth()->user()->nickname, 0, 1))]
        : null;
@endphp
<script>window.__AUTH__ = @json($authState);</script>
<canvas id="hexbg"></canvas>
<div class="toasts" id="toasts"></div>

<x-notification-panel />
<x-profile-menu />

@yield('content')

<x-modals.info />
<x-modals.feedback />
<x-modals.login />
<x-modals.post />

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

@livewireScripts
<script>
    function togglePw(id, btn) {
        var inp = document.getElementById(id);
        var isHidden = inp.type === 'password';
        inp.type = isHidden ? 'text' : 'password';
        document.getElementById(id + '-eye').style.display = isHidden ? 'none' : '';
        document.getElementById(id + '-eye-off').style.display = isHidden ? '' : 'none';
        btn.style.color = isHidden ? 'var(--t)' : 'var(--light)';
    }

    document.addEventListener('livewire:initialized', function () {
        Livewire.on('open-login', function () { if (typeof openLg === 'function') openLg(); });

        Livewire.on('close-post-modal', function () { if (typeof closePM === 'function') closePM(); });

        Livewire.on('post-deleted', function (event) {
            var el = document.getElementById('post-' + event.postId);
            if (el) el.remove();
            if (window.__POSTS__) {
                window.__POSTS__ = window.__POSTS__.filter(function (p) { return p.id !== event.postId; });
            }
            if (window.state) {
                window.state.posts = window.state.posts.filter(function (p) { return p.id !== event.postId; });
            }
            if (typeof render === 'function') render();
        });

        Livewire.on('posts-refreshed', function (event) {
            if (event && event.posts) {
                window.__POSTS__ = event.posts;
                if (window.state) window.state.posts = event.posts.map(function (p) { return Object.assign({}, p, { expanded: false, showC: false }); });
                if (typeof render === 'function') render();
            }
        });
    });
</script>
</body>
</html>
