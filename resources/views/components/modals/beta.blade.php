<div id="beta-modal-bg" class="mbg" style="z-index:9999;display:none">
    <div class="modal" style="max-width:480px;text-align:center">
        <div style="font-size:48px;margin-bottom:16px">🚧</div>
        <div class="mttl" style="font-size:20px">Diese Plattform befindet sich im Aufbau</div>
        <div class="msub" style="font-size:13.5px;margin-top:8px;line-height:1.7;color:var(--muted)">
            HSPConnect ist aktuell in der <strong style="color:var(--ink)">Testphase</strong>.
            Inhalte, Daten und Funktionen können sich noch ändern.
            Wir freuen uns über dein Interesse – schau gerne schon mal rein!
        </div>
        <div style="font-size:12px;color:var(--light);margin-bottom:20px">
            This platform is currently in a <strong>beta / test phase</strong>. Things may change.
        </div>
        <button class="mbtn" onclick="closeBetaModal()" style="max-width:240px;margin:0 auto">
            Alles klar, weiter geht's!
        </button>
    </div>
</div>

<script>
(function () {
    if (!localStorage.getItem('hsp_beta_seen')) {
        document.getElementById('beta-modal-bg').style.display = 'flex';
    }
    window.closeBetaModal = function () {
        localStorage.setItem('hsp_beta_seen', '1');
        document.getElementById('beta-modal-bg').style.display = 'none';
    };
})();
</script>
