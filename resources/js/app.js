import { state, getSS } from './state.js';
import { toast, checkAuthThen } from './utils.js';
import { initHexBg } from './hexbg.js';
import { applyPostState, updateSavedCount, expand, toggleLike, toggleSave, togC, rate, addC, deletePost, deleteComment, reportPost, createPostElement } from './posts.js';
import { render, go, toggleTag, setTab, onComposeSrch, clearComposeSrch, filterTags, clearAll, showSaved, showMine, markNav, setActiveNav } from './feed.js';
import { setLoggedInUI, openLg, closeLg, setLT, doLogin, doReg, doPasswordResetLink, doSocialLogin, doLogout } from './auth.js';
import { renderNotifList, openNotifs, closeNotifs, markNotifRead, deleteNotif, deleteAllNotifs } from './notifications.js';
import { openProfileMenu, closeProfileMenu, openAccountPage, closeAccountPage } from './profile.js';
import { openFAQPage, closeFAQPage, renderFAQ, toggleFAQ, filterFAQ, showFAQForm, submitFAQQuestion } from './faq.js';
import { openPM, closePM, setType, savePost, openFeedback, closeFB, submitFeedback, fbFocus, fbBlur, openInfo, closeInfo } from './modals.js';

function init() {
  initHexBg('hexbg');

  if (window.__AUTH__) {
    state.loggedIn = true;
    state.currentUser = window.__AUTH__;
    setLoggedInUI();
  }

  state.posts = (window.__POSTS__ || []).map(function (p) {
    return Object.assign({}, p, { expanded: false, showC: false });
  });

  const TAGS = window.__TAGS__ || [];
  const TC = window.__TAG_COLORS__ || {};
  const TN = window.__TAG_COUNTS__ || {};

  const ht = document.getElementById('hero-tags');
  if (ht) {
    Object.entries(TN).sort(function (a, b) { return b[1] - a[1]; }).slice(0, 5).forEach(function ([t]) {
      const b = document.createElement('button');
      b.className = 'htag'; b.id = 'ht-' + t; b.textContent = '# ' + t;
      b.onclick = function () { toggleTag(t); };
      ht.appendChild(b);
    });
  }

  const st = document.getElementById('sb-tags');
  const sel = document.getElementById('mod-tag');
  if (st) {
    TAGS.forEach(function (t) {
      const b = document.createElement('button');
      b.className = 'titem'; b.id = 'nav-' + t;
      b.innerHTML = '<span class="tlbl"><span class="tdot" style="background:' + TC[t] + '"></span># ' + t + '</span><span class="tcnt">' + TN[t] + '</span>';
      b.onclick = function () { toggleTag(t); };
      st.appendChild(b);
    });
  }
  if (sel) {
    TAGS.forEach(function (t) {
      sel.insertAdjacentHTML('beforeend', '<option value="' + t + '">' + t + '</option>');
    });
  }

  const savedR = getSS('ratings', {});
  const savedL = getSS('likes', {});
  const savedS = getSS('saves', {});
  state.posts.forEach(function (p) {
    if (savedL[p.id]) p.liked = true;
    if (savedS[p.id]) p.saved = true;
    if (savedR[p.id]) p.uRat = savedR[p.id];
    applyPostState(p);
  });

  render();
  updateSavedCount();
  setActiveNav('home');

  if (new URLSearchParams(window.location.search).get('login') === '1') {
    openLg();
  }
}

init();

Object.assign(window, {
  toast, checkAuthThen,
  expand, toggleLike, toggleSave, togC, rate, addC, deletePost, deleteComment, reportPost,
  render, go, toggleTag, setTab, onComposeSrch, clearComposeSrch, filterTags, clearAll, showSaved, showMine, markNav, setActiveNav,
  openLg, closeLg, setLT, doLogin, doReg, doPasswordResetLink, doSocialLogin, doLogout,
  openNotifs, closeNotifs, markNotifRead, deleteNotif, deleteAllNotifs, renderNotifList,
  openProfileMenu, closeProfileMenu, openAccountPage, closeAccountPage,
  openFAQPage, closeFAQPage, renderFAQ, toggleFAQ, filterFAQ, showFAQForm, submitFAQQuestion,
  openPM, closePM, setType, savePost, openFeedback, closeFB, submitFeedback, fbFocus, fbBlur, openInfo, closeInfo,
});
