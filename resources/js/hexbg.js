export function initHexBg(id) {
  const existing = document.getElementById(id);
  if (!existing || existing._hexInit) return;
  existing._hexInit = true;
  const cv = existing, ctx = cv.getContext('2d');
  let W, H, t = 0;
  function resize() { W = cv.width = window.innerWidth; H = cv.height = window.innerHeight; }
  resize();
  window.addEventListener('resize', resize);
  const R = 28;
  function hex(cx, cy, r, fill, stroke, lw) {
    ctx.beginPath();
    for (let i = 0; i < 6; i++) {
      const a = Math.PI / 3 * i - Math.PI / 6;
      ctx.lineTo(cx + r * Math.cos(a), cy + r * Math.sin(a));
    }
    ctx.closePath();
    if (fill) { ctx.fillStyle = fill; ctx.fill(); }
    if (stroke) { ctx.strokeStyle = stroke; ctx.lineWidth = lw || 1; ctx.stroke(); }
  }
  function draw() {
    ctx.clearRect(0, 0, W, H);
    const g = ctx.createLinearGradient(0, 0, W, H);
    g.addColorStop(0, '#e6f4f6'); g.addColorStop(0.55, '#f3ede4'); g.addColorStop(1, '#fdf5e8');
    ctx.fillStyle = g; ctx.fillRect(0, 0, W, H);
    t += 0.006;
    const cols = Math.ceil(W / (R * Math.sqrt(3))) + 2;
    const rows = Math.ceil(H / (R * 1.5)) + 2;
    for (let row = -1; row < rows; row++) {
      for (let col = -1; col < cols; col++) {
        const cx = col * R * Math.sqrt(3) + (row % 2) * R * Math.sqrt(3) / 2;
        const cy = row * R * 1.5;
        const wave = Math.sin(cx * 0.014 + t) * Math.cos(cy * 0.012 + t * 0.7);
        const pulse = (wave + 1) / 2;
        const isTeal = (Math.floor(row + col * 1.3)) % 3 !== 0;
        if (pulse > 0.3) {
          const a = (pulse - 0.3) * 0.28;
          hex(cx, cy, R - 1, isTeal ? `rgba(10,110,122,${a})` : `rgba(184,118,42,${a * 0.85})`, null, 0);
        }
        const ba = 0.06 + pulse * 0.09;
        hex(cx, cy, R - 1, null, isTeal ? `rgba(10,110,122,${ba})` : `rgba(184,118,42,${ba * 0.75})`, 1);
      }
    }
    requestAnimationFrame(draw);
  }
  draw();
}
