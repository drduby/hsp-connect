import { state } from './state.js';
import { toast } from './utils.js';

export const INFO_CONTENT = {
  impressum: { title: 'Impressum', body: '<h3>Angaben gemäß § 5 ECG (Österreich)</h3><p><strong>HSPConnect</strong><br>Musterstraße 1<br>1010 Wien, Österreich</p><p>E-Mail: hallo@hspconnect.at</p><h3>Haftungsausschluss</h3><p>Alle Inhalte werden von Mitgliedern erstellt und stellen keine medizinischen Empfehlungen dar.</p>' },
  datenschutz: { title: 'Datenschutzerklärung', body: '<h3>Verantwortliche Stelle</h3><p>HSPConnect — hallo@hspconnect.at</p><h3>Erhobene Daten</h3><ul><li>Name, E-Mail, Passwort (verschlüsselt)</li><li>Beiträge, Kommentare, Bewertungen</li></ul><h3>Deine Rechte (DSGVO)</h3><ul><li>Auskunft, Berichtigung, Löschung</li><li>Datenportabilität</li></ul><p>Anfragen: hallo@hspconnect.at</p><h3>Cookies</h3><p>Nur technisch notwendige Session-Cookies. Kein Tracking.</p>' },
  nutzung: { title: 'Nutzungsbedingungen', body: '<h3>Geltungsbereich</h3><p>Mit der Registrierung stimmst du diesen Bedingungen zu.</p><h3>Verbotene Inhalte</h3><ul><li>Medizinische Empfehlungen ohne Qualifikation</li><li>Beleidigungen, Diskriminierung</li><li>Spam und Werbung</li></ul><div class="warn-box"><strong>⚕️ Wichtig:</strong> HSPConnect ersetzt keinen Arztbesuch.</div>' },
  regeln: { title: 'Community-Regeln', body: '<h3>Grundwerte</h3><p>Respekt, Verständnis und Unterstützung.</p><div class="warn-box"><strong>⚕️ Medizinische Inhalte:</strong> Nur eigene Erfahrungen teilen. "Bei mir hat X geholfen" ist erlaubt. Diagnosen oder Heilungsversprechen sind verboten.</div><h3>✅ Erwünscht</h3><ul><li>Eigene Erfahrungen teilen</li><li>Fragen stellen</li><li>Andere unterstützen</li></ul><h3>🚫 Verboten</h3><ul><li>Medizinische Empfehlungen ohne Qualifikation</li><li>Beleidigungen und Diskriminierung</li><li>Spam und Werbung</li></ul>' },
  faq: { title: 'FAQ', body: '<h3>Was ist HSPConnect?</h3><p>Community für Menschen mit HSP und Spastik.</p><h3>Kostenlos?</h3><p>Ja, vollständig kostenlos.</p><h3>Inhalte medizinisch geprüft?</h3><p>Nein — persönliche Erfahrungsberichte, kein Arztbesatz.</p><h3>Konto löschen?</h3><p>E-Mail an hallo@hspconnect.at.</p>' },
  kontakt: { title: 'Kontakt', body: '<h3>Schreib uns</h3><p><strong>E-Mail:</strong> hallo@hspconnect.at</p><h3>Reaktionszeit</h3><p>2–3 Werktage.</p>' },
};

export function openPM(t) {
  const typeMap = { 'Erfahrung': 'experience', 'Frage': 'question' };
  const type = typeMap[t] || t || 'experience';
  window.dispatchEvent(new CustomEvent('open-create-post', { detail: { type } }));
  document.getElementById('pmbg').classList.add('on');
}

export function closePM() {
  document.getElementById('pmbg').classList.remove('on');
}

let _fbType = '';

export function openFeedback(type) {
  _fbType = type;
  document.getElementById('fb-ttl').textContent = type === 'idee' ? '💡 Idee oder Wunsch' : '🐛 Technisches Problem';
  document.getElementById('fb-sub').textContent = type === 'idee' ? 'Was würdest du dir wünschen?' : 'Was funktioniert nicht?';
  document.getElementById('fb-txt').value = '';
  document.getElementById('fb-topic').value = '';
  const emailWrap = document.getElementById('fb-email-wrap');
  const emailNote = document.getElementById('fb-email-note');
  if (emailWrap) emailWrap.style.display = state.loggedIn ? 'none' : 'block';
  if (emailNote) {
    emailNote.style.display = state.loggedIn ? 'block' : 'none';
    if (state.currentUser) emailNote.textContent = 'Antwort geht an: ' + state.currentUser.name + '.';
  }
  document.getElementById('fbmbg').classList.add('on');
}

export function closeFB() {
  document.getElementById('fbmbg').classList.remove('on');
}

export function submitFeedback() {
  const txt = document.getElementById('fb-txt').value.trim();
  if (!txt) { toast('⚠️ Bitte Beschreibung eingeben'); return; }
  closeFB();
  const tp = document.getElementById('fb-topic'); if (tp) tp.value = '';
  document.getElementById('fb-txt').value = '';
  const fe = document.getElementById('fb-email'); if (fe) fe.value = '';
  toast(_fbType === 'idee' ? '💡 Danke für deine Idee!' : '🐛 Problem gemeldet — danke!');
}

let _confirmCallback = null;

export function openConfirm(title, message, callback) {
  _confirmCallback = callback || null;
  document.getElementById('confirm-modal-title').textContent = title;
  document.getElementById('confirm-modal-msg').textContent = message;
  document.getElementById('confirm-modal-bg').style.display = 'flex';
}

export function closeConfirm() {
  _confirmCallback = null;
  document.getElementById('confirm-modal-bg').style.display = 'none';
}

export function doConfirm() {
  const cb = _confirmCallback;
  closeConfirm();
  if (cb) cb();
}

export function fbFocus(el) { el.style.borderColor = 'var(--t)'; }
export function fbBlur(el) { el.style.borderColor = 'rgba(10,110,122,.15)'; }

let _savedScroll = 0;

export function openInfo(key) {
  const content = INFO_CONTENT[key]; if (!content) return;
  _savedScroll = window.scrollY;
  document.getElementById('info-title').textContent = content.title;
  document.getElementById('info-body').innerHTML = content.body;
  document.getElementById('mbg-info').classList.add('on');
  document.getElementById('info-body').scrollTop = 0;
}

export function closeInfo() {
  document.getElementById('mbg-info').classList.remove('on');
  setTimeout(function () { window.scrollTo({ top: _savedScroll, behavior: 'instant' }); }, 50);
}
