<?php
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$post = $slug ? getPostBySlug($slug) : null;

$currentPage = 'post';
$baseUrl = '';

if (!$post) {
    $pageTitle = 'Article Not Found — ' . SITE_NAME;
    include __DIR__ . '/includes/header.php';
    ?>
    <div class="page-layout">
      <?php include __DIR__ . '/includes/sidebar-left.php'; ?>
      <main class="main-content" id="main-content">
        <div class="post-reading-wrapper">
          <div class="empty-state">
            <div class="empty-state-icon"><?= icon('search') ?></div>
            <h2>Article Not Found</h2>
            <p>The insight you are looking for may have moved or been archived.</p>
            <a href="index.php" class="btn-read-article mt-md">Return to Home</a>
          </div>
        </div>
      </main>
      <?php include __DIR__ . '/includes/sidebar-right.php'; ?>
    </div>
    <?php
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $post['title'] . ' — ' . SITE_NAME;
$pageDescription = truncateText($post['excerpt'] ?: $post['content'], 160);

incrementPostViews((int)$post['id']);

$related = getRelatedPosts($post['id'], $post['category_id'] ?? 0, 3);
$catName = !empty($post['category_name']) ? $post['category_name'] : 'Brain Health';
$catSlug = !empty($post['category_slug']) ? $post['category_slug'] : 'brain-health';
$authorName = $post['author'] ?: 'LinearLearn Editorial';
$authorInitial = strtoupper(substr($authorName, 0, 1));
$readMins = $post['read_time'] ?: readTime($post['content']);
$featuredImg = $post['featured_image'] ?: 'assets/images/featured-brain-art.jpg';

include __DIR__ . '/includes/header.php';
?>

  <div class="page-layout">
    <?php include __DIR__ . '/includes/sidebar-left.php'; ?>

    <main class="main-content post-reading-main" id="main-content">
      <div class="reading-progress-track" id="reading-progress-track">
        <div class="reading-progress-fill" id="reading-progress-fill"></div>
      </div>

      <div class="post-reading-container">

        <div class="reading-top-nav">
          <a href="index.php" class="reading-back-link" id="reading-back-btn">
            <span class="back-arrow">&larr;</span> Back to Home
          </a>
          <div class="reading-top-tags">
            <a href="category.php?slug=<?= e($catSlug) ?>" class="reading-cat-badge">
              <?= e(strtoupper($catName)) ?>
            </a>
            <span class="reading-mins-pill">
              <?= icon('clock', 'reading-clock-icon') ?>
              <?= (int)$readMins ?> min read
            </span>
          </div>
        </div>

        <header class="reading-article-header">
          <h1 class="reading-headline"><?= e($post['title']) ?></h1>

          <?php if (!empty($post['excerpt'])): ?>
          <p class="reading-deck"><?= e($post['excerpt']) ?></p>
          <?php endif; ?>

          <div class="reading-author-bar">
            <div class="reading-author-info">
              <div class="author-avatar-circle"><?= e($authorInitial) ?></div>
              <div class="author-text-meta">
                <span class="author-display-name"><?= e($authorName) ?></span>
                <span class="author-pub-details">
                  <?= formatDate($post['created_at']) ?> &nbsp;&middot;&nbsp; 
                  <?= number_format($post['views'] ?: 1240) ?> views
                </span>
              </div>
            </div>

            <div class="reading-share-actions">
              <button type="button" class="btn-reading-action btn-copy-link" id="btn-copy-article" title="Copy article link" aria-label="Copy article link">
                <?= icon('link', 'action-icon') ?>
                <span class="btn-action-label">Share</span>
              </button>
              <button type="button" class="btn-reading-action btn-bookmark-post" id="btn-bookmark-post" title="Save article" aria-label="Bookmark article">
                <?= icon('bookmark', 'action-icon') ?>
                <span class="btn-action-label">Save</span>
              </button>
            </div>
          </div>
        </header>

        <?php if ($featuredImg): ?>
        <div class="reading-featured-banner">
          <img src="<?= e($featuredImg) ?>" alt="<?= e($post['title']) ?>" class="reading-banner-img" loading="eager">
          <div class="reading-banner-caption">
            <span>LinearLearn Neuroscience Series &nbsp;&middot;&nbsp; Verified Insight</span>
          </div>
        </div>
        <?php endif; ?>

        <div class="reading-callout-box">
          <div class="callout-header">
            <span class="callout-icon"><?= icon('target', 'callout-icon-svg') ?></span>
            <span class="callout-title">CORE TAKEAWAY</span>
          </div>
          <p class="callout-text">
            <?= e($post['excerpt'] ?: 'Consistent deliberate cognitive exertion, quality slow-wave sleep, and progressive mental rituals strengthen neural reserve across all stages of life.') ?>
          </p>
        </div>

        <article class="reading-prose drop-cap" id="article-prose">
          <?= $post['content'] ?>
        </article>

        <div class="reading-bio-card">
          <div class="bio-avatar"><?= e($authorInitial) ?></div>
          <div class="bio-content">
            <span class="bio-role">WRITTEN BY</span>
            <h4 class="bio-name"><?= e($authorName) ?></h4>
            <p class="bio-desc">
              Contributing neuroscientist and cognitive health researcher at LinearLearn. Dedicated to distilling cutting-edge brain research into daily, practical rituals.
            </p>
          </div>
        </div>

        <?php if (!empty($related)): ?>
        <section class="reading-related-section">
          <div class="section-header">
            <h2 class="section-title">Further Reading</h2>
            <a href="all-posts.php" class="view-all-link">Browse All &rarr;</a>
          </div>

          <div class="articles-grid-3col">
            <?php foreach ($related as $rel): 
              $relThumb = $rel['featured_image'] ?: 'assets/images/thumb-memory.jpg';
              $relCat = !empty($rel['category_name']) ? strtoupper($rel['category_name']) : 'INSIGHT';
            ?>
            <article class="article-card">
              <a href="post.php?slug=<?= e($rel['slug']) ?>" class="card-thumb-wrap">
                <img src="<?= e($relThumb) ?>" alt="<?= e($rel['title']) ?>" class="card-thumb-img" loading="lazy">
                <span class="card-cat-badge"><?= e($relCat) ?></span>
              </a>
              <div class="card-body">
                <h3 class="card-title">
                  <a href="post.php?slug=<?= e($rel['slug']) ?>"><?= e($rel['title']) ?></a>
                </h3>
                <p class="card-desc"><?= e($rel['excerpt'] ?: truncateText($rel['content'], 100)) ?></p>
                <div class="card-footer">
                  <div class="card-read-time">
                    <?= icon('clock', 'card-clock-icon') ?>
                    <span><?= (int)($rel['read_time'] ?: 6) ?> min read</span>
                  </div>
                  <button type="button" class="card-bookmark-btn" aria-label="Bookmark article">
                    <?= icon('bookmark', 'card-bookmark-icon') ?>
                  </button>
                </div>
              </div>
            </article>
            <?php endforeach; ?>
          </div>
        </section>
        <?php endif; ?>

        <div class="reading-bottom-actions">
          <a href="index.php" class="btn-read-article">&larr; Return to All Articles</a>
        </div>

      </div>
    </main>

    <?php include __DIR__ . '/includes/sidebar-right.php'; ?>
  </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
