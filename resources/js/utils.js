import { state } from './state.js';

export function toast(msg) {
  const c = document.getElementById('toasts');
  const t = document.createElement('div');
  t.className = 'toast';
  t.textContent = msg;
  c.appendChild(t);
  setTimeout(function () {
    t.classList.add('out');
    setTimeout(function () { t.remove(); }, 250);
  }, 2400);
}

/* Fires a DOM event so auth.js can open the login modal without a circular import */
export function requireAuth(msg) {
  if (state.loggedIn) return true;
  toast(msg || '🔒 Bitte erst anmelden!');
  document.dispatchEvent(new CustomEvent('app:require-login'));
  return false;
}

export function checkAuthThen(fn) {
  if (!requireAuth()) return;
  if (typeof fn === 'function') fn();
}
