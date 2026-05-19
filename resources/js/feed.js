import { state } from './state.js';

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

function updateTabCounts(counts) {
  const c = counts !== undefined ? counts
    : (state.activeTags.size === 0 && !state.srch && state.curView === 'home' ? (window.__COUNTS__ || null) : null);
  if (!c) {
    document.getElementById('tab-Alle').textContent = 'Alle';
    document.getElementById('tab-Erfahrung').textContent = '✨ Erfahrungen';
    document.getElementById('tab-Frage').textContent = '❓ Fragen';
    return;
  }
  document.getElementById('tab-Alle').textContent = 'Alle (' + c.all + ')';
  document.getElementById('tab-Erfahrung').textContent = '✨ Erfahrungen (' + c.experiences + ')';
  document.getElementById('tab-Frage').textContent = '❓ Fragen (' + c.questions + ')';
}

document.addEventListener('livewire:init', function () {
  Livewire.on('tab-counts-updated', function (counts) {
    if (state.activeTags.size === 0 && !state.srch && state.curView === 'home') {
      window.__COUNTS__ = counts;
    }
    updateTabCounts(counts);
  });
});

function dispatchToFeed() {
  if (typeof Livewire !== 'undefined') {
    Livewire.dispatch('livewire-filter-updated', {
      search: state.srch,
      type: state.curType,
      tags: [...state.activeTags],
      view: state.curView === 'home' ? 'all' : state.curView,
    });
  }
}

export function render() {
  updateTabCounts();

  const feedEmpty = document.getElementById('feed-empty');
  if (feedEmpty && state.curView === 'home') { feedEmpty.style.display = 'none'; }

  const af = document.getElementById('afilter'), parts = [];
  if (state.activeTags.size > 0) parts.push([...state.activeTags].map(function (t) { return '# ' + t; }).join(', '));
  if (state.srch) parts.push('"' + state.srch + '"');
  if (parts.length) {
    af.classList.add('on');
    document.getElementById('af-txt').textContent = 'Filter: ' + parts.join(' · ');
  } else {
    af.classList.remove('on');
  }
}

export function go(p) {
  state.page = p; window.scrollTo({ top: 260, behavior: 'smooth' });
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
  state.page = 1;
  dispatchToFeed();
  render();
}

export function setTab(t) {
  state.curType = t; state.page = 1;
  state.curView = 'home'; markNav('home');
  document.querySelectorAll('.ft').forEach(function (e) { e.classList.remove('on'); });
  document.getElementById('tab-' + t).classList.add('on');
  dispatchToFeed();
  render();
}

export function onComposeSrch() {
  const v = document.getElementById('compose-srch').value;
  state.srch = v.toLowerCase(); state.page = 1;
  document.getElementById('c-srch-x').style.display = v ? '' : 'none';
  dispatchToFeed();
  render();
}

export function clearComposeSrch() {
  document.getElementById('compose-srch').value = '';
  document.getElementById('c-srch-x').style.display = 'none';
  state.srch = ''; state.page = 1;
  dispatchToFeed();
  render();
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
  filterTags();
  dispatchToFeed();
  render();
}

export function showSaved() {
  if (state.curView === 'saved') { clearAll(); return; }
  state.curView = 'saved'; markNav('saved');
  state.activeTags.clear(); state.curType = 'Alle'; state.srch = ''; state.page = 1;
  document.querySelectorAll('.ft').forEach(function (e) { e.classList.remove('on'); });
  document.getElementById('tab-Alle').classList.add('on');
  dispatchToFeed();
  const af = document.getElementById('afilter');
  af.classList.add('on');
  document.getElementById('af-txt').textContent = 'Gespeicherte Beiträge';
}

export function showMine() {
  if (state.curView === 'mine') { clearAll(); return; }
  state.curView = 'mine'; markNav('mine');
  state.activeTags.clear(); state.curType = 'Alle'; state.srch = ''; state.page = 1;
  document.querySelectorAll('.ft').forEach(function (e) { e.classList.remove('on'); });
  document.getElementById('tab-Alle').classList.add('on');
  dispatchToFeed();
  const af = document.getElementById('afilter');
  af.classList.add('on');
  document.getElementById('af-txt').textContent = 'Meine Beiträge';
}
