import { state } from './state.js';
import { toast, checkAuthThen } from './utils.js';
import { initHexBg } from './hexbg.js';
import { render, go, toggleTag, onComposeSrch, clearComposeSrch, filterTags, clearAll, showSaved, showMine, markNav, setActiveNav, applyViewUI } from './feed.js';
import { setLoggedInUI, openLg, closeLg, doSocialLogin } from './auth.js';
import { renderNotifList, openNotifs, closeNotifs, markNotifRead, deleteNotif, deleteAllNotifs, initNotifDot } from './notifications.js';
import { openProfileMenu, closeProfileMenu, openAccountPage, closeAccountPage } from './profile.js';
import { openPM, closePM, openFeedback, closeFB, submitFeedback, fbFocus, fbBlur, openInfo, closeInfo, openConfirm, closeConfirm, doConfirm } from './modals.js';

function init() {
  initHexBg('hexbg');

  if (window.__AUTH__) {
    state.loggedIn = true;
    state.currentUser = window.__AUTH__;
    setLoggedInUI();
    initNotifDot();
  }

  const TAGS_DATA = window.__TAGS__ || [];
  const TAGS = TAGS_DATA.map(function (t) { return t.name; });

  const ht = document.getElementById('hero-tags');
  if (ht) {
    TAGS.slice(0, 5).forEach(function (t) {
      const b = document.createElement('button');
      b.className = 'htag'; b.id = 'ht-' + t; b.textContent = '# ' + t;
      b.onclick = function () { toggleTag(t); };
      ht.appendChild(b);
    });
  }

  const st = document.getElementById('sb-tags');
  if (st) {
    if (st.children.length === 0) {
      TAGS_DATA.forEach(function (tag) {
        const b = document.createElement('button');
        b.className = 'titem'; b.id = 'nav-' + tag.name; b.dataset.tag = tag.name;

        const lbl = document.createElement('span');
        lbl.className = 'tlbl';
        const dot = document.createElement('span');
        dot.className = 'tdot';
        dot.style.background = tag.color;
        lbl.appendChild(dot);
        lbl.appendChild(document.createTextNode('# ' + tag.name));

        const cnt = document.createElement('span');
        cnt.className = 'tcnt';
        cnt.textContent = '0';

        b.appendChild(lbl);
        b.appendChild(cnt);
        st.appendChild(b);
      });
    }
    st.querySelectorAll('.titem[data-tag]').forEach(function (b) {
      b.onclick = function () { toggleTag(b.dataset.tag); };
    });
  }

  const srchEl = document.getElementById('compose-srch');
  const srchX = document.getElementById('c-srch-x');
  if (srchEl) { srchEl.value = ''; }
  if (srchX) { srchX.style.display = 'none'; }
  setTimeout(function () {
    if (srchEl && srchEl.value) { srchEl.value = ''; }
  }, 300);

  const savedCount = window.__SAVED_COUNT__ || 0;
  const savedCountEl = document.getElementById('nav-saved-count');
  if (savedCountEl) savedCountEl.textContent = savedCount > 0 ? ' (' + savedCount + ')' : '';
  const accSavedEl = document.getElementById('acc-saved-cnt');
  if (accSavedEl) accSavedEl.textContent = savedCount;

  const myPostCount = window.__MY_POST_COUNT__ || 0;
  const mineCountEl = document.getElementById('nav-mine-count');
  if (mineCountEl) mineCountEl.textContent = myPostCount > 0 ? ' (' + myPostCount + ')' : '';
  const accPostsEl = document.getElementById('acc-posts');
  if (accPostsEl) accPostsEl.textContent = myPostCount;

  const likesGiven = window.__LIKES_GIVEN__ || 0;
  const accLikesEl = document.getElementById('acc-likes');
  if (accLikesEl) accLikesEl.textContent = likesGiven;

  render();

  if (new URLSearchParams(window.location.search).get('login') === '1') {
    openLg();
  }

  if (new URLSearchParams(window.location.search).get('verified') === '1') {
    openVerifiedModal();
  }

  const filterParam = new URLSearchParams(window.location.search).get('filter');
  if (filterParam === 'mine' || filterParam === 'saved') {
    applyViewUI(filterParam);
  }
}

document.addEventListener('livewire:navigated', init);

function goToFilter(view) {
  if (document.getElementById('afilter')) {
    closeAccountPage();
    if (view === 'mine') { showMine(); } else { showSaved(); }
  } else {
    Livewire.navigate('/?filter=' + view);
  }
}

function openVerifiedModal() {
  const el = document.getElementById('verified-modal');
  if (el) el.style.display = 'flex';
}

function closeVerifiedModal() {
  const el = document.getElementById('verified-modal');
  if (el) el.style.display = 'none';
  const url = new URL(window.location);
  url.searchParams.delete('verified');
  history.replaceState({}, '', url.pathname + (url.searchParams.size ? '?' + url.searchParams : ''));
}

Object.assign(window, {
  state,
  openVerifiedModal, closeVerifiedModal, goToFilter,
  toast, checkAuthThen,
  render, go, toggleTag, onComposeSrch, clearComposeSrch, filterTags, clearAll, showSaved, showMine, markNav, setActiveNav, applyViewUI,
  openLg, closeLg, doSocialLogin,
  openNotifs, closeNotifs, markNotifRead, deleteNotif, deleteAllNotifs, renderNotifList, initNotifDot,
  openProfileMenu, closeProfileMenu, openAccountPage, closeAccountPage,
  openPM, closePM, openFeedback, closeFB, submitFeedback, fbFocus, fbBlur, openInfo, closeInfo, openConfirm, closeConfirm, doConfirm,
});
