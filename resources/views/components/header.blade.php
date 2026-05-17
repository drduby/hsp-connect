<header>
    <a class="logo" onclick="clearAll()">
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
        <button class="h-btn-muted" id="h-home-btn" onclick="clearAll()">Home</button>
        <button class="h-btn-muted" id="h-faq-btn" onclick="openFAQPage()">FAQ</button>
        @if(Auth::check() && Auth::user()->hasVerifiedEmail())
            <button class="h-btn-o" id="h-login-btn" onclick="openLg()">Profile</button>
        @else
            <button class="h-btn-muted" id="h-login-btn" onclick="openLg()">Anmelden</button>
            <button class="h-btn-muted" id="h-reg-btn" onclick="openLg('up')">Registrieren</button>
        @endif
    </div>
</header>
