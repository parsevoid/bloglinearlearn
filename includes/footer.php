  <?php if (!empty($showFooter)): ?>
  <footer class="site-footer">
    <div class="footer-divider">
      <span class="divider-line"></span>
      <span class="divider-ornament">✦</span>
      <span class="divider-line"></span>
    </div>
    <p class="footer-text">A CALMER MIND <span class="footer-star">✦</span> A BRIGHTER YOU</p>
  </footer>
  <?php endif; ?>

  <div class="mobile-menu-overlay" id="mobile-menu-overlay" aria-hidden="true" style="display: none;">
    <div class="mobile-menu-panel" id="mobile-menu-panel">
      <div class="mobile-menu-header">
        <span class="mobile-menu-title">Pages</span>
        <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Close menu">
          <?= icon('close') ?>
        </button>
      </div>
      <nav class="mobile-menu-nav">
        <?php
        $menuPages = getPages();
        foreach ($menuPages as $pg):
        ?>
        <a href="<?= $baseUrl ?? '' ?><?= $pg['slug'] === 'search' ? 'search.php' : '#' ?>" class="mobile-menu-link">
          <?= icon($pg['icon'], 'menu-link-icon') ?>
          <div>
            <span class="menu-link-title"><?= e($pg['title']) ?></span>
            <span class="menu-link-sub"><?= e($pg['subtitle']) ?></span>
          </div>
        </a>
        <?php endforeach; ?>
      </nav>
      <div class="mobile-menu-footer">
        <p class="mobile-menu-quote">"Better thoughts.<br>Brighter tomorrows."</p>
      </div>
    </div>
  </div>

  <script src="<?= $baseUrl ?? '' ?>js/app.js"></script>
</body>
</html>
