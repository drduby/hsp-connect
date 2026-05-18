import { state } from './state.js';
import { toggleTag, setActiveNav } from './feed.js';
import { initHexBg } from './hexbg.js';

const FAQ_TAG_ITEMS = [
  { q: 'Was gibt es zum Thema Spastik?', a: 'Im Feed findest du viele Erfahrungen und Fragen rund um Spastik. Klicke auf den Tag # Spastik in der linken Sidebar oder im Hero-Bereich um alle Beiträge zu diesem Thema zu sehen.', tags: ['spastik'] },
  { q: 'Tipps zu Physiotherapie?', a: 'Unter dem Tag # Physiotherapie findest du Erfahrungsberichte und Fragen zur Physiotherapie bei Spastik. Viele Mitglieder teilen ihre Übungen und Empfehlungen.', tags: ['physiotherapie'] },
  { q: 'Hilfsmittel für den Alltag?', a: 'Unter # Hilfsmittel und # Alltag teilen Mitglieder Empfehlungen für praktische Helfer. Von Greifhilfen bis zu speziellen Schuheinlagen.', tags: ['hilfsmittel', 'alltag'] },
  { q: 'Entspannungstechniken bei Spastik?', a: 'Unter dem Tag # Entspannung findest du Erfahrungen mit verschiedenen Entspannungsmethoden — Wärme, Massage, Yoga und mehr.', tags: ['entspannung'] },
  { q: 'Ernährung und Spastik?', a: 'Einige Mitglieder berichten unter # Ernährung über den Einfluss der Ernährung auf ihre Symptome.', tags: ['ernährung'] },
  { q: 'Schlafprobleme bei Spastik?', a: 'Unter # Schlaf tauschen sich Betroffene über Schlafprobleme und Lösungen aus — von Lagerungshilfen bis zu Abendroutinen.', tags: ['schlaf'] },
  { q: 'Muskeln und Muskelspannung?', a: 'Zum Thema Muskelspannung und -entspannung gibt es viele Beiträge unter dem Tag # Muskeln.', tags: ['muskeln'] },
];

export const FAQ_ITEMS = [...FAQ_TAG_ITEMS,
  { q: 'Was ist HSPConnect?', a: 'Eine kostenlose Community für Menschen mit Hereditärer Spastischer Paraplegie (HSP) und anderen Formen von Spastik. Erfahrungen teilen, Fragen stellen, vernetzen.' },
  { q: 'Muss ich angemeldet sein um zu lesen?', a: 'Nein — alle Beiträge sind öffentlich lesbar. Für Kommentare, Likes und eigene Beiträge ist ein Konto erforderlich.' },
  { q: 'Ist HSPConnect kostenlos?', a: 'Ja, vollständig kostenlos. Kein Abo, keine versteckten Kosten.' },
  { q: 'Sind die Inhalte medizinisch geprüft?', a: 'Nein. Alle Beiträge sind persönliche Erfahrungsberichte der Mitglieder. Sie ersetzen keinen Arztbesuch. Bei gesundheitlichen Fragen bitte immer medizinisches Fachpersonal aufsuchen.' },
  { q: 'Wie melde ich einen problematischen Beitrag?', a: 'Schreib uns an hallo@hspconnect.at mit einer kurzen Beschreibung. Wir nehmen alle Meldungen ernst und reagieren so schnell wie möglich.' },
  { q: 'Kann ich meinen Nickname später ändern?', a: 'Ja, nach dem Einloggen kannst du deinen Nickname in den Profileinstellungen anpassen.' },
  { q: 'Kann ich mein Konto löschen?', a: 'Ja. Schreib uns an hallo@hspconnect.at — wir löschen dein Konto und alle Daten innerhalb von 7 Tagen.' },
  { q: 'Wer betreibt HSPConnect?', a: 'HSPConnect ist ein privates Community-Projekt von Betroffenen für Betroffene. Kein kommerzielles Unternehmen.' },
  { q: 'Welche Sprachen werden unterstützt?', a: 'Aktuell hauptsächlich Deutsch. Englische Beiträge sind willkommen.' },
  { q: 'Tipps zur Rehabilitation bei Spastik?', a: 'Unter dem Tag # Reha tauschen sich Mitglieder über Rehabilitations-Erfahrungen aus — stationäre Reha, ambulante Therapien, und was wirklich hilft.', tags: ['reha'] },
  { q: 'Wie kann ich einen Beitrag speichern?', a: 'Klicke auf das 🏷️-Symbol unter jedem Beitrag. Gespeicherte Beiträge findest du unter "Mein Bereich" in der linken Seitenleiste (nach dem Anmelden).' },
];

export function openFAQPage() {
  document.getElementById('faq-page').style.display = 'block';
  document.body.style.overflow = 'hidden';
  setActiveNav('faq');
  renderFAQ(FAQ_ITEMS);
  document.getElementById('faq-srch').value = '';
  initHexBg('hexbg2');

  const fht = document.getElementById('faq-hero-tags');
  if (fht && !fht.children.length) {
    const TN = window.__TAG_COUNTS__ || {};
    Object.entries(TN).sort(function (a, b) { return b[1] - a[1]; }).slice(0, 5).forEach(function ([t]) {
      const b = document.createElement('button');
      b.className = 'htag'; b.textContent = '# ' + t;
      b.onclick = function () { toggleTag(t); closeFAQPage(); };
      fht.appendChild(b);
    });
  }

  const tf = document.getElementById('faq-tag-filter');
  if (tf && !tf.children.length) {
    (window.__TAGS__ || []).forEach(function (t) {
      const btn = document.createElement('button');
      btn.textContent = '# ' + t; btn.dataset.tag = t;
      btn.style.cssText = 'padding:4px 11px;border-radius:40px;border:1.5px solid var(--bord2);background:var(--surf2);font-family:var(--body);font-size:12px;font-weight:600;color:var(--muted);cursor:pointer;transition:all .15s';
      btn.onclick = function () {
        const active = btn.dataset.active === '1'; btn.dataset.active = active ? '0' : '1';
        btn.style.background = active ? 'var(--surf2)' : 'var(--t3)';
        btn.style.color = active ? 'var(--muted)' : 'var(--t)';
        btn.style.borderColor = active ? 'var(--bord2)' : 'var(--t)';
        filterFAQ();
      };
      tf.appendChild(btn);
    });
  }

  const chips = document.getElementById('topic-chips');
  if (chips && !chips.children.length) {
    (window.__TAGS__ || []).forEach(function (t) {
      const btn = document.createElement('button');
      btn.textContent = '# ' + t;
      btn.style.cssText = 'padding:4px 11px;border-radius:40px;border:1.5px solid var(--bord2);background:var(--surf2);font-family:var(--body);font-size:12px;font-weight:600;color:var(--muted);cursor:pointer;transition:all .18s;margin-bottom:4px';
      btn.dataset.selected = '0';
      btn.onclick = function () {
        const sel = btn.dataset.selected === '1'; btn.dataset.selected = sel ? '0' : '1';
        btn.style.background = sel ? 'var(--surf2)' : 'var(--t3)';
        btn.style.color = sel ? 'var(--muted)' : 'var(--t)';
        btn.style.borderColor = sel ? 'var(--bord2)' : 'var(--t)';
        const selTags = [];
        chips.querySelectorAll('button[data-selected="1"]').forEach(function (b) { selTags.push(b.textContent.replace('# ', '')); });
        const inp = document.getElementById('faq-q-topic'); if (inp) inp.value = selTags.join(', ');
      };
      chips.appendChild(btn);
    });
  }
}

export function closeFAQPage() {
  document.getElementById('faq-page').style.display = 'none';
  document.body.style.overflow = '';
  setActiveNav('home');
}

export function renderFAQ(items) {
  const list = document.getElementById('faq-list');
  const empty = document.getElementById('faq-empty');
  if (!items || !items.length) { if (list) list.innerHTML = ''; if (empty) empty.style.display = 'block'; return; }
  if (empty) empty.style.display = 'none';
  window._faqItems = items;
  list.innerHTML = items.map(function (item, i) {
    return '<div style="background:var(--surf);border:1px solid rgba(10,110,122,.1);border-radius:14px;margin-bottom:10px;overflow:hidden;box-shadow:var(--sh)">'
      + '<button onclick="toggleFAQ(' + i + ')" style="width:100%;padding:16px 20px;background:none;border:none;display:flex;align-items:center;justify-content:space-between;gap:14px;cursor:pointer;text-align:left;font-family:var(--body)">'
      + '<span style="font-size:14.5px;font-weight:700;color:var(--ink);line-height:1.4">' + item.q + '</span>'
      + '<span id="faq-arrow-' + i + '" style="font-size:18px;color:var(--t);flex-shrink:0">+</span>'
      + '</button>'
      + '<div id="faq-ans-' + i + '" style="display:none;padding:0 20px 16px;font-size:13.5px;color:var(--ink2);line-height:1.8;font-weight:300;border-top:1px solid rgba(10,110,122,.08)">' + item.a + '</div>'
      + '</div>';
  }).join('');
}

export function toggleFAQ(i) {
  const ans = document.getElementById('faq-ans-' + i);
  const arr = document.getElementById('faq-arrow-' + i);
  if (!ans) return;
  const open = ans.style.display === 'block';
  ans.style.display = open ? 'none' : 'block';
  if (arr) arr.textContent = open ? '+' : '−';
}

export function filterFAQ() {
  const q = document.getElementById('faq-srch').value.toLowerCase().trim();
  const activeFaqTags = [];
  const tf = document.getElementById('faq-tag-filter');
  if (tf) tf.querySelectorAll('button[data-active="1"]').forEach(function (b) { activeFaqTags.push(b.dataset.tag.toLowerCase()); });
  const filtered = FAQ_ITEMS.filter(function (item) {
    const matchQ = !q || (item.q.toLowerCase().includes(q) || item.a.toLowerCase().includes(q) || (item.tags && item.tags.some(function (t) { return t.includes(q); })));
    const matchT = activeFaqTags.length === 0 || (item.tags && item.tags.some(function (t) { return activeFaqTags.includes(t.toLowerCase()); }));
    return matchQ && matchT;
  });
  renderFAQ(filtered);
}

export function showFAQForm() {
  const intro = document.getElementById('faq-intro-text');
  const fields = document.getElementById('faq-fields');
  if (intro && intro.style.display !== 'none') {
    intro.style.display = 'none';
    if (fields) fields.style.display = 'block';
    return;
  }
  submitFAQQuestion();
}

export function submitFAQQuestion() {
  const txt = document.getElementById('faq-q-txt');
  if (!txt || !txt.value.trim()) return;
  document.getElementById('faq-ask-form').style.display = 'none';
  document.getElementById('faq-ask-thanks').style.display = 'block';
  setTimeout(function () {
    document.getElementById('faq-ask-form').style.display = 'block';
    document.getElementById('faq-ask-thanks').style.display = 'none';
    txt.value = '';
    const ft = document.getElementById('faq-q-topic'); if (ft) ft.value = '';
    const chips = document.getElementById('topic-chips');
    if (chips) chips.querySelectorAll('button').forEach(function (b) {
      b.dataset.selected = '0'; b.style.background = 'var(--surf2)'; b.style.color = 'var(--muted)'; b.style.borderColor = 'var(--bord2)';
    });
    const fields = document.getElementById('faq-fields'); if (fields) fields.style.display = 'none';
    const intro = document.getElementById('faq-intro-text'); if (intro) intro.style.display = '';
  }, 3000);
}
