import { state } from './state.js';

/* Open login when any module fires this event (avoids circular imports) */
document.addEventListener('app:require-login', function () { openLg(); });

export function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

export function openLg(tab) {
  Livewire.dispatch('open-auth-modal', { tab: tab === 'up' ? 'register' : (tab || 'login') });
}

export function closeLg() {
  Livewire.dispatch('open-auth-modal', { tab: 'login' });
}

export function doSocialLogin() {
  if (typeof toast === 'function') toast('🚧 Social Login kommt bald!');
}

export function setLoggedInUI() {
  const ca = document.getElementById('compose-ava');
  if (ca) { ca.textContent = state.currentUser.ava; ca.style.display = 'flex'; }
  const wt = document.getElementById('compose-welcome-title');
  if (wt) wt.textContent = 'Willkommen zurück, ' + state.currentUser.name + '!';
  const hlb = document.getElementById('h-login-btn');
  if (hlb) { hlb.textContent = state.currentUser.name; hlb.onclick = function () { document.dispatchEvent(new CustomEvent('app:open-profile-menu')); }; }
  const hrb = document.getElementById('h-reg-btn');
  if (hrb) hrb.style.display = 'none';
  const an = document.getElementById('auth-nav'); if (an) an.style.cssText = 'display:block !important';
  const nb = document.getElementById('notif-btn'); if (nb) nb.style.display = 'flex';
  const flb = document.getElementById('faq-h-login-btn');
  if (flb) { flb.textContent = state.currentUser.name; flb.onclick = function () { document.dispatchEvent(new CustomEvent('app:open-profile-menu')); }; }
  const frb = document.getElementById('faq-h-reg-btn'); if (frb) frb.style.display = 'none';
  const alb = document.getElementById('acc-h-login-btn');
  if (alb) { alb.textContent = state.currentUser.name; alb.onclick = function () { document.dispatchEvent(new CustomEvent('app:open-profile-menu')); }; }
  const fnb = document.getElementById('faq-notif-btn'); if (fnb) fnb.style.display = 'flex';
  const anb = document.getElementById('accp-notif-btn'); if (anb) anb.style.display = 'flex';
}
