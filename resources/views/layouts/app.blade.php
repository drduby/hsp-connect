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

@livewireScripts
<script>
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('open-login', function () { if (typeof openLg === 'function') openLg(); });
        Livewire.on('post-deleted', function (event) {
            var el = document.getElementById('post-' + event.postId);
            if (el) el.remove();
            if (window.__POSTS__) {
                window.__POSTS__ = window.__POSTS__.filter(function (p) { return p.id !== event.postId; });
            }
            if (typeof render === 'function') render();
        });
    });
</script>
</body>
</html>
