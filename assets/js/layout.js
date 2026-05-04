/**
 * LAYOUT.JS - Responsive Layout Control (Stage 7-D)
 * 
 * Features:
 * - Sidebar drawer toggle (mobile only)
 * - Edge swipe gestures to open/close sidebar
 * - Focus management (open → focus sidebar, close → focus toggle)
 * - Escape key to close drawer
 * - Click overlay to close drawer
 * 
 * Usage: Include in footer.php before closing </body>
 */

const Layout = (() => {
  let touchStartX = 0;
  let touchStartY = 0;

  /**
   * Initialize sidebar drawer toggle button
   */
  function initSidebar() {
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (!toggle || !sidebar) return;

    /**
     * Open sidebar drawer
     */
    function open() {
      sidebar.classList.add('is-open');
      if (overlay) overlay.classList.add('is-visible');
      toggle.setAttribute('aria-expanded', 'true');
      // Focus sidebar for accessibility
      sidebar.focus();
    }

    /**
     * Close sidebar drawer
     */
    function close() {
      sidebar.classList.remove('is-open');
      if (overlay) overlay.classList.remove('is-visible');
      toggle.setAttribute('aria-expanded', 'false');
      // Return focus to toggle button
      toggle.focus();
    }

    /**
     * Toggle sidebar on button click
     */
    toggle.addEventListener('click', () => {
      sidebar.classList.contains('is-open') ? close() : open();
    });

    /**
     * Close drawer when overlay clicked
     */
    if (overlay) {
      overlay.addEventListener('click', close);
    }

    /**
     * Close drawer on Escape key
     */
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && sidebar.classList.contains('is-open')) {
        close();
      }
    });

    /**
     * Close sidebar when a link is clicked (navigate)
     */
    const sidebarLinks = sidebar.querySelectorAll('a');
    sidebarLinks.forEach((link) => {
      link.addEventListener('click', () => {
        // Only close on actual page navigation
        // (not on anchor links within same page)
        const href = link.getAttribute('href');
        if (href && !href.startsWith('#')) {
          close();
        }
      });
    });
  }

  /**
   * Initialize swipe gestures for sidebar
   * - Swipe right from left edge (< 30px) to open sidebar
   * - Swipe left on open sidebar to close
   */
  function initSwipeGestures() {
    document.addEventListener(
      'touchstart',
      (e) => {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
      },
      { passive: true }
    );

    document.addEventListener(
      'touchend',
      (e) => {
        const touchEndX = e.changedTouches[0].clientX;
        const touchEndY = e.changedTouches[0].clientY;

        const dx = touchEndX - touchStartX; // positive = right
        const dy = Math.abs(touchEndY - touchStartY); // vertical distance

        // Ignore vertical swipes
        if (dy > 50) return;

        const sidebar = document.getElementById('sidebar');
        if (!sidebar) return;

        // Swipe right from left edge (< 30px) to open sidebar
        if (dx > 60 && touchStartX < 30 && !sidebar.classList.contains('is-open')) {
          sidebar.classList.add('is-open');
          const overlay = document.getElementById('sidebar-overlay');
          if (overlay) overlay.classList.add('is-visible');
          const toggle = document.getElementById('sidebar-toggle');
          if (toggle) toggle.setAttribute('aria-expanded', 'true');
        }

        // Swipe left to close sidebar
        if (dx < -60 && sidebar.classList.contains('is-open')) {
          sidebar.classList.remove('is-open');
          const overlay = document.getElementById('sidebar-overlay');
          if (overlay) overlay.classList.remove('is-visible');
          const toggle = document.getElementById('sidebar-toggle');
          if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.focus();
          }
        }
      },
      { passive: true }
    );
  }

  /**
   * Adjust main content padding when sidebar is visible on desktop
   */
  function initResponsiveLayout() {
    const updateLayout = () => {
      const isMobile = window.innerWidth < 768;
      const sidebar = document.getElementById('sidebar');
      if (!sidebar) return;

      if (isMobile) {
        sidebar.classList.remove('is-open');
      }
    };

    // Update on window resize
    window.addEventListener('resize', updateLayout);
    updateLayout();
  }

  /**
   * Initialize all layout features
   */
  function init() {
    // Only run after DOM is ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => {
        initSidebar();
        initSwipeGestures();
        initResponsiveLayout();
      });
    } else {
      initSidebar();
      initSwipeGestures();
      initResponsiveLayout();
    }
  }

  return {
    init,
  };
})();

// Auto-initialize on script load
Layout.init();
