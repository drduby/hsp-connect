<header x-data="{ mobileOpen: false }" @click.outside="mobileOpen = false">
    @if(request()->routeIs('home'))
    <a class="logo" onclick="clearAll(); mobileOpen = false">
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
            <a href="{{ route('home') }}" wire:navigate class="h-btn-muted">{{ __('ui.nav.home') }}</a>
            <a href="{{ route('faq') }}" wire:navigate class="h-btn-muted h-active" id="h-faq-btn">{{ __('ui.nav.faq') }}</a>
        @else
            <button class="h-btn-muted" id="h-home-btn" onclick="clearAll()">{{ __('ui.nav.home') }}</button>
            <a href="{{ route('faq') }}" wire:navigate class="h-btn-muted" id="h-faq-btn">{{ __('ui.nav.faq') }}</a>
        @endif

        @if(Auth::check() && Auth::user()->is_admin)
            <a href="{{ route('admin.dashboard') }}" class="h-btn-muted">Admin</a>
        @endif

        @if(Auth::check() && Auth::user()->hasVerifiedEmail())
            <button class="h-btn-o" id="h-login-btn" onclick="openLg()">{{ Auth::user()->nickname }}</button>
            <span class="h-avatar" style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0a6e7a,#b8762a);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;user-select:none">
                {{ strtoupper(mb_substr(Auth::user()->nickname, 0, 1)) }}
            </span>
        @else
            <button class="h-btn-muted" id="h-login-btn" onclick="openLg()">{{ __('ui.nav.login') }}</button>
            <button class="h-btn-muted" id="h-reg-btn" onclick="openLg('up')">{{ __('ui.nav.register') }}</button>
        @endif

        <div class="h-lang" style="display:flex;align-items:center;gap:2px;margin-left:4px">
            <form method="POST" action="{{ route('language.switch', 'de') }}" style="margin:0">
                @csrf
                <button type="submit" style="padding:4px 7px;border-radius:6px;border:none;font-family:var(--body);font-size:11.5px;font-weight:700;cursor:pointer;transition:background .15s;background:{{ app()->getLocale() === 'de' ? 'var(--t)' : 'transparent' }};color:{{ app()->getLocale() === 'de' ? '#fff' : 'var(--muted)' }}">DE</button>
            </form>
            <form method="POST" action="{{ route('language.switch', 'en') }}" style="margin:0">
                @csrf
                <button type="submit" style="padding:4px 7px;border-radius:6px;border:none;font-family:var(--body);font-size:11.5px;font-weight:700;cursor:pointer;transition:background .15s;background:{{ app()->getLocale() === 'en' ? 'var(--t)' : 'transparent' }};color:{{ app()->getLocale() === 'en' ? '#fff' : 'var(--muted)' }}">EN</button>
            </form>
        </div>

        {{-- Hamburger (mobile only) --}}
        <button class="h-hamburger" @click.stop="mobileOpen = !mobileOpen" aria-label="Menu">
            <svg x-show="!mobileOpen" width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <line x1="2" y1="4.5" x2="16" y2="4.5"/><line x1="2" y1="9" x2="16" y2="9"/><line x1="2" y1="13.5" x2="16" y2="13.5"/>
            </svg>
            <svg x-show="mobileOpen" x-cloak width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <line x1="3" y1="3" x2="15" y2="15"/><line x1="15" y1="3" x2="3" y2="15"/>
            </svg>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div class="h-mobile-menu" :class="{ 'is-open': mobileOpen }" @click="mobileOpen = false">

        {{-- Navigation --}}
        @if(request()->routeIs('faq'))
            <a href="{{ route('home') }}" wire:navigate class="h-mobile-item">🏠 {{ __('ui.nav.home') }}</a>
            <a href="{{ route('faq') }}" wire:navigate class="h-mobile-item h-active">❓ {{ __('ui.nav.faq') }}</a>
        @else
            <button class="h-mobile-item" onclick="clearAll()">🏠 {{ __('ui.nav.home') }}</button>
            <a href="{{ route('faq') }}" wire:navigate class="h-mobile-item">❓ {{ __('ui.nav.faq') }}</a>
        @endif

        @if(Auth::check() && Auth::user()->is_admin)
            <a href="{{ route('admin.dashboard') }}" class="h-mobile-item">⚙️ Admin</a>
        @endif

        <div class="h-mobile-sep"></div>

        {{-- Account section --}}
        @if(Auth::check() && Auth::user()->hasVerifiedEmail())
            <button class="h-mobile-item" onclick="openAccountPage()">
                <span style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#0a6e7a,#b8762a);display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;margin-right:10px;flex-shrink:0">
                    {{ strtoupper(mb_substr(Auth::user()->nickname, 0, 1)) }}
                </span>
                {{ Auth::user()->nickname }} — {{ __('ui.nav.account') }}
            </button>
            <form method="POST" action="/logout" style="margin:0" @click.stop>
                @csrf
                <button type="submit" class="h-mobile-item" style="color:#c04040;width:100%">
                    ↦ {{ __('ui.nav.logout') }}
                </button>
            </form>
        @else
            <button class="h-mobile-item" onclick="openLg()">🔑 {{ __('ui.nav.login') }}</button>
            <button class="h-mobile-item" onclick="openLg('up')">✨ {{ __('ui.nav.register') }}</button>
        @endif

        <div class="h-mobile-sep"></div>

        {{-- Language --}}
        <div class="h-mobile-lang">
            <span style="font-size:12px;font-weight:700;color:var(--light);letter-spacing:.05em;text-transform:uppercase;margin-right:6px">Language</span>
            <form method="POST" action="{{ route('language.switch', 'de') }}" style="margin:0" @click.stop>
                @csrf
                <button type="submit" style="padding:5px 10px;border-radius:8px;border:none;font-family:var(--body);font-size:12px;font-weight:700;cursor:pointer;transition:background .15s;background:{{ app()->getLocale() === 'de' ? 'var(--t)' : 'var(--surf2)' }};color:{{ app()->getLocale() === 'de' ? '#fff' : 'var(--muted)' }}">DE</button>
            </form>
            <form method="POST" action="{{ route('language.switch', 'en') }}" style="margin:0" @click.stop>
                @csrf
                <button type="submit" style="padding:5px 10px;border-radius:8px;border:none;font-family:var(--body);font-size:12px;font-weight:700;cursor:pointer;transition:background .15s;background:{{ app()->getLocale() === 'en' ? 'var(--t)' : 'var(--surf2)' }};color:{{ app()->getLocale() === 'en' ? '#fff' : 'var(--muted)' }}">EN</button>
            </form>
        </div>
    </div>
</header>
