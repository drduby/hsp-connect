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
            <button class="h-btn-muted" onclick="closeFAQPage()" style="display:flex;align-items:center;gap:5px"><span style="font-size:14px">&#x2190;</span> Home</button>
            <div class="h-ico-btn" id="faq-notif-btn" onclick="openNotifs()" style="display:none">
                &#x1F514;<span class="notif-dot"></span>
            </div>
            <button class="h-btn-muted h-active" id="faq-h-faq-btn">FAQ</button>
            <button class="h-btn-o" id="faq-h-login-btn" onclick="closeFAQPage();openLg()">Anmelden</button>
            <button class="h-btn" id="faq-h-reg-btn" onclick="closeFAQPage();openLg('up')">Registrieren</button>
        </div>
    </header>

    <x-hero tags-id="faq-hero-tags" :member-count="$memberCount" :post-count="$counts['all']" :online-count="$onlineCount"/>

    <div style="position:relative;z-index:1">
        <div class="layout" style="position:relative;z-index:1">
            <aside class="lsb">
                <livewire:faq-question />
                <div class="sb-card" id="faq-fb-widget">
                    <div class="sb-hdg">Feedback</div>
                    <button class="fb-btn" onclick="closeFAQPage();openFeedback('idee')" style="margin-bottom:6px">&#x1F4A1; Idee oder Wunsch</button>
                    <button class="fb-btn fb-btn-red" onclick="closeFAQPage();openFeedback('problem')">&#x1F41B; Technisches Problem</button>
                </div>
            </aside>

            <main style="min-width:0">
                <div class="compose" style="margin-bottom:14px">
                    <div class="c-srch-row">
                        <div class="c-srch-wrap">
                            <input id="faq-srch" type="text" placeholder="FAQ durchsuchen..." oninput="filterFAQ()" class="c-srch-inp" style="padding-left:16px">
                        </div>
                    </div>
                    <div id="faq-tag-filter" style="display:flex;flex-wrap:wrap;gap:5px;margin-top:10px"></div>
                </div>
                <div id="faq-list"></div>
                <div id="faq-empty" style="display:none;text-align:center;padding:40px;color:var(--muted);font-size:14px">Keine Eintr&#xE4;ge gefunden &#x2014; stell deine Frage links!</div>
            </main>
        </div>
    </div>

    <x-footer />
</div>
