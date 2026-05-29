<div id="notif-panel" style="display:none;position:fixed;top:80px;right:clamp(14px,3vw,44px);width:320px;background:var(--surf);border:1px solid var(--bord2);border-radius:var(--rlg);box-shadow:var(--sh3);z-index:600;overflow:hidden;flex-direction:column;max-height:420px">
    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-bottom:1px solid var(--bord);flex-shrink:0;background:var(--surf);position:sticky;top:0">
        <span id="notif-unread-count" style="font-family:var(--disp);font-size:14px;font-weight:700;color:var(--ink)">{{ __('ui.notifications.title') }}</span>
        <button onclick="event.stopPropagation();deleteAllNotifs()" style="background:none;border:none;font-size:12px;color:var(--muted);cursor:pointer;font-family:var(--body);font-weight:600">{{ __('ui.notifications.delete_all') }}</button>
    </div>
    <div id="notif-list" style="overflow-y:auto"></div>
</div>
