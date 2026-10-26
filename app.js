'use strict';
const root = document.documentElement;
const themeToggle = document.getElementById('themeToggle');
function setTheme(theme) {
  root.dataset.theme = theme;
  themeToggle.setAttribute('aria-label', `Switch to ${theme === 'dark' ? 'light' : 'dark'} theme`);
  try { localStorage.setItem('hashim-theme', theme); } catch (_) {}
}
try { const saved = localStorage.getItem('hashim-theme'); if (saved === 'dark' || saved === 'light') setTheme(saved); } catch (_) {}
themeToggle.addEventListener('click', () => setTheme(root.dataset.theme === 'dark' ? 'light' : 'dark'));
const menuToggle = document.getElementById('menuToggle');
const navigation = document.getElementById('navigation');
function closeMenu() { navigation.classList.remove('open'); menuToggle.setAttribute('aria-expanded','false'); menuToggle.setAttribute('aria-label','Open navigation'); }
menuToggle.addEventListener('click', () => { const open = navigation.classList.toggle('open'); menuToggle.setAttribute('aria-expanded',String(open)); menuToggle.setAttribute('aria-label',open ? 'Close navigation' : 'Open navigation'); });
navigation.querySelectorAll('a').forEach(link => link.addEventListener('click',closeMenu));
document.addEventListener('keydown', event => { if (event.key === 'Escape') closeMenu(); });
const filters = document.querySelectorAll('[data-filter]');
const panels = document.querySelectorAll('[data-category]');
filters.forEach(button => button.addEventListener('click', () => {
  const category = button.dataset.filter;
  filters.forEach(filter => { const selected = filter === button; filter.classList.toggle('active',selected); filter.setAttribute('aria-pressed',String(selected)); });
  let count = 0;
  panels.forEach(panel => { panel.hidden = category !== 'all' && panel.dataset.category !== category; if (!panel.hidden) count++; });
  document.getElementById('skillCount').textContent = category === 'all' ? 'Showing all 4 areas of expertise.' : `Showing ${count} ${count === 1 ? 'area' : 'areas'} of expertise.`;
}));
document.getElementById('copyEmail').addEventListener('click', async () => {
  const email = 'muhammadhashimabdulsattar@gmail.com';
  const status = document.getElementById('copyStatus');
  try { await navigator.clipboard.writeText(email); status.textContent = 'Email address copied.'; }
  catch (_) { status.textContent = `Email: ${email}`; }
});
document.getElementById('year').textContent = new Date().getFullYear();
