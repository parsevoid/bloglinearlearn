<?php

require_once __DIR__ . '/../config/database.php';

function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function readTime(string $content): int {
    $wordCount = str_word_count(strip_tags($content));
    $minutes = max(1, ceil($wordCount / 200));
    return (int)$minutes;
}

function formatDate(string $date): string {
    return date('M j, Y', strtotime($date));
}

function truncateText(string $text, int $length = 150): string {
    $text = strip_tags($text);
    if (strlen($text) <= $length) return $text;
    return rtrim(substr($text, 0, strrpos(substr($text, 0, $length), ' ')), '.,!? ') . '…';
}

function iconPath(string $name): string {
    return 'assets/icons/' . $name . '.svg';
}

function icon(string $name, string $class = ''): string {
    global $baseUrl;
    $bUrl = $baseUrl ?? '';

    $clsAttr = $class ? ' class="' . htmlspecialchars($class) . '"' : '';

    $svgs = [
        'home' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
        'brain' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96.44 2.5 2.5 0 0 1-2.96-3.08 3 3 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 4.44-2.04"/><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96.44 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-4.44-2.04"/></svg>',
        'database' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>',
        'memory' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>',
        'target' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>',
        'focus' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>',
        'book-open' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
        'learning' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
        'moon' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>',
        'sleep' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>',
        'leaf' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>',
        'mindfulness' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>',
        'check-circle' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>',
        'habits' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>',
        'network' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>',
        'neuroscience' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>',
        'chart-bar' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
        'trending' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
        'sun' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>',
        'clock' => '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        'bookmark' => '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>',
        'search' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"' . $clsAttr . '><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>',
    ];

    if (isset($svgs[$name])) {
        return $svgs[$name];
    }

    $path = __DIR__ . '/../assets/icons/' . $name . '.svg';
    if (file_exists($path)) {
        $svg = file_get_contents($path);
        if ($class) {
            $svg = str_replace('<svg ', '<svg class="' . htmlspecialchars($class) . '" ', $svg);
        }
        return $svg;
    }
    return '';
}

function e(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function getFallbackPosts(): array {
    return [
        [
            'id' => 1,
            'title' => 'A Healthier Brain for a Brighter You',
            'slug' => 'a-healthier-brain-for-a-brighter-you',
            'content' => '<p>The human brain is remarkably adaptable throughout life. In neuroscience, this quality is known as neuroplasticity — the lifelong capacity of the central nervous system to dynamically reorganize its structure and functions in response to experience, learning, and environmental demands.</p><h2>Building Cognitive Reserve</h2><p>Cognitive reserve refers to your brain\'s resilience against damage or decline. Just like physical muscles respond to progressive resistance training, neural circuits strengthen when challenged with deliberate cognitive exertion, quality sleep, and consistent mental nourishment.</p><blockquote>"Clearer thinking leads to calmer days, and small conscious steps compound into a brighter tomorrow."</blockquote>',
            'excerpt' => 'Practical science-backed ways to improve memory, focus and daily habits — and build a calmer, sharper mind.',
            'author' => 'Dr. Elena Vance',
            'category_id' => 1,
            'category_name' => 'Brain Health',
            'category_slug' => 'brain-health',
            'featured_image' => 'assets/images/featured-brain-art.jpg',
            'read_time' => 7,
            'is_featured' => 1,
            'status' => 'published',
            'views' => 2450,
            'created_at' => date('Y-m-d H:i:s'),
        ],
        [
            'id' => 2,
            'title' => 'Why Memory Fades — and How to Strengthen It',
            'slug' => 'why-memory-fades-and-how-to-strengthen-it',
            'content' => '<p>Memory isn\'t a video camera that faithfully replays past events; it is an active reconstructive process. Every time you recall a memory, your brain rewires and reconsolidates it based on your current emotional state and context.</p>',
            'excerpt' => 'Understand why we forget, and practical ways to improve long-term memory in everyday life.',
            'author' => 'Marcus Thorne',
            'category_id' => 2,
            'category_name' => 'Memory',
            'category_slug' => 'memory',
            'featured_image' => 'assets/images/thumb-memory.jpg',
            'read_time' => 6,
            'is_featured' => 0,
            'status' => 'published',
            'views' => 1890,
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        ],
        [
            'id' => 3,
            'title' => 'The Science of Deep Focus',
            'slug' => 'the-science-of-deep-focus',
            'content' => '<p>In an economy powered by digital distraction, sustained deep attention is both rare and extraordinarily valuable. Focus is governed by the prefrontal cortex and modulated by neurotransmitters such as dopamine and acetylcholine.</p>',
            'excerpt' => 'How attention works, what distracts us, and proven strategies to focus deeper and get more done.',
            'author' => 'Julian Hayes',
            'category_id' => 3,
            'category_name' => 'Focus',
            'category_slug' => 'focus',
            'featured_image' => 'assets/images/thumb-focus.jpg',
            'read_time' => 8,
            'is_featured' => 0,
            'status' => 'published',
            'views' => 1640,
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 day')),
        ],
        [
            'id' => 4,
            'title' => 'Small Habits, Big Changes in the Brain',
            'slug' => 'small-habits-big-changes-in-the-brain',
            'content' => '<p>Every habit is an automated neural loop composed of a cue, a routine, and a reward. When a behavior is performed repeatedly in a consistent context, myelination increases along that neural pathway, making the action effortless.</p>',
            'excerpt' => 'How tiny, consistent habits can rewire your brain and lead to a calmer, sharper and healthier you.',
            'author' => 'Dr. Sarah Mitchell',
            'category_id' => 7,
            'category_name' => 'Habits',
            'category_slug' => 'habits',
            'featured_image' => 'assets/images/thumb-habits.jpg',
            'read_time' => 5,
            'is_featured' => 0,
            'status' => 'published',
            'views' => 1420,
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 day')),
        ],
    ];
}

function getFallbackCategories(): array {
    return [
        ['id' => 1, 'name' => 'Brain Health',  'slug' => 'brain-health',  'icon' => 'brain',        'sort_order' => 1],
        ['id' => 2, 'name' => 'Memory',        'slug' => 'memory',        'icon' => 'database',     'sort_order' => 2],
        ['id' => 3, 'name' => 'Focus',         'slug' => 'focus',         'icon' => 'target',       'sort_order' => 3],
        ['id' => 4, 'name' => 'Learning',      'slug' => 'learning',      'icon' => 'book-open',    'sort_order' => 4],
        ['id' => 5, 'name' => 'Sleep',         'slug' => 'sleep',         'icon' => 'moon',         'sort_order' => 5],
        ['id' => 6, 'name' => 'Mindfulness',   'slug' => 'mindfulness',   'icon' => 'leaf',         'sort_order' => 6],
        ['id' => 7, 'name' => 'Habits',        'slug' => 'habits',        'icon' => 'check-circle', 'sort_order' => 7],
        ['id' => 8, 'name' => 'Neuroscience',  'slug' => 'neuroscience',  'icon' => 'network',      'sort_order' => 8],
    ];
}

function getPublishedPosts(int $limit = 50, int $offset = 0): array {
    $db = getDB();
    if (!$db) {
        $fallbacks = array_values(array_filter(getFallbackPosts(), fn($p) => empty($p['is_featured'])));
        return array_slice($fallbacks, $offset, $limit);
    }
    try {
        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'published'
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll();
        return !empty($res) ? $res : array_values(array_filter(getFallbackPosts(), fn($p) => empty($p['is_featured'])));
    } catch (Exception $e) {
        return array_values(array_filter(getFallbackPosts(), fn($p) => empty($p['is_featured'])));
    }
}

function getFeaturedPost(): ?array {
    $db = getDB();
    if (!$db) {
        $fallbacks = getFallbackPosts();
        return $fallbacks[0] ?? null;
    }
    try {
        $stmt = $db->query("
            SELECT p.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'published' AND p.is_featured = 1
            ORDER BY p.created_at DESC
            LIMIT 1
        ");
        $featured = $stmt->fetch() ?: null;
        if (!$featured) {
            $fallbacks = getFallbackPosts();
            return $fallbacks[0] ?? null;
        }
        return $featured;
    } catch (Exception $e) {
        $fallbacks = getFallbackPosts();
        return $fallbacks[0] ?? null;
    }
}

function getPostBySlug(string $slug): ?array {
    $db = getDB();
    if (!$db) {
        foreach (getFallbackPosts() as $p) {
            if ($p['slug'] === $slug) return $p;
        }
        return null;
    }
    try {
        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.slug = :slug
        ");
        $stmt->execute([':slug' => $slug]);
        $res = $stmt->fetch() ?: null;
        if (!$res) {
            foreach (getFallbackPosts() as $p) {
                if ($p['slug'] === $slug) return $p;
            }
        }
        return $res;
    } catch (Exception $e) {
        foreach (getFallbackPosts() as $p) {
            if ($p['slug'] === $slug) return $p;
        }
        return null;
    }
}

function getPostById(int $id): ?array {
    $db = getDB();
    if (!$db) {
        foreach (getFallbackPosts() as $p) {
            if ($p['id'] === $id) return $p;
        }
        return null;
    }
    try {
        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function getPostsByCategory(int $categoryId, int $limit = 20): array {
    $db = getDB();
    if (!$db) return [];
    try {
        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'published' AND p.category_id = :cat_id
            ORDER BY p.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':cat_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function getTopPosts(int $limit = 10): array {
    $db = getDB();
    if (!$db) return getFallbackPosts();
    try {
        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'published'
            ORDER BY p.views DESC, p.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll();
        return !empty($res) ? $res : getFallbackPosts();
    } catch (Exception $e) {
        return getFallbackPosts();
    }
}

function getTrendingPosts(int $limit = 5): array {
    return getTopPosts($limit);
}

function incrementPostViews(int $postId): bool {
    $db = getDB();
    if (!$db) return false;
    try {
        $stmt = $db->prepare("UPDATE posts SET views = views + 1 WHERE id = :id");
        return $stmt->execute([':id' => $postId]);
    } catch (Exception $e) {
        return false;
    }
}

function searchPosts(string $query): array {
    $db = getDB();
    $term = '%' . $query . '%';
    $stmt = $db->prepare("
        SELECT p.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'published'
          AND (p.title LIKE :q1 OR p.content LIKE :q2 OR p.excerpt LIKE :q3)
        ORDER BY p.created_at DESC
        LIMIT 50
    ");
    $stmt->execute([':q1' => $term, ':q2' => $term, ':q3' => $term]);
    return $stmt->fetchAll();
}

function getAllPosts(): array {
    $db = getDB();
    $stmt = $db->query("
        SELECT p.*, c.name as category_name, c.slug as category_slug
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC
    ");
    return $stmt->fetchAll();
}

function buildContentFromSections(array $sections): string {
    if (empty($sections)) return '';
    $html = '<div class="narrative-story-flow">' . "\n";
    foreach ($sections as $index => $sec) {
        $img = trim($sec['image'] ?? '');
        $caption = trim($sec['caption'] ?? '');
        $title = trim($sec['title'] ?? '');
        $text = trim($sec['text'] ?? ($sec['content'] ?? ''));

        if (!$img && !$text && !$title) continue;

        $stepNum = $index + 1;
        $html .= '  <section class="narrative-step-card" data-step="' . $stepNum . '">' . "\n";

        if ($img) {
            $html .= '    <figure class="narrative-step-figure">' . "\n";
            $html .= '      <img src="' . htmlspecialchars($img) . '" alt="' . htmlspecialchars($title ?: ($caption ?: ('Insight Part ' . $stepNum))) . '" class="narrative-step-image" loading="lazy">' . "\n";
            if ($caption) {
                $html .= '      <figcaption class="narrative-step-caption">' . htmlspecialchars($caption) . '</figcaption>' . "\n";
            }
            $html .= '    </figure>' . "\n";
        }

        if ($title || $text) {
            $html .= '    <div class="narrative-step-prose">' . "\n";
            if ($title) {
                $html .= '      <h3 class="narrative-step-title">' . htmlspecialchars($title) . '</h3>' . "\n";
            }
            if ($text) {
                if (strpos($text, '<p>') !== false || strpos($text, '<div') !== false) {
                    $html .= '      ' . $text . "\n";
                } else {
                    $paragraphs = preg_split('/\n\s*\n/', $text);
                    foreach ($paragraphs as $p) {
                        $p = trim($p);
                        if ($p) {
                            $html .= '      <p>' . nl2br(htmlspecialchars($p)) . '</p>' . "\n";
                        }
                    }
                }
            }
            $html .= '    </div>' . "\n";
        }

        $html .= '  </section>' . "\n";
    }
    $html .= '</div>' . "\n";
    return $html;
}

function createPost(array $data): int {
    $db = getDB();

    $sectionsJson = null;
    if (!empty($data['sections']) && is_array($data['sections'])) {
        $sectionsJson = json_encode($data['sections'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (empty($data['content'])) {
            $data['content'] = buildContentFromSections($data['sections']);
        }
    } elseif (!empty($data['sections']) && is_string($data['sections'])) {
        $sectionsJson = $data['sections'];
        $decoded = json_decode($data['sections'], true);
        if (is_array($decoded) && empty($data['content'])) {
            $data['content'] = buildContentFromSections($decoded);
        }
    }

    $content = $data['content'] ?? '';

    $stmt = $db->prepare("
        INSERT INTO posts (title, slug, content, excerpt, author, category_id, featured_image, read_time, is_featured, status, sections)
        VALUES (:title, :slug, :content, :excerpt, :author, :category_id, :featured_image, :read_time, :is_featured, :status, :sections)
    ");
    $stmt->execute([
        ':title'          => $data['title'],
        ':slug'           => $data['slug'] ?: slugify($data['title']),
        ':content'        => $content,
        ':excerpt'        => $data['excerpt'] ?? '',
        ':author'         => $data['author'] ?: 'Anonymous',
        ':category_id'    => $data['category_id'] ?: null,
        ':featured_image' => $data['featured_image'] ?? '',
        ':read_time'      => $data['read_time'] ?? readTime($content),
        ':is_featured'    => $data['is_featured'] ?? 0,
        ':status'         => $data['status'] ?? 'draft',
        ':sections'       => $sectionsJson,
    ]);
    return (int)$db->lastInsertId();
}

function updatePost(int $id, array $data): bool {
    $db = getDB();

    $sectionsJson = null;
    if (!empty($data['sections']) && is_array($data['sections'])) {
        $sectionsJson = json_encode($data['sections'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (empty($data['content'])) {
            $data['content'] = buildContentFromSections($data['sections']);
        }
    } elseif (!empty($data['sections']) && is_string($data['sections'])) {
        $sectionsJson = $data['sections'];
        $decoded = json_decode($data['sections'], true);
        if (is_array($decoded) && empty($data['content'])) {
            $data['content'] = buildContentFromSections($decoded);
        }
    }

    $content = $data['content'] ?? '';

    $stmt = $db->prepare("
        UPDATE posts SET
            title = :title,
            slug = :slug,
            content = :content,
            excerpt = :excerpt,
            author = :author,
            category_id = :category_id,
            featured_image = :featured_image,
            read_time = :read_time,
            is_featured = :is_featured,
            status = :status,
            sections = :sections
        WHERE id = :id
    ");
    return $stmt->execute([
        ':id'             => $id,
        ':title'          => $data['title'],
        ':slug'           => $data['slug'] ?: slugify($data['title']),
        ':content'        => $content,
        ':excerpt'        => $data['excerpt'] ?? '',
        ':author'         => $data['author'] ?: 'Anonymous',
        ':category_id'    => $data['category_id'] ?: null,
        ':featured_image' => $data['featured_image'] ?? '',
        ':read_time'      => $data['read_time'] ?? readTime($content),
        ':is_featured'    => $data['is_featured'] ?? 0,
        ':status'         => $data['status'] ?? 'draft',
        ':sections'       => $sectionsJson,
    ]);
}

function deletePost(int $id): bool {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM posts WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}

function getCategories(): array {
    $db = getDB();
    if (!$db) return getFallbackCategories();
    try {
        $stmt = $db->query("SELECT * FROM categories ORDER BY sort_order ASC");
        $res = $stmt->fetchAll();
        return !empty($res) ? $res : getFallbackCategories();
    } catch (Exception $e) {
        return getFallbackCategories();
    }
}

function getCategoryBySlug(string $slug): ?array {
    $db = getDB();
    if (!$db) {
        foreach (getFallbackCategories() as $c) {
            if ($c['slug'] === $slug) return $c;
        }
        return null;
    }
    try {
        $stmt = $db->prepare("SELECT * FROM categories WHERE slug = :slug");
        $stmt->execute([':slug' => $slug]);
        $res = $stmt->fetch() ?: null;
        if (!$res) {
            foreach (getFallbackCategories() as $c) {
                if ($c['slug'] === $slug) return $c;
            }
        }
        return $res;
    } catch (Exception $e) {
        foreach (getFallbackCategories() as $c) {
            if ($c['slug'] === $slug) return $c;
        }
        return null;
    }
}

function getCategoryById(int $id): ?array {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM categories WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function getPages(): array {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM pages ORDER BY sort_order ASC");
    return $stmt->fetchAll();
}

function getStats(): array {
    $db = getDB();
    $total = $db->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    $published = $db->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn();
    $drafts = $db->query("SELECT COUNT(*) FROM posts WHERE status='draft'")->fetchColumn();
    $cats = $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    return [
        'total' => $total,
        'published' => $published,
        'drafts' => $drafts,
        'categories' => $cats,
    ];
}

function isLoggedIn(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function loginAdmin(string $username, string $password): bool {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $user['display_name'];
        return true;
    }
    return false;
}

function logoutAdmin(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    session_destroy();
}

function requireAuth(): void {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getRelatedPosts(int $postId, int $categoryId, int $limit = 3): array {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT p.*, c.name as category_name, c.slug as category_slug
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'published' AND p.id != :id AND p.category_id = :cat_id
        ORDER BY p.created_at DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':id', $postId, PDO::PARAM_INT);
    $stmt->bindValue(':cat_id', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $related = $stmt->fetchAll();
    if (count($related) < $limit) {
        $remaining = $limit - count($related);
        $ids = array_column($related, 'id');
        $ids[] = $postId;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt2 = $db->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'published' AND p.id NOT IN ($placeholders)
            ORDER BY p.created_at DESC
            LIMIT $remaining
        ");
        $stmt2->execute($ids);
        $related = array_merge($related, $stmt2->fetchAll());
    }
    return $related;
}
