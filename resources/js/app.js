import { state, getSS } from './state.js';
import { toast, checkAuthThen } from './utils.js';
import { initHexBg } from './hexbg.js';
import { applyPostState, updateSavedCount, expand, toggleLike, toggleSave, togC, rate, addC, deletePost, deleteComment, reportPost } from './posts.js';
import { render, go, toggleTag, setTab, onComposeSrch, clearComposeSrch, filterTags, clearAll, showSaved, showMine, markNav, setActiveNav } from './feed.js';
import { setLoggedInUI, openLg, closeLg, setLT, doLogin, doReg, doPasswordResetLink, doSocialLogin, doLogout, updatePwChecklist } from './auth.js';
import { renderNotifList, openNotifs, closeNotifs, markNotifRead, deleteNotif, deleteAllNotifs } from './notifications.js';
import { openProfileMenu, closeProfileMenu, openAccountPage, closeAccountPage } from './profile.js';
import { openFAQPage, closeFAQPage, renderFAQ, toggleFAQ, filterFAQ, showFAQForm, submitFAQQuestion } from './faq.js';
import { openPM, closePM, openFeedback, closeFB, submitFeedback, fbFocus, fbBlur, openInfo, closeInfo } from './modals.js';

function init() {
  initHexBg('hexbg');

  if (window.__AUTH__) {
    state.loggedIn = true;
    state.currentUser = window.__AUTH__;
    setLoggedInUI();
  }

  const postsPayload = window.__POSTS__ || [];
  const posts = Array.isArray(postsPayload) ? postsPayload : (Array.isArray(postsPayload.data) ? postsPayload.data : []);

  state.posts = posts.map(function (p) {
    return Object.assign({}, p, { expanded: false, showC: false });
  });

  const TAGS_DATA = window.__TAGS__ || [];
  const TAGS = TAGS_DATA.map(function (t) { return t.name; });
  const TC = Object.fromEntries(TAGS_DATA.map(function (t) { return [t.name, t.color]; }));

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
        b.innerHTML = '<span class="tlbl"><span class="tdot" style="background:' + tag.color + '"></span># ' + tag.name + '</span><span class="tcnt">0</span>';
        st.appendChild(b);
      });
    }
    st.querySelectorAll('.titem[data-tag]').forEach(function (b) {
      b.onclick = function () { toggleTag(b.dataset.tag); };
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
  state,
  toast, checkAuthThen,
  expand, toggleLike, toggleSave, togC, rate, addC, deletePost, deleteComment, reportPost,
  render, go, toggleTag, setTab, onComposeSrch, clearComposeSrch, filterTags, clearAll, showSaved, showMine, markNav, setActiveNav,
  openLg, closeLg, setLT, doLogin, doReg, doPasswordResetLink, doSocialLogin, doLogout, updatePwChecklist,
  openNotifs, closeNotifs, markNotifRead, deleteNotif, deleteAllNotifs, renderNotifList,
  openProfileMenu, closeProfileMenu, openAccountPage, closeAccountPage,
  openFAQPage, closeFAQPage, renderFAQ, toggleFAQ, filterFAQ, showFAQForm, submitFAQQuestion,
  openPM, closePM, openFeedback, closeFB, submitFeedback, fbFocus, fbBlur, openInfo, closeInfo,
});
