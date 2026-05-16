<div class="pmbg" id="pmbg" onclick="if(event.target===this)closePM()">
    <div class="pmodal">
        <button class="mc" onclick="closePM()">&#x2715;</button>
        <div
            style="font-family:var(--disp);font-size:20px;font-weight:800;color:var(--ink);margin-bottom:13px;letter-spacing:-.03em">
            Beitrag erstellen
        </div>
        <livewire:create-post />
    </div>
</div>
