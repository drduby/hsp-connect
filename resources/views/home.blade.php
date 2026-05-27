@extends('layouts.app')

@section('title', 'HSPConnect — Community für Spastik')
@section('description', 'Die deutschsprachige Community für Menschen mit Hereditärer Spastischer Paraplegie. Teile deine Erfahrungen, stelle Fragen und finde Unterstützung von ' . $memberCount . ' Mitgliedern.')
@section('canonical', route('home'))

@section('structured-data')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "HSPConnect",
  "description": "Community für Menschen mit Hereditärer Spastischer Paraplegie",
  "url": "{{ route('home') }}",
  "inLanguage": "de-DE",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "{{ route('home') }}?search={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>
@endsection

@section('content')

    <x-header/>

    <x-hero tags-id="hero-tags" :member-count="$memberCount" :post-count="$counts['all']" :online-count="$onlineCount" />

    <div class="layout">
        <x-sidebar :tags="$tags"/>

        <main>
            <div style="display:none"><span id="nav-Alle"></span></div>

            {{-- Compose / Search box --}}
            <div class="compose">
                <div class="c-srch-row">
                    <div class="ava" id="compose-ava"
                         style="display:none;background:var(--bg3);color:var(--muted);font-size:16px;cursor:pointer"
                         onclick="if(state.loggedIn)openProfileMenu()"></div>
                    <div class="c-srch-wrap">
                        <input type="text" name="fake-user" style="display:none" tabindex="-1" aria-hidden="true">
                        <input type="password" name="fake-pass" style="display:none" tabindex="-1" aria-hidden="true">
                        <svg class="c-srch-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input class="c-srch-inp" id="compose-srch" type="text"
                               placeholder="Beitr&#xE4;ge suchen&#x2026;"
                               oninput="onComposeSrch()"
                               autocomplete="off">
                        <button class="c-srch-x" id="c-srch-x" onclick="clearComposeSrch()" style="display:none">
                            &#xD7;
                        </button>
                    </div>
                </div>
                <div id="compose-welcome"
                     style="margin-top:12px;padding:12px 14px;background:var(--surf2);border-radius:12px;display:flex;align-items:flex-start;gap:11px">
                    <span style="font-size:22px;flex-shrink:0">&#x1F44B;</span>
                    <div>
                        <div id="compose-welcome-title"
                             style="font-family:var(--disp);font-size:14px;font-weight:700;color:var(--ink);margin-bottom:3px">
                            Willkommen bei HSPConnect!
                        </div>
                        <div style="font-size:12px;color:var(--muted);line-height:1.65;font-weight:300">Bevor du eine
                            Frage postest &#x2014; schau kurz in der <a href="{{ route('faq') }}" wire:navigate
                                                                           style="color:var(--t);font-weight:600;text-decoration:none">FAQ</a>
                            nach. Hast du eine Erfahrung? Teile sie &#x2014; das hilft der Community!
                        </div>
                    </div>
                </div>
                <div style="margin-top:10px">
                    <button class="c-big-btn c-big-btn-e" onclick="checkAuthThen(()=>openPM('Erfahrung'))"
                            style="width:100%;padding:13px 18px;justify-content:flex-start;gap:14px;border-radius:12px">
                        <span style="font-size:26px;line-height:1">&#x1F4AC;</span>
                        <span style="font-size:14px;font-weight:700">Erfahrung teilen</span>
                    </button>
                </div>
            </div>

            {{-- Active filter bar --}}
            <div class="afilter" id="afilter"><span id="af-txt"></span>
                <button class="af-x" onclick="clearAll()">&#xD7;</button>
            </div>

            {{-- Post feed --}}
            <div id="feed">
                <livewire:post-feed />
            </div>

            <div id="feed-empty" class="empty" style="display:none">
                <div class="empty-i">&#x1F30A;</div>
                <div class="empty-t">Keine Beitr&#xE4;ge gefunden</div>
                <p>Andere Filter oder neuen Beitrag erstellen!</p>
            </div>

        </main>
    </div>

    <x-footer/>

    {{-- Overlay pages --}}

    <script>
        window.__COUNTS__ = @json($counts);
        window.__SAVED_COUNT__ = {{ $savedCount }};
        window.__MY_POST_COUNT__ = {{ $myPostCount }};
        window.__LIKES_GIVEN__ = {{ $likesGivenCount }};
        window.__TAGS__ = @json($tags->sortByDesc('posts_count')->values()->map(fn ($t) => ['name' => $t->name, 'color' => $t->color, 'count' => $t->posts_count]));
    </script>

@endsection
