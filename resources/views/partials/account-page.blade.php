<div id="account-page" style="display:none;position:fixed;inset:0;z-index:500;overflow-y:auto;background:var(--bg)">
    <canvas id="hexbg3" style="position:fixed;inset:0;z-index:0;pointer-events:none"></canvas>

    <header>
        <a class="logo" onclick="closeAccountPage()">
            <x-logo />
            <div class="logo-text">
                <div class="logo-name">HSP<span>Connect</span></div>
                <div class="logo-sub">Community</div>
            </div>
        </a>
        <div class="h-acts">
            <div class="h-ico-btn" id="accp-notif-btn" onclick="openNotifs()" style="display:none">
                &#x1F514;<span class="notif-dot"></span>
            </div>
            <button class="h-btn-muted" id="accp-home-btn" onclick="closeAccountPage()">Home</button>
            <a class="h-btn-muted" href="{{ route('faq') }}" wire:navigate>FAQ</a>
            <button class="h-btn-o" id="acc-h-login-btn" onclick="openLg()">Anmelden</button>
        </div>
    </header>

    <x-hero :member-count="$memberCount" :post-count="$counts['all']" :online-count="$onlineCount"/>

    <div style="position:relative;z-index:1;background:var(--bg)">
        <div style="max-width:600px;margin:0 auto;padding:24px clamp(16px,3vw,32px) 60px">
            <div style="background:var(--surf);border:1px solid var(--bord);border-radius:var(--rlg);padding:28px 24px;margin-bottom:16px;box-shadow:var(--sh);display:flex;align-items:center;gap:20px">
                <div id="acc-avatar" style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,var(--t),var(--g));display:flex;align-items:center;justify-content:center;color:#fff;font-family:var(--disp);font-size:26px;font-weight:800;flex-shrink:0"></div>
                <div>
                    <div id="acc-name" style="font-family:var(--disp);font-size:22px;font-weight:800;color:var(--ink);letter-spacing:-.03em"></div>
                    <div style="font-size:12.5px;color:var(--muted);margin-top:3px">Mitglied seit 2026</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:16px">
                <div class="sb-card" style="text-align:center;padding:16px 12px">
                    <div id="acc-posts" style="font-family:var(--disp);font-size:24px;font-weight:800;color:var(--t)">0</div>
                    <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-top:3px">Beitr&#xE4;ge</div>
                </div>
                <div class="sb-card" style="text-align:center;padding:16px 12px">
                    <div id="acc-saved-cnt" style="font-family:var(--disp);font-size:24px;font-weight:800;color:var(--t)">0</div>
                    <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-top:3px">Gespeichert</div>
                </div>
                <div class="sb-card" style="text-align:center;padding:16px 12px">
                    <div id="acc-likes" style="font-family:var(--disp);font-size:24px;font-weight:800;color:var(--t)">0</div>
                    <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-top:3px">Likes gegeben</div>
                </div>
            </div>

            @auth
                <livewire:profile-editor />
            @endauth

            <div class="sb-card" style="padding:6px">
                <button onclick="closeAccountPage();showMine()" style="width:100%;padding:11px 14px;background:none;border:none;text-align:left;font-family:var(--body);font-size:14px;font-weight:600;color:var(--ink2);cursor:pointer;border-radius:10px;display:flex;align-items:center;gap:10px">&#x270F;&#xFE0F; Meine Beitr&#xE4;ge</button>
                <button onclick="closeAccountPage();showSaved()" style="width:100%;padding:11px 14px;background:none;border:none;text-align:left;font-family:var(--body);font-size:14px;font-weight:600;color:var(--ink2);cursor:pointer;border-radius:10px;display:flex;align-items:center;gap:10px">&#x1F516; Gespeicherte Beitr&#xE4;ge</button>
                <div style="height:1px;background:var(--bord);margin:4px 8px"></div>
                <form method="POST" action="/logout" style="margin:0">
                    @csrf
                    <button type="submit" style="width:100%;padding:11px 14px;background:none;border:none;text-align:left;font-family:var(--body);font-size:14px;font-weight:600;color:#c04040;cursor:pointer;border-radius:10px;display:flex;align-items:center;gap:10px">&#x21A6; Abmelden</button>
                </form>
            </div>
        </div>
    </div>

    <x-footer />
</div>
