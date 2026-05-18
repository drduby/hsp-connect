import { state } from './state.js';
import { toggleTag } from './feed.js';
import { initHexBg } from './hexbg.js';

let _profileOpen = false;

document.addEventListener('app:open-profile-menu', function () { openProfileMenu(); });

export function openProfileMenu() {
  if (_profileOpen) { closeProfileMenu(); return; }
  _profileOpen = true;
  const m = document.getElementById('profile-menu');
  const nm = document.getElementById('profile-menu-name');
  if (nm && state.currentUser) nm.textContent = state.currentUser.name;
  m.style.display = 'block';
  setTimeout(function () { document.addEventListener('click', _outsideProfile); }, 10);
}

function _outsideProfile(e) {
  const m = document.getElementById('profile-menu');
  const lb = document.getElementById('h-login-btn');
  if (m && !m.contains(e.target) && e.target !== lb) { closeProfileMenu(); }
}

export function closeProfileMenu() {
  _profileOpen = false;
  const m = document.getElementById('profile-menu');
  if (m) m.style.display = 'none';
  document.removeEventListener('click', _outsideProfile);
}

export function openAccountPage() {
  closeProfileMenu();
  if (!state.loggedIn) return;
  const pg = document.getElementById('account-page');
  if (!pg) return;

  const av = document.getElementById('acc-avatar');
  const nm = document.getElementById('acc-name');
  if (av) av.textContent = state.currentUser.ava;
  if (nm) nm.textContent = state.currentUser.name;

  pg.style.display = 'block';
  initHexBg('hexbg3');

  const aht = document.getElementById('acc-hero-tags');
  if (aht && !aht.children.length) {
    const TN = window.__TAG_COUNTS__ || {};
    Object.entries(TN).sort(function (a, b) { return b[1] - a[1]; }).slice(0, 5).forEach(function ([t]) {
      const b = document.createElement('button');
      b.className = 'htag'; b.textContent = '# ' + t;
      b.onclick = function () { toggleTag(t); closeAccountPage(); };
      aht.appendChild(b);
    });
  }
}

export function closeAccountPage() {
  const pg = document.getElementById('account-page');
  if (pg) pg.style.display = 'none';
}
