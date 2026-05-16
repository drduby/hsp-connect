<div class="mbg" id="lmbg" onclick="if(event.target===this)closeLg()">
    <div class="modal">
        <button class="mc" onclick="closeLg()">&#x2715;</button>
        <div class="mttl" id="lg-title">Willkommen zur&#xFC;ck</div>
        <div class="msub" id="lg-sub">Melde dich an oder erstelle ein Konto</div>
        <div id="lt-in" style="display:none"></div>
        <div id="lt-up" style="display:none"></div>
        <div id="lf-in">
            <label>E-Mail</label>
            <input type="email" id="l-em" placeholder="deine@email.at">
            <label>Passwort</label>
            <input type="password" id="l-pw" placeholder="&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;">
            <div id="login-error" style="display:none;color:#c04040;font-size:12.5px;margin:-4px 0 10px;padding:8px 12px;background:rgba(192,64,64,.08);border-radius:8px;line-height:1.5"></div>
            <button class="mbtn" onclick="doLogin()">Anmelden</button>
            <div class="mlink">Noch kein Konto? <a onclick="setLT('up')">Registrieren</a></div>
        </div>
        <div id="lf-up" style="display:none">
            <label>Vorname</label>
            <input type="text" id="r-fn" placeholder="z.B. Maria">
            <div id="reg-err-first_name" class="field-error"></div>
            <label>Nachname</label>
            <input type="text" id="r-ln" placeholder="z.B. Schmidt">
            <div id="reg-err-last_name" class="field-error"></div>
            <label>Nickname <span style="font-size:11px;color:var(--light);font-weight:400">(sichtbar f&#xFC;r alle)</span></label>
            <input type="text" id="r-nick" placeholder="z.B. spastik_warrior" maxlength="30">
            <div style="font-size:11px;color:var(--light);margin:-8px 0 4px">Nur Buchstaben, Zahlen und _ erlaubt.</div>
            <div id="reg-err-nickname" class="field-error"></div>
            <label>E-Mail</label>
            <input type="email" id="r-em" placeholder="deine@email.at">
            <div id="reg-err-email" class="field-error"></div>
            <label>Passwort</label>
            <input type="password" id="r-pw" placeholder="Mindestens 8 Zeichen">
            <div id="reg-err-password" class="field-error"></div>
            <label>Passwort wiederholen</label>
            <input type="password" id="r-pw2" placeholder="Mindestens 8 Zeichen">
            <div id="reg-err-password_confirmation" class="field-error"></div>
            <button class="mbtn" onclick="doReg()">Konto erstellen</button>
            <div class="mlink">Bereits registriert? <a onclick="setLT('in')">Anmelden</a></div>
        </div>
    </div>
</div>
