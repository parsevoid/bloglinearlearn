<aside class="sidebar sidebar-right">
  <div class="sidebar-card quote-feature-card">
    <div class="quote-card-content">
      <blockquote class="quote-text">&ldquo;A curious mind<br>stays open.&rdquo;</blockquote>
      <div class="quote-author">ALBERT EINSTEIN</div>
    </div>
    <div class="quote-botanical-deco">
      <img src="<?= $baseUrl ?? '' ?>assets/images/botanical-twig.png" alt="" class="botanical-twig-img" loading="lazy">
    </div>
  </div>

  <div class="sidebar-card focus-card" id="today-focus-card">
    <div class="focus-card-header">
      <div class="focus-title-group">
        <span class="card-header-icon"><?= icon('target', 'icon-focus-target') ?></span>
        <h3 class="focus-card-title">Today's Focus</h3>
      </div>
      <span class="focus-counter-pill" id="focus-counter-pill" title="Completed today">1/4</span>
    </div>
    <ul class="focus-checklist" id="focus-checklist">
      <li class="focus-item checked" data-task-id="0">
        <label class="focus-checkbox-label">
          <input type="checkbox" class="focus-checkbox" data-task="0" checked>
          <span class="custom-checkbox"></span>
          <span class="focus-text">10 minutes of deep focus</span>
        </label>
      </li>
      <li class="focus-item" data-task-id="1">
        <label class="focus-checkbox-label">
          <input type="checkbox" class="focus-checkbox" data-task="1">
          <span class="custom-checkbox"></span>
          <span class="focus-text">Learn something new</span>
        </label>
      </li>
      <li class="focus-item" data-task-id="2">
        <label class="focus-checkbox-label">
          <input type="checkbox" class="focus-checkbox" data-task="2">
          <span class="custom-checkbox"></span>
          <span class="focus-text">Take a mindful break</span>
        </label>
      </li>
      <li class="focus-item" data-task-id="3">
        <label class="focus-checkbox-label">
          <input type="checkbox" class="focus-checkbox" data-task="3">
          <span class="custom-checkbox"></span>
          <span class="focus-text">Be a little better today</span>
        </label>
      </li>
    </ul>
  </div>

  <div class="focus-outside-quote">
    <p class="focus-footer-quote">Small steps.<br>A sharper you.</p>
  </div>

  <div class="sidebar-card trending-card">
    <div class="trending-card-header">
      <div class="trending-title-group">
        <span class="card-header-icon"><?= icon('chart-bar', 'icon-trending-bars') ?></span>
        <h3 class="trending-card-title">Trending Topics</h3>
      </div>
    </div>
    <ul class="trending-list">
      <li class="trending-item">
        <a href="<?= $baseUrl ?? '' ?>category.php?slug=brain-health" class="trending-link">
          <span class="trending-num">01</span>
          <span class="trending-name">Brain Health</span>
          <span class="trending-arrow">&rarr;</span>
        </a>
      </li>
      <li class="trending-item">
        <a href="<?= $baseUrl ?? '' ?>category.php?slug=memory" class="trending-link">
          <span class="trending-num">02</span>
          <span class="trending-name">Memory</span>
          <span class="trending-arrow">&rarr;</span>
        </a>
      </li>
      <li class="trending-item">
        <a href="<?= $baseUrl ?? '' ?>category.php?slug=focus" class="trending-link">
          <span class="trending-num">03</span>
          <span class="trending-name">Focus</span>
          <span class="trending-arrow">&rarr;</span>
        </a>
      </li>
      <li class="trending-item">
        <a href="<?= $baseUrl ?? '' ?>category.php?slug=sleep" class="trending-link">
          <span class="trending-num">04</span>
          <span class="trending-name">Sleep</span>
          <span class="trending-arrow">&rarr;</span>
        </a>
      </li>
      <li class="trending-item">
        <a href="<?= $baseUrl ?? '' ?>category.php?slug=habits" class="trending-link">
          <span class="trending-num">05</span>
          <span class="trending-name">Habits</span>
          <span class="trending-arrow">&rarr;</span>
        </a>
      </li>
    </ul>
  </div>

  <div class="sidebar-right-bottom-motto">
    <span class="motto-divider-line"></span>
    <span class="motto-text">HEALTHIER MINDS<br>BRIGHTER LIVES</span>
  </div>
</aside>
