export function expand(id) {
  const pbody = document.getElementById('pbody-' + id);
  const readmore = document.getElementById('readmore-' + id);
  if (pbody) pbody.classList.remove('cl');
  if (readmore) readmore.style.display = 'none';
}
