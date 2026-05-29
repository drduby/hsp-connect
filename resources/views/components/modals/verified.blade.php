<div class="mbg" id="verified-modal" onclick="if(event.target===this)closeVerifiedModal()" style="display:none">
    <div class="modal" style="text-align:center;padding:40px 32px 32px">
        <button class="mc" onclick="closeVerifiedModal()">&#x2715;</button>
        <div style="font-size:56px;margin-bottom:18px;line-height:1">&#x2705;</div>
        <div class="mttl" style="margin-bottom:8px">{{ __('ui.verified.title') }}</div>
        <div class="msub" style="margin-bottom:20px">{{ __('ui.verified.sub') }}</div>
        <div style="font-size:13.5px;color:var(--muted);line-height:1.7;margin-bottom:28px">
            {{ __('ui.verified.text') }}<br>
            {{ __('ui.verified.text2') }}
        </div>
        <button class="mbtn" onclick="closeVerifiedModal();openLg()">{{ __('ui.verified.login_now') }}</button>
        <div class="mlink" style="margin-top:12px"><a onclick="closeVerifiedModal()">{{ __('ui.verified.login_later') }}</a></div>
    </div>
</div>
