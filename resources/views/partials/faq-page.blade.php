<div id="faq-page" style="display:none;position:fixed;inset:0;z-index:500;overflow-y:auto;background:var(--bg)">
    <canvas id="hexbg2" style="position:fixed;inset:0;z-index:0;pointer-events:none"></canvas>

    <header>
        <a class="logo" onclick="closeFAQPage()">
            <x-logo />
            <div class="logo-text">
                <div class="logo-name">HSP<span>Connect</span></div>
                <div class="logo-sub">Community</div>
            </div>
        </a>
        <div class="h-acts">
            <button class="h-btn-muted" onclick="closeFAQPage()" style="display:flex;align-items:center;gap:5px"><span style="font-size:14px">&#x2190;</span> {{ __('ui.nav.home') }}</button>
            <div class="h-ico-btn" id="faq-notif-btn" onclick="openNotifs()" style="display:none">
                &#x1F514;<span class="notif-dot"></span>
            </div>
            <button class="h-btn-muted h-active" id="faq-h-faq-btn">FAQ</button>
            <div style="display:flex;align-items:center;gap:2px;margin:0 2px">
                <form method="POST" action="{{ route('language.switch', 'de') }}" style="margin:0">
                    @csrf
                    <button type="submit"
                        style="padding:4px 7px;border-radius:6px;border:none;font-family:var(--body);font-size:11.5px;font-weight:700;cursor:pointer;transition:background .15s;background:{{ app()->getLocale() === 'de' ? 'var(--t)' : 'transparent' }};color:{{ app()->getLocale() === 'de' ? '#fff' : 'var(--muted)' }}">
                        DE
                    </button>
                </form>
                <form method="POST" action="{{ route('language.switch', 'en') }}" style="margin:0">
                    @csrf
                    <button type="submit"
                        style="padding:4px 7px;border-radius:6px;border:none;font-family:var(--body);font-size:11.5px;font-weight:700;cursor:pointer;transition:background .15s;background:{{ app()->getLocale() === 'en' ? 'var(--t)' : 'transparent' }};color:{{ app()->getLocale() === 'en' ? '#fff' : 'var(--muted)' }}">
                        EN
                    </button>
                </form>
            </div>

            <button class="h-btn-o" id="faq-h-login-btn" onclick="closeFAQPage();openLg()">{{ __('ui.nav.login') }}</button>
            <button class="h-btn" id="faq-h-reg-btn" onclick="closeFAQPage();openLg('up')">{{ __('ui.nav.register') }}</button>
        </div>
    </header>

    <x-hero tags-id="faq-hero-tags" :member-count="$memberCount" :post-count="$counts['all']" :online-count="$onlineCount"/>

    <div style="position:relative;z-index:1">
        <div class="layout" style="position:relative;z-index:1">
            <aside class="lsb">
                <livewire:faq-question />
                <div class="sb-card" id="faq-fb-widget">
                    <div class="sb-hdg">{{ __('ui.sidebar.feedback') }}</div>
                    <button class="fb-btn" onclick="closeFAQPage();openFeedback('idee')" style="margin-bottom:6px">&#x1F4A1; {{ __('ui.sidebar.idea') }}</button>
                    <button class="fb-btn fb-btn-red" onclick="closeFAQPage();openFeedback('problem')">&#x1F41B; {{ __('ui.sidebar.bug') }}</button>
                </div>
            </aside>

            <main style="min-width:0">
                <div class="compose" style="margin-bottom:14px">
                    <div class="c-srch-row">
                        <div class="c-srch-wrap">
                            <input id="faq-srch" type="text" placeholder="{{ __('ui.faq.search_placeholder') }}" oninput="filterFAQ()" class="c-srch-inp" style="padding-left:16px">
                        </div>
                    </div>
                    <div id="faq-tag-filter" style="display:flex;flex-wrap:wrap;gap:5px;margin-top:10px"></div>
                </div>
                <div id="faq-list"></div>
                <div id="faq-empty" style="display:none;text-align:center;padding:40px;color:var(--muted);font-size:14px">{{ __('ui.faq.no_results') }}</div>
            </main>
        </div>
    </div>

    <x-footer />
</div>
