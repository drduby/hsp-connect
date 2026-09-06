import { state } from './state.js';
import { toast } from './utils.js';

export const INFO_CONTENT = {};

export function openPM(t) {
  const typeMap = { 'Erfahrung': 'experience', 'Frage': 'question' };
  const type = typeMap[t] || t || 'experience';
  window.dispatchEvent(new CustomEvent('open-create-post', { detail: { type } }));
  document.getElementById('pmbg').classList.add('on');
}

export function closePM() {
  document.getElementById('pmbg').classList.remove('on');
}

export function openFeedback(type) {
  const mapped = type === 'idee' ? 'idea' : 'bug';
  if (typeof Livewire !== 'undefined') {
    Livewire.dispatch('open-feedback', { type: mapped });
  }
}

export function closeFB() {
  // handled by Livewire component
}

export function submitFeedback() {
  // handled by Livewire
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
  const content = (window.__LEGAL__ && window.__LEGAL__[key]) ? window.__LEGAL__[key] : INFO_CONTENT[key];
  if (!content) return;
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
