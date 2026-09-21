<?php
$currentPage = $currentPage ?? 'home';
$pageTitle = $pageTitle ?? SITE_NAME . ' — ' . SITE_TAGLINE;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($pageDescription ?? 'Thoughts, stories and practical guides for a calmer, healthier and happier life.') ?>">
  <title><?= e($pageTitle) ?></title>
  <link rel="icon" type="image/webp" href="<?= $baseUrl ?? '' ?>assets/favicon.webp">
  <link rel="stylesheet" href="<?= $baseUrl ?? '' ?>css/theme.css">
  <link rel="stylesheet" href="<?= $baseUrl ?? '' ?>css/blog.css">
  <link rel="stylesheet" href="<?= $baseUrl ?? '' ?>css/responsive.css">
  <link rel="stylesheet" href="<?= $baseUrl ?? '' ?>css/darkmode.css">
  <?php if (isset($extraCSS)): ?>
    <link rel="stylesheet" href="<?= $baseUrl ?? '' ?><?= $extraCSS ?>">
  <?php endif; ?>
  <script>
    (function(){
      if(localStorage.getItem('linearlearn_theme')==='dark'){
        document.documentElement.classList.add('dark-mode');
      }
    })();
  </script>
</head>
<body class="paper-bg <?= ($currentPage ?? '') === 'home' ? 'home-layout-locked' : 'page-scrollable' ?>">

  <header class="site-header">
    <div class="header-inner">
      <a href="<?= $baseUrl ?? '' ?>index.php" class="header-brand">
        <span class="brand-brain-wrap">
          <img src="<?= $baseUrl ?? '' ?>assets/favicon.webp" alt="Brain" class="brand-brain-icon">
        </span>
        <span class="brand-text-group">
          <span class="brand-domain">blog@Linearlearn.com</span>
          <span class="brand-subtext">A HEALTHIER MIND &nbsp;&middot;&nbsp; A BRIGHTER TOMORROW</span>
        </span>
      </a>

      <div class="header-center-search">
        <form action="<?= $baseUrl ?? '' ?>search.php" method="GET" class="header-search-form">
          <span class="search-icon-wrap"><?= icon('search', 'search-icon') ?></span>
          <input type="text" name="q" placeholder="Search articles, topics..." class="header-search-input" autocomplete="off">
        </form>
      </div>

      <div class="header-right-actions">
        <button type="button" class="theme-toggle-pill" id="theme-toggle-btn" aria-label="Toggle dark mode">
          <span class="toggle-knob">
            <svg class="toggle-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
            </svg>
            <svg class="toggle-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
          </span>
          <svg class="toggle-track-icon toggle-track-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
          </svg>
          <svg class="toggle-track-icon toggle-track-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
          </svg>
        </button>
        <div class="header-motto-stack">
          <span>LEARN</span>
          <span>REFLECT</span>
          <span>IMPROVE</span>
        </div>
      </div>
    </div>
  </header>