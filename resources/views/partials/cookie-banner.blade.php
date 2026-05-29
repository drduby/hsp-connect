<div id="cookie-banner" role="dialog" aria-label="{{ __('ui.cookie.title') }}">
    <div class="cb-text">
        <strong>{{ __('ui.cookie.title') }}</strong>
        {{ __('ui.cookie.text') }}
        <a href="#" onclick="event.preventDefault();openInfo('datenschutz')">{{ __('ui.cookie.privacy_link') }}</a>.
    </div>
    <div class="cb-btns">
        <button class="cb-btn-reject" onclick="cookieConsent('reject')">{{ __('ui.cookie.reject') }}</button>
        <button class="cb-btn-accept" onclick="cookieConsent('accept')">{{ __('ui.cookie.accept') }}</button>
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
