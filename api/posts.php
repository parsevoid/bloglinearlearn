<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/functions.php';

function authenticateApiRequest(): bool {
    $apiKey = defined('API_KEY') ? API_KEY : 'linearlearn_secret_key_2026';

    $headers = getallheaders();
    $providedKey = $headers['X-API-Key'] ?? ($headers['x-api-key'] ?? null);

    if ($providedKey && hash_equals($apiKey, $providedKey)) {
        return true;
    }

    $authHeader = $headers['Authorization'] ?? ($headers['authorization'] ?? '');
    if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        if (hash_equals($apiKey, trim($matches[1]))) {
            return true;
        }
    }

    $user = $_SERVER['PHP_AUTH_USER'] ?? null;
    $pass = $_SERVER['PHP_AUTH_PW'] ?? null;
    if ($user && $pass) {
        if (loginAdmin($user, $pass)) {
            return true;
        }
    }

    if (!empty($_GET['api_key']) && hash_equals($apiKey, $_GET['api_key'])) {
        return true;
    }

    if (isLoggedIn()) {
        return true;
    }

    return false;
}

if (!authenticateApiRequest()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error'   => 'Unauthorized',
        'message' => 'Valid API Key or Admin credentials required. Provide via header "X-API-Key: <key>", "Authorization: Bearer <key>", or Basic Auth.'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $slug = $_GET['slug'] ?? '';
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($slug) {
        $post = getPostBySlug($slug);
        if (!$post) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Post not found']);
            exit;
        }
        $post['sections'] = !empty($post['sections']) ? json_decode($post['sections'], true) : [];
        echo json_encode(['success' => true, 'post' => $post], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    if ($id) {
        $post = getPostById($id);
        if (!$post) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Post not found']);
            exit;
        }
        $post['sections'] = !empty($post['sections']) ? json_decode($post['sections'], true) : [];
        echo json_encode(['success' => true, 'post' => $post], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 20;
    $posts = getPublishedPosts($limit);
    foreach ($posts as &$p) {
        $p['sections'] = !empty($p['sections']) ? json_decode($p['sections'], true) : [];
    }
    echo json_encode(['success' => true, 'count' => count($posts), 'posts' => $posts], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($method === 'POST') {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $data = [];

    if (stripos($contentType, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: [];
    } else {
        $data = $_POST;
        if (isset($data['sections']) && is_string($data['sections'])) {
            $decoded = json_decode($data['sections'], true);
            if (is_array($decoded)) $data['sections'] = $decoded;
        }
    }

    $title = trim($data['title'] ?? '');
    if (!$title) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Title is required'], JSON_PRETTY_PRINT);
        exit;
    }

    $categoryId = !empty($data['category_id']) ? (int)$data['category_id'] : null;
    if (!$categoryId && !empty($data['category'])) {
        $cat = getCategoryBySlug(slugify($data['category']));
        if ($cat) $categoryId = (int)$cat['id'];
    }

    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $featuredImage = trim($data['featured_image'] ?? '');
    if (!empty($_FILES['featured_image']['tmp_name']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION)) ?: 'jpg';
        $fname = slugify($title) . '-cover-' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $uploadDir . $fname)) {
            $featuredImage = 'uploads/' . $fname;
        }
    }

    $sections = [];
    if (!empty($data['sections']) && is_array($data['sections'])) {
        foreach ($data['sections'] as $i => $sec) {
            $secImage = trim($sec['image'] ?? '');
            $secCaption = trim($sec['caption'] ?? '');
            $secTitle = trim($sec['title'] ?? '');
            $secText = trim($sec['text'] ?? ($sec['content'] ?? ''));

            $fileKey = 'section_image_' . $i;
            if (!empty($_FILES[$fileKey]['tmp_name']) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION)) ?: 'jpg';
                $fname = slugify($title) . '-sec' . ($i + 1) . '-' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $uploadDir . $fname)) {
                    $secImage = 'uploads/' . $fname;
                }
            }

            if ($secImage || $secText || $secTitle) {
                $sections[] = [
                    'image'   => $secImage,
                    'caption' => $secCaption,
                    'title'   => $secTitle,
                    'text'    => $secText,
                ];
            }
        }
    }

    if (!$featuredImage && !empty($sections[0]['image'])) {
        $featuredImage = $sections[0]['image'];
    }

    $content = trim($data['content'] ?? '');
    if (empty($content) && !empty($sections)) {
        $content = buildContentFromSections($sections);
    }

    $status = in_array(strtolower($data['status'] ?? ''), ['published', 'draft']) ? strtolower($data['status']) : 'published';
    $isFeatured = !empty($data['is_featured']) ? 1 : 0;
    $author = trim($data['author'] ?? 'LinearLearn Editorial');
    $excerpt = trim($data['excerpt'] ?? '');
    if (!$excerpt && !empty($sections[0]['text'])) {
        $excerpt = truncateText($sections[0]['text'], 140);
    }

    $postData = [
        'title'          => $title,
        'slug'           => slugify($data['slug'] ?? $title),
        'content'        => $content,
        'excerpt'        => $excerpt,
        'author'         => $author,
        'category_id'    => $categoryId,
        'featured_image' => $featuredImage,
        'read_time'      => !empty($data['read_time']) ? (int)$data['read_time'] : readTime($content),
        'is_featured'    => $isFeatured,
        'status'         => $status,
        'sections'       => $sections,
    ];

    $postId = createPost($postData);

    if (!$postId) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to create post in database'], JSON_PRETTY_PRINT);
        exit;
    }

    $createdPost = getPostById($postId);
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8080');

    http_response_code(201);
    echo json_encode([
        'success'        => true,
        'message'        => 'Post created successfully in [Image -> Text] narrative format',
        'post_id'        => $postId,
        'title'          => $createdPost['title'] ?? $title,
        'slug'           => $createdPost['slug'] ?? $postData['slug'],
        'url'            => $baseUrl . '/post.php?slug=' . ($createdPost['slug'] ?? $postData['slug']),
        'featured_image' => $featuredImage,
        'sections_count' => count($sections),
        'status'         => $status,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
