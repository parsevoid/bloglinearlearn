<?php

require_once __DIR__ . '/database.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '" . DB_NAME . "' created.\n";

    $pdo->exec("USE `" . DB_NAME . "`");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) UNIQUE NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `display_name` VARCHAR(100),
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
    echo "✓ Table 'users' created.\n";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `categories` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `slug` VARCHAR(100) UNIQUE NOT NULL,
            `icon` VARCHAR(50) DEFAULT 'grid',
            `sort_order` INT DEFAULT 0
        ) ENGINE=InnoDB
    ");
    echo "✓ Table 'categories' created.\n";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `posts` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `slug` VARCHAR(255) UNIQUE NOT NULL,
            `content` LONGTEXT,
            `excerpt` TEXT,
            `author` VARCHAR(100) DEFAULT 'Anonymous',
            `category_id` INT,
            `featured_image` VARCHAR(255) DEFAULT '',
            `read_time` INT DEFAULT 5,
            `views` INT UNSIGNED DEFAULT 0,
            `is_featured` TINYINT(1) DEFAULT 0,
            `status` ENUM('published','draft') DEFAULT 'draft',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_posts_status_created` (`status`, `created_at`),
            INDEX `idx_posts_status_views` (`status`, `views`),
            INDEX `idx_posts_slug` (`slug`),
            INDEX `idx_posts_category` (`category_id`),
            FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB
    ");
    echo "✓ Table 'posts' created.\n";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `pages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(100) NOT NULL,
            `slug` VARCHAR(100) UNIQUE NOT NULL,
            `icon` VARCHAR(50) DEFAULT 'about',
            `subtitle` VARCHAR(200) DEFAULT '',
            `sort_order` INT DEFAULT 0
        ) ENGINE=InnoDB
    ");
    echo "✓ Table 'pages' created.\n";

    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO `users` (`username`, `password`, `display_name`) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $hash, 'Editor']);
    echo "✓ Admin user created (username: admin, password: admin123).\n";

    $categories = [
        ['Brain Health',  'brain-health',  'brain',        1],
        ['Memory',        'memory',        'database',     2],
        ['Focus',         'focus',         'target',       3],
        ['Learning',      'learning',      'book-open',    4],
        ['Sleep',         'sleep',         'moon',         5],
        ['Mindfulness',   'mindfulness',   'leaf',         6],
        ['Habits',        'habits',        'check-circle', 7],
        ['Neuroscience',  'neuroscience',  'network',      8],
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO `categories` (`name`, `slug`, `icon`, `sort_order`) VALUES (?, ?, ?, ?)");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }
    echo "✓ " . count($categories) . " categories seeded.\n";

    $pages = [
        ['About',      'about',      'about',     'Our story & mission', 1],
        ['Guides',     'guides',     'book-open', 'Step-by-step help',   2],
        ['Resources',  'resources',  'compass',   'Tools & recommendations', 3],
        ['Newsletter', 'newsletter', 'mail',      'Thoughts in your inbox', 4],
        ['Contact',    'contact',    'chat',      "Let's talk",          5],
        ['Search',     'search',     'search',    'Find what you need',  6],
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO `pages` (`title`, `slug`, `icon`, `subtitle`, `sort_order`) VALUES (?, ?, ?, ?, ?)");
    foreach ($pages as $page) {
        $stmt->execute($page);
    }
    echo "✓ " . count($pages) . " pages seeded.\n";

    $posts = [
        [
            'A Healthier Brain for a Brighter You',
            'a-healthier-brain-for-a-brighter-you',
            '<p>The human brain is remarkably adaptable throughout life. In neuroscience, this quality is known as neuroplasticity — the lifelong capacity of the central nervous system to dynamically reorganize its structure and functions in response to experience, learning, and environmental demands.</p><h2>Building Cognitive Reserve</h2><p>Cognitive reserve refers to your brain\'s resilience against damage or decline. Just like physical muscles respond to progressive resistance training, neural circuits strengthen when challenged with deliberate cognitive exertion, quality sleep, and consistent mental nourishment.</p><blockquote>"Clearer thinking leads to calmer days, and small conscious steps compound into a brighter tomorrow."</blockquote><h2>Daily Practices for Brain Longevity</h2><p><strong>1. Prioritize Slow Wave Sleep:</strong> During deep sleep, the brain\'s glymphatic system clears out metabolic waste accumulated during waking hours.</p><p><strong>2. Interleaved Mental Practice:</strong> Challenge your cognitive pathways with novel tasks — learn a language, play an instrument, or practice strategic mental exercises.</p><p><strong>3. Aerobic Circulation:</strong> Physical movement enhances neurogenesis in the hippocampus by releasing Brain-Derived Neurotrophic Factor (BDNF).</p>',
            'Practical science-backed ways to improve memory, focus and daily habits — and build a calmer, sharper mind.',
            'Dr. Elena Vance',
            1,
            'assets/images/featured-brain-art.jpg',
            7,
            1,
            'published',
            2450
        ],
        [
            'Why Memory Fades — and How to Strengthen It',
            'why-memory-fades-and-how-to-strengthen-it',
            '<p>Memory isn\'t a video camera that faithfully replays past events; it is an active reconstructive process. Every time you recall a memory, your brain rewires and reconsolidates it based on your current emotional state and context.</p><h2>The Dual-Trace Mechanism</h2><p>Working memory and long-term consolidation rely on distinct synaptic pathways. When working memory is overloaded by multitasking and notifications, encoding never fully transitions into long-term hippocampal storage.</p><h2>Proven Strategies to Bolster Retention</h2><p><strong>Spaced Retrieval:</strong> Rather than passive re-reading, actively test yourself at expanding time intervals. The effort required to retrieve information signals to your synapses that the data is critical.</p><p><strong>Contextual Association:</strong> Anchor new concepts to existing neural schemas. Mnemonics, sensory cues, and spatial memory maps engage broader cortical areas.</p>',
            'Understand why we forget, and practical ways to improve long-term memory in everyday life.',
            'Marcus Thorne',
            2,
            'assets/images/thumb-memory.jpg',
            6,
            0,
            'published',
            1890
        ],
        [
            'The Science of Deep Focus',
            'the-science-of-deep-focus',
            '<p>In an economy powered by digital distraction, sustained deep attention is both rare and extraordinarily valuable. Focus is governed by the prefrontal cortex and modulated by neurotransmitters such as dopamine and acetylcholine.</p><h2>The Cost of Attentional Residue</h2><p>Research confirms that switching between tasks leaves an \'attentional residue\' that hampers cognitive throughput for up to twenty minutes. Single-tasking isn\'t merely polite; it is mathematically superior for high-order synthesis.</p><h2>Cultivating a Focus Sanctuary</h2><p>Create visual and auditory rituals that prime your nervous system for immersion. Set clear boundaries on digital interruptions, leverage ultradian rhythms (90 minutes of focused effort followed by 20 minutes of restorative rest), and protect your prime cognitive hours.</p>',
            'How attention works, what distracts us, and proven strategies to focus deeper and get more done.',
            'Julian Hayes',
            3,
            'assets/images/thumb-focus.jpg',
            8,
            0,
            'published',
            1640
        ],
        [
            'Small Habits, Big Changes in the Brain',
            'small-habits-big-changes-in-the-brain',
            '<p>Every habit is an automated neural loop composed of a cue, a routine, and a reward. When a behavior is performed repeatedly in a consistent context, myelination increases along that neural pathway, making the action effortless.</p><h2>The 1% Compounding Principle</h2><p>Dramatic overhauls typically trigger the amygdala\'s threat response, leading to resistance and burnout. Micro-habits bypass this resistance by requiring negligible initial willpower while still initiating neurological adaptation.</p><h2>Habit Stacking</h2><p>Anchor tiny desired behaviors to existing automatic sequences in your day. Over weeks and months, these miniature circuits coalesce into powerful foundational routines that elevate mental clarity and resilience.</p>',
            'How tiny, consistent habits can rewire your brain and lead to a calmer, sharper and healthier you.',
            'Dr. Sarah Mitchell',
            7,
            'assets/images/thumb-habits.jpg',
            5,
            0,
            'published',
            1420
        ],
    ];

    try {
        $pdo->query("SELECT `views` FROM `posts` LIMIT 1");
    } catch (Exception $e) {
        $pdo->exec("ALTER TABLE `posts` ADD COLUMN `views` INT UNSIGNED DEFAULT 0");
        try {
            $pdo->exec("ALTER TABLE `posts` ADD INDEX `idx_posts_status_views` (`status`, `views`)");
        } catch (Exception $e2) {}
    }

    try {
        $pdo->query("SELECT `read_time` FROM `posts` LIMIT 1");
    } catch (Exception $e) {
        $pdo->exec("ALTER TABLE `posts` ADD COLUMN `read_time` INT DEFAULT 5");
    }

    try {
        $pdo->query("SELECT `is_featured` FROM `posts` LIMIT 1");
    } catch (Exception $e) {
        $pdo->exec("ALTER TABLE `posts` ADD COLUMN `is_featured` TINYINT(1) DEFAULT 0");
    }

    try {
        $pdo->query("SELECT `sections` FROM `posts` LIMIT 1");
    } catch (Exception $e) {
        $pdo->exec("ALTER TABLE `posts` ADD COLUMN `sections` LONGTEXT NULL");
    }

    $pdo->exec("DELETE FROM `posts` WHERE `featured_image` LIKE '%ministry%' OR `featured_image` LIKE '%quidditch%' OR `slug` = 'its-okay-to-not-be-okay'");

    $stmt = $pdo->prepare("
        INSERT INTO `posts` (`title`, `slug`, `content`, `excerpt`, `author`, `category_id`, `featured_image`, `read_time`, `is_featured`, `status`, `views`, `created_at`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW() - INTERVAL ? DAY)
        ON DUPLICATE KEY UPDATE
            `title` = VALUES(`title`),
            `content` = VALUES(`content`),
            `excerpt` = VALUES(`excerpt`),
            `author` = VALUES(`author`),
            `category_id` = VALUES(`category_id`),
            `featured_image` = VALUES(`featured_image`),
            `read_time` = VALUES(`read_time`),
            `is_featured` = VALUES(`is_featured`),
            `status` = VALUES(`status`),
            `views` = VALUES(`views`)
    ");

    foreach ($posts as $i => $post) {
        $views = array_pop($post);
        $post[] = $views;
        $post[] = $i;
        $stmt->execute($post);
    }
    echo "✓ " . count($posts) . " sample posts seeded.\n";

    echo "\nSetup complete! Your database is ready.\n";
    echo "Admin login: username=admin, password=admin123\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
