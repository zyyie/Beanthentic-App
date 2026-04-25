// UI interactions and animations

const BEANTHENTIC_USER_KEY = 'beanthentic_user';

function escapeHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function getBeanthenticUser() {
  try {
    const raw = localStorage.getItem(BEANTHENTIC_USER_KEY);
    if (!raw) return null;
    const u = JSON.parse(raw);
    if (u && typeof u.email === 'string' && u.email) return u;
  } catch (e) {
    /* ignore */
  }
  return null;
}

function refreshHeaderAuthUI() {
  const user = getBeanthenticUser();
  const snippet = document.getElementById('header-account-snippet');
  const display = document.getElementById('header-account-display');
  const drawerAccount = document.getElementById('header-drawer-account');
  const signOutBtn = document.getElementById('header-sign-out-btn');
  const loginHref = (() => {
    try {
      return new URL('login.php', window.location.href).href;
    } catch (e) {
      return 'login.php';
    }
  })();

  if (user) {
    const label = user.name && String(user.name).trim()
      ? String(user.name).trim()
      : user.email.split('@')[0];
    if (snippet) snippet.hidden = false;
    if (display) display.textContent = label;
    if (drawerAccount) {
      drawerAccount.innerHTML = `
        <p class="header-drawer-account-label">Signed in</p>
        <p class="header-drawer-account-name">${escapeHtml(label)}</p>
        <p class="header-drawer-account-email">${escapeHtml(user.email)}</p>`;
    }
    if (signOutBtn) signOutBtn.hidden = false;
  } else {
    if (snippet) snippet.hidden = true;
    if (display) display.textContent = '';
    if (drawerAccount) {
      drawerAccount.innerHTML = `<p class="header-drawer-guest"><a href="${loginHref}">Sign in</a> to save your preferences and see your account here.</p>`;
    }
    if (signOutBtn) signOutBtn.hidden = true;
  }

  window.dispatchEvent(new CustomEvent('beanthentic-auth-changed'));
}

/** Keeps the brown variety pill in sync with the visible About panel. */
function syncAboutPillLabel(panelId) {
  const pillText = document.querySelector('.about-pill-text');
  if (!pillText || !panelId) return;
  const panel = document.querySelector(`.about-topic[data-about-panel="${panelId}"]`);
  const label = panel && panel.getAttribute('data-about-pill-label');
  if (label) pillText.textContent = label;
}

class UIController {
  constructor() {
    this.init();
  }

  init() {
    this.setupGlobalPageLoader();
    this.setupAnimations();
    this.setupInteractions();
    this.setupHeroSlider();
    this.setupHomeAboutViewSwitch();
    this.setupAboutSidebarToggle();
    this.setupHomepageSidebarActive();
    this.setupHomepageSidebarDrawer();
    this.setupAboutNavDropdown();
    this.setupAboutMenu();
    this.setupBottomNavAboutMenu();
    this.setupMobileMainNav();
    this.setupHeaderNotifications();
    this.setupHeaderAccountShortcut();
    this.setupHeaderNavDrawer();
    this.loadYear();
  }

  setupHeaderAccountShortcut() {
    const snippet = document.getElementById('header-account-snippet');
    if (!snippet || snippet.dataset.bound === '1') return;
    snippet.dataset.bound = '1';
    snippet.setAttribute('role', 'button');
    snippet.setAttribute('tabindex', '0');
    snippet.setAttribute('aria-label', 'Open account');

    const goAccount = () => {
      try {
        window.location.assign(new URL('account.php', window.location.href).href);
      } catch (_err) {
        window.location.assign('account.php');
      }
    };

    snippet.addEventListener('click', goAccount);
    snippet.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        goAccount();
      }
    });
  }

  setupGlobalPageLoader() {
    let loader = document.getElementById('page-loader');
    if (!loader) {
      loader = document.createElement('div');
      loader.id = 'page-loader';
      loader.style.position = 'fixed';
      loader.style.inset = '0';
      loader.style.background = '#ffffff';
      loader.style.display = 'flex';
      loader.style.alignItems = 'center';
      loader.style.justifyContent = 'center';
      loader.style.flexDirection = 'column';
      loader.style.zIndex = '99999';
      loader.innerHTML =
        '<img src="coffee_bean_loading.png" alt="Loading..." style="width:96px;height:96px;object-fit:contain;animation:beanthentic-bounce 1s ease-in-out infinite;" /><p style="margin-top:10px;color:#6b7280;font-size:14px;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;">Please wait for a moment.</p>';

      if (!document.getElementById('beanthentic-loader-style')) {
        const style = document.createElement('style');
        style.id = 'beanthentic-loader-style';
        style.textContent =
          '@keyframes beanthentic-bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}';
        document.head.appendChild(style);
      }
      document.body.appendChild(loader);
    }

    let startedAt = Date.now();
    const minVisibleMs = 450;
    let hideTimer = null;

    const hideLoader = () => {
      if (!loader) return;
      const elapsed = Date.now() - startedAt;
      const delay = Math.max(0, minVisibleMs - elapsed);
      if (hideTimer) clearTimeout(hideTimer);
      hideTimer = setTimeout(() => {
        loader.style.display = 'none';
      }, delay);
    };

    window.addEventListener('load', hideLoader);
    if (hideTimer) clearTimeout(hideTimer);
    hideTimer = setTimeout(hideLoader, minVisibleMs + 2200);

    document.addEventListener(
      'click',
      (e) => {
        const a = e.target && e.target.closest ? e.target.closest('a') : null;
        if (!a) return;
        const href = a.getAttribute('href') || '';
        if (!href) return;
        if (href.startsWith('#')) return;
        if (a.getAttribute('data-no-loader') === 'true') return;
        if (a.hasAttribute('download')) return;
        if (/^\s*javascript:/i.test(href)) return;

        loader.style.display = 'flex';
        startedAt = Date.now();
        if (hideTimer) clearTimeout(hideTimer);
        hideTimer = null;
      },
      true
    );
  }

  setupHeaderNavDrawer() {
    const btn = document.getElementById('header-burger-btn');
    const root = document.getElementById('header-nav-drawer');
    if (!btn || !root) return;

    const backdrop = root.querySelector('.header-nav-drawer-backdrop');
    const panel = root.querySelector('.header-nav-drawer-panel');
    const signOutBtn = document.getElementById('header-sign-out-btn');

    const close = () => {
      root.classList.remove('is-open');
      btn.setAttribute('aria-expanded', 'false');
      btn.setAttribute('aria-label', 'Open menu');
      document.body.classList.remove('header-drawer-open');
      window.setTimeout(() => {
        if (!root.classList.contains('is-open')) root.hidden = true;
      }, 280);
    };

    const open = () => {
      root.hidden = false;
      requestAnimationFrame(() => {
        root.classList.add('is-open');
        btn.setAttribute('aria-expanded', 'true');
        btn.setAttribute('aria-label', 'Close menu');
        document.body.classList.add('header-drawer-open');
      });
    };

    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      if (root.classList.contains('is-open')) close();
      else open();
    });

    if (backdrop) backdrop.addEventListener('click', close);

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && root.classList.contains('is-open')) close();
    });

    root.querySelectorAll('a.header-drawer-link').forEach((a) => {
      a.addEventListener('click', (e) => {
        // Social → open Beanthentic Coffee Facebook page directly.
        if (a.classList.contains('header-drawer-link--social')) {
          e.preventDefault();
          try {
            window.location.assign('https://www.facebook.com/share/1G6kwxhijL/');
          } catch (_err) {
            window.location.assign('https://www.facebook.com/share/1G6kwxhijL/');
          }
          window.setTimeout(close, 150);
          return;
        }
        const href = a.getAttribute('href');
        // Same-window relative pages (e.g. privacy.php): resolve to absolute URL so file:// WebViews navigate reliably.
        if (href && !href.startsWith('#') && !/^https?:/i.test(href) && href.indexOf(':') === -1) {
          e.preventDefault();
          try {
            window.location.assign(new URL(href, window.location.href).href);
          } catch (_err) {
            window.location.assign(href);
          }
        }
        window.setTimeout(close, 150);
      });
    });

    if (signOutBtn) {
      signOutBtn.addEventListener('click', () => {
        try {
          localStorage.removeItem(BEANTHENTIC_USER_KEY);
        } catch (err) {
          /* ignore */
        }
        refreshHeaderAuthUI();
        close();
      });
    }

    window.addEventListener('storage', (e) => {
      if (e.key === BEANTHENTIC_USER_KEY) refreshHeaderAuthUI();
    });

    refreshHeaderAuthUI();
  }

  setupMobileMainNav() {
    const toggle = document.getElementById('nav-menu-toggle');
    const navLinks = document.getElementById('main-nav-links');
    const overlay = document.getElementById('nav-mobile-overlay');
    if (!toggle || !navLinks) return;

    const isDesktop = () => window.matchMedia('(min-width: 901px)').matches;

    const close = () => {
      navLinks.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', 'Open menu');
      if (overlay) {
        overlay.classList.remove('is-visible');
        overlay.hidden = true;
        overlay.setAttribute('aria-hidden', 'true');
      }
      document.body.classList.remove('nav-menu-open');
    };

    const open = () => {
      navLinks.classList.add('is-open');
      toggle.setAttribute('aria-expanded', 'true');
      toggle.setAttribute('aria-label', 'Close menu');
      if (overlay) {
        overlay.classList.add('is-visible');
        overlay.hidden = false;
        overlay.setAttribute('aria-hidden', 'false');
      }
      document.body.classList.add('nav-menu-open');
    };

    toggle.addEventListener('click', (e) => {
      e.stopPropagation();
      if (isDesktop()) return;
      if (navLinks.classList.contains('is-open')) close();
      else open();
    });

    if (overlay) {
      overlay.addEventListener('click', () => close());
    }

    window.addEventListener('resize', () => {
      if (isDesktop()) close();
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') close();
    });

    // Close on link selection (keep About toggles open until user picks a destination)
    navLinks.addEventListener('click', (e) => {
      if (isDesktop()) return;
      const a = e.target && e.target.closest ? e.target.closest('a') : null;
      if (!a || !navLinks.contains(a)) return;
      if (a.id === 'about-nav-toggle' || a.id === 'about-history-toggle') return;
      setTimeout(close, 80);
    });
  }

  setupHeaderNotifications() {
    const btn = document.getElementById('header-notifications-btn');
    if (!btn) return;
    btn.addEventListener('click', () => {
      this.showNotification('No new notifications yet.', 'info');
    });
  }

  setupAboutNavDropdown() {
    const toggle = document.getElementById('about-nav-toggle');
    const dropdown = document.getElementById('about-nav-dropdown');
    if (!toggle || !dropdown) return;

    const historyToggle = document.getElementById('about-history-toggle');
    const historySubmenu = document.getElementById('about-history-submenu');

    const open = () => {
      dropdown.hidden = false;
      dropdown.setAttribute('aria-hidden', 'false');
      toggle.setAttribute('aria-expanded', 'true');
    };

    const close = () => {
      if (historySubmenu) {
        historySubmenu.hidden = true;
        historySubmenu.setAttribute('aria-hidden', 'true');
      }
      if (historyToggle) {
        historyToggle.setAttribute('aria-expanded', 'false');
      }
      dropdown.hidden = true;
      dropdown.setAttribute('aria-hidden', 'true');
      toggle.setAttribute('aria-expanded', 'false');
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

    // History submenu (Liberica / Robusta / Excelsa) opens when "History" is chosen; see setupHomeAboutViewSwitch.

    // Close after selecting an item
    dropdown.addEventListener('click', (e) => {
      const link = e.target && e.target.closest ? e.target.closest('a.about-menu-item') : null;
      if (!link) return;
      // Don't close if the user is toggling the nested History submenu.
      if (link.id === 'about-history-toggle') return;
      close();
    });
  }

  setupBottomNavAboutMenu() {
    const toggle = document.getElementById('bottom-nav-about-toggle');
    const menu = document.getElementById('bottom-nav-about-menu');
    if (!toggle || !menu) return;

    const historyToggle = document.getElementById('bottom-nav-history-toggle');
    const historySubmenu = document.getElementById('bottom-nav-history-submenu');

    const collapseHistorySubmenu = () => {
      if (!historySubmenu || !historyToggle) return;
      historySubmenu.hidden = true;
      historyToggle.setAttribute('aria-expanded', 'false');
    };

    const open = () => {
      menu.hidden = false;
      toggle.setAttribute('aria-expanded', 'true');
    };

    const close = () => {
      menu.hidden = true;
      toggle.setAttribute('aria-expanded', 'false');
      collapseHistorySubmenu();
    };

    const isOpen = () => toggle.getAttribute('aria-expanded') === 'true';

    toggle.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (isOpen()) close();
      else open();
    });

    if (historyToggle && historySubmenu) {
      historyToggle.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const expanded = historyToggle.getAttribute('aria-expanded') === 'true';
        if (expanded) {
          historySubmenu.hidden = true;
          historyToggle.setAttribute('aria-expanded', 'false');
        } else if (document.getElementById('about-mission-vision')) {
          historySubmenu.hidden = false;
          historyToggle.setAttribute('aria-expanded', 'true');
          window.dispatchEvent(
            new CustomEvent('beanthentic-goto-about-panel', { detail: { panel: 'about-history' } })
          );
        } else {
          const homeNav = document.querySelector('#nav-home[href]');
          let url = 'index.php#about-history';
          if (homeNav) {
            const href = homeNav.getAttribute('href') || '';
            if (href.includes('#')) url = `${href.replace(/#.*$/, '')}#about-history`;
            else url = `${href.replace(/\/?$/, '')}#about-history`;
          }
          window.location.assign(url);
        }
      });
    }

    document.addEventListener('click', (e) => {
      const t = e.target;
      if (!t) return;
      if (menu.contains(t) || toggle.contains(t)) return;
      close();
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') close();
    });

    menu.addEventListener('click', (e) => {
      const a = e.target && e.target.closest ? e.target.closest('a.app-bottom-nav-about-item') : null;
      if (!a) return;
      close();
    });
  }

  setupHomeAboutViewSwitch() {
    const homeSection = document.getElementById('home');
    const aboutMissionSection = document.getElementById('about-mission-vision');
    if (!homeSection || !aboutMissionSection) return;

    /**
     * History + Liberica/Robusta/Excelsa: walang homepage hero sa taas (parang hiwalay na page).
     * Ibang About tabs: hero + about, scroll normal.
     */
    const aboutPanelsWithoutHomeHero = new Set([
      'about-history',
      'about-liberica',
      'about-robusta',
      'about-excelsa',
    ]);

    const setView = (view, aboutPanelId) => {
      const showHome = view === 'home';
      const hideHomeHero = !showHome && aboutPanelsWithoutHomeHero.has(aboutPanelId);

      homeSection.hidden = !showHome && hideHomeHero;
      homeSection.setAttribute(
        'aria-hidden',
        showHome ? 'false' : hideHomeHero ? 'true' : 'false'
      );
      aboutMissionSection.hidden = showHome;
      aboutMissionSection.setAttribute('aria-hidden', showHome ? 'true' : 'false');
      document.body.classList.toggle('beanthentic-about-only', hideHomeHero);
    };

    const scrollAboutSectionToTop = (panelId) => {
      requestAnimationFrame(() => {
        let targetEl = null;
        if (panelId && panelId !== 'about-mission-vision') {
          targetEl = document.getElementById(panelId);
        }
        if (targetEl && aboutMissionSection.contains(targetEl)) {
          targetEl.scrollIntoView({ behavior: 'auto', block: 'start' });
        } else {
          aboutMissionSection.scrollIntoView({ behavior: 'auto', block: 'start' });
        }
      });
    };

    const activateAboutPanel = (id) => {
      const panels = Array.from(document.querySelectorAll('.about-topic[data-about-panel]'));
      if (panels.length === 0) return id;

      const hasTarget = panels.some((p) => p.dataset.aboutPanel === id);
      const fallbackId = 'about-mission-vision';
      const nextId = hasTarget ? id : fallbackId;
      panels.forEach((p) => p.classList.toggle('is-active', p.dataset.aboutPanel === nextId));
      syncAboutPillLabel(nextId);
      return nextId;
    };

    const emitHashSync = () => {
      window.dispatchEvent(new Event('hashchange'));
    };

    const gotoAboutPanel = (rawId) => {
      if (!rawId || !String(rawId).startsWith('about-')) return;
      const panelId = activateAboutPanel(rawId);
      setView('about', panelId);
      scrollAboutSectionToTop(panelId);
      if (history && history.replaceState) {
        history.replaceState(null, '', `#${panelId}`);
        emitHashSync();
      } else {
        window.location.hash = `#${panelId}`;
      }
    };

    window.addEventListener('beanthentic-goto-about-panel', (ev) => {
      const id = ev.detail && ev.detail.panel;
      if (typeof id === 'string') gotoAboutPanel(id);
    });

    const applyHashToView = () => {
      const h = (window.location.hash || '').replace(/^#/, '');
      if (h && h !== 'home' && h.startsWith('about-')) {
        const panelId = activateAboutPanel(h);
        setView('about', panelId);
        scrollAboutSectionToTop(panelId);
      } else {
        setView('home');
      }
    };

    applyHashToView();
    window.addEventListener('hashchange', applyHashToView);

    // Bottom bar & in-page links use href="#about-…" while About section may still be hidden.
    // Run before navigation.js smooth-scroll so we unhide first (capture phase).
    document.addEventListener(
      'click',
      (e) => {
        const a = e.target.closest('a[href^="#about-"]');
        if (!a) return;
        if (a.dataset.noScroll === 'true' || a.hasAttribute('data-no-scroll')) return;
        if (a.classList.contains('about-menu-item')) return;

        const id = (a.getAttribute('href') || '').slice(1);
        if (!id.startsWith('about-')) return;

        e.preventDefault();
        e.stopImmediatePropagation();

        gotoAboutPanel(id);
      },
      true
    );

    document.querySelectorAll('.about-menu-item[data-about-target]').forEach((item) => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        const historySubmenu = document.getElementById('about-history-submenu');
        if (item.id === 'about-history-toggle' && historySubmenu) {
          const isExpanded = item.getAttribute('aria-expanded') === 'true';
          historySubmenu.hidden = isExpanded;
          historySubmenu.setAttribute('aria-hidden', isExpanded ? 'true' : 'false');
          item.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
          return;
        }

        const targetId = item.dataset.aboutTarget || 'about-mission-vision';

        if (historySubmenu && !item.classList.contains('about-history-subitem')) {
          historySubmenu.hidden = true;
          historySubmenu.setAttribute('aria-hidden', 'true');
          const inPageHistoryToggle = document.getElementById('about-history-toggle');
          if (inPageHistoryToggle) inPageHistoryToggle.setAttribute('aria-expanded', 'false');
        }

        gotoAboutPanel(targetId);
      });
    });

    document.querySelectorAll('a[href="#home"], .logo').forEach((link) => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        setView('home');

        if (history && history.replaceState) {
          history.replaceState(null, '', '#home');
          emitHashSync();
        } else {
          window.location.hash = '#home';
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
      syncAboutPillLabel(id);
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

        // Keep About navigation behavior consistent: every About item
        // should bring the About section into view.
        const aboutSection = document.getElementById('about-mission-vision');
        if (aboutSection && typeof aboutSection.scrollIntoView === 'function') {
          aboutSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

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
      syncAboutPillLabel(id);
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
