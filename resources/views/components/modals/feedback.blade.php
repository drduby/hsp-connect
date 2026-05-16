<div class="mbg" id="fbmbg" onclick="if(event.target===this)closeFB()">
    <div class="modal">
        <button class="mc" onclick="closeFB()">&#x2715;</button>
        <div class="mttl" id="fb-ttl">Feedback</div>
        <div class="msub" id="fb-sub">Dein Feedback hilft uns.</div>
        <label>Thema</label>
        <input type="text" id="fb-topic" placeholder="z.B. Login, Suche, Beitr&#xE4;ge&#x2026;"
            style="width:100%;padding:10px 13px;border:1.5px solid rgba(10,110,122,.15);border-radius:10px;font-family:var(--body);font-size:14px;outline:none;color:var(--ink);background:var(--surf2);margin-bottom:12px;transition:border-color .18s"
            onfocus="fbFocus(this)" onblur="fbBlur(this)">
        <label>Beschreibung</label>
        <textarea id="fb-txt"
            style="width:100%;padding:10px 13px;border:1.5px solid rgba(10,110,122,.15);border-radius:10px;font-family:var(--body);font-size:13.5px;outline:none;color:var(--ink);background:var(--surf2);resize:vertical;min-height:90px;margin-bottom:12px;transition:border-color .18s"
            onfocus="fbFocus(this)" onblur="fbBlur(this)"
            placeholder="Beschreibe dein Feedback..."></textarea>
        <div id="fb-email-wrap">
            <label>E-Mail <span style="font-size:10.5px;color:var(--light);font-weight:400">(optional, f&#xFC;r R&#xFC;ckfragen)</span></label>
            <input type="email" id="fb-email" placeholder="deine@email.at"
                style="width:100%;padding:10px 13px;border:1.5px solid rgba(10,110,122,.15);border-radius:10px;font-family:var(--body);font-size:14px;outline:none;color:var(--ink);background:var(--surf2);margin-bottom:12px;transition:border-color .18s"
                onfocus="fbFocus(this)" onblur="fbBlur(this)">
        </div>
        <div id="fb-email-note" style="display:none;font-size:12px;color:var(--t);background:var(--t3);border-radius:8px;padding:8px 12px;margin-bottom:12px;font-weight:500"></div>
        <button class="mbtn" onclick="submitFeedback()">Feedback senden</button>
    </div>
</div>
