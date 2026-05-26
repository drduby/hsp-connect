<header>
    @if(request()->routeIs('home'))
    <a class="logo" onclick="clearAll()">
    @else
    <a class="logo" href="{{ route('home') }}" wire:navigate>
    @endif
        <x-logo />
        <div class="logo-text">
            <div class="logo-name">HSP<span>Connect</span></div>
            <div class="logo-sub">Community</div>
        </div>
    </a>
    <div class="h-acts">
        <div class="h-ico-btn" id="notif-btn" onclick="openNotifs()" style="display:none">
            &#x1F514;<span class="notif-dot"></span>
        </div>
        @if(request()->routeIs('faq'))
            <a href="{{ route('home') }}" wire:navigate class="h-btn-muted">Home</a>
            <a href="{{ route('faq') }}" wire:navigate class="h-btn-muted h-active" id="h-faq-btn">FAQ</a>
        @else
            <button class="h-btn-muted" id="h-home-btn" onclick="clearAll()">Home</button>
            <a href="{{ route('faq') }}" wire:navigate class="h-btn-muted" id="h-faq-btn">FAQ</a>
        @endif
        @if(Auth::check() && Auth::user()->hasVerifiedEmail())
            <button class="h-btn-o" id="h-login-btn" onclick="openLg()">{{ Auth::user()->nickname }}</button>
            <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0a6e7a,#b8762a);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;user-select:none">
                {{ strtoupper(mb_substr(Auth::user()->nickname, 0, 1)) }}
            </span>
        @else
            <button class="h-btn-muted" id="h-login-btn" onclick="openLg()">Anmelden</button>
            <button class="h-btn-muted" id="h-reg-btn" onclick="openLg('up')">Registrieren</button>
        @endif
    </div>
</header>
