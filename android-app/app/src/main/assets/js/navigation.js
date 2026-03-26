// Navigation functionality
class Navigation {
  constructor() {
    this.init();
  }

  init() {
    this.setupSmoothScrolling();
    this.setupActiveNavigation();
  }

  setupSmoothScrolling() {
    // Use event delegation so injected components work without re-init.
    document.addEventListener('click', (e) => {
      const anchor = e.target.closest('a[href^="#"]');
      if (!anchor) return;

      const href = anchor.getAttribute('href');
      if (!href || href === '#') return;

      const target = document.querySelector(href);
      if (!target) return;

      e.preventDefault();
      target.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    });
  }

  setupActiveNavigation() {
    // Highlight active navigation section based on scroll position
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-links a[href^="#"], .sidebar-links a[href^="#"]');

    const observerOptions = {
      rootMargin: '-20% 0px -70% 0px',
      threshold: 0
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          navLinks.forEach(link => link.classList.remove('active'));
          const activeLinks = document.querySelectorAll(`.nav-links a[href="#${entry.target.id}"], .sidebar-links a[href="#${entry.target.id}"]`);
          activeLinks.forEach(link => link.classList.add('active'));
        }
      });
    }, observerOptions);

    sections.forEach(section => observer.observe(section));
  }
}

// Initialize navigation when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  new Navigation();
});
