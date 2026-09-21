<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$apiKey = defined('API_KEY') ? API_KEY : 'linearlearn_secret_key_2026';
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8080');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>API Access &amp; Docs — <?= SITE_NAME ?> Admin</title>
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
        <a href="editor.php" class="admin-nav-link">New Article</a>
        <a href="api.php" class="admin-nav-link active">API &amp; Docs</a>
        <a href="../index.php" class="admin-nav-link" target="_blank">View Site &rarr;</a>
        <a href="logout.php" class="admin-nav-link" style="color: var(--admin-danger);">Logout</a>
      </nav>
    </div>
  </header>

  <div class="admin-container" style="max-width: 960px;">

    <div class="admin-page-header">
      <div>
        <h1 class="admin-page-title">REST API Access &amp; Documentation</h1>
        <p class="admin-page-subtitle">Programmatically create articles with multiple images, captions, and explanatory narratives</p>
      </div>
      <div class="admin-header-actions">
        <a href="editor.php" class="btn-admin btn-admin-primary">+ New Article in UI</a>
      </div>
    </div>

    <div class="admin-card" style="padding: 24px; margin-bottom: 24px;">
      <h3 class="admin-card-title" style="margin-bottom: 8px;">Your API Credentials</h3>
      <p style="font-size: 0.85rem; color: var(--admin-text-muted); margin-bottom: 16px;">
        Include your API Key in all HTTP requests using the <code>X-API-Key</code> header or <code>Authorization: Bearer &lt;key&gt;</code>.
      </p>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div>
          <label class="form-label">API Base URL</label>
          <div class="api-key-badge" style="width: 100%; justify-content: space-between;">
            <span><?= $baseUrl ?>/api/</span>
            <button type="button" class="btn-admin btn-admin-secondary btn-admin-sm" onclick="copyText('<?= $baseUrl ?>/api/')">Copy</button>
          </div>
        </div>

        <div>
          <label class="form-label">Secret API Key</label>
          <div class="api-key-badge" style="width: 100%; justify-content: space-between;">
            <span style="font-weight: 700;"><?= e($apiKey) ?></span>
            <button type="button" class="btn-admin btn-admin-secondary btn-admin-sm" onclick="copyText('<?= e($apiKey) ?>')">Copy Key</button>
          </div>
        </div>
      </div>
    </div>

    <div class="admin-card" style="padding: 24px; margin-bottom: 24px;">
      <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
        <span style="background: #DEF7EC; color: #03543F; font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 4px;">POST</span>
        <h3 class="admin-card-title" style="margin: 0;">/api/posts.php &nbsp;&middot;&nbsp; Create Article with Narrative Sections</h3>
      </div>
      <p style="font-size: 0.85rem; color: var(--admin-text-muted); margin-bottom: 14px;">
        Uploads an article with multiple images and corresponding explanatory texts formatted as <code>[Image &rarr; Text &rarr; Image &rarr; Text]</code>.
      </p>

      <div class="api-code-card">curl -X POST <?= $baseUrl ?>/api/posts.php \
  -H "X-API-Key: <?= e($apiKey) ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Mapping Cognitive Reserve",
    "excerpt": "A step-by-step visual exploration of neuroplasticity.",
    "author": "Dr. Elena Vance",
    "category": "Brain Health",
    "status": "published",
    "sections": [
      {
        "image": "assets/images/thumb-memory.jpg",
        "caption": "Figure 1 — Synaptic reorganization during learning",
        "title": "Step 1: Synaptic Reorganization",
        "text": "Every new skill reshapes dendritic spines in the cortex, establishing resilient collateral networks."
      },
      {
        "image": "assets/images/thumb-focus.jpg",
        "caption": "Figure 2 — Prefrontal attentional gating",
        "title": "Step 2: Deep Attentional Immersion",
        "text": "Sustained single-task focus modulates acetylcholine and noradrenaline to prevent synaptic noise."
      }
    ]
  }'</div>

      <h4 style="font-size: 0.85rem; font-weight: 700; margin: 16px 0 8px;">Response (201 Created):</h4>
      <div class="api-code-card" style="margin-bottom: 0;">{
  "success": true,
  "message": "Post created successfully in [Image -> Text] narrative format",
  "post_id": 18,
  "title": "Mapping Cognitive Reserve",
  "slug": "mapping-cognitive-reserve",
  "url": "<?= $baseUrl ?>/post.php?slug=mapping-cognitive-reserve",
  "sections_count": 2,
  "status": "published"
}</div>
    </div>

    <div class="admin-card" style="padding: 24px; margin-bottom: 24px;">
      <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
        <span style="background: #DEF7EC; color: #03543F; font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 4px;">POST</span>
        <h3 class="admin-card-title" style="margin: 0;">/api/upload.php &nbsp;&middot;&nbsp; Upload Image Asset</h3>
      </div>
      <p style="font-size: 0.85rem; color: var(--admin-text-muted); margin-bottom: 14px;">
        Uploads a raw image file (JPEG, PNG, WebP) and returns its accessible URL.
      </p>

      <div class="api-code-card">curl -X POST <?= $baseUrl ?>/api/upload.php \
  -H "X-API-Key: <?= e($apiKey) ?>" \
  -F "image=@/path/to/my-diagram.jpg"</div>

      <h4 style="font-size: 0.85rem; font-weight: 700; margin: 16px 0 8px;">Response (200 OK):</h4>
      <div class="api-code-card" style="margin-bottom: 0;">{
  "success": true,
  "url": "uploads/my-diagram-1726857600.jpg",
  "full_url": "<?= $baseUrl ?>/uploads/my-diagram-1726857600.jpg",
  "filename": "my-diagram-1726857600.jpg"
}</div>
    </div>

    <div class="admin-card" style="padding: 24px;">
      <h3 class="admin-card-title" style="margin-bottom: 8px;">Python Upload Example</h3>
      <p style="font-size: 0.85rem; color: var(--admin-text-muted); margin-bottom: 14px;">
        Use this Python snippet to automate creating multi-image posts from your scripts or automated workflows:
      </p>

      <div class="api-code-card" style="margin-bottom: 0;">import requests

API_URL = "<?= $baseUrl ?>/api/posts.php"
API_KEY = "<?= e($apiKey) ?>"

headers = {
    "X-API-Key": API_KEY,
    "Content-Type": "application/json"
}

payload = {
    "title": "Automated Neural Study",
    "excerpt": "Visual summary generated by analysis pipeline.",
    "author": "Dr. Elena Vance",
    "status": "published",
    "sections": [
        {
            "image": "assets/images/thumb-memory.jpg",
            "caption": "Step 1: Stimulus Encoding",
            "text": "First paragraph explaining the data visualization shown above."
        },
        {
            "image": "assets/images/thumb-focus.jpg",
            "caption": "Step 2: Circuit Modulation",
            "text": "Second paragraph explaining the attentional modulation."
        }
    ]
}

response = requests.post(API_URL, json=payload, headers=headers)
print("Created Post:", response.json())</div>
    </div>

  </div>

  <script>
    function copyText(str) {
      navigator.clipboard.writeText(str).then(() => {
        alert('Copied to clipboard: ' + str);
      });
    }
  </script>

</body>
</html>
