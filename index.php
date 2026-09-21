<?php
require_once __DIR__ . '/includes/functions.php';

$currentPage = 'home';
$baseUrl = '';
$pageTitle = SITE_NAME . ' · ' . SITE_TAGLINE . ' — ' . SITE_SUBTITLE;

$featured = getFeaturedPost();
$posts = getPublishedPosts(10);

if ($featured) {
    $posts = array_filter($posts, fn($p) => (int)$p['id'] !== (int)$featured['id']);
    $posts = array_values($posts);
}

include __DIR__ . '/includes/header.php';
?>

  <div class="page-layout">

    <?php include __DIR__ . '/includes/sidebar-left.php'; ?>

    <main class="main-content" id="main-content">

      <?php if ($featured): 
        $featuredImg = $featured['featured_image'] ?: 'assets/images/featured-brain-art.jpg';
      ?>
      <section class="featured-card">
        <div class="featured-image-wrapper">
          <img src="<?= e($featuredImg) ?>" alt="<?= e($featured['title']) ?>" class="featured-bg-img" loading="eager">
          <div class="featured-top-right-tag">
            <span>CLEARER</span>
            <span>THINKING</span>
            <span>HAPPIER</span>
            <span>LIVING</span>
          </div>
        </div>

        <div class="featured-content">
          <div class="featured-dot-tag">
            <span class="pill-dot">&bull;</span> &nbsp;FEATURED ARTICLE
          </div>
          <h1 class="featured-title">
            <a href="post.php?slug=<?= e($featured['slug']) ?>"><?= e($featured['title']) ?></a>
          </h1>
          <p class="featured-desc">
            <?= e($featured['excerpt'] ?: truncateText($featured['content'], 170)) ?>
          </p>
          <div class="featured-action">
            <a href="post.php?slug=<?= e($featured['slug']) ?>" class="btn-read-article">Read Article &rarr;</a>
          </div>
          <div class="featured-sub-tag">
            SCIENCE &nbsp;&middot;&nbsp; PRACTICE &nbsp;&middot;&nbsp; A BETTER YOU
          </div>
        </div>
      </section>
      <?php endif; ?>

      <section class="latest-section">
        <div class="section-header">
          <h2 class="section-title">Latest Articles</h2>
          <a href="all-posts.php" class="view-all-link">
            View All &rarr;
          </a>
        </div>

        <?php
        $defaultCards = [
            0 => [
                'cat'   => 'MEMORY',
                'thumb' => 'assets/images/thumb-memory.jpg',
                'time'  => '6 min read',
            ],
            1 => [
                'cat'   => 'FOCUS',
                'thumb' => 'assets/images/thumb-focus.jpg',
                'time'  => '8 min read',
            ],
            2 => [
                'cat'   => 'HABITS',
                'thumb' => 'assets/images/thumb-habits.jpg',
                'time'  => '5 min read',
            ],
        ];
        ?>

        <div class="articles-grid-3col">
          <?php foreach (array_slice($posts, 0, 3) as $idx => $post): 
            $def = $defaultCards[$idx] ?? [];
            $postThumb = $post['featured_image'] ?: ($def['thumb'] ?? 'assets/images/thumb-memory.jpg');
            $cardCat = !empty($post['category_name']) ? strtoupper($post['category_name']) : ($def['cat'] ?? 'BRAIN HEALTH');
            $cardDesc = $post['excerpt'] ?: truncateText($post['content'], 110);
            $cardTime = !empty($post['read_time']) ? ($post['read_time'] . ' min read') : ($def['time'] ?? '6 min read');
          ?>
          <article class="article-card">
            <a href="post.php?slug=<?= e($post['slug']) ?>" class="card-thumb-wrap">
              <img src="<?= e($postThumb) ?>" alt="<?= e($post['title']) ?>" class="card-thumb-img" loading="lazy">
              <span class="card-cat-badge"><?= e($cardCat) ?></span>
            </a>
            <div class="card-body">
              <h3 class="card-title">
                <a href="post.php?slug=<?= e($post['slug']) ?>"><?= e($post['title']) ?></a>
              </h3>
              <p class="card-desc"><?= e($cardDesc) ?></p>
              <div class="card-footer">
                <div class="card-read-time">
                  <?= icon('clock', 'card-clock-icon') ?>
                  <span><?= e($cardTime) ?></span>
                </div>
                <button type="button" class="card-bookmark-btn" aria-label="Bookmark article">
                  <?= icon('bookmark', 'card-bookmark-icon') ?>
                </button>
              </div>
            </div>
          </article>
          <?php endforeach; ?>

          <?php if (empty($posts)): ?>
          <div class="empty-state" style="grid-column: 1 / -1;">
            <p>No articles published yet. Check back soon!</p>
          </div>
          <?php endif; ?>
        </div>
      </section>

    </main>

    <?php include __DIR__ . '/includes/sidebar-right.php'; ?>

  </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
