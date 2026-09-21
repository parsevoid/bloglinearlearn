<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$baseUrl = '../';
$stats = getStats();
$posts = getAllPosts();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    deletePost((int)$_POST['delete_id']);
    header('Location: index.php?deleted=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — <?= SITE_NAME ?> Admin</title>
  <link rel="icon" type="image/webp" href="../assets/favicon.webp">
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin-body">

  <header class="admin-header">
    <div class="admin-header-inner">
      <a href="index.php" class="admin-brand">
        <span class="admin-brand-badge">ADMIN</span>
        <span><?= SITE_NAME ?></span>
      </a>
      <nav class="admin-nav">
        <a href="index.php" class="admin-nav-link active">Dashboard</a>
        <a href="editor.php" class="admin-nav-link">New Article</a>
        <a href="api.php" class="admin-nav-link">API &amp; Docs</a>
        <a href="../index.php" class="admin-nav-link" target="_blank">View Site &rarr;</a>
        <a href="logout.php" class="admin-nav-link" style="color: var(--admin-danger);">Logout</a>
      </nav>
    </div>
  </header>

  <div class="admin-container">

    <div class="admin-page-header">
      <div>
        <h1 class="admin-page-title">Articles &amp; Insights</h1>
        <p class="admin-page-subtitle">Manage publications, narrative sections, and API uploads</p>
      </div>
      <div class="admin-header-actions">
        <a href="api.php" class="btn-admin btn-admin-secondary">API Access</a>
        <a href="editor.php" class="btn-admin btn-admin-primary">+ New Article</a>
      </div>
    </div>

    <div class="admin-stats-grid">
      <div class="admin-stat-card">
        <div class="admin-stat-label">Total Articles</div>
        <div class="admin-stat-value"><?= (int)($stats['total'] ?? count($posts)) ?></div>
      </div>
      <div class="admin-stat-card">
        <div class="admin-stat-label">Published</div>
        <div class="admin-stat-value" style="color: var(--admin-success);"><?= (int)($stats['published'] ?? 0) ?></div>
      </div>
      <div class="admin-stat-card">
        <div class="admin-stat-label">Drafts</div>
        <div class="admin-stat-value" style="color: var(--admin-text-muted);"><?= (int)($stats['drafts'] ?? 0) ?></div>
      </div>
      <div class="admin-stat-card">
        <div class="admin-stat-label">Topics</div>
        <div class="admin-stat-value"><?= (int)($stats['categories'] ?? 8) ?></div>
      </div>
    </div>

    <div class="admin-card">
      <div class="admin-card-header">
        <h3 class="admin-card-title">All Publications</h3>
        <span style="font-size: 0.8rem; color: var(--admin-text-muted);"><?= count($posts) ?> entries</span>
      </div>

      <table class="admin-table">
        <thead>
          <tr>
            <th>Article</th>
            <th>Category</th>
            <th>Author</th>
            <th>Format</th>
            <th>Status</th>
            <th>Date</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($posts)): ?>
          <tr>
            <td colspan="7" style="text-align:center; padding: 36px; color: var(--admin-text-muted);">
              No articles found. Click "+ New Article" or use the API to publish.
            </td>
          </tr>
          <?php else: ?>
          <?php foreach ($posts as $post): 
            $sections = !empty($post['sections']) ? json_decode($post['sections'], true) : [];
            $secCount = is_array($sections) ? count($sections) : 0;
          ?>
          <tr>
            <td>
              <a href="editor.php?id=<?= $post['id'] ?>" class="table-post-title">
                <?= e($post['title']) ?>
              </a>
              <div class="table-post-slug">/post.php?slug=<?= e($post['slug']) ?></div>
            </td>
            <td>
              <span style="font-size: 0.82rem; font-weight: 500; color: #4B5563;">
                <?= e($post['category_name'] ?? 'General') ?>
              </span>
            </td>
            <td>
              <span style="font-size: 0.82rem; color: var(--admin-text-muted);">
                <?= e($post['author'] ?: 'Anonymous') ?>
              </span>
            </td>
            <td>
              <?php if ($secCount > 0): ?>
              <span style="font-size: 0.75rem; background: #EEF2FF; color: #4338CA; padding: 2px 7px; border-radius: 4px; font-weight: 600;">
                <?= $secCount ?> Narrative <?= $secCount === 1 ? 'Step' : 'Steps' ?>
              </span>
              <?php else: ?>
              <span style="font-size: 0.75rem; color: #9CA3AF;">Classic Prose</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="status-pill <?= $post['status'] === 'published' ? 'published' : 'draft' ?>">
                <?= e($post['status']) ?>
              </span>
            </td>
            <td>
              <span style="font-size: 0.8rem; color: var(--admin-text-muted);">
                <?= formatDate($post['created_at']) ?>
              </span>
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; align-items: center; gap: 6px;">
                <a href="../post.php?slug=<?= e($post['slug']) ?>" target="_blank" class="btn-admin btn-admin-secondary btn-admin-sm">View</a>
                <a href="editor.php?id=<?= $post['id'] ?>" class="btn-admin btn-admin-secondary btn-admin-sm">Edit</a>
                <form method="POST" action="" onsubmit="return confirm('Delete this article permanently?');" style="display:inline;">
                  <input type="hidden" name="delete_id" value="<?= (int)$post['id'] ?>">
                  <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>

</body>
</html>
