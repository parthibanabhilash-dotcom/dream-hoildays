// Runs in the head before the page can paint.
(() => {
 'use strict';
 if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
 try { if (sessionStorage.getItem('dream-holidays-intro-v1')) return; } catch {}
 document.documentElement.classList.add('intro-pending');
 // Reveal the page if the main intro script fails to load.
 window.dreamIntroFallback = setTimeout(() => {
  document.documentElement.classList.remove('intro-pending');
 }, 6000);
})();
