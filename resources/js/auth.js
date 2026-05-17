import { state } from './state.js';
import { toast } from './utils.js';
import { applyPostState } from './posts.js';

/* Open login when any module fires this event (avoids circular imports) */
document.addEventListener('app:require-login', function () { openLg(); });

export function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

export async function authFetch(url, body) {
  const res = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
    },
    body: JSON.stringify(body),
  });
  const isJson = res.headers.get('content-type')?.includes('application/json');
  const data = isJson ? await res.json() : {};
  return { ok: res.ok, status: res.status, data };
}

function authError(data) {
  if (data.errors) return Object.values(data.errors)[0]?.[0] || 'Fehler';
  return data.message || 'Fehler';
}

export function setLoggedInUI() {
  const ca = document.getElementById('compose-ava');
  ca.textContent = state.currentUser.ava; ca.style.display = 'flex';
  const wt = document.getElementById('compose-welcome-title');
  if (wt) wt.textContent = 'Willkommen zurück, ' + state.currentUser.name + '!';
  const hlb = document.getElementById('h-login-btn');
  if (hlb) { hlb.textContent = state.currentUser.name; hlb.onclick = function () { document.dispatchEvent(new CustomEvent('app:open-profile-menu')); }; }
  const hrb = document.getElementById('h-reg-btn');
  if (hrb) { hrb.textContent = 'Abmelden'; hrb.onclick = doLogout; hrb.style.display = ''; }
  const an = document.getElementById('auth-nav'); if (an) an.style.cssText = 'display:block !important';
  const nb = document.getElementById('notif-btn'); if (nb) nb.style.display = 'flex';
  const flb = document.getElementById('faq-h-login-btn');
  if (flb) { flb.textContent = state.currentUser.name; flb.onclick = function () { document.dispatchEvent(new CustomEvent('app:open-profile-menu')); }; }
  const frb = document.getElementById('faq-h-reg-btn'); if (frb) { frb.textContent = 'Abmelden'; frb.onclick = doLogout; }
  const alb = document.getElementById('acc-h-login-btn');
  if (alb) { alb.textContent = state.currentUser.name; alb.onclick = function () { document.dispatchEvent(new CustomEvent('app:open-profile-menu')); }; }
  const arb = document.getElementById('acc-h-reg-btn'); if (arb) { arb.textContent = 'Abmelden'; arb.onclick = doLogout; }
  const fnb = document.getElementById('faq-notif-btn'); if (fnb) fnb.style.display = 'flex';
  const anb = document.getElementById('accp-notif-btn'); if (anb) anb.style.display = 'flex';
}

/* ── Login modal ── */

export function openLg(tab) {
  document.getElementById('lf-in').style.display = '';
  document.getElementById('lf-up').style.display = 'none';
  document.getElementById('lf-reset').style.display = 'none';
  document.getElementById('lg-title').textContent = 'Willkommen zurück';
  document.getElementById('lg-sub').textContent = 'Melde dich an oder erstelle ein Konto';
  document.getElementById('lmbg').classList.add('on');
  if (tab) setLT(tab);
}

export function closeLg() {
  document.getElementById('lmbg').classList.remove('on');
  setTimeout(function () { setLT('in'); }, 300);
}

export function setLT(t) {
  document.getElementById('lf-in').style.display = t === 'in' ? '' : 'none';
  document.getElementById('lf-up').style.display = t === 'up' ? '' : 'none';
  document.getElementById('lf-reset').style.display = t === 'reset' ? '' : 'none';
  document.getElementById('lg-title').textContent = t === 'in' ? 'Willkommen zurück' : (t === 'up' ? 'Konto erstellen' : 'Passwort zurücksetzen');
  document.getElementById('lg-sub').textContent = t === 'in' ? 'Schön, dass du wieder da bist!' : (t === 'up' ? 'Werde Teil der Community' : 'Wir senden dir einen Link per E-Mail');
  if (t === 'reset') {
    document.getElementById('reset-em').value = document.getElementById('l-em').value.trim();
  }
  setLoginError('');
  setResetMessage('');
  setRegErrors(null);
}

export function setLoginError(msg) {
  const el = document.getElementById('login-error');
  if (!el) return;
  el.textContent = msg;
  el.style.display = msg ? '' : 'none';
}

export function setRegErrors(errors) {
  ['first_name', 'last_name', 'nickname', 'email', 'password', 'password_confirmation'].forEach(function (field) {
    const el = document.getElementById('reg-err-' + field);
    if (el) el.textContent = (errors && errors[field]) ? errors[field][0] : '';
  });
}

export function setResetMessage(msg, type = 'error') {
  const el = document.getElementById('reset-message');
  if (!el) return;
  el.textContent = msg;
  el.style.display = msg ? '' : 'none';
  el.style.color = type === 'success' ? 'var(--t)' : '#c04040';
  el.style.background = type === 'success' ? 'var(--t3)' : 'rgba(192,64,64,.08)';
}

export function updatePwChecklist() {
  const pw = document.getElementById('r-pw')?.value || '';
  const list = document.getElementById('pw-checklist');
  if (list) list.style.display = pw.length ? 'flex' : 'none';
  function mark(id, ok, text) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = (ok ? '✓ ' : '✗ ') + text;
    el.style.color = ok ? 'var(--t)' : 'var(--light)';
  }
  mark('pwc-len', pw.length >= 8, 'Mindestens 8 Zeichen');
  mark('pwc-letter', /[a-zA-ZäöüÄÖÜß]/.test(pw), 'Mindestens 1 Buchstabe');
  mark('pwc-num', /[0-9]/.test(pw), 'Mindestens 1 Zahl');
}

export async function doLogin() {
  const email = document.getElementById('l-em').value.trim();
  const password = document.getElementById('l-pw').value;
  setLoginError('');
  if (!email || !password) { setLoginError('Bitte E-Mail und Passwort eingeben.'); return; }
  try {
    const { ok, data } = await authFetch('/login', { email, password });
    if (ok) {
      closeLg();
      state.loggedIn = true;
      state.currentUser = data.user;
      setLoggedInUI();
      toast('✅ Willkommen zurück, ' + state.currentUser.name + '!');
      state.posts.forEach(applyPostState);
    } else {
      setLoginError(authError(data));
    }
  } catch (e) {
    setLoginError('Verbindungsfehler. Bitte erneut versuchen.');
  }
}

export async function doReg() {
  const fn = document.getElementById('r-fn').value.trim();
  const ln = document.getElementById('r-ln').value.trim();
  const nick = document.getElementById('r-nick').value.trim();
  const em = document.getElementById('r-em').value.trim();
  const pw = document.getElementById('r-pw').value;
  const pw2 = document.getElementById('r-pw2').value;
  setRegErrors(null);
  const clientErrors = {};
  if (!fn) clientErrors.first_name = ['Bitte Vorname eingeben.'];
  if (!ln) clientErrors.last_name = ['Bitte Nachname eingeben.'];
  if (!nick) clientErrors.nickname = ['Bitte Nickname eingeben.'];
  if (!em) clientErrors.email = ['Bitte E-Mail eingeben.'];
  if (!pw) clientErrors.password = ['Bitte Passwort eingeben.'];
  else if (pw.length < 8) clientErrors.password = ['Passwort mind. 8 Zeichen.'];
  else if (!/[a-zA-ZäöüÄÖÜß]/.test(pw)) clientErrors.password = ['Passwort muss mind. 1 Buchstaben enthalten.'];
  else if (!/[0-9]/.test(pw)) clientErrors.password = ['Passwort muss mind. 1 Zahl enthalten.'];
  if (pw && pw2 && pw !== pw2) clientErrors.password_confirmation = ['Passwörter stimmen nicht überein.'];
  if (Object.keys(clientErrors).length) { setRegErrors(clientErrors); return; }
  try {
    const { ok, data } = await authFetch('/register', {
      first_name: fn, last_name: ln, nickname: nick,
      email: em, password: pw, password_confirmation: pw2,
    });
    if (ok) {
      closeLg();
      state.loggedIn = true;
      state.currentUser = data.user;
      setLoggedInUI();
      toast('🎉 Willkommen, ' + nick + '!');
      state.posts.forEach(applyPostState);
    } else {
      setRegErrors(data.errors || null);
    }
  } catch (e) {
    setRegErrors({ first_name: ['Verbindungsfehler. Bitte erneut versuchen.'] });
  }
}

export async function doPasswordResetLink() {
  const email = document.getElementById('reset-em').value.trim();
  setResetMessage('');
  if (!email) { setResetMessage('Bitte E-Mail eingeben.'); return; }
  try {
    const { ok, data } = await authFetch('/forgot-password', { email });
    if (ok) {
      setResetMessage(data.status || 'Wenn ein Konto existiert, senden wir dir einen Link zum Zurücksetzen.', 'success');
    } else {
      setResetMessage(authError(data));
    }
  } catch (e) {
    setResetMessage('Verbindungsfehler. Bitte erneut versuchen.');
  }
}

export function doSocialLogin() {
  toast('🚧 Social Login kommt bald!');
}

export async function doLogout() {
  try { await authFetch('/logout', {}); } catch (e) {}
  window.location.href = '/';
}
