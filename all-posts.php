<?php
require_once __DIR__ . '/includes/functions.php';

$currentPage = 'all-posts';
$baseUrl = '';
$pageTitle = 'All Posts — ' . SITE_NAME;

$catFilter = $_GET['cat'] ?? '';
$categories = getCategories();

if ($catFilter) {
    $filterCat = getCategoryBySlug($catFilter);
    $posts = $filterCat ? getPostsByCategory($filterCat['id']) : getPublishedPosts();
} else {
    $posts = getPublishedPosts();
}

include __DIR__ . '/includes/header.php';
?>

  <div class="page-layout">
    <?php include __DIR__ . '/includes/sidebar-left.php'; ?>

    <main class="main-content" id="main-content">
      <div class="page-title-area">
        <a href="index.php" class="post-back"><?= icon('arrow-left') ?> Home</a>
        <h1>All Posts</h1>
      </div>

      <div class="filter-tabs">
        <a href="all-posts.php" class="filter-tab <?= !$catFilter ? 'active' : '' ?>">All</a>
        <?php foreach ($categories as $cat): ?>
        <a href="all-posts.php?cat=<?= e($cat['slug']) ?>" class="filter-tab <?= $catFilter === $cat['slug'] ? 'active' : '' ?>">
          <?= e($cat['name']) ?>
        </a>
        <?php endforeach; ?>
      </div>

      <?php if (!empty($posts)): ?>
      <div class="posts-list">
        <?php foreach ($posts as $post): ?>
        <a href="post.php?slug=<?= e($post['slug']) ?>" class="post-list-item">
          <div class="post-list-thumb">
            <?php if ($post['featured_image']): ?>
              <img src="<?= e($post['featured_image']) ?>" alt="" loading="lazy">
            <?php else: ?>
              <div class="post-list-thumb-placeholder">📝</div>
            <?php endif; ?>
          </div>
          <div class="post-list-content">
            <h3><?= e($post['title']) ?></h3>
            <p class="card-excerpt"><?= e(truncateText($post['excerpt'] ?: $post['content'], 120)) ?></p>
            <div class="post-list-meta">
              <span class="card-meta"><?= formatDate($post['created_at']) ?> · <?= $post['read_time'] ?> min read</span>
              <span class="card-bookmark"><?= icon('bookmark') ?></span>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <div class="empty-state-icon">📚</div>
        <p>No articles found.</p>
      </div>
      <?php endif; ?>
    </main>

    <?php include __DIR__ . '/includes/sidebar-right.php'; ?>
  </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
