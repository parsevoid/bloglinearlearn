(() => {
  'use strict';

  const FOCUS_STORAGE_KEY = 'linearlearn_today_focus';

  function getTodayDateString() {
    const d = new Date();
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  function initTodayFocus() {
    const focusCard = document.getElementById('today-focus-card');
    const checklist = document.getElementById('focus-checklist');
    const counterPill = document.getElementById('focus-counter-pill');
    if (!checklist) return;

    const today = getTodayDateString();
    let focusData = null;

    try {
      const raw = localStorage.getItem(FOCUS_STORAGE_KEY);
      if (raw) focusData = JSON.parse(raw);
    } catch (e) {
      console.warn('Failed to parse focus tasks from localStorage', e);
    }

    if (!focusData || focusData.date !== today) {
      focusData = {
        date: today,
        tasks: {
          '0': true,
          '1': false,
          '2': false,
          '3': false
        }
      };
      try {
        localStorage.setItem(FOCUS_STORAGE_KEY, JSON.stringify(focusData));
      } catch (e) {}
    }

    const items = checklist.querySelectorAll('.focus-item');
    let completedCount = 0;

    items.forEach(item => {
      const taskId = item.getAttribute('data-task-id');
      const checkbox = item.querySelector('.focus-checkbox');
      const isChecked = !!focusData.tasks[taskId];

      if (checkbox) checkbox.checked = isChecked;
      if (isChecked) {
        item.classList.add('checked');
        completedCount++;
      } else {
        item.classList.remove('checked');
      }
    });

    updateFocusCounter(completedCount, items.length, focusCard, counterPill);

    if (!checklist.dataset.bound) {
      checklist.dataset.bound = 'true';
      checklist.addEventListener('change', (e) => {
        const checkbox = e.target.closest('.focus-checkbox');
        if (!checkbox) return;

        const item = checkbox.closest('.focus-item');
        const taskId = item.getAttribute('data-task-id');
        const isChecked = checkbox.checked;

        if (isChecked) {
          item.classList.add('checked');
        } else {
          item.classList.remove('checked');
        }

        let count = 0;
        const currentTasks = {};
        checklist.querySelectorAll('.focus-item').forEach(el => {
          const id = el.getAttribute('data-task-id');
          const cb = el.querySelector('.focus-checkbox');
          const val = cb ? cb.checked : false;
          currentTasks[id] = val;
          if (val) count++;
        });

        try {
          localStorage.setItem(FOCUS_STORAGE_KEY, JSON.stringify({
            date: today,
            tasks: currentTasks
          }));
        } catch (err) {}

        updateFocusCounter(count, items.length, focusCard, counterPill);
      });
    }
  }

  function updateFocusCounter(count, total, card, pill) {
    if (pill) pill.textContent = `${count}/${total}`;
    if (card) {
      if (count === total && total > 0) {
        card.classList.add('all-done');
      } else {
        card.classList.remove('all-done');
      }
    }
  }

  function initReadingFeatures() {
    const mainContent = document.getElementById('main-content');
    const progressFill = document.getElementById('reading-progress-fill');
    const copyBtn = document.getElementById('btn-copy-article');
    const bookmarkBtn = document.getElementById('btn-bookmark-post');

    if (mainContent && progressFill) {
      const updateScrollProgress = () => {
        const scrollable = mainContent.scrollHeight - mainContent.clientHeight;
        if (scrollable <= 0) {
          progressFill.style.width = '100%';
          return;
        }
        const pct = Math.min(100, Math.max(0, (mainContent.scrollTop / scrollable) * 100));
        progressFill.style.width = `${pct}%`;
      };

      mainContent.removeEventListener('scroll', updateScrollProgress);
      mainContent.addEventListener('scroll', updateScrollProgress, { passive: true });
      updateScrollProgress();
    }

    if (copyBtn) {
      copyBtn.addEventListener('click', async () => {
        try {
          await navigator.clipboard.writeText(window.location.href);
          const label = copyBtn.querySelector('.btn-action-label');
          if (label) {
            const originalText = label.textContent;
            label.textContent = 'Copied!';
            copyBtn.style.color = '#1a1a1a';
            copyBtn.style.borderColor = '#1a1a1a';
            setTimeout(() => {
              label.textContent = originalText;
              copyBtn.style.color = '';
              copyBtn.style.borderColor = '';
            }, 2000);
          }
        } catch (e) {
          prompt('Copy link to article:', window.location.href);
        }
      });
    }

    if (bookmarkBtn) {
      const articleSlug = new URLSearchParams(window.location.search).get('slug') || '';
      let bookmarks = [];
      try {
        bookmarks = JSON.parse(localStorage.getItem('linearlearn_bookmarks') || '[]');
      } catch (e) {}

      if (articleSlug && bookmarks.includes(articleSlug)) {
        bookmarkBtn.classList.add('saved');
        const label = bookmarkBtn.querySelector('.btn-action-label');
        if (label) label.textContent = 'Saved';
      }

      bookmarkBtn.addEventListener('click', () => {
        if (!articleSlug) return;
        try {
          bookmarks = JSON.parse(localStorage.getItem('linearlearn_bookmarks') || '[]');
        } catch (e) {}

        const idx = bookmarks.indexOf(articleSlug);
        const label = bookmarkBtn.querySelector('.btn-action-label');
        if (idx >= 0) {
          bookmarks.splice(idx, 1);
          bookmarkBtn.classList.remove('saved');
          if (label) label.textContent = 'Save';
        } else {
          bookmarks.push(articleSlug);
          bookmarkBtn.classList.add('saved');
          if (label) label.textContent = 'Saved';
        }
        try {
          localStorage.setItem('linearlearn_bookmarks', JSON.stringify(bookmarks));
        } catch (e) {}
      });
    }
  }

  let isNavigating = false;

  function updateActiveNav(targetUrl) {
    const urlObj = new URL(targetUrl, window.location.origin);
    const path = urlObj.pathname;
    const search = urlObj.search;
    const fullTarget = path + search;

    document.querySelectorAll('.sidebar-nav-link').forEach(link => {
      const href = link.getAttribute('href');
      if (!href) return;
      const linkUrl = new URL(href, window.location.origin);
      const linkFull = linkUrl.pathname + linkUrl.search;

      const isHomeMatch = (fullTarget === '/' || fullTarget.endsWith('index.php')) && (linkFull === '/' || linkFull.endsWith('index.php'));
      const isExactMatch = linkFull === fullTarget;

      if (isHomeMatch || isExactMatch) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
  }

  async function loadPageSmooth(url, pushState = true) {
    if (isNavigating) return;
    const mainContainer = document.getElementById('main-content');
    if (!mainContainer) {
      window.location.href = url;
      return;
    }

    isNavigating = true;
    updateActiveNav(url);
    mainContainer.classList.add('is-transitioning');

    try {
      const response = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }

      const htmlText = await response.text();
      const parser = new DOMParser();
      const newDoc = parser.parseFromString(htmlText, 'text/html');

      const newMain = newDoc.getElementById('main-content') || newDoc.querySelector('.main-content');
      if (!newMain) {
        window.location.href = url;
        return;
      }

      await new Promise(r => setTimeout(r, 120));

      mainContainer.innerHTML = newMain.innerHTML;
      mainContainer.className = newMain.className;

      if (newDoc.title) {
        document.title = newDoc.title;
      }

      if (pushState) {
        window.history.pushState({ url }, '', url);
      }

      mainContainer.scrollTop = 0;
      initReadingFeatures();

    } catch (err) {
      console.error('Smooth navigation fallback:', err);
      window.location.href = url;
      return;
    } finally {
      setTimeout(() => {
        mainContainer.classList.remove('is-transitioning');
        isNavigating = false;
      }, 50);
    }
  }

  function initSmoothNavigation() {
    document.addEventListener('click', (e) => {
      if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

      const anchor = e.target.closest('a');
      if (!anchor) return;

      const href = anchor.getAttribute('href');
      if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;

      const targetUrl = new URL(href, window.location.href);
      if (targetUrl.origin !== window.location.origin) return;
      if (targetUrl.pathname.includes('/admin/') || targetUrl.pathname.includes('/uploads/')) return;

      if (targetUrl.href === window.location.href) {
        e.preventDefault();
        const main = document.getElementById('main-content');
        if (main) main.scrollTo({ top: 0, behavior: 'smooth' });
        return;
      }

      e.preventDefault();
      loadPageSmooth(targetUrl.href, true);
    });

    window.addEventListener('popstate', () => {
      loadPageSmooth(window.location.href, false);
    });
  }

  const THEME_STORAGE_KEY = 'linearlearn_theme';

  function initDarkMode() {
    const toggleBtn = document.getElementById('theme-toggle-btn');
    if (!toggleBtn) return;

    if (!toggleBtn.dataset.themeBound) {
      toggleBtn.dataset.themeBound = 'true';
      toggleBtn.addEventListener('click', () => {
        const isDark = document.documentElement.classList.toggle('dark-mode');
        localStorage.setItem(THEME_STORAGE_KEY, isDark ? 'dark' : 'light');
      });
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    initTodayFocus();
    initReadingFeatures();
    initSmoothNavigation();
    initDarkMode();

    setInterval(() => {
      initTodayFocus();
    }, 60000);
  });

})();
