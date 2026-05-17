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
            <div style="position:relative;margin-bottom:12px">
                <input type="password" id="l-pw" placeholder="&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;" style="padding-right:42px;width:100%;margin-bottom:0">
                <button type="button" onclick="togglePw('l-pw',this)" tabindex="-1"
                    style="position:absolute;right:12px;top:0;bottom:0;margin:auto 0;height:18px;background:none;border:none;cursor:pointer;color:var(--light);padding:0;display:flex;align-items:center">
                    <svg id="l-pw-eye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg id="l-pw-eye-off" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
            <div class="mlink mlink-tight">Passwort vergessen? <a onclick="setLT('reset')">Reset password</a></div>
            <div id="login-error" style="display:none;color:#c04040;font-size:12.5px;margin:-4px 0 10px;padding:8px 12px;background:rgba(192,64,64,.08);border-radius:8px;line-height:1.5"></div>
            <button class="mbtn" onclick="doLogin()">Anmelden</button>
            <div class="mlink">Noch kein Konto? <a onclick="setLT('up')">Registrieren</a></div>
        </div>
        <div id="lf-up" style="display:none">
            {{-- Vorname + Nachname --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 12px">
                <div>
                    <label>Vorname</label>
                    <input type="text" id="r-fn" placeholder="z.B. Maria">
                    <div id="reg-err-first_name" class="field-error"></div>
                </div>
                <div>
                    <label>Nachname</label>
                    <input type="text" id="r-ln" placeholder="z.B. Schmidt">
                    <div id="reg-err-last_name" class="field-error"></div>
                </div>
            </div>

            <label>Nickname <span style="font-size:11px;color:var(--light);font-weight:400">(sichtbar f&#xFC;r alle)</span></label>
            <input type="text" id="r-nick" placeholder="z.B. spastik_warrior" maxlength="30">
            <div style="font-size:11px;color:var(--light);margin:-8px 0 4px">Nur Buchstaben, Zahlen und _ erlaubt.</div>
            <div id="reg-err-nickname" class="field-error"></div>

            <label>E-Mail</label>
            <input type="email" id="r-em" placeholder="deine@email.at">
            <div id="reg-err-email" class="field-error"></div>

            {{-- Passwort + Passwort wiederholen --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 12px;align-items:start">
                <div>
                    <label>Passwort</label>
                    <div style="position:relative;margin-bottom:12px">
                        <input type="password" id="r-pw" placeholder="Min. 8 Zeichen" style="padding-right:36px;width:100%;margin-bottom:0" oninput="updatePwChecklist()">
                        <button type="button" onclick="togglePw('r-pw',this)" tabindex="-1"
                            style="position:absolute;right:10px;top:0;bottom:0;margin:auto 0;height:18px;background:none;border:none;cursor:pointer;color:var(--light);padding:0;display:flex;align-items:center">
                            <svg id="r-pw-eye" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="r-pw-eye-off" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                    <div id="pw-checklist" style="font-size:11px;margin:-4px 0 8px;display:none;flex-direction:column;gap:2px">
                        <span id="pwc-len" style="color:var(--light)">✗ Min. 8 Zeichen</span>
                        <span id="pwc-letter" style="color:var(--light)">✗ 1 Buchstabe</span>
                        <span id="pwc-num" style="color:var(--light)">✗ 1 Zahl</span>
                    </div>
                    <div id="reg-err-password" class="field-error"></div>
                </div>
                <div>
                    <label>Wiederholen</label>
                    <div style="position:relative;margin-bottom:12px">
                        <input type="password" id="r-pw2" placeholder="Min. 8 Zeichen" style="padding-right:36px;width:100%;margin-bottom:0">
                        <button type="button" onclick="togglePw('r-pw2',this)" tabindex="-1"
                            style="position:absolute;right:10px;top:0;bottom:0;margin:auto 0;height:18px;background:none;border:none;cursor:pointer;color:var(--light);padding:0;display:flex;align-items:center">
                            <svg id="r-pw2-eye" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="r-pw2-eye-off" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                    <div id="reg-err-password_confirmation" class="field-error"></div>
                </div>
            </div>

            <button class="mbtn" onclick="doReg()">Konto erstellen</button>
            <div class="mlink">Bereits registriert? <a onclick="setLT('in')">Anmelden</a></div>
        </div>
        <div id="lf-reset" style="display:none">
            <label>E-Mail</label>
            <input type="email" id="reset-em" placeholder="deine@email.at">
            <div id="reset-message" style="display:none;font-size:12.5px;margin:-4px 0 10px;padding:8px 12px;border-radius:8px;line-height:1.5"></div>
            <button class="mbtn" onclick="doPasswordResetLink()">Reset password</button>
            <div class="mlink">Zur&#xFC;ck zum Login? <a onclick="setLT('in')">Anmelden</a></div>
        </div>
    </div>
</div>
