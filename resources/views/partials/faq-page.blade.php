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
                <div class="sb-card">
                    <div class="sb-hdg">Frage stellen</div>
                    <div id="faq-ask-form">
                        <p id="faq-intro-text" style="font-size:12.5px;color:var(--muted);line-height:1.65;font-weight:300;margin-bottom:12px">Frage nicht gefunden? Stell sie hier &#x2014; die Community hilft!</p>
                        <div id="faq-fields" style="display:none">
                            <label style="font-size:11.5px;font-weight:700;color:var(--ink2);display:block;margin-bottom:6px">Thema</label>
                            <div id="topic-chips" style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:8px"></div>
                            <input type="text" id="faq-q-topic" placeholder="oder freies Thema..."
                                style="width:100%;padding:8px 12px;border:1.5px solid var(--bord2);border-radius:10px;font-family:var(--body);font-size:13px;outline:none;color:var(--ink);background:var(--surf2);margin-bottom:8px;display:block">
                            <label style="font-size:11.5px;font-weight:700;color:var(--ink2);display:block;margin-bottom:4px">Deine Frage</label>
                            <textarea id="faq-q-txt" placeholder="Was m&#xF6;chtest du wissen?"
                                style="width:100%;padding:8px 12px;border:1.5px solid var(--bord2);border-radius:10px;font-family:var(--body);font-size:13px;outline:none;color:var(--ink);background:var(--surf2);resize:none;height:80px;margin-bottom:10px;display:block"></textarea>
                        </div>
                        <button onclick="showFAQForm()" style="width:100%;background:var(--t);color:#fff;border:none;padding:9px;border-radius:40px;font-family:var(--body);font-size:13px;font-weight:700;cursor:pointer">Frage einreichen &#x2192;</button>
                    </div>
                    <div id="faq-ask-thanks" style="display:none;text-align:center;padding:12px 0">
                        <div style="font-size:22px;margin-bottom:8px">&#x1F64F;</div>
                        <div style="font-family:var(--disp);font-size:14px;font-weight:700;color:var(--t);margin-bottom:6px">Danke f&#xFC;r deine Frage!</div>
                        <div style="font-size:12.5px;color:var(--muted);line-height:1.65;font-weight:300">Wir hoffen, die Community kann dir helfen.</div>
                    </div>
                </div>
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
