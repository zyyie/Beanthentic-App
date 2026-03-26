// UI interactions and animations
class UIController {
  constructor() {
    this.init();
  }

  init() {
    this.setupAnimations();
    this.setupInteractions();
    this.setupHeroSlider();
    this.setupPrimarySectionNavigation();
    this.setupAboutSidebarToggle();
    this.setupHomepageSidebarActive();
    this.setupHomepageSidebarDrawer();
    this.setupAboutNavDropdown();
    this.setupAboutMenu();
    this.setupAboutHistorySubmenu();
    this.loadYear();
  }

  showAboutOnly() {
    const homeSection = document.getElementById('home');
    const aboutSection = document.getElementById('about');
    if (homeSection) {
      homeSection.hidden = true;
      homeSection.setAttribute('hidden', '');
    }
    if (!aboutSection) return;
    aboutSection.hidden = false;
    aboutSection.removeAttribute('hidden');
  }

  showHomeOnly() {
    const homeSection = document.getElementById('home');
    const aboutSection = document.getElementById('about');
    if (homeSection) {
      homeSection.hidden = false;
      homeSection.removeAttribute('hidden');
    }
    if (aboutSection) {
      aboutSection.hidden = true;
      aboutSection.setAttribute('hidden', '');
    }
  }

  setupPrimarySectionNavigation() {
    const homeToggle = document.getElementById('home-nav-toggle');
    if (!homeToggle) return;

    // Homepage-only view on initial load.
    this.showHomeOnly();

    homeToggle.addEventListener('click', (e) => {
      e.preventDefault();
      this.showHomeOnly();
      if (history && history.replaceState) {
        history.replaceState(null, '', '#home');
      }
    });
  }

  setupAboutNavDropdown() {
    const toggle = document.getElementById('about-nav-toggle');
    const dropdown = document.getElementById('about-nav-dropdown');
    if (!toggle || !dropdown) return;

    const historyToggle = document.getElementById('about-nav-history-toggle');
    const historySubmenu = document.getElementById('about-history-nav-submenu');

    const setHistorySubmenuOpen = (shouldOpen) => {
      if (!historyToggle || !historySubmenu) return;
      historySubmenu.hidden = !shouldOpen;
      historySubmenu.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');
      historyToggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
    };

    const open = () => {
      dropdown.hidden = false;
      dropdown.setAttribute('aria-hidden', 'false');
      toggle.setAttribute('aria-expanded', 'true');
      // Keep homepage visible while the user is choosing
      // (History / Mission and Vision) from the dropdown.
      this.showHomeOnly();

      // Align dropdown under the "About Beanthentic" toggle.
      // nav-links is position: relative, and dropdown is absolutely positioned inside it.
      const parent = toggle.parentElement;
      if (parent) {
        const left = Math.max(0, Math.min(toggle.offsetLeft, parent.clientWidth - dropdown.offsetWidth));
        dropdown.style.left = left + 'px';
      }

      // Default: collapse History submenu inside the navbar dropdown.
      setHistorySubmenuOpen(false);
    };

    const close = () => {
      dropdown.hidden = true;
      dropdown.setAttribute('aria-hidden', 'true');
      toggle.setAttribute('aria-expanded', 'false');

      setHistorySubmenuOpen(false);
    };

    const isOpen = () => toggle.getAttribute('aria-expanded') === 'true';

    toggle.addEventListener('click', (e) => {
      // Prevent default hash jump; this is a dropdown toggle.
      e.preventDefault();
      e.stopPropagation();

      if (isOpen()) close();
      else open();
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
      const target = e.target;
      if (!target) return;
      if (dropdown.contains(target) || toggle.contains(target)) return;
      close();
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') close();
    });

    // Close after selecting an item
    dropdown.addEventListener('click', (e) => {
      const link = e.target && e.target.closest ? e.target.closest('a.about-menu-item') : null;
      if (!link) return;

      // Bring About content into view after selecting an item.
      // Show About content, then close dropdown so it doesn't overlap the About page.
      this.showAboutOnly();
      const aboutSection = document.getElementById('about');
      if (aboutSection && typeof aboutSection.scrollIntoView === 'function') {
        aboutSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }

      // Close dropdown after selection.
      close();

      // If user clicked a non-history item, collapse the nested submenu.
      if (historySubmenu && historyToggle && link !== historyToggle && !historySubmenu.contains(link)) {
        setHistorySubmenuOpen(false);
      }
    });

    // Toggle nested History submenu
    if (historyToggle && historySubmenu) {
      historyToggle.addEventListener('click', (e) => {
        // Prevent hash jump; we handle activation via About History submenu logic.
        e.preventDefault();
        e.stopPropagation();
        const shouldOpen = historySubmenu.hidden;
        setHistorySubmenuOpen(shouldOpen);
      });
    }
  }

  setupAboutHistorySubmenu() {
    const items = Array.from(document.querySelectorAll('[data-about-history-target]'));
    const panels = Array.from(document.querySelectorAll('[data-about-history-panel]'));

    if (items.length === 0 || panels.length === 0) return;

    const setActive = (id) => {
      items.forEach((a) => {
        a.classList.toggle('is-active', a.dataset.aboutHistoryTarget === id);
      });
      panels.forEach((p) => {
        p.classList.toggle('is-active', p.dataset.aboutHistoryPanel === id);
      });
    };

    // Default active (use existing markup if available).
    const initial = panels.find(p => p.classList.contains('is-active'))?.dataset.aboutHistoryPanel;
    if (initial) setActive(initial);

    // Deep-link by hash (optional).
    const hash = (window.location.hash || '').replace('#', '');
    if (hash && panels.some(p => p.id === hash)) setActive(hash);

    items.forEach((a) => {
      a.addEventListener('click', (e) => {
        e.preventDefault();
        const id = a.dataset.aboutHistoryTarget;
        if (!id) return;

        // Ensure only About content is visible when selecting History.
        this.showAboutOnly();

        setActive(id);

        if (history && history.replaceState) {
          history.replaceState(null, '', `#${id}`);
        }
      });
    });
  }

  setupHomepageSidebarActive() {
    // Sidebar "Home" highlight should happen only on explicit clicks.
    const sidebarLinks = document.querySelector('.home-sidebar .sidebar-links');
    if (!sidebarLinks) return;

    const homeLink = sidebarLinks.querySelector('a[href="#home"]');
    if (!homeLink) return;

    const clearActive = () => {
      sidebarLinks.querySelectorAll('a.active').forEach(a => a.classList.remove('active'));
    };

    clearActive();

    sidebarLinks.addEventListener('click', (e) => {
      const link = e.target && e.target.closest ? e.target.closest('a[href^="#"]') : null;
      if (!link) return;

      const href = link.getAttribute('href') || '';

      if (href === '#home') {
        clearActive();
        homeLink.classList.add('active');

        // When Home is clicked, remove highlights from About submenu items
        // and reset About panels to the overview.
        document.querySelectorAll('.about-menu-item.is-active').forEach(a => a.classList.remove('is-active'));
        document.querySelectorAll('.about-topic.is-active').forEach(p => p.classList.remove('is-active'));
        const aboutOverviewPanel = document.querySelector('.about-topic[data-about-panel="about"]');
        if (aboutOverviewPanel) aboutOverviewPanel.classList.add('is-active');

        // Also close the About submenu (History / Mission and Vision).
        const submenu = document.getElementById('about-sidebar-submenu');
        const aboutToggle = document.querySelector('.home-sidebar .sidebar-about-toggle[data-toggle-about-submenu="true"]');
        if (submenu) {
          submenu.classList.remove('is-open');
          submenu.setAttribute('aria-hidden', 'true');
        }
        if (aboutToggle) {
          aboutToggle.setAttribute('aria-expanded', 'false');
        }
      } else {
        // History / Mission / About / Dashboard should not keep Home highlighted.
        clearActive();
      }
    });
  }

  setupHomepageSidebarDrawer() {
    const burger = document.getElementById('sidebar-burger');
    const overlay = document.getElementById('home-sidebar-overlay');
    const sidebar = document.getElementById('home-sidebar');
    if (!burger || !overlay || !sidebar) return;

    const isMobile = () => window.matchMedia('(max-width: 860px)').matches;

    const openDrawer = () => {
      if (!isMobile()) return;
      sidebar.classList.add('is-drawer-open');
      overlay.classList.add('is-open');
      burger.setAttribute('aria-expanded', 'true');
      overlay.setAttribute('aria-hidden', 'false');
      document.body.classList.add('no-scroll');
    };

    const closeDrawer = () => {
      if (!isMobile()) return;
      sidebar.classList.remove('is-drawer-open');
      overlay.classList.remove('is-open');
      burger.setAttribute('aria-expanded', 'false');
      overlay.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('no-scroll');
    };

    burger.addEventListener('click', () => {
      const isOpen = sidebar.classList.contains('is-drawer-open');
      if (isOpen) closeDrawer();
      else openDrawer();
    });

    overlay.addEventListener('click', () => closeDrawer());

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeDrawer();
    });

    // Close drawer when a sidebar link is clicked.
    sidebar.addEventListener('click', (e) => {
      const a = e.target && e.target.closest ? e.target.closest('a[href^="#"]') : null;
      if (!a) return;
      closeDrawer();
    });
  }

  setupAnimations() {
    // Add entrance animations to elements
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate-in');
        }
      });
    }, observerOptions);

    // Observe elements for animation
    document.querySelectorAll('.hero-card, .about-card').forEach(el => {
      observer.observe(el);
    });
  }

  setupInteractions() {
    // Button interactions
    document.querySelectorAll('.btn-primary').forEach(button => {
      button.addEventListener('mouseenter', () => {
        button.style.transform = 'translateY(-1px)';
      });
      
      button.addEventListener('mouseleave', () => {
        button.style.transform = 'translateY(0)';
      });
    });

    // Add ripple effect to buttons
    document.querySelectorAll('.btn-primary').forEach(button => {
      button.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        this.appendChild(ripple);
        
        setTimeout(() => {
          ripple.remove();
        }, 600);
      });
    });
  }

  setupAboutMenu() {
    const items = Array.from(document.querySelectorAll('.about-menu-item[data-about-target]'));
    const panels = Array.from(document.querySelectorAll('.about-topic[data-about-panel]'));
    if (items.length === 0 || panels.length === 0) return;

    const setActive = (id) => {
      items.forEach((a) => a.classList.toggle('is-active', a.dataset.aboutTarget === id));
      panels.forEach((p) => p.classList.toggle('is-active', p.dataset.aboutPanel === id));
    };

    // Default active (from markup), but allow deep-link by hash.
    const hash = (window.location.hash || '').replace('#', '');
    const hasHashPanel = panels.some(p => p.id === hash);
    if (hasHashPanel) setActive(hash);

    items.forEach((a) => {
      a.addEventListener('click', (e) => {
        e.preventDefault();

        const id = a.dataset.aboutTarget;
        if (!id) return;

        setActive(id);

        // Update hash without jumping the page.
        if (history && history.replaceState) {
          history.replaceState(null, '', `#${id}`);
        } else {
          window.location.hash = `#${id}`;
        }
      });
    });
  }

  setupAboutSidebarToggle() {
    const submenu = document.getElementById('about-sidebar-submenu');
    const toggleLinks = Array.from(document.querySelectorAll('.sidebar-about-toggle[data-toggle-about-submenu="true"]'));
    if (!submenu || toggleLinks.length === 0) return;

    const setActiveAboutPanel = (id) => {
      const items = Array.from(document.querySelectorAll('.about-menu-item[data-about-target]'));
      const panels = Array.from(document.querySelectorAll('.about-topic[data-about-panel]'));

      items.forEach((a) => {
        a.classList.toggle('is-active', a.dataset.aboutTarget === id);
      });
      panels.forEach((p) => {
        p.classList.toggle('is-active', p.dataset.aboutPanel === id);
      });
    };

    const showSubmenu = (shouldShow) => {
      submenu.classList.toggle('is-open', shouldShow);
      submenu.setAttribute('aria-hidden', shouldShow ? 'false' : 'true');
      toggleLinks.forEach((link) => {
        link.setAttribute('aria-expanded', shouldShow ? 'true' : 'false');
      });
    };

    // Keep submenu closed on initial page load.
    // It will open only when the user clicks the About arrow toggle.
    showSubmenu(false);

    toggleLinks.forEach((link) => {
      link.addEventListener('click', (e) => {
        e.preventDefault();

        const nextOpen = !submenu.classList.contains('is-open');
        showSubmenu(nextOpen);

        // When opening the submenu from the parent toggle, show the About overview content.
        if (nextOpen) setActiveAboutPanel('about');

        // Bring About section into view so the content updates are visible.
        const aboutSection = document.getElementById('about');
        if (aboutSection && typeof aboutSection.scrollIntoView === 'function') {
          aboutSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });
  }

  setupHeroSlider() {
    const slider = document.querySelector('.hero-slider');
    if (!slider) return;

    const track = slider.querySelector('.hero-slider-track');
    const slides = Array.from(slider.querySelectorAll('.hero-slide'));
    const dots = Array.from(slider.querySelectorAll('.hero-slider-dot'));

    if (!track || slides.length === 0) return;

    let index = 0;
    let timerId = null;

    const setIndex = (nextIndex) => {
      index = (nextIndex + slides.length) % slides.length;
      track.style.transform = `translateX(-${index * 100}%)`;
      dots.forEach((dot, i) => {
        dot.setAttribute('aria-selected', i === index ? 'true' : 'false');
      });
    };

    const start = () => {
      stop();
      timerId = window.setInterval(() => setIndex(index + 1), 4500);
    };

    const stop = () => {
      if (timerId) window.clearInterval(timerId);
      timerId = null;
    };

    dots.forEach((dot) => {
      dot.addEventListener('click', () => {
        const slide = Number.parseInt(dot.dataset.slide || '0', 10);
        if (Number.isFinite(slide)) {
          setIndex(slide);
          start();
        }
      });
    });

    // Pause on hover/focus for accessibility.
    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', start);
    slider.addEventListener('focusin', stop);
    slider.addEventListener('focusout', start);

    setIndex(0);
    start();
  }

  loadYear() {
    // Set current year in footer
    const yearElement = document.getElementById('year');
    if (yearElement) {
      yearElement.textContent = new Date().getFullYear();
    }
  }

  // Utility method to show notifications
  showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
      notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
      notification.classList.remove('show');
      setTimeout(() => {
        notification.remove();
      }, 300);
    }, 3000);
  }
}

// Initialize UI controller when DOM is ready and expose globally
document.addEventListener('DOMContentLoaded', () => {
  window.uiController = new UIController();
});
