import { toast } from './utils.js';

const NOTIFS = [];

function getNotifIcon(icon) {
  return icon === 'LOGO'
    ? '<svg width="32" height="32" viewBox="0 0 40 40" fill="none"><rect x="1" y="1" width="38" height="38" rx="10" fill="rgba(10,110,122,.12)"/><circle cx="13" cy="11" r="4" fill="#0a6e7a"/><path d="M10 15.5 Q10 20 10 23 L16 23 Q16 20 16 15.5Z" fill="#0a6e7a" opacity=".9"/><circle cx="27" cy="11" r="4" fill="#b8762a"/><path d="M24 15.5 Q24 20 24 23 L30 23 Q30 20 30 15.5Z" fill="#b8762a" opacity=".9"/><path d="M16 17 Q20 13 20 13" stroke="#0a6e7a" stroke-width="2" stroke-linecap="round" fill="none"/><path d="M24 17 Q20 13 20 13" stroke="#b8762a" stroke-width="2" stroke-linecap="round" fill="none"/><path d="M20 13 C20 13 18 10.5 18 9 C18 7.5 19 6.5 20 7.5 C21 6.5 22 7.5 22 9 C22 10.5 20 13 20 13Z" fill="#b8762a"/></svg>'
    : icon;
}

export function renderNotifList() {
  const list = document.getElementById('notif-list');
  const uc = document.getElementById('notif-unread-count');
  if (!list) return;
  const unread = NOTIFS.filter(function (n) { return !n.read; }).length;
  if (uc) uc.textContent = unread > 0 ? unread + ' ungelesen' : 'Benachrichtigungen';
  if (!NOTIFS.length) {
    list.innerHTML = '<div style="padding:28px 16px 20px;text-align:center">'
      + '<div style="color:var(--light);font-size:13.5px;font-style:italic;margin-bottom:14px">Keine Benachrichtigungen</div>'
      + '<button onclick="event.stopPropagation();closeNotifs()" style="background:var(--t);color:#fff;border:none;padding:8px 22px;border-radius:40px;font-family:var(--body);font-size:13px;font-weight:700;cursor:pointer">Schlie&#xDF;en</button>'
      + '</div>';
    return;
  }
  list.innerHTML = NOTIFS.map(function (n, i) {
    return '<div style="display:flex;gap:10px;padding:11px 14px;border-bottom:1px solid var(--bord);background:' + (n.read ? 'transparent' : 'rgba(10,110,122,.04)') + '">'
      + '<span style="font-size:20px;flex-shrink:0;margin-top:2px">' + getNotifIcon(n.icon) + '</span>'
      + '<div style="flex:1;min-width:0">'
      + '<div style="font-size:12.5px;line-height:1.55;font-weight:' + (n.read ? 300 : 500) + ';color:' + (n.read ? 'var(--muted)' : 'var(--ink2)') + '">' + n.text + '</div>'
      + '<div style="font-size:11px;color:var(--light);margin-top:3px">' + n.time + '</div>'
      + '</div>'
      + '<button onclick="event.stopPropagation();deleteNotif(' + i + ')" style="background:none;border:none;color:var(--light);cursor:pointer;font-size:16px;line-height:1;padding:2px 4px;flex-shrink:0;align-self:flex-start">&#x2715;</button>'
      + '</div>';
  }).join('');
}

let notifsOpen = false;

export function openNotifs() {
  if (notifsOpen) { closeNotifs(); return; }
  notifsOpen = true;
  renderNotifList();
  document.getElementById('notif-panel').style.display = 'flex';
  setTimeout(function () { document.addEventListener('click', outsideNotif); }, 10);
}

function outsideNotif(e) {
  const panel = document.getElementById('notif-panel');
  if (!panel) return;
  const fromBtn = ['notif-btn', 'faq-notif-btn', 'accp-notif-btn'].some(function (id) {
    const el = document.getElementById(id); return el && (el === e.target || el.contains(e.target));
  });
  if (!panel.contains(e.target) && !fromBtn) closeNotifs();
}

export function closeNotifs() {
  notifsOpen = false;
  document.getElementById('notif-panel').style.display = 'none';
  document.removeEventListener('click', outsideNotif);
}

export function markNotifRead(i) { NOTIFS[i].read = !NOTIFS[i].read; renderNotifList(); }
export function deleteNotif(i) { NOTIFS.splice(i, 1); renderNotifList(); }
export function deleteAllNotifs() { NOTIFS.length = 0; renderNotifList(); }
