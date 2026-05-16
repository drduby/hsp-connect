import { state, PS } from './state.js';

export function setActiveNav(pg) {
  ['h-home-btn', 'h-faq-btn', 'faq-h-home-btn', 'faq-h-faq-btn'].forEach(function (id) {
    const el = document.getElementById(id); if (el) el.classList.remove('h-active');
  });
  if (pg === 'home') {
    const hh = document.getElementById('h-home-btn'); if (hh) hh.classList.add('h-active');
    const fhh = document.getElementById('faq-h-home-btn'); if (fhh) fhh.classList.add('h-active');
  } else if (pg === 'faq') {
    const hf = document.getElementById('h-faq-btn'); if (hf) hf.classList.add('h-active');
    const fhf = document.getElementById('faq-h-faq-btn'); if (fhf) fhf.classList.add('h-active');
  }
}

export function markNav(which) {
  const navSaved = document.getElementById('nav-saved');
  const navMine = document.getElementById('nav-mine');
  if (navSaved) navSaved.classList.toggle('active-nav', which === 'saved');
  if (navMine) navMine.classList.toggle('active-nav', which === 'mine');
}

function updateTabCounts() {
  if (state.activeTags.size > 0 || state.srch) {
    document.getElementById('tab-Alle').textContent = 'Alle';
    document.getElementById('tab-Erfahrung').textContent = '✨ Erfahrungen';
    document.getElementById('tab-Frage').textContent = '❓ Fragen';
    return;
  }
  document.getElementById('tab-Alle').textContent = 'Alle (' + state.posts.length + ')';
  document.getElementById('tab-Erfahrung').textContent = '✨ Erfahrungen (' + state.posts.filter(function (p) { return p.type === 'Erfahrung'; }).length + ')';
  document.getElementById('tab-Frage').textContent = '❓ Fragen (' + state.posts.filter(function (p) { return p.type === 'Frage'; }).length + ')';
}

export function render() {
  const feed = document.getElementById('feed');
  const feedEmpty = document.getElementById('feed-empty');
  updateTabCounts();

  const filtered = state.posts.filter(function (p) {
    const postTags = Array.isArray(p.tags) && p.tags.length ? p.tags : (p.tag ? [p.tag] : []);
    const matchTag = state.activeTags.size === 0 || postTags.some(function (t) { return state.activeTags.has(t); });
    const matchType = state.curType === 'Alle' || p.type === state.curType;
    const matchSrch = !state.srch || (p.title + ' ' + p.content + ' ' + postTags.join(' ')).toLowerCase().includes(state.srch);
    return matchTag && matchType && matchSrch;
  });

  const tot = Math.ceil(filtered.length / PS);
  if (state.page > tot && tot > 0) state.page = 1;
  const pageIds = new Set(filtered.slice((state.page - 1) * PS, state.page * PS).map(function (p) { return p.id; }));

  feed.querySelectorAll('.post[id^="post-"], .post-client[id^="post-"]').forEach(function (el) {
    const pid = parseInt(el.id.replace('post-', ''), 10);
    el.style.display = pageIds.has(pid) ? '' : 'none';
  });

  if (feedEmpty) feedEmpty.style.display = filtered.length === 0 ? '' : 'none';

  const af = document.getElementById('afilter'), parts = [];
  if (state.activeTags.size > 0) parts.push([...state.activeTags].map(function (t) { return '# ' + t; }).join(', '));
  if (state.srch) parts.push('"' + state.srch + '"');
  if (parts.length) {
    af.classList.add('on');
    document.getElementById('af-txt').textContent = 'Filter: ' + parts.join(' · ') + ' — ' + filtered.length + ' Beitrag' + (filtered.length !== 1 ? 'e' : '');
  } else {
    af.classList.remove('on');
  }

  renderPag(tot);
}

function renderPag(tot) {
  const c = document.getElementById('pag');
  if (tot <= 1) { c.innerHTML = ''; return; }
  let h = '<button class="pgb" onclick="go(' + (state.page - 1) + ')" ' + (state.page <= 1 ? 'disabled' : '') + '>‹</button>';
  for (let i = 1; i <= tot; i++) h += '<button class="pgb ' + (i === state.page ? 'on' : '') + '" onclick="go(' + i + ')">' + i + '</button>';
  h += '<button class="pgb" onclick="go(' + (state.page + 1) + ')" ' + (state.page >= tot ? 'disabled' : '') + '>›</button>';
  c.innerHTML = h;
}

export function go(p) {
  state.page = p; render(); window.scrollTo({ top: 260, behavior: 'smooth' });
}

export function toggleTag(t) {
  if (state.activeTags.has(t)) {
    state.activeTags.delete(t);
    const el = document.getElementById('nav-' + t); if (el) el.classList.remove('on');
    const he = document.getElementById('ht-' + t); if (he) he.classList.remove('on');
  } else {
    state.activeTags.add(t);
    const navAlle = document.getElementById('nav-Alle'); if (navAlle) navAlle.classList.remove('on');
    const el = document.getElementById('nav-' + t); if (el) el.classList.add('on');
    const he = document.getElementById('ht-' + t); if (he) he.classList.add('on');
  }
  state.page = 1; render();
}

export function setTab(t) {
  state.curType = t; state.page = 1;
  state.curView = 'home'; markNav('home');
  document.querySelectorAll('.ft').forEach(function (e) { e.classList.remove('on'); });
  document.getElementById('tab-' + t).classList.add('on');
  render();
}

export function onComposeSrch() {
  const v = document.getElementById('compose-srch').value;
  state.srch = v.toLowerCase(); state.page = 1;
  document.getElementById('c-srch-x').style.display = v ? '' : 'none';
  render();
}

export function clearComposeSrch() {
  document.getElementById('compose-srch').value = '';
  document.getElementById('c-srch-x').style.display = 'none';
  state.srch = ''; state.page = 1; render();
}

export function filterTags() {
  const el = document.getElementById('tag-srch');
  if (!el) return;
  const q = el.value.toLowerCase();
  document.querySelectorAll('.titem').forEach(function (c) {
    c.style.display = (!q || c.textContent.toLowerCase().includes(q)) ? '' : 'none';
  });
}

export function clearAll() {
  state.curView = 'home'; markNav('home');
  state.activeTags.clear(); state.curType = 'Alle'; state.srch = ''; state.page = 1;
  ['compose-srch', 'tag-srch'].forEach(function (id) { const e = document.getElementById(id); if (e) e.value = ''; });
  document.getElementById('c-srch-x').style.display = 'none';
  document.querySelectorAll('.titem,.htag').forEach(function (e) { e.classList.remove('on'); });
  document.querySelectorAll('.ft').forEach(function (e) { e.classList.remove('on'); });
  document.getElementById('tab-Alle').classList.add('on');
  filterTags(); render();
}

export function showSaved() {
  if (state.curView === 'saved') { clearAll(); return; }
  state.curView = 'saved'; markNav('saved');
  state.activeTags.clear(); state.curType = 'Alle'; state.srch = ''; state.page = 1;
  document.querySelectorAll('.ft').forEach(function (e) { e.classList.remove('on'); });
  document.getElementById('tab-Alle').classList.add('on');

  const savedIds = new Set(state.posts.filter(function (p) { return p.saved; }).map(function (p) { return p.id; }));
  const feed = document.getElementById('feed');
  const feedEmpty = document.getElementById('feed-empty');
  feed.querySelectorAll('.post[id^="post-"], .post-client[id^="post-"]').forEach(function (el) {
    el.style.display = savedIds.has(parseInt(el.id.replace('post-', ''), 10)) ? '' : 'none';
  });
  if (feedEmpty) feedEmpty.style.display = savedIds.size === 0 ? '' : 'none';

  const af = document.getElementById('afilter');
  af.classList.add('on');
  document.getElementById('af-txt').textContent = 'Gespeicherte Beiträge';
  document.getElementById('pag').innerHTML = '';
}

export function showMine() {
  if (state.curView === 'mine') { clearAll(); return; }
  state.curView = 'mine'; markNav('mine');
  state.activeTags.clear(); state.curType = 'Alle'; state.srch = ''; state.page = 1;
  document.querySelectorAll('.ft').forEach(function (e) { e.classList.remove('on'); });
  document.getElementById('tab-Alle').classList.add('on');

  const myIds = new Set(state.posts.filter(function (p) { return p.mine; }).map(function (p) { return p.id; }));
  const feed = document.getElementById('feed');
  const feedEmpty = document.getElementById('feed-empty');
  feed.querySelectorAll('.post[id^="post-"], .post-client[id^="post-"]').forEach(function (el) {
    el.style.display = myIds.has(parseInt(el.id.replace('post-', ''), 10)) ? '' : 'none';
  });
  if (feedEmpty) feedEmpty.style.display = myIds.size === 0 ? '' : 'none';

  const af = document.getElementById('afilter');
  af.classList.add('on');
  document.getElementById('af-txt').textContent = 'Meine Beiträge (' + myIds.size + ')';
  const nm = document.getElementById('nav-mine');
  if (nm) nm.innerHTML = '&#x270F;&#xFE0F; Meine Beitr&#xE4;ge (' + myIds.size + ')';
  document.getElementById('pag').innerHTML = '';
}
