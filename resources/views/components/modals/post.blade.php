<div class="pmbg" id="pmbg" onclick="if(event.target===this)closePM()">
    <div class="pmodal">
        <button class="mc" onclick="closePM()">&#x2715;</button>
        <div style="font-family:var(--disp);font-size:20px;font-weight:800;color:var(--ink);margin-bottom:13px;letter-spacing:-.03em">Beitrag erstellen</div>
        <div style="font-family:var(--disp);font-size:13px;font-weight:700;color:var(--t);margin-bottom:13px;padding:6px 10px;background:var(--t3);border-radius:8px">&#x2728; Erfahrung teilen</div>
        <label>Thema</label>
        <select id="mod-tag"></select>
        <label>Titel</label>
        <input type="text" id="mod-h" placeholder="Worum geht es?">
        <label>Inhalt</label>
        <textarea id="mod-b" placeholder="Teile deine Gedanken..."></textarea>
        <button class="mbtn" onclick="savePost()">Ver&#xF6;ffentlichen</button>
    </div>
</div>
