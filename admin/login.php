<?php
require_once __DIR__ . '/../includes/functions.php';

$error = '';
$baseUrl = '../';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (loginAdmin($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password. Please try again.';
    }
}

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — <?= SITE_NAME ?> Admin</title>
  <link rel="icon" type="image/webp" href="../assets/favicon.webp">
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="login-body">
  <div class="minimal-login-card">
    <div class="login-brand-header">
      <h1 class="login-title"><?= SITE_NAME ?></h1>
      <p class="login-subtitle">Content Management & Editorial Admin</p>
    </div>

    <?php if ($error): ?>
    <div class="login-alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label class="form-label" for="username">Username</label>
        <input type="text" id="username" name="username" class="form-input" placeholder="admin" required autocomplete="username" value="<?= e($_POST['username'] ?? '') ?>" autofocus>
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; justify-content: center; padding: 10px; margin-top: 6px;">
        Sign In
      </button>
    </form>

    <div class="login-footer-link">
      <a href="../index.php">&larr; Return to Public Website</a>
    </div>
  </div>
</body>
</html>
