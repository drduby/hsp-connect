export const state = {
  posts: [],
  activeTags: new Set(),
  curType: 'Alle',
  srch: '',
  page: 1,
  modType: 'Erfahrung',
  curView: 'home',
  loggedIn: false,
  currentUser: null,
};

export const PS = 5;

export function getSS(k, d) {
  try { const v = sessionStorage.getItem(k); return v !== null ? JSON.parse(v) : d; } catch (e) { return d; }
}
export function setSS(k, v) {
  try { sessionStorage.setItem(k, JSON.stringify(v)); } catch (e) {}
}
