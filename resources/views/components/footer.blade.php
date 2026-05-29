<footer>
    <div class="fg">
        <div>
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:12px">
                <x-logo variant="footer" />
                <div style="display:flex;flex-direction:column;line-height:1.15">
                    <span style="font-family:var(--disp);font-size:23px;font-weight:800;color:#fff;letter-spacing:-.03em">HSP<span style="color:rgba(184,118,42,.9)">Connect</span></span>
                    <span style="font-size:9px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.45)">Community</span>
                </div>
            </div>
            <p class="fd">{{ __('ui.footer.tagline') }}</p>
        </div>
        <div>
            <div class="fh">{{ __('ui.footer.legal') }}</div>
            <a class="fa" onclick="openInfo('impressum')">{{ __('ui.footer.imprint') }}</a>
            <a class="fa" onclick="openInfo('datenschutz')">{{ __('ui.footer.privacy') }}</a>
            <a class="fa" onclick="openInfo('nutzung')">{{ __('ui.footer.terms') }}</a>
        </div>
        <div>
            <div class="fh">{{ __('ui.footer.support') }}</div>
            <a class="fa" href="{{ route('faq') }}" wire:navigate>{{ __('ui.nav.faq') }}</a>
            <a class="fa" onclick="openInfo('regeln')">{{ __('ui.footer.rules') }}</a>
            <a class="fa" onclick="openInfo('kontakt')">{{ __('ui.footer.contact') }}</a>
        </div>
    </div>
    <div class="fbot">
        <span>{{ __('ui.footer.copyright') }}</span>
    </div>
</footer>
