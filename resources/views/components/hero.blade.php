@props(['tagsId' => '', 'memberCount' => 0, 'postCount' => 0, 'onlineCount' => 0])

<div class="hero">
    <div class="hero-in">
        <div class="hero-l">
            <div class="online-pill">
                <span class="online-dot"></span>
                {{ $onlineCount === 1 ? __('ui.hero.online_singular', ['n' => $onlineCount]) : __('ui.hero.online_plural', ['n' => $onlineCount]) }}
            </div>
            <h1>{{ __('ui.hero.headline_line1') }}<br>{{ __('ui.hero.headline_line2') }}</h1>
            <div class="hero-sub">{{ __('ui.hero.subline') }}</div>
            @if($tagsId)
            <div style="font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.5);margin-top:14px;margin-bottom:6px">{{ __('ui.hero.top_topics') }}</div>
            <div class="hero-tags" id="{{ $tagsId }}"></div>
            @endif
        </div>
        <div class="hero-r">
            <div class="hero-stats">
                <div class="hs"><div class="hs-n">{{ number_format($memberCount, 0, ',', '.') }}</div><div class="hs-l">{{ __('ui.hero.members') }}</div></div>
                <div class="hs"><div class="hs-n">{{ number_format($postCount, 0, ',', '.') }}</div><div class="hs-l">{{ __('ui.hero.posts') }}</div></div>
                <div class="hs"><div class="hs-n">{{ $onlineCount }}</div><div class="hs-l">{{ __('ui.hero.online') }}</div></div>
            </div>
        </div>
    </div>
</div>
