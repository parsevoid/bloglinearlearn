CREATE DATABASE IF NOT EXISTS `dailyblog` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `dailyblog`;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `display_name` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `username`, `password`, `display_name`) VALUES
(1, 'admin', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Editor');

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `icon` VARCHAR(50) DEFAULT 'grid',
  `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `sort_order`) VALUES
(1, 'Brain Health',  'brain-health',  'brain',        1),
(2, 'Memory',        'memory',        'database',     2),
(3, 'Focus',         'focus',         'target',       3),
(4, 'Learning',      'learning',      'book-open',    4),
(5, 'Sleep',         'sleep',         'moon',         5),
(6, 'Mindfulness',   'mindfulness',   'leaf',         6),
(7, 'Habits',        'habits',        'check-circle', 7),
(8, 'Neuroscience',  'neuroscience',  'network',      8);

DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `icon` VARCHAR(50) DEFAULT 'about',
  `subtitle` VARCHAR(200) DEFAULT '',
  `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pages` (`id`, `title`, `slug`, `icon`, `subtitle`, `sort_order`) VALUES
(1, 'About',      'about',      'about',     'Our story & mission',     1),
(2, 'Guides',     'guides',     'book-open', 'Science-backed guides',   2),
(3, 'Resources',  'resources',  'compass',   'Tools & recommendations', 3),
(4, 'Newsletter', 'newsletter', 'mail',      'Weekly brain insights',   4),
(5, 'Contact',    'contact',    'chat',      'Get in touch',            5),
(6, 'Search',     'search',     'search',    'Find articles',           6);

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `content` LONGTEXT,
  `excerpt` TEXT,
  `author` VARCHAR(100) DEFAULT 'LinearLearn Team',
  `category_id` INT DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `excerpt`, `author`, `category_id`, `featured_image`, `read_time`, `views`, `is_featured`, `status`, `created_at`) VALUES
(1, 
 'A Healthier Brain for a Brighter You', 
 'a-healthier-brain-for-a-brighter-you', 
 '<p>The human brain is remarkably adaptable throughout life. In neuroscience, this quality is known as neuroplasticity — the lifelong capacity of the central nervous system to dynamically reorganize its structure and functions in response to experience, learning, and environmental demands.</p><h2>Building Cognitive Reserve</h2><p>Cognitive reserve refers to your brain\'s resilience against damage or decline. Just like physical muscles respond to progressive resistance training, neural circuits strengthen when challenged with deliberate cognitive exertion, quality sleep, and consistent mental nourishment.</p><blockquote>\"Clearer thinking leads to calmer days, and small conscious steps compound into a brighter tomorrow.\"</blockquote><h2>Daily Practices for Brain Longevity</h2><p><strong>1. Prioritize Slow Wave Sleep:</strong> During deep sleep, the brain\'s glymphatic system clears out metabolic waste accumulated during waking hours.</p><p><strong>2. Interleaved Mental Practice:</strong> Challenge your cognitive pathways with novel tasks — learn a language, play an instrument, or practice strategic mental exercises.</p><p><strong>3. Aerobic Circulation:</strong> Physical movement enhances neurogenesis in the hippocampus by releasing Brain-Derived Neurotrophic Factor (BDNF).</p>', 
 'Practical science-backed ways to improve memory, focus and daily habits — and build a calmer, sharper mind.', 
 'Dr. Elena Vance', 
 1, 
 'assets/images/featured-brain-art.jpg', 
 7, 
 2450, 
 1, 
 'published', 
 NOW()),

(2, 
 'Why Memory Fades — and How to Strengthen It', 
 'why-memory-fades-and-how-to-strengthen-it', 
 '<p>Memory isn\'t a video camera that faithfully replays past events; it is an active reconstructive process. Every time you recall a memory, your brain rewires and reconsolidates it based on your current emotional state and context.</p><h2>The Dual-Trace Mechanism</h2><p>Working memory and long-term consolidation rely on distinct synaptic pathways. When working memory is overloaded by multitasking and notifications, encoding never fully transitions into long-term hippocampal storage.</p><h2>Proven Strategies to Bolster Retention</h2><p><strong>Spaced Retrieval:</strong> Rather than passive re-reading, actively test yourself at expanding time intervals. The effort required to retrieve information signals to your synapses that the data is critical.</p><p><strong>Contextual Association:</strong> Anchor new concepts to existing neural schemas. Mnemonics, sensory cues, and spatial memory maps engage broader cortical areas.</p>', 
 'Understand why we forget, and practical ways to improve long-term memory in everyday life.', 
 'Marcus Thorne', 
 2, 
 'assets/images/thumb-memory.jpg', 
 6, 
 1890, 
 0, 
 'published', 
 NOW() - INTERVAL 1 DAY),

(3, 
 'The Science of Deep Focus', 
 'the-science-of-deep-focus', 
 '<p>In an economy powered by digital distraction, sustained deep attention is both rare and extraordinarily valuable. Focus is governed by the prefrontal cortex and modulated by neurotransmitters such as dopamine and acetylcholine.</p><h2>The Cost of Attentional Residue</h2><p>Research confirms that switching between tasks leaves an \'attentional residue\' that hampers cognitive throughput for up to twenty minutes. Single-tasking isn\'t merely polite; it is mathematically superior for high-order synthesis.</p><h2>Cultivating a Focus Sanctuary</h2><p>Create visual and auditory rituals that prime your nervous system for immersion. Set clear boundaries on digital interruptions, leverage ultradian rhythms (90 minutes of focused effort followed by 20 minutes of restorative rest), and protect your prime cognitive hours.</p>', 
 'How attention works, what distracts us, and proven strategies to focus deeper and get more done.', 
 'Julian Hayes', 
 3, 
 'assets/images/thumb-focus.jpg', 
 8, 
 1640, 
 0, 
 'published', 
 NOW() - INTERVAL 2 DAY),

(4, 
 'Small Habits, Big Changes in the Brain', 
 'small-habits-big-changes-in-the-brain', 
 '<p>Every habit is an automated neural loop composed of a cue, a routine, and a reward. When a behavior is performed repeatedly in a consistent context, myelination increases along that neural pathway, making the action effortless.</p><h2>The 1% Compounding Principle</h2><p>Dramatic overhauls typically trigger the amygdala\'s threat response, leading to resistance and burnout. Micro-habits bypass this resistance by requiring negligible initial willpower while still initiating neurological adaptation.</p><h2>Habit Stacking</h2><p>Anchor tiny desired behaviors to existing automatic sequences in your day. Over weeks and months, these miniature circuits coalesce into powerful foundational routines that elevate mental clarity and resilience.</p>', 
 'How tiny, consistent habits can rewire your brain and lead to a calmer, sharper and healthier you.', 
 'Dr. Sarah Mitchell', 
 7, 
 'assets/images/thumb-habits.jpg', 
 5, 
 1420, 
 0, 
 'published', 
 NOW() - INTERVAL 3 DAY);
