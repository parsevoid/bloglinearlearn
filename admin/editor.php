<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$baseUrl = '../';
$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = $editId ? getPostById($editId) : null;
$categories = getCategories();
$message = '';
$msgType = '';

$existingSections = [];
if ($post && !empty($post['sections'])) {
    $decoded = json_decode($post['sections'], true);
    if (is_array($decoded)) $existingSections = $decoded;
}
if (empty($existingSections)) {
    $existingSections = [
        ['image' => '', 'caption' => '', 'title' => '', 'text' => '']
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $author = trim($_POST['author'] ?? 'LinearLearn Editorial');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $status = $_POST['status'] ?? 'published';
    $customContent = trim($_POST['content'] ?? '');

    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $featuredImage = $post['featured_image'] ?? '';
    if (!empty($_POST['featured_image_url'])) {
        $featuredImage = trim($_POST['featured_image_url']);
    }
    if (isset($_FILES['featured_image_file']) && $_FILES['featured_image_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['featured_image_file']['name'], PATHINFO_EXTENSION)) ?: 'jpg';
        $fname = slugify($title) . '-cover-' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['featured_image_file']['tmp_name'], $uploadDir . $fname)) {
            $featuredImage = 'uploads/' . $fname;
        }
    }
    if (!empty($_POST['remove_featured_image'])) {
        $featuredImage = '';
    }

    $sectionsData = [];
    $secTitles = $_POST['sec_title'] ?? [];
    $secCaptions = $_POST['sec_caption'] ?? [];
    $secTexts = $_POST['sec_text'] ?? [];
    $secUrls = $_POST['sec_image_url'] ?? [];

    $count = max(count($secTitles), count($secTexts), count($secUrls));

    for ($i = 0; $i < $count; $i++) {
        $img = trim($secUrls[$i] ?? '');
        $caption = trim($secCaptions[$i] ?? '');
        $secTitle = trim($secTitles[$i] ?? '');
        $text = trim($secTexts[$i] ?? '');

        if (isset($_FILES['sec_image_file']['tmp_name'][$i]) && $_FILES['sec_image_file']['error'][$i] === UPLOAD_ERR_OK) {
            $origName = $_FILES['sec_image_file']['name'][$i];
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION)) ?: 'jpg';
            $fname = slugify($title) . '-sec' . ($i + 1) . '-' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['sec_image_file']['tmp_name'][$i], $uploadDir . $fname)) {
                $img = 'uploads/' . $fname;
            }
        }

        if ($img || $text || $secTitle) {
            $sectionsData[] = [
                'image'   => $img,
                'caption' => $caption,
                'title'   => $secTitle,
                'text'    => $text,
            ];
        }
    }

    if (!$featuredImage && !empty($sectionsData[0]['image'])) {
        $featuredImage = $sectionsData[0]['image'];
    }

    $finalContent = $customContent;
    if (!empty($sectionsData)) {
        $finalContent = buildContentFromSections($sectionsData);
    }

    if (!$title) {
        $message = 'Title is required.';
        $msgType = 'error';
    } else {
        $data = [
            'title'          => $title,
            'slug'           => slugify($title),
            'content'        => $finalContent,
            'excerpt'        => $excerpt,
            'author'         => $author,
            'category_id'    => $categoryId ?: null,
            'featured_image' => $featuredImage,
            'read_time'      => readTime($finalContent),
            'is_featured'    => $isFeatured,
            'status'         => $status,
            'sections'       => $sectionsData,
        ];

        if ($editId && $post) {
            updatePost($editId, $data);
            $message = 'Article updated successfully!';
        } else {
            $editId = createPost($data);
            $message = 'Article published successfully!';
        }
        $msgType = 'success';
        $post = getPostById($editId);
        $existingSections = $sectionsData;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $editId ? 'Edit' : 'New' ?> Article — <?= SITE_NAME ?> Admin</title>
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
        <a href="index.php" class="admin-nav-link">Dashboard</a>
        <a href="editor.php" class="admin-nav-link active">Editor</a>
        <a href="api.php" class="admin-nav-link">API &amp; Docs</a>
        <a href="../index.php" class="admin-nav-link" target="_blank">View Site &rarr;</a>
        <a href="logout.php" class="admin-nav-link" style="color: var(--admin-danger);">Logout</a>
      </nav>
    </div>
  </header>

  <div class="admin-container">

    <div class="admin-page-header">
      <div>
        <h1 class="admin-page-title"><?= $editId ? 'Edit Article' : 'New Publication' ?></h1>
        <p class="admin-page-subtitle">Multi-image structured editorial format: Image &rarr; Text &rarr; Image &rarr; Text</p>
      </div>
      <div class="admin-header-actions">
        <a href="index.php" class="btn-admin btn-admin-secondary">&larr; Back to Dashboard</a>
      </div>
    </div>

    <?php if ($message): ?>
    <div style="background: <?= $msgType === 'success' ? '#DEF7EC' : '#FEE2E2' ?>; border: 1px solid <?= $msgType === 'success' ? '#31C48D' : '#F87171' ?>; color: <?= $msgType === 'success' ? '#03543F' : '#991B1B' ?>; padding: 10px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.88rem; font-weight: 600;">
      <?= e($message) ?>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="form-grid">

        <div>

          <div class="admin-card" style="padding: 22px; margin-bottom: 24px;">
            <div class="form-group">
              <label class="form-label" for="title">Article Headline</label>
              <input type="text" id="title" name="title" class="form-input form-input-lg" placeholder="e.g. The Neurobiology of Deep Focus" required value="<?= e($post['title'] ?? '') ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" for="excerpt">Excerpt / Dek</label>
              <textarea id="excerpt" name="excerpt" class="form-textarea" placeholder="A concise 1-2 sentence overview of the article..."><?= e($post['excerpt'] ?? '') ?></textarea>
            </div>
          </div>

          <div class="admin-card" style="padding: 22px; margin-bottom: 24px;">
            <div class="narrative-builder-header">
              <div>
                <h3 class="narrative-builder-title">Visual Story Sections</h3>
                <p class="narrative-builder-desc">Sequential format: Image &rarr; Explanatory Text &rarr; Image &rarr; Explanatory Text</p>
              </div>
              <button type="button" class="btn-admin btn-admin-secondary btn-admin-sm" id="btn-add-section-top">+ Add Step</button>
            </div>

            <div class="narrative-sections-list" id="narrative-sections-container">
              <?php foreach ($existingSections as $idx => $sec): ?>
              <div class="narrative-section-card" data-index="<?= $idx ?>">
                <div class="narrative-section-top">
                  <span class="narrative-section-badge">Step <?= $idx + 1 ?> &nbsp;&middot;&nbsp; Image &amp; Explanation</span>
                  <button type="button" class="btn-remove-section" onclick="removeSection(this)">Remove</button>
                </div>

                <div class="section-grid">
                  <div>
                    <div class="form-group">
                      <label class="form-label">Step Image File</label>
                      <input type="file" name="sec_image_file[]" accept="image/*" class="form-input" style="padding: 6px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                      <label class="form-label">Or Image URL / Path</label>
                      <input type="text" name="sec_image_url[]" class="form-input" placeholder="assets/images/thumb-focus.jpg" value="<?= e($sec['image'] ?? '') ?>">
                      <?php if (!empty($sec['image'])): ?>
                      <img src="../<?= e($sec['image']) ?>" alt="Preview" class="section-image-preview">
                      <?php endif; ?>
                    </div>
                    <div class="form-group" style="margin-top: 10px; margin-bottom: 0;">
                      <label class="form-label">Caption (Optional)</label>
                      <input type="text" name="sec_caption[]" class="form-input" placeholder="Figure <?= $idx + 1 ?>: Short description" value="<?= e($sec['caption'] ?? '') ?>">
                    </div>
                  </div>

                  <div>
                    <div class="form-group">
                      <label class="form-label">Step Subheading (Optional)</label>
                      <input type="text" name="sec_title[]" class="form-input" placeholder="e.g. Phase <?= $idx + 1 ?>: Attentional Selection" value="<?= e($sec['title'] ?? '') ?>">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                      <label class="form-label">Explanatory Text for this Image</label>
                      <textarea name="sec_text[]" class="form-textarea" style="min-height: 140px;" placeholder="Explain what is occurring in this image, key research takeaways, and implications..."><?= e($sec['text'] ?? ($sec['content'] ?? '')) ?></textarea>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>

            <button type="button" class="btn-add-section" id="btn-add-section-bottom">
              + Add Another Image &amp; Explanatory Text Section
            </button>
          </div>

        </div>

        <div>

          <div class="admin-card" style="padding: 20px; margin-bottom: 20px;">
            <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 14px;">Publish Status</h4>
            <div class="form-group">
              <label class="form-label" for="status">Publication State</label>
              <select id="status" name="status" class="form-select">
                <option value="published" <?= (($post['status'] ?? 'published') === 'published') ? 'selected' : '' ?>>Published (Live)</option>
                <option value="draft" <?= (($post['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Draft (Hidden)</option>
              </select>
            </div>

            <div class="form-group">
              <label style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; font-weight: 600; cursor: pointer;">
                <input type="checkbox" name="is_featured" value="1" <?= ($post['is_featured'] ?? 0) ? 'checked' : '' ?>>
                <span>Feature on Front Page</span>
              </label>
            </div>

            <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 20px;">
              <button type="submit" class="btn-admin btn-admin-primary" style="justify-content: center; padding: 10px;">
                <?= $editId ? 'Save & Update Article' : 'Publish Article' ?>
              </button>
            </div>
          </div>

          <div class="admin-card" style="padding: 20px; margin-bottom: 20px;">
            <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 14px;">Metadata</h4>

            <div class="form-group">
              <label class="form-label" for="category_id">Topic / Category</label>
              <select id="category_id" name="category_id" class="form-select">
                <option value="">— Select Category —</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (($post['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                  <?= e($cat['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" for="author">Author Name</label>
              <input type="text" id="author" name="author" class="form-input" placeholder="e.g. Dr. Elena Vance" value="<?= e($post['author'] ?? 'LinearLearn Editorial') ?>">
            </div>
          </div>

          <div class="admin-card" style="padding: 20px;">
            <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 14px;">Cover / Header Artwork</h4>

            <?php if (!empty($post['featured_image'])): ?>
            <div style="margin-bottom: 12px;">
              <img src="../<?= e($post['featured_image']) ?>" alt="Cover" style="width: 100%; border-radius: 6px; border: 1px solid var(--admin-border);">
              <label style="display: flex; align-items: center; gap: 6px; font-size: 0.78rem; color: #EF4444; margin-top: 6px; cursor: pointer;">
                <input type="checkbox" name="remove_featured_image" value="1"> Remove current cover
              </label>
            </div>
            <?php endif; ?>

            <div class="form-group">
              <label class="form-label">Upload New Cover File</label>
              <input type="file" name="featured_image_file" accept="image/*" class="form-input" style="padding: 6px;">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label">Or Cover Image URL</label>
              <input type="text" name="featured_image_url" class="form-input" placeholder="assets/images/featured-brain-art.jpg" value="<?= e($post['featured_image'] ?? '') ?>">
            </div>
          </div>

        </div>

      </div>
    </form>

  </div>

  <script>
    function removeSection(btn) {
      const card = btn.closest('.narrative-section-card');
      const container = document.getElementById('narrative-sections-container');
      if (container.querySelectorAll('.narrative-section-card').length <= 1) {
        alert('At least one narrative section is required.');
        return;
      }
      card.remove();
      renumberSections();
    }

    function renumberSections() {
      const cards = document.querySelectorAll('.narrative-section-card');
      cards.forEach((card, idx) => {
        const badge = card.querySelector('.narrative-section-badge');
        if (badge) badge.textContent = `Step ${idx + 1} · Image & Explanation`;
      });
    }

    function addSection() {
      const container = document.getElementById('narrative-sections-container');
      const count = container.querySelectorAll('.narrative-section-card').length + 1;

      const card = document.createElement('div');
      card.className = 'narrative-section-card';
      card.innerHTML = `
        <div class="narrative-section-top">
          <span class="narrative-section-badge">Step ${count} &nbsp;&middot;&nbsp; Image &amp; Explanation</span>
          <button type="button" class="btn-remove-section" onclick="removeSection(this)">Remove</button>
        </div>
        <div class="section-grid">
          <div>
            <div class="form-group">
              <label class="form-label">Step Image File</label>
              <input type="file" name="sec_image_file[]" accept="image/*" class="form-input" style="padding: 6px;">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label">Or Image URL / Path</label>
              <input type="text" name="sec_image_url[]" class="form-input" placeholder="assets/images/thumb-habits.jpg">
            </div>
            <div class="form-group" style="margin-top: 10px; margin-bottom: 0;">
              <label class="form-label">Caption (Optional)</label>
              <input type="text" name="sec_caption[]" class="form-input" placeholder="Figure ${count}: Short description">
            </div>
          </div>
          <div>
            <div class="form-group">
              <label class="form-label">Step Subheading (Optional)</label>
              <input type="text" name="sec_title[]" class="form-input" placeholder="e.g. Phase ${count}: Mechanistic Insight">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label">Explanatory Text for this Image</label>
              <textarea name="sec_text[]" class="form-textarea" style="min-height: 140px;" placeholder="Explain what is occurring in this image, key research takeaways, and implications..."></textarea>
            </div>
          </div>
        </div>
      `;
      container.appendChild(card);
    }

    document.getElementById('btn-add-section-top').addEventListener('click', addSection);
    document.getElementById('btn-add-section-bottom').addEventListener('click', addSection);
  </script>

</body>
</html>
