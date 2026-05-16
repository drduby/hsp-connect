import { state, getSS, setSS, PS } from './state.js';
import { toast, requireAuth } from './utils.js';

export function updateSavedCount() {
  const n = state.posts.filter(function (p) { return p.saved; }).length;
  const btn = document.getElementById('nav-saved');
  if (btn) btn.innerHTML = '&#x1F516; Gespeichert' + (n > 0 ? ' (' + n + ')' : '');
}

export function applyPostState(p) {
  const el = document.getElementById('post-' + p.id);
  if (!el) return;

  const likeBtn = document.getElementById('like-btn-' + p.id);
  const likeIcon = document.getElementById('like-icon-' + p.id);
  const likesCount = document.getElementById('likes-' + p.id);
  if (likeBtn) likeBtn.classList.toggle('lk', p.liked);
  if (likeIcon) likeIcon.textContent = p.liked ? '❤️' : '🤍';
  if (likesCount) likesCount.textContent = p.likes;

  const saveBtn = document.getElementById('save-btn-' + p.id);
  const saveIcon = document.getElementById('save-icon-' + p.id);
  const saveLabel = document.getElementById('save-label-' + p.id);
  if (saveBtn) saveBtn.classList.toggle('sv', p.saved);
  if (saveIcon) saveIcon.textContent = p.saved ? '🔖' : '🏷️';
  if (saveLabel) saveLabel.textContent = p.saved ? 'Gespeichert' : 'Speichern';

  const starsEl = document.getElementById('stars-' + p.id);
  if (starsEl) {
    starsEl.querySelectorAll('.star').forEach(function (s, idx) {
      s.classList.toggle('on', p.uRat >= idx + 1);
    });
  }

  const avgrEl = document.getElementById('avgr-' + p.id);
  if (avgrEl) {
    const avg = p.rCnt > 0 ? (p.rSum / p.rCnt).toFixed(1) : '—';
    avgrEl.textContent = 'ø ' + avg;
  }

  const pbody = document.getElementById('pbody-' + p.id);
  const readmore = document.getElementById('readmore-' + p.id);
  if (pbody) pbody.classList.toggle('cl', !p.expanded);
  if (readmore) readmore.style.display = p.expanded ? 'none' : '';

  const cmtsEl = document.getElementById('cmts-' + p.id);
  const cmtToggle = document.getElementById('cmt-toggle-' + p.id);
  if (cmtsEl) cmtsEl.style.display = p.showC ? '' : 'none';
  if (cmtToggle) cmtToggle.style.display = p.showC ? 'none' : '';

  if (p.showC) {
    const cinp = document.getElementById('ci-' + p.id);
    const cava = document.getElementById('cmt-ava-' + p.id);
    if (cinp) {
      if (state.loggedIn && state.currentUser) {
        cinp.readOnly = false;
        cinp.placeholder = 'Dein Kommentar...';
        cinp.style.cursor = '';
        cinp.onclick = null;
      } else {
        cinp.readOnly = true;
        cinp.placeholder = 'Anmelden zum Kommentieren';
        cinp.style.cursor = 'pointer';
        cinp.onclick = function () { requireAuth(); };
      }
    }
    if (cava && state.loggedIn && state.currentUser) cava.textContent = state.currentUser.ava;
  }

  el.querySelectorAll('[data-owner]').forEach(function (btn) {
    const owner = btn.getAttribute('data-owner');
    btn.style.display = (state.loggedIn && state.currentUser && state.currentUser.name === owner) ? '' : 'none';
  });
}

export function expand(id) {
  const p = state.posts.find(function (x) { return x.id === id; });
  if (!p) return;
  p.expanded = true;
  const pbody = document.getElementById('pbody-' + id);
  const readmore = document.getElementById('readmore-' + id);
  if (pbody) pbody.classList.remove('cl');
  if (readmore) readmore.style.display = 'none';
}

export function toggleLike(id) {
  if (!requireAuth('🔒 Anmelden zum Liken')) return;
  const p = state.posts.find(function (x) { return x.id === id; });
  if (!p) return;
  p.liked = !p.liked;
  p.likes += p.liked ? 1 : -1;
  const l = getSS('likes', {}); l[id] = p.liked; setSS('likes', l);
  const likeBtn = document.getElementById('like-btn-' + id);
  const likeIcon = document.getElementById('like-icon-' + id);
  const likesCount = document.getElementById('likes-' + id);
  if (likeBtn) likeBtn.classList.toggle('lk', p.liked);
  if (likeIcon) likeIcon.textContent = p.liked ? '❤️' : '🤍';
  if (likesCount) likesCount.textContent = p.likes;
}

export function toggleSave(id) {
  if (!requireAuth('🔒 Anmelden zum Speichern')) return;
  const p = state.posts.find(function (x) { return x.id === id; });
  if (!p) return;
  p.saved = !p.saved;
  const s = getSS('saves', {}); s[id] = p.saved; setSS('saves', s);
  const saveBtn = document.getElementById('save-btn-' + id);
  const saveIcon = document.getElementById('save-icon-' + id);
  const saveLabel = document.getElementById('save-label-' + id);
  if (saveBtn) saveBtn.classList.toggle('sv', p.saved);
  if (saveIcon) saveIcon.textContent = p.saved ? '🔖' : '🏷️';
  if (saveLabel) saveLabel.textContent = p.saved ? 'Gespeichert' : 'Speichern';
  toast(p.saved ? '🔖 Gespeichert!' : 'Entfernt');
  updateSavedCount();
}

export function togC(id) {
  const p = state.posts.find(function (x) { return x.id === id; });
  if (!p) return;
  p.showC = !p.showC;
  const cmtsEl = document.getElementById('cmts-' + id);
  const cmtToggle = document.getElementById('cmt-toggle-' + id);
  if (cmtsEl) cmtsEl.style.display = p.showC ? '' : 'none';
  if (cmtToggle) cmtToggle.style.display = p.showC ? 'none' : '';
  if (p.showC) {
    const cinp = document.getElementById('ci-' + id);
    const cava = document.getElementById('cmt-ava-' + id);
    if (cinp) {
      if (state.loggedIn && state.currentUser) {
        cinp.readOnly = false;
        cinp.placeholder = 'Dein Kommentar...';
        cinp.style.cursor = '';
        cinp.onclick = null;
      } else {
        cinp.readOnly = true;
        cinp.placeholder = 'Anmelden zum Kommentieren';
        cinp.style.cursor = 'pointer';
        cinp.onclick = function () { requireAuth(); };
      }
    }
    if (cava && state.loggedIn && state.currentUser) cava.textContent = state.currentUser.ava;
  }
}

export function rate(id, v) {
  if (!requireAuth('🔒 Anmelden zum Bewerten')) return;
  const p = state.posts.find(function (x) { return x.id === id; });
  if (!p) return;
  const r = getSS('ratings', {});
  const prev = r[id] || 0;
  if (p.uRat === v) { p.rSum -= v; p.rCnt--; p.uRat = 0; delete r[id]; }
  else { if (prev) p.rSum -= prev; else p.rCnt++; p.rSum += v; p.uRat = v; r[id] = v; }
  setSS('ratings', r);
  const starsEl = document.getElementById('stars-' + id);
  if (starsEl) {
    starsEl.querySelectorAll('.star').forEach(function (s, idx) {
      s.classList.toggle('on', p.uRat >= idx + 1);
    });
  }
  const avgrEl = document.getElementById('avgr-' + id);
  if (avgrEl) {
    const avg = p.rCnt > 0 ? (p.rSum / p.rCnt).toFixed(1) : '—';
    avgrEl.textContent = 'ø ' + avg;
  }
}

export function addC(id) {
  if (!requireAuth('🔒 Bitte erst anmelden!')) return;
  const cinp = document.getElementById('ci-' + id);
  if (!cinp || !cinp.value.trim()) return;
  const p = state.posts.find(function (x) { return x.id === id; });
  if (!p) return;
  const ci = p.comments.length;
  const comment = { a: state.currentUser.ava, n: state.currentUser.name, t: cinp.value };
  p.comments.push(comment);
  cinp.value = '';
  const list = document.getElementById('cmt-list-' + id);
  if (list) {
    const div = document.createElement('div');
    div.className = 'cmt';
    div.id = 'cmt-' + id + '-' + ci;
    div.innerHTML = '<div class="cava">' + comment.a + '</div>'
      + '<div class="cbub"><div class="cname" style="display:flex;justify-content:space-between;align-items:center">'
      + comment.n
      + '<button data-owner="' + comment.n + '" onclick="deleteComment(' + id + ',' + ci + ')" style="background:none;border:none;color:var(--light);font-size:11px;cursor:pointer;padding:0;line-height:1">&#x2715;</button>'
      + '</div><div class="ctext">' + comment.t + '</div></div>';
    list.appendChild(div);
  }
  const cntEl = document.getElementById('cmt-count-' + id);
  if (cntEl) cntEl.textContent = p.comments.length;
}

export function deletePost(id) {
  const p = state.posts.find(function (x) { return x.id === id; });
  if (!p || !p.mine || !state.loggedIn) { toast('⚠️ Keine Berechtigung.'); return; }
  if (!confirm('Beitrag wirklich löschen?')) return;
  state.posts = state.posts.filter(function (x) { return x.id !== id; });
  const el = document.getElementById('post-' + id);
  if (el) el.remove();
  updateSavedCount();
  toast('🗑️ Beitrag gelöscht');
}

export function deleteComment(pid, ci) {
  const p = state.posts.find(function (x) { return x.id === pid; });
  if (!p || ci < 0 || ci >= p.comments.length) return;
  p.comments.splice(ci, 1);
  const el = document.getElementById('cmt-' + pid + '-' + ci);
  if (el) el.remove();
  const cntEl = document.getElementById('cmt-count-' + pid);
  if (cntEl) cntEl.textContent = p.comments.length;
}

export function reportPost(id) {
  if (!requireAuth('🔒 Bitte erst anmelden')) return;
  const p = state.posts.find(function (x) { return x.id === id; });
  if (p && p.mine) { toast('⚠️ Eigene Beiträge können nicht gemeldet werden.'); return; }
  if (!p) return;
  toast('⚠️ Beitrag gemeldet. Danke!');
}

export function createPostElement(p) {
  const TC = window.__TAG_COLORS__ || {};
  const tc = TC[p.tag] || 'var(--t)';
  const avg = p.rCnt > 0 ? (p.rSum / p.rCnt).toFixed(1) : '—';
  const div = document.createElement('div');
  div.className = 'post post-client';
  div.id = 'post-' + p.id;
  div.setAttribute('data-type', p.type);
  div.setAttribute('data-tag', p.tag);
  div.setAttribute('data-search', (p.title + ' ' + p.content + ' ' + p.tag).toLowerCase());
  div.style.borderLeft = '3px solid ' + tc;
  div.innerHTML = '<div class="post-inner">'
    + '<div class="pmeta">'
    + '<div class="pava">' + p.ava + '</div>'
    + '<div><div class="pname">' + p.author + '</div><div class="ptime">' + (p.time || 'gerade eben') + '</div></div>'
    + '<span class="pbadge ' + (p.type === 'Erfahrung' ? 'pb-e' : 'pb-f') + '">' + p.type + '</span>'
    + '<span class="ptag" style="border-color:' + tc + '30;color:' + tc + '" onclick="toggleTag(\'' + p.tag + '\')"># ' + p.tag + '</span>'
    + '</div>'
    + '<div class="ptitle">' + p.title + '</div>'
    + '<div class="pbody" id="pbody-' + p.id + '">' + p.content + '</div>'
    + '</div>'
    + '<div class="pacts">'
    + (p.mine
      ? '<span class="pab" style="opacity:.3;cursor:default">🤍 <span id="likes-' + p.id + '">' + p.likes + '</span></span>'
        + '<button class="pab" onclick="deletePost(' + p.id + ')" style="margin-left:auto;color:#c04040;font-size:12px">&#x1F5D1; L&ouml;schen</button>'
      : '<button class="pab" id="like-btn-' + p.id + '" onclick="toggleLike(' + p.id + ')">'
        + '<span id="like-icon-' + p.id + '">🤍</span> <span id="likes-' + p.id + '">' + p.likes + '</span></button>')
    + '<button class="pab" onclick="togC(' + p.id + ')">💬 <span id="cmt-count-' + p.id + '">0</span></button>'
    + '<button class="pab" id="save-btn-' + p.id + '" onclick="toggleSave(' + p.id + ')">'
    + '<span id="save-icon-' + p.id + '">🏷️</span> <span id="save-label-' + p.id + '">Speichern</span></button>'
    + '<button class="pab" onclick="reportPost(' + p.id + ')" style="margin-left:auto;color:var(--light);font-size:11px">&#x26A0; Melden</button>'
    + '<span class="sepv"></span>'
    + (p.mine
      ? '<div class="stars" style="opacity:.3;pointer-events:none">★★★★★</div><span class="avgr" style="opacity:.5">ø ' + avg + '</span>'
      : '<div class="stars" id="stars-' + p.id + '">'
        + [1, 2, 3, 4, 5].map(function (s) { return '<span class="star" onclick="rate(' + p.id + ',' + s + ')">★</span>'; }).join('')
        + '</div><span class="avgr" id="avgr-' + p.id + '">ø ' + avg + '</span>')
    + '</div>'
    + '<div class="cmts" id="cmts-' + p.id + '" style="display:none">'
    + '<button class="togcmt" onclick="togC(' + p.id + ')">▲ Kommentare ausblenden</button>'
    + '<div id="cmt-list-' + p.id + '"></div>'
    + '<div class="cmt-write-label">Kommentar schreiben</div>'
    + '<div class="cform"><div class="cava" id="cmt-ava-' + p.id + '">?</div>'
    + '<input class="cinp" id="ci-' + p.id + '" placeholder="Anmelden zum Kommentieren" readonly style="cursor:pointer"'
    + ' onclick="checkAuthThen(function(){})" onkeydown="if(event.key===\'Enter\')addC(' + p.id + ')">'
    + '<button class="csend" onclick="addC(' + p.id + ')">Senden</button></div>'
    + '</div>';
  return div;
}
