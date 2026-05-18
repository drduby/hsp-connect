@props(['tagsId' => 'hero-tags', 'memberCount' => 0, 'postCount' => 0, 'onlineCount' => 0])

<div class="hero">
    <div class="hero-in">
        <div class="hero-l">
            <div class="online-pill">
                <span class="online-dot"></span>
                {{ $onlineCount }} {{ $onlineCount === 1 ? 'Mitglied' : 'Mitglieder' }} gerade online
            </div>
            <h1>Community f&#xFC;r Menschen<br>mit Spastik</h1>
            <div class="hero-sub">Erfahrungen teilen &middot; Fragen stellen &middot; Einander unterst&#xFC;tzen</div>
            <div style="font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.5);margin-top:14px;margin-bottom:6px">Top-Themen</div>
            <div class="hero-tags" id="{{ $tagsId }}"></div>
        </div>
        <div class="hero-r">
            <div class="hero-stats">
                <div class="hs"><div class="hs-n">{{ number_format($memberCount, 0, ',', '.') }}</div><div class="hs-l">Mitglieder</div></div>
                <div class="hs"><div class="hs-n">{{ number_format($postCount, 0, ',', '.') }}</div><div class="hs-l">Beitr&#xE4;ge</div></div>
                <div class="hs"><div class="hs-n">{{ $onlineCount }}</div><div class="hs-l">Online</div></div>
            </div>
        </div>
    </div>
</div>
