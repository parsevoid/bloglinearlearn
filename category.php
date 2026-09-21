<?php
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$category = $slug ? getCategoryBySlug($slug) : null;

if (!$category) {
    header('Location: index.php');
    exit;
}

$posts = getPostsByCategory($category['id']);
$currentPage = 'category';
$currentCategory = $category['slug'];
$baseUrl = '';
$pageTitle = $category['name'] . ' — ' . SITE_NAME;

include __DIR__ . '/includes/header.php';
?>

  <div class="page-layout">
    <?php include __DIR__ . '/includes/sidebar-left.php'; ?>

    <main class="main-content" id="main-content">
      <div class="reading-top-nav">
        <a href="index.php" class="reading-back-link">
          <span class="back-arrow">&larr;</span> Back to Home
        </a>
        <span class="reading-cat-badge"><?= strtoupper(e($category['name'])) ?></span>
      </div>

      <div class="section-header" style="margin-bottom: 20px;">
        <h1 class="section-title" style="font-size: 1.8rem; display: flex; align-items: center; gap: 10px;">
          <span style="color: #2b2723;"><?= icon($category['icon'], 'cat-title-icon') ?></span>
          <?= e($category['name']) ?>
        </h1>
        <span class="text-muted" style="font-size: 0.85rem; font-weight: 500;">
          <?= count($posts) ?> article<?= count($posts) !== 1 ? 's' : '' ?>
        </span>
      </div>

      <?php if (!empty($posts)): ?>
      <div class="articles-grid-3col">
        <?php foreach ($posts as $post): 
          $postThumb = $post['featured_image'] ?: 'assets/images/thumb-memory.jpg';
        ?>
        <article class="article-card">
          <a href="post.php?slug=<?= e($post['slug']) ?>" class="card-thumb-wrap">
            <img src="<?= e($postThumb) ?>" alt="<?= e($post['title']) ?>" class="card-thumb-img" loading="lazy">
            <span class="card-cat-badge"><?= strtoupper(e($category['name'])) ?></span>
          </a>
          <div class="card-body">
            <h3 class="card-title">
              <a href="post.php?slug=<?= e($post['slug']) ?>"><?= e($post['title']) ?></a>
            </h3>
            <p class="card-desc"><?= e($post['excerpt'] ?: truncateText($post['content'], 110)) ?></p>
            <div class="card-footer">
              <div class="card-read-time">
                <?= icon('clock', 'card-clock-icon') ?>
                <span><?= (int)($post['read_time'] ?: 6) ?> min read</span>
              </div>
              <button type="button" class="card-bookmark-btn" aria-label="Bookmark article">
                <?= icon('bookmark', 'card-bookmark-icon') ?>
              </button>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="empty-state" style="padding: 60px 20px; text-align: center;">
        <div class="empty-state-icon" style="font-size: 2rem; margin-bottom: 12px; color: #888;"><?= icon($category['icon']) ?></div>
        <h3 style="font-family: var(--font-display); font-size: 1.25rem; margin-bottom: 8px;">No articles in this topic yet</h3>
        <p class="text-muted">Check back soon for new insights on <?= e($category['name']) ?>.</p>
        <a href="index.php" class="btn-read-article" style="margin-top: 16px;">Return to Home</a>
      </div>
      <?php endif; ?>
    </main>

    <?php include __DIR__ . '/includes/sidebar-right.php'; ?>
  </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
