import { csrfToken } from './auth.js';

let NOTIFS = [];

function getNotifIcon(icon) {
  return icon === 'LOGO'
    ? '<svg width="32" height="32" viewBox="0 0 40 40" fill="none"><rect x="1" y="1" width="38" height="38" rx="10" fill="rgba(10,110,122,.12)"/><circle cx="13" cy="11" r="4" fill="#0a6e7a"/><path d="M10 15.5 Q10 20 10 23 L16 23 Q16 20 16 15.5Z" fill="#0a6e7a" opacity=".9"/><circle cx="27" cy="11" r="4" fill="#b8762a"/><path d="M24 15.5 Q24 20 24 23 L30 23 Q30 20 30 15.5Z" fill="#b8762a" opacity=".9"/><path d="M16 17 Q20 13 20 13" stroke="#0a6e7a" stroke-width="2" stroke-linecap="round" fill="none"/><path d="M24 17 Q20 13 20 13" stroke="#b8762a" stroke-width="2" stroke-linecap="round" fill="none"/><path d="M20 13 C20 13 18 10.5 18 9 C18 7.5 19 6.5 20 7.5 C21 6.5 22 7.5 22 9 C22 10.5 20 13 20 13Z" fill="#b8762a"/></svg>'
    : icon;
}

function updateNotifDot() {
  const hasUnread = NOTIFS.some(function (n) { return !n.read; });
  ['notif-btn', 'faq-notif-btn', 'accp-notif-btn'].forEach(function (id) {
    const el = document.getElementById(id);
    if (!el) { return; }
    const dot = el.querySelector('.notif-dot');
    if (dot) { dot.style.display = hasUnread ? 'block' : 'none'; }
  });
}

export function initNotifDot() {
  const count = window.__NOTIF_COUNT__ || 0;
  ['notif-btn', 'faq-notif-btn', 'accp-notif-btn'].forEach(function (id) {
    const el = document.getElementById(id);
    if (!el) { return; }
    const dot = el.querySelector('.notif-dot');
    if (dot) { dot.style.display = count > 0 ? 'block' : 'none'; }
  });
}

async function fetchNotifs() {
  try {
    const res = await fetch('/notifications', {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });
    if (res.ok) {
      NOTIFS = await res.json();
    }
  } catch (_) {
    // ignore fetch errors
  }
}

export function renderNotifList() {
  const list = document.getElementById('notif-list');
  const uc = document.getElementById('notif-unread-count');
  if (!list) { return; }
  const unread = NOTIFS.filter(function (n) { return !n.read; }).length;
  const t = window.__TRANS__ || {};
  if (uc) { uc.textContent = unread > 0 ? (t.unread || ':count ungelesen').replace(':count', unread) : (t.notificationsTitle || 'Benachrichtigungen'); }
  if (!NOTIFS.length) {
    const t = window.__TRANS__ || {};
    list.innerHTML = '<div style="padding:28px 16px 20px;text-align:center">'
      + '<div style="color:var(--light);font-size:13.5px;font-style:italic;margin-bottom:14px">' + (t.noNotifications || 'Keine Benachrichtigungen') + '</div>'
      + '<button onclick="event.stopPropagation();closeNotifs()" style="background:var(--t);color:#fff;border:none;padding:8px 22px;border-radius:40px;font-family:var(--body);font-size:13px;font-weight:700;cursor:pointer">' + (t.close || 'Schließen') + '</button>'
      + '</div>';
    return;
  }
  list.innerHTML = NOTIFS.map(function (n) {
    return '<div style="display:flex;gap:10px;padding:11px 14px;border-bottom:1px solid var(--bord);background:' + (n.read ? 'transparent' : 'rgba(10,110,122,.08)') + ';border-left:' + (n.read ? '3px solid transparent' : '3px solid var(--t)') + '">'
      + '<span style="font-size:20px;flex-shrink:0;margin-top:2px">' + getNotifIcon(n.icon) + '</span>'
      + '<div style="flex:1;min-width:0">'
      + '<div style="font-size:13px;line-height:1.55;font-weight:' + (n.read ? 400 : 700) + ';color:' + (n.read ? 'var(--muted)' : 'var(--ink)') + '">' + n.text + '</div>'
      + '<div style="font-size:11px;color:' + (n.read ? 'var(--light)' : 'var(--t)') + ';margin-top:3px;font-weight:' + (n.read ? 400 : 600) + '">' + n.time + '</div>'
      + '</div>'
      + '<button onclick="event.stopPropagation();deleteNotif(\'' + n.id + '\')" style="background:none;border:none;color:var(--light);cursor:pointer;font-size:16px;line-height:1;padding:2px 4px;flex-shrink:0;align-self:flex-start">&#x2715;</button>'
      + '</div>';
  }).join('');
}

let notifsOpen = false;

export async function openNotifs() {
  if (notifsOpen) { closeNotifs(); return; }
  notifsOpen = true;
  await fetchNotifs();
  renderNotifList();
  document.getElementById('notif-panel').style.display = 'flex';
  setTimeout(function () { document.addEventListener('click', outsideNotif); }, 10);
  NOTIFS.filter(function (n) { return !n.read; }).forEach(function (n) {
    n.read = true;
    fetch('/notifications/' + n.id + '/read', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });
  });
  updateNotifDot();
}

function outsideNotif(e) {
  const panel = document.getElementById('notif-panel');
  if (!panel) { return; }
  const fromBtn = ['notif-btn', 'faq-notif-btn', 'accp-notif-btn'].some(function (id) {
    const el = document.getElementById(id); return el && (el === e.target || el.contains(e.target));
  });
  if (!panel.contains(e.target) && !fromBtn) { closeNotifs(); }
}

export function closeNotifs() {
  notifsOpen = false;
  document.getElementById('notif-panel').style.display = 'none';
  document.removeEventListener('click', outsideNotif);
}

export function markNotifRead(id) {
  const n = NOTIFS.find(function (item) { return item.id === id; });
  if (n) {
    n.read = !n.read;
    fetch('/notifications/' + id + '/read', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });
  }
  updateNotifDot();
  renderNotifList();
}

export function deleteNotif(id) {
  NOTIFS = NOTIFS.filter(function (n) { return n.id !== id; });
  updateNotifDot();
  renderNotifList();
  fetch('/notifications/' + id, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
  });
}

export function deleteAllNotifs() {
  NOTIFS = [];
  updateNotifDot();
  renderNotifList();
  fetch('/notifications', {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
  });
}
