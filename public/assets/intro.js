(() => {
 'use strict';
 const intro = document.getElementById('travel-intro');
 if (!intro || !document.documentElement.classList.contains('intro-pending')) return;
 if (matchMedia('(prefers-reduced-motion: reduce)').matches) {
  document.documentElement.classList.remove('intro-pending');
  return;
 }
 clearTimeout(window.dreamIntroFallback);
 const key = 'dream-holidays-intro-v1';
 try { sessionStorage.setItem(key, 'seen'); } catch {}
 const background = [...document.body.children].filter(el => el !== intro && !['SCRIPT', 'STYLE'].includes(el.tagName));
 const priorFocus = document.activeElement;
 const priorOverflow = document.body.style.overflow;
 const priorInert = background.map(el => el.inert);
 let leaving = false;
 let timer;
 const finish = () => {
  if (leaving) return;
  leaving = true;
  clearTimeout(timer);
  intro.classList.add('is-leaving');
  setTimeout(() => {
   intro.hidden = true;
   document.documentElement.classList.remove('intro-pending');
   document.body.style.overflow = priorOverflow;
   background.forEach((el, i) => { el.inert = priorInert[i]; });
   document.removeEventListener('keydown', onKey);
   if (intro.contains(document.activeElement)) {
    const target = priorFocus && priorFocus !== document.body ? priorFocus : document.querySelector('.site-header .brand');
    target?.focus({preventScroll: true});
   }
  }, 650);
 };
 const onKey = event => {
  if (event.key === 'Escape') finish();
  if (event.key === 'Tab') { event.preventDefault(); intro.querySelector('button').focus(); }
 };
 intro.querySelector('button').addEventListener('click', finish);
 document.addEventListener('keydown', onKey);
 background.forEach(el => { el.inert = true; });
 document.body.style.overflow = 'hidden';
 intro.hidden = false;
 intro.querySelector('button').focus({preventScroll: true});
 timer = setTimeout(finish, 2800);
 window.addEventListener('pagehide', finish, {once: true});
})();
