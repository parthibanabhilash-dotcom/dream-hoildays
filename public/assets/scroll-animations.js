export function initScrollAnimations(gsap, reduced) {
  if (!gsap || reduced.matches || !('IntersectionObserver' in window)) return;
  const selector = '.page-guide-copy, .page-guide-photo, .guide-card, .inspiration-photo-grid > figure, .about-hero-copy, .about-hero-visual, .about-story-photo, .about-story-copy, .hero-copy, .page-heading, .section-head, .category-title, .filters, .grid > .card, .gallery-grid > figure, .gallery-grid > .gallery-open, .empty, .booking-panel, .reading > .prose, .contact-grid > div, .itinerary, .site-footer .footer-grid > div';
  const candidates = [...document.querySelectorAll(selector)];
  const targets = candidates.filter(element => !candidates.some(parent => parent !== element && parent.contains(element)));
  const clear = element => {
    gsap.killTweensOf(element);
    element.classList.remove('motion-ready');
    gsap.set(element, { clearProps: 'opacity,transform' });
  };
  const observer = new IntersectionObserver(entries => {
    for (const entry of entries) if (entry.isIntersecting) {
      const element = entry.target;
      observer.unobserve(element);
      const stagger = element.parentElement.matches('.grid, .gallery-grid')
        ? [...element.parentElement.children].indexOf(element) % 3 * .07 : 0;
      gsap.to(element, {
        opacity: 1, y: 0, delay: stagger,
        duration: innerWidth < 760 ? .4 : .65, ease: 'power2.out',
        onComplete: () => clear(element),
      });
    }
  }, { threshold: .04, rootMargin: '0px 0px -12px 0px' });
  try {
    for (const element of targets) {
      element.classList.add('motion-ready');
      gsap.set(element, { opacity: 0, y: innerWidth < 760 ? 12 : 22 });
      observer.observe(element);
    }
    // Keyboard users must never focus an invisible control.
    document.addEventListener('focusin', event => {
      for (const element of targets) if (element.contains(event.target)) {
        observer.unobserve(element); clear(element);
      }
    });
    reduced.addEventListener('change', () => {
      if (reduced.matches) { observer.disconnect(); targets.forEach(clear); }
    });
    window.addEventListener('pageshow', event => {
      if (event.persisted) { observer.disconnect(); targets.forEach(clear); }
    });
  } catch {
    observer.disconnect(); targets.forEach(clear);
  }
}
