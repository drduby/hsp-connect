<div id="cookie-banner" role="dialog" aria-label="Cookie-Einstellungen">
    <div class="cb-text">
        <strong>&#x1F36A; Hinweis zu Cookies</strong>
        Wir verwenden Cookies, um die Nutzung der Seite zu verbessern und anonyme Statistiken zu erheben.
        Weitere Informationen findest du in unserer
        <a href="#" onclick="event.preventDefault();openInfo('datenschutz')">Datenschutzerkl&#xE4;rung</a>.
    </div>
    <div class="cb-btns">
        <button class="cb-btn-reject" onclick="cookieConsent('reject')">Ablehnen</button>
        <button class="cb-btn-accept" onclick="cookieConsent('accept')">Alle akzeptieren</button>
    </div>
</div>

<script>
(function () {
    if (!localStorage.getItem('hsp_cookie_consent')) {
        requestAnimationFrame(function () {
            setTimeout(function () {
                var b = document.getElementById('cookie-banner');
                if (b) b.classList.add('cb-show');
            }, 600);
        });
    }

    window.cookieConsent = function (choice) {
        localStorage.setItem('hsp_cookie_consent', choice);
        var b = document.getElementById('cookie-banner');
        if (b) {
            b.style.transition = 'transform .3s cubic-bezier(.55,0,1,.45)';
            b.classList.remove('cb-show');
        }
    };
})();
</script>
