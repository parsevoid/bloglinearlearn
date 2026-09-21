<?php
require_once __DIR__ . '/includes/functions.php';

$currentPage = 'search';
$baseUrl = '';
$pageTitle = 'Search — ' . SITE_NAME;

$query = trim($_GET['q'] ?? '');
$results = $query ? searchPosts($query) : [];

include __DIR__ . '/includes/header.php';
?>

  <div class="page-layout">
    <?php include __DIR__ . '/includes/sidebar-left.php'; ?>

    <main class="main-content" id="main-content">
      <div class="page-title-area">
        <a href="index.php" class="post-back"><?= icon('arrow-left') ?> Home</a>
        <h1>Search</h1>
      </div>

      <form action="search.php" method="GET" class="search-box">
        <?= icon('search') ?>
        <input type="text" name="q" placeholder="Search articles…" value="<?= e($query) ?>" autofocus>
        <?php if ($query): ?>
        <a href="search.php" style="color:var(--text-muted);font-size:1.2rem;line-height:1;">×</a>
        <?php endif; ?>
      </form>

      <?php if ($query): ?>
      <p class="search-results-count"><?= count($results) ?> article<?= count($results) !== 1 ? 's' : '' ?> found</p>

        <?php if (!empty($results)): ?>
        <div class="posts-list">
          <?php foreach ($results as $post): ?>
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
              </div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
          <div class="empty-state-icon">🔍</div>
          <p>No articles matching "<?= e($query) ?>"</p>
        </div>
        <?php endif; ?>
      <?php endif; ?>
    </main>

    <?php include __DIR__ . '/includes/sidebar-right.php'; ?>
  </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
