<?php
$navItems = [
    ['name' => 'Home', 'slug' => 'home', 'icon' => 'home', 'url' => ($baseUrl ?? '') . 'index.php'],
    ['name' => 'Brain Health', 'slug' => 'brain-health', 'icon' => 'brain', 'url' => ($baseUrl ?? '') . 'category.php?slug=brain-health'],
    ['name' => 'Memory', 'slug' => 'memory', 'icon' => 'database', 'url' => ($baseUrl ?? '') . 'category.php?slug=memory'],
    ['name' => 'Focus', 'slug' => 'focus', 'icon' => 'target', 'url' => ($baseUrl ?? '') . 'category.php?slug=focus'],
    ['name' => 'Learning', 'slug' => 'learning', 'icon' => 'book-open', 'url' => ($baseUrl ?? '') . 'category.php?slug=learning'],
    ['name' => 'Sleep', 'slug' => 'sleep', 'icon' => 'moon', 'url' => ($baseUrl ?? '') . 'category.php?slug=sleep'],
    ['name' => 'Mindfulness', 'slug' => 'mindfulness', 'icon' => 'leaf', 'url' => ($baseUrl ?? '') . 'category.php?slug=mindfulness'],
    ['name' => 'Habits', 'slug' => 'habits', 'icon' => 'check-circle', 'url' => ($baseUrl ?? '') . 'category.php?slug=habits'],
    ['name' => 'Neuroscience', 'slug' => 'neuroscience', 'icon' => 'network', 'url' => ($baseUrl ?? '') . 'category.php?slug=neuroscience'],
];
?>
<aside class="sidebar sidebar-left">
  <div class="sidebar-nav-container">
    <nav class="sidebar-menu">
      <?php foreach ($navItems as $item): 
        $isActive = ($currentPage === $item['slug']) || ($currentPage === 'home' && $item['slug'] === 'home');
      ?>
      <a href="<?= $item['url'] ?>" class="sidebar-nav-link <?= $isActive ? 'active' : '' ?>">
        <span class="nav-link-icon-wrap"><?= icon($item['icon'], 'nav-link-icon') ?></span>
        <span class="nav-link-label"><?= e($item['name']) ?></span>
      </a>
      <?php endforeach; ?>
    </nav>
  </div>

  <div class="sidebar-left-footer">
    <p class="sidebar-italic-quote">&ldquo;A healthier mind<br>builds a brighter<br>tomorrow.&rdquo;</p>
    <div class="sidebar-sub-tag">
      <span>KNOW MORE</span>
      <span class="sub-tag-sep">&bull;</span>
      <span>LIVE BETTER</span>
    </div>
  </div>
</aside>
