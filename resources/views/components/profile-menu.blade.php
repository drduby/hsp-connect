<div id="profile-menu" style="display:none;position:fixed;top:80px;right:clamp(14px,3vw,44px);width:210px;background:var(--surf);border:1px solid var(--bord2);border-radius:var(--rlg);box-shadow:var(--sh3);z-index:600;overflow:hidden">
    <div style="padding:13px 16px 10px;border-bottom:1px solid var(--bord)">
        <div id="profile-menu-name" style="font-family:var(--disp);font-size:15px;font-weight:700;color:var(--ink);letter-spacing:-.02em"></div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px">Angemeldet</div>
    </div>
    <div style="padding:5px">
        <button onclick="openAccountPage()" style="width:100%;padding:9px 12px;background:none;border:none;text-align:left;font-family:var(--body);font-size:13.5px;font-weight:600;color:var(--ink2);cursor:pointer;border-radius:8px;display:flex;align-items:center;gap:8px">&#x1F464; Konto</button>
        <div style="height:1px;background:var(--bord);margin:4px 0"></div>
        <button onclick="closeProfileMenu();doLogout()" style="width:100%;padding:9px 12px;background:none;border:none;text-align:left;font-family:var(--body);font-size:13.5px;font-weight:600;color:#c04040;cursor:pointer;border-radius:8px;display:flex;align-items:center;gap:8px">&#x21A6; Abmelden</button>
    </div>
</div>
