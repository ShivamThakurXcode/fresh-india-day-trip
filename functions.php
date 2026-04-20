<?php
/**
 * Generate a URL-friendly slug from a string
 *
 * @param string $string The string to convert to slug
 * @param string $table The table name to check uniqueness
 * @param int $id The ID to exclude from uniqueness check (for updates)
 * @return string The generated slug
 */
function generateSlug($string, $table = null, $id = null) {
    // Convert to lowercase
    $slug = strtolower($string);

    // Replace non-letter or digits with -
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

    // Trim leading/trailing -
    $slug = trim($slug, '-');

    // Remove multiple consecutive -
    $slug = preg_replace('/-+/', '-', $slug);

    // If table is provided, ensure uniqueness
    if ($table && isset($GLOBALS['pdo'])) {
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = "SELECT COUNT(*) FROM $table WHERE slug = ?";
            $params = [$slug];

            if ($id) {
                $query .= " AND id != ?";
                $params[] = $id;
            }

            $stmt = $GLOBALS['pdo']->prepare($query);
            $stmt->execute($params);
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                break;
            }

            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
    }

    return $slug;
}

/**
 * Get database connection
 */
function getDBConnection() {
    static $pdo = null;

    if ($pdo === null) {
        require_once 'config.php';
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return $pdo;
}

/**
 * Render a tour card
 *
 * @param array $tour Tour data from database
 * @return string HTML for the tour card
 */
function renderTourCard($tour) {
    $imagePath = $tour['images'] ? json_decode($tour['images'], true)[0] : 'taj_mahal_tour/taj_mahal-1.webp';
    $imageUrl = "../assets/img/{$imagePath}";
    $detailUrl = "../tour/{$tour['slug']}";
    $rating = number_format($tour['rating'], 1);
    $ratingPercent = $tour['rating'] * 20; // For width percentage, assuming 5 stars = 100%

    $titleParts = explode(' ', $tour['title']);
    $dataType = strtolower($titleParts[0] . '-' . $titleParts[1]);
    $dataLocation = strtolower(str_replace(' - ', ' ', $tour['location']));
    $dataDuration = htmlspecialchars($tour['duration']);
    $displayLocation = htmlspecialchars($tour['location']) ;
    $displayDuration = htmlspecialchars($tour['duration']) . ' Day';
    $altText = ' Discover the Majestic ' . $titleParts[0] . ' ' . $titleParts[1];

    $html = '<div class="tour-box th-ani gsap-cursor" data-type="' . $dataType . '" data-location="' . $dataLocation . '" data-duration="' . $dataDuration . '">
                                            <div class="tour-box_img global-img">
                                            <a href="' . $detailUrl . '">
                                                <img src="' . $imageUrl . '" alt="' . $altText . '">
                                                </a>
                                            </div>
                                            <div class="tour-content">
                                                <h3 class="box-title">
                                                    <a href="' . $detailUrl . '">' . htmlspecialchars($tour['title']) . '</a>
                                                </h3>
                                                <div class="tour-rating">
                                                    <div class="star-rating" role="img" aria-label="Rated ' . $rating . ' out of 5">
                                                        <span style="width: ' . $ratingPercent . '%">Rated <strong class="rating">' . $rating . '</strong> out of 5</span>
                                                    </div>
                                                    <a href="#" class="woocommerce-review-link">(' . rand(50, 200) . '+ Reviews)</a>
                                                </div>
                                                <h4 class="tour-box_price">
                                                    <span class="currency"> <i class="fas fa-location"></i> ' . $displayLocation . '</span>
                                                </h4>
                                                <div class="tour-action">
                                                    <span><i class="fa-light fa-clock"></i>' . $displayDuration . '</span>
                                                    <a href="' . $detailUrl . '" class="th-btn text-nowrap style4 th-icon">Read More</a>
                                                </div>
                                            </div>
                                        </div>';

    return $html;
}

/**
 * Get tours by category
 *
 * @param string $category Category name or null for all
 * @param int $limit Number of tours to fetch
 * @param int $exclude_id Tour ID to exclude
 * @param string $search Search term
 * @param string $orderby Sort order
 * @return array Array of tours
 */
function getTours($category = null, $limit = null, $exclude_id = null, $search = null, $orderby = null, $location = null, $duration = null, $price_min = null, $price_max = null, $rating = null) {
    global $pdo;

    $query = "SELECT t.*, c.name as category_name FROM tours t LEFT JOIN categories c ON t.category_id = c.id WHERE 1=1";
    $params = [];

    if ($category) {
        $query .= " AND c.name = ?";
        $params[] = $category;
    }

    if ($exclude_id) {
        $query .= " AND t.id != ?";
        $params[] = $exclude_id;
    }

    if ($search) {
        $query .= " AND (t.title LIKE ? OR t.description LIKE ?)";
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
    }

    if ($location) {
        $query .= " AND t.location LIKE ?";
        $params[] = '%' . $location . '%';
    }

    if ($duration) {
        $query .= " AND t.duration LIKE ?";
        $params[] = '%' . $duration . '%';
    }

    if ($price_min !== null) {
        $query .= " AND t.pricing >= ?";
        $params[] = $price_min;
    }

    if ($price_max !== null) {
        $query .= " AND t.pricing <= ?";
        $params[] = $price_max;
    }

    if ($rating) {
        $query .= " AND t.rating >= ?";
        $params[] = $rating;
    }

    $orderClause = " ORDER BY t.created_at DESC";
    if ($orderby) {
        switch ($orderby) {
            case 'popularity':
                $orderClause = " ORDER BY t.rating DESC";
                break;
            case 'rating':
                $orderClause = " ORDER BY t.rating DESC";
                break;
            case 'date':
                $orderClause = " ORDER BY t.created_at DESC";
                break;
            case 'price':
                $orderClause = " ORDER BY t.pricing ASC";
                break;
            case 'price-desc':
                $orderClause = " ORDER BY t.pricing DESC";
                break;
            default:
                $orderClause = " ORDER BY t.created_at DESC";
        }
    }
    $query .= $orderClause;

    if ($limit) {
        $query .= " LIMIT " . (int)$limit;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Render a tour box (specific style)
 *
 * @param array $tour Tour data from database
 * @return string HTML for the tour box
 */
function renderTourBox($tour) {
    $imagePath = $tour['images'] ? json_decode($tour['images'], true)[0] : 'default.webp';
    $imageUrl = "assets/img/{$imagePath}";
    $detailUrl = "tour/{$tour['slug']}";
    $rating = number_format($tour['rating'], 1);
    $ratingPercent = $tour['rating'] * 20; // For width percentage

    $html = '<div class="tour-box th-ani gsap-cursor" data-type="' . htmlspecialchars(strtolower(str_replace(' ', '-', $tour['title']))) . '" data-location="' . htmlspecialchars($tour['location']) . '" data-duration="' . htmlspecialchars($tour['duration']) . '">
                                            <div class="tour-box_img global-img">
                                                <img src="' . $imageUrl . '" alt=" Discover the Majestic ' . htmlspecialchars($tour['title']) . '">
                                            </div>
                                            <div class="tour-content">
                                                <h3 class="box-title">
                                                    <a href="' . $detailUrl . '">' . htmlspecialchars($tour['title']) . '</a>
                                                </h3>
                                                <div class="tour-rating">
                                                    <div class="star-rating" role="img" aria-label="Rated ' . $rating . ' out of 5">
                                                        <span style="width: ' . $ratingPercent . '%">Rated <strong class="rating">' . $rating . '</strong> out of 5</span>
                                                    </div>
                                                    <a href="#" class="woocommerce-review-link">(' . rand(50, 200) . '+ Reviews)</a>
                                                </div>
                                                <h4 class="tour-box_price">
                                                    <span class="currency"> <i class="fas fa-location"></i> ' . htmlspecialchars($tour['location']) . '</span>
                                                </h4>
                                                <div class="tour-action">
                                                    <span><i class="fa-light fa-clock"></i>' . htmlspecialchars($tour['duration']) . '</span>
                                                    <a href="' . $detailUrl . '" class="th-btn text-nowrap style4 th-icon">Read More</a>
                                                </div>
                                            </div>
                                        </div>';

    return $html;
}

/**
 * Render a blog card
 *
 * @param array $blog Blog data from database
 * @param string $style 'grid' or 'list' for different layouts
 * @return string HTML for the blog card
 */
function renderBlogCard($blog, $style = 'grid', $detailUrlPrefix = 'blog/') {
    $containerClass = ($style === 'swiper') ? '' : "col-xxl-4 col-lg-4 col-md-6 mb-4";
    $imagePath = $blog['featured_image'] ?: 'default.webp';
    $imageUrl = "../assets/img/blog/{$imagePath}";
    $detailUrl = $detailUrlPrefix . $blog['slug'];
    $excerpt = $blog['excerpt'] ?: substr(strip_tags($blog['content']), 0, 150) . '...';

    $html = "<style>
                .blog-card-description {
                    display: -webkit-box !important;
                    -webkit-line-clamp: 3 !important;
                    -webkit-box-orient: vertical !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                    line-height: 1.4 !important;
                    max-height: 4.2em !important;
                }
                .blog-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                }
                .blog-card:hover .blog-card-image img {
                    transform: scale(1.05);
                }
                @media (max-width: 767px) {
                    .blog-card {
                        height: auto !important;
                        margin-bottom: 20px;
                    }
                    .blog-card-content {
                        height: auto !important;
                    }
                }
            </style>
            <div class='{$containerClass}'>
                <div class='blog-card' style='height: 400px; min-width: 310px; background: #FFFFFF; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease;'>
                    <div class='blog-card-image' style='position: relative; height: 200px; overflow: hidden;'>
                        <img src='{$imageUrl}' alt='" . htmlspecialchars($blog['title']) . "' style='width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;'>
                    </div>
                    <div class='blog-card-content' style='padding: 16px; height: calc(100% - 200px); display: flex; flex-direction: column;'>
                        <h3 class='blog-card-title' style='font-size: 18px; font-weight: 600; color: #333; margin: 0 0 8px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;'>
                            <a href='{$detailUrl}' style='color: inherit; text-decoration: none;'>" . htmlspecialchars($blog['title']) . "</a>
                        </h3>
                        <p class='blog-card-description' style='font-size: 14px; color: #666; margin: 0 0 12px 0; flex-grow: 1;'>
                            " . htmlspecialchars($excerpt) . "
                        </p>
                        <div class='blog-card-meta' style='display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #999;'>
                            <span>By " . htmlspecialchars($blog['author']) . "</span>
                            <span>" . date('M d, Y', strtotime($blog['publication_date'])) . "</span>
                        </div>
                    </div>
                </div>
            </div>";

    return $html;
}

/**
 * Get blogs by category or all
 *
 * @param string $category Category name or null for all
 * @param int $limit Number of blogs to fetch
 * @param string $status 'published' or null for all
 * @return array Array of blogs
 */
function getBlogs($category = null, $limit = null, $status = 'published') {
    global $pdo;

    $query = "SELECT b.*, c.name as category_name FROM blogs b LEFT JOIN categories c ON b.category_id = c.id WHERE 1=1";
    $params = [];

    if ($status) {
        $query .= " AND b.status = ?";
        $params[] = $status;
    }

    if ($category) {
        $query .= " AND c.name = ?";
        $params[] = $category;
    }

    $query .= " ORDER BY b.publication_date DESC";

    if ($limit) {
        $query .= " LIMIT " . (int)$limit;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get blog comments
 *
 * @param int $blog_id Blog ID
 * @param string $status 'approved' or null for all
 * @return array Array of comments
 */
function getBlogComments($blog_id, $status = 'approved') {
    global $pdo;

    $query = "SELECT * FROM blog_comments WHERE blog_id = ?";
    $params = [$blog_id];

    if ($status) {
        $query .= " AND status = ?";
        $params[] = $status;
    }

    $query .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Check if a PDO exception is caused by a missing table
 *
 * @param Exception $exception Exception instance
 * @return bool True when the exception indicates SQL table not found
 */
function isMissingTableError($exception) {
    return $exception instanceof PDOException
        && isset($exception->errorInfo[1])
        && (int)$exception->errorInfo[1] === 1146;
}

/**
 * Get SEO data for a page
 *
 * @param string $page_key The page key (e.g., 'home', 'about')
 * @return array|null SEO data or null if not found
 */
function getPageSEO($page_key) {
    global $pdo;

    static $pageSeoTableExists = null;

    if ($pageSeoTableExists === false) {
        return null;
    }

    if ($pageSeoTableExists === null) {
        try {
            $check = $pdo->query("SHOW TABLES LIKE 'page_seo'");
            $pageSeoTableExists = (bool)$check->fetchColumn();
        } catch (PDOException $e) {
            if (isMissingTableError($e)) {
                $pageSeoTableExists = false;
                return null;
            }

            throw $e;
        }
    }

    if (!$pageSeoTableExists) {
        return null;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM page_seo WHERE page_key = ?");
        $stmt->execute([$page_key]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        if (isMissingTableError($e)) {
            $pageSeoTableExists = false;
            return null;
        }

        throw $e;
    }
}

/**
 * Render SEO head tags for a page
 *
 * @param string $page_key The page key
 * @param array $overrides Optional overrides for specific fields
 */
function renderSEOHead($page_key, $overrides = []) {
    $seo = getPageSEO($page_key);

    if (!$seo) {
        // Default fallback
        $seo = [
            'page_title' => 'India Day Trip - Agra Based Tour & Travel Company',
            'meta_description' => 'India Day Trip - Agra based tour and travel company offering Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours',
            'meta_keywords' => 'India Day Trip, Agra tours, Taj Mahal tours, Golden Triangle tours',
            'canonical_url' => 'https://indiadaytrip.com/',
            'robots_meta' => 'INDEX,FOLLOW'
        ];
    }

    // Apply overrides
    foreach ($overrides as $key => $value) {
        if (isset($seo[$key])) {
            $seo[$key] = $value;
        }
    }

    // Output title
    echo '<title>' . htmlspecialchars($seo['page_title']) . '</title>' . "\n";

    // Output meta tags
    if (!empty($seo['meta_description'])) {
        echo '    <meta name="description" content="' . htmlspecialchars($seo['meta_description']) . '">' . "\n";
    }

    if (!empty($seo['meta_keywords'])) {
        echo '    <meta name="keywords" content="' . htmlspecialchars($seo['meta_keywords']) . '">' . "\n";
    }

    if (!empty($seo['robots_meta'])) {
        echo '    <meta name="robots" content="' . htmlspecialchars($seo['robots_meta']) . '">' . "\n";
    }

    // Canonical URL
    $canonical = $seo['canonical_url'] ?? '';
    if (empty($canonical)) {
        $canonical = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    echo '    <link rel="canonical" href="' . htmlspecialchars($canonical) . '">' . "\n";

    // Open Graph
    if (!empty($seo['og_title'])) {
        echo '    <meta property="og:title" content="' . htmlspecialchars($seo['og_title']) . '">' . "\n";
    }

    if (!empty($seo['og_description'])) {
        echo '    <meta property="og:description" content="' . htmlspecialchars($seo['og_description']) . '">' . "\n";
    }

    if (!empty($seo['og_image'])) {
        echo '    <meta property="og:image" content="' . htmlspecialchars($seo['og_image']) . '">' . "\n";
    }

    if (!empty($seo['og_type'])) {
        echo '    <meta property="og:type" content="' . htmlspecialchars($seo['og_type']) . '">' . "\n";
    }

    echo '    <meta property="og:url" content="' . htmlspecialchars($canonical) . '">' . "\n";

    // Twitter
    if (!empty($seo['twitter_card'])) {
        echo '    <meta property="twitter:card" content="' . htmlspecialchars($seo['twitter_card']) . '">' . "\n";
    }

    if (!empty($seo['twitter_title'])) {
        echo '    <meta property="twitter:title" content="' . htmlspecialchars($seo['twitter_title']) . '">' . "\n";
    }

    if (!empty($seo['twitter_description'])) {
        echo '    <meta property="twitter:description" content="' . htmlspecialchars($seo['twitter_description']) . '">' . "\n";
    }

    if (!empty($seo['twitter_image'])) {
        echo '    <meta property="twitter:image" content="' . htmlspecialchars($seo['twitter_image']) . '">' . "\n";
    }

    // Schema markup
    if (!empty($seo['schema_markup'])) {
        echo '    <script type="application/ld+json">' . "\n";
        echo $seo['schema_markup'] . "\n";
        echo '    </script>' . "\n";
    }
}

/**
 * Render SEO head tags for a tour
 *
 * @param array $tour Tour data from database
 */
function renderTourSEOHead($tour) {
    global $pdo;

    // Backward compatibility for older schema fields used by admin/forms.
    if (empty($tour['schema_markup'])) {
        if (!empty($tour['schemas'])) {
            $tour['schema_markup'] = $tour['schemas'];
        } elseif (!empty($tour['schema'])) {
            $tour['schema_markup'] = $tour['schema'];
        }
    }

    // Check if tour has custom SEO
    $seo_fields = ['meta_title', 'meta_description', 'meta_keywords', 'canonical_url', 'robots_meta', 'og_title', 'og_description', 'og_image', 'twitter_title', 'twitter_description', 'twitter_image', 'schema_markup'];
    $has_custom_seo = false;

    foreach ($seo_fields as $field) {
        if (!empty($tour[$field])) {
            $has_custom_seo = true;
            break;
        }
    }

    if ($has_custom_seo) {
        // Use tour's custom SEO
        $seo = [];
        foreach ($seo_fields as $field) {
            $seo[$field] = $tour[$field] ?? '';
        }
        $seo['page_title'] = $tour['meta_title'] ?? '';
        $seo['og_type'] = 'article';
        $seo['twitter_card'] = 'summary_large_image';
    } else {
        // Generate SEO from tour data
        $title = htmlspecialchars($tour['title']);
        $description = htmlspecialchars(substr(strip_tags($tour['description']), 0, 160));
        $keywords = 'India Day Trip, ' . htmlspecialchars($tour['location']) . ' tours, ' . htmlspecialchars(str_replace(' - ', ' ', $tour['title']));

        $seo = [
            'page_title' => $title . ' - India Day Trip',
            'meta_description' => $description,
            'meta_keywords' => $keywords,
            'canonical_url' => 'https://indiadaytrip.com/tour/' . htmlspecialchars($tour['slug']),
            'robots_meta' => 'INDEX,FOLLOW',
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => !empty($tour['images']) ? 'https://indiadaytrip.com/assets/img/' . json_decode($tour['images'], true)[0] : '',
            'og_type' => 'article',
            'twitter_title' => $title,
            'twitter_description' => $description,
            'twitter_image' => !empty($tour['images']) ? 'https://indiadaytrip.com/assets/img/' . json_decode($tour['images'], true)[0] : '',
            'twitter_card' => 'summary_large_image',
            'schema_markup' => ''
        ];
    }

    // Output title
    echo '<title>' . htmlspecialchars($seo['page_title']) . '</title>' . "\n";

    // Output meta tags
    if (!empty($seo['meta_description'])) {
        echo '    <meta name="description" content="' . htmlspecialchars($seo['meta_description']) . '">' . "\n";
    }

    if (!empty($seo['meta_keywords'])) {
        echo '    <meta name="keywords" content="' . htmlspecialchars($seo['meta_keywords']) . '">' . "\n";
    }

    if (!empty($seo['robots_meta'])) {
        echo '    <meta name="robots" content="' . htmlspecialchars($seo['robots_meta']) . '">' . "\n";
    }

    // Canonical URL
    $canonical = $seo['canonical_url'] ?? '';
    if (empty($canonical)) {
        $canonical = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    echo '    <link rel="canonical" href="' . htmlspecialchars($canonical) . '">' . "\n";

    // Open Graph
    if (!empty($seo['og_title'])) {
        echo '    <meta property="og:title" content="' . htmlspecialchars($seo['og_title']) . '">' . "\n";
    }

    if (!empty($seo['og_description'])) {
        echo '    <meta property="og:description" content="' . htmlspecialchars($seo['og_description']) . '">' . "\n";
    }

    if (!empty($seo['og_image'])) {
        echo '    <meta property="og:image" content="' . htmlspecialchars($seo['og_image']) . '">' . "\n";
    }

    if (!empty($seo['og_type'])) {
        echo '    <meta property="og:type" content="' . htmlspecialchars($seo['og_type']) . '">' . "\n";
    }

    echo '    <meta property="og:url" content="' . htmlspecialchars($canonical) . '">' . "\n";

    // Twitter
    if (!empty($seo['twitter_card'])) {
        echo '    <meta property="twitter:card" content="' . htmlspecialchars($seo['twitter_card']) . '">' . "\n";
    }

    if (!empty($seo['twitter_title'])) {
        echo '    <meta property="twitter:title" content="' . htmlspecialchars($seo['twitter_title']) . '">' . "\n";
    }

    if (!empty($seo['twitter_description'])) {
        echo '    <meta property="twitter:description" content="' . htmlspecialchars($seo['twitter_description']) . '">' . "\n";
    }

    if (!empty($seo['twitter_image'])) {
        echo '    <meta property="twitter:image" content="' . htmlspecialchars($seo['twitter_image']) . '">' . "\n";
    }

    // Schema markup
    if (!empty($seo['schema_markup'])) {
        echo '    <script type="application/ld+json">' . "\n";
        echo $seo['schema_markup'] . "\n";
        echo '    </script>' . "\n";
    }
}

/**
 * Render SEO head tags for a blog
 *
 * @param array $blog Blog data from database
 */
function renderBlogSEOHead($blog) {
    // Backward compatibility for older schema fields used by admin/forms.
    if (empty($blog['schema_markup'])) {
        if (!empty($blog['schemas'])) {
            $blog['schema_markup'] = $blog['schemas'];
        } elseif (!empty($blog['schema'])) {
            $blog['schema_markup'] = $blog['schema'];
        }
    }

    // Check if blog has custom SEO
    $seo_fields = ['meta_title', 'meta_description', 'meta_keywords', 'canonical_url', 'robots_meta', 'og_title', 'og_description', 'og_image', 'twitter_title', 'twitter_description', 'twitter_image', 'schema_markup'];
    $has_custom_seo = false;

    foreach ($seo_fields as $field) {
        if (!empty($blog[$field])) {
            $has_custom_seo = true;
            break;
        }
    }

    if ($has_custom_seo) {
        // Use blog's custom SEO
        $seo = [];
        foreach ($seo_fields as $field) {
            $seo[$field] = $blog[$field] ?? '';
        }
        $seo['og_type'] = 'article';
        $seo['twitter_card'] = 'summary_large_image';
    } else {
        // Generate SEO from blog data
        $title = htmlspecialchars($blog['title']);
        $description = htmlspecialchars($blog['excerpt'] ?: substr(strip_tags($blog['content']), 0, 160));
        $keywords = 'India travel blog, ' . htmlspecialchars($blog['title']) . ', India Day Trip';

        $seo = [
            'page_title' => $title . ' - India Day Trip Blog',
            'meta_description' => $description,
            'meta_keywords' => $keywords,
            'canonical_url' => 'https://indiadaytrip.com/blog/' . htmlspecialchars($blog['slug']),
            'robots_meta' => 'INDEX,FOLLOW',
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => !empty($blog['featured_image']) ? 'https://indiadaytrip.com/assets/img/blog/' . htmlspecialchars($blog['featured_image']) : '',
            'og_type' => 'article',
            'twitter_title' => $title,
            'twitter_description' => $description,
            'twitter_image' => !empty($blog['featured_image']) ? 'https://indiadaytrip.com/assets/img/blog/' . htmlspecialchars($blog['featured_image']) : '',
            'twitter_card' => 'summary_large_image',
            'schema_markup' => ''
        ];
    }

    // Output title
    echo '<title>' . htmlspecialchars($seo['page_title']) . '</title>' . "\n";

    // Output meta tags
    if (!empty($seo['meta_description'])) {
        echo '    <meta name="description" content="' . htmlspecialchars($seo['meta_description']) . '">' . "\n";
    }

    if (!empty($seo['meta_keywords'])) {
        echo '    <meta name="keywords" content="' . htmlspecialchars($seo['meta_keywords']) . '">' . "\n";
    }

    if (!empty($seo['robots_meta'])) {
        echo '    <meta name="robots" content="' . htmlspecialchars($seo['robots_meta']) . '">' . "\n";
    }

    // Canonical URL
    $canonical = $seo['canonical_url'] ?? '';
    if (empty($canonical)) {
        $canonical = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    echo '    <link rel="canonical" href="' . htmlspecialchars($canonical) . '">' . "\n";

    // Open Graph
    if (!empty($seo['og_title'])) {
        echo '    <meta property="og:title" content="' . htmlspecialchars($seo['og_title']) . '">' . "\n";
    }

    if (!empty($seo['og_description'])) {
        echo '    <meta property="og:description" content="' . htmlspecialchars($seo['og_description']) . '">' . "\n";
    }

    if (!empty($seo['og_image'])) {
        echo '    <meta property="og:image" content="' . htmlspecialchars($seo['og_image']) . '">' . "\n";
    }

    if (!empty($seo['og_type'])) {
        echo '    <meta property="og:type" content="' . htmlspecialchars($seo['og_type']) . '">' . "\n";
    }

    echo '    <meta property="og:url" content="' . htmlspecialchars($canonical) . '">' . "\n";

    // Open Graph article specific
    echo '    <meta property="article:published_time" content="' . date('c', strtotime($blog['publication_date'])) . '">' . "\n";
    echo '    <meta property="article:author" content="' . htmlspecialchars($blog['author']) . '">' . "\n";

    // Twitter
    if (!empty($seo['twitter_card'])) {
        echo '    <meta property="twitter:card" content="' . htmlspecialchars($seo['twitter_card']) . '">' . "\n";
    }

    if (!empty($seo['twitter_title'])) {
        echo '    <meta property="twitter:title" content="' . htmlspecialchars($seo['twitter_title']) . '">' . "\n";
    }

    if (!empty($seo['twitter_description'])) {
        echo '    <meta property="twitter:description" content="' . htmlspecialchars($seo['twitter_description']) . '">' . "\n";
    }

    if (!empty($seo['twitter_image'])) {
        echo '    <meta property="twitter:image" content="' . htmlspecialchars($seo['twitter_image']) . '">' . "\n";
    }

    // Schema markup
    if (!empty($seo['schema_markup'])) {
        echo '    <script type="application/ld+json">' . "\n";
        echo $seo['schema_markup'] . "\n";
        echo '    </script>' . "\n";
    }
}

/**
 * Update SEO data for a page
 *
 * @param string $page_key The page key
 * @param array $data SEO data to update
 * @return bool Success status
 */
function updatePageSEO($page_key, $data) {
    global $pdo;

    $fields = ['page_title', 'meta_description', 'meta_keywords', 'canonical_url', 'robots_meta',
               'og_title', 'og_description', 'og_image', 'og_type',
               'twitter_title', 'twitter_description', 'twitter_image', 'twitter_card',
               'schema_markup'];

    $setParts = [];
    $params = [];

    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $setParts[] = "$field = ?";
            $params[] = $data[$field];
        }
    }

    if (empty($setParts)) {
        return false;
    }

    $params[] = $page_key;
    $query = "UPDATE page_seo SET " . implode(', ', $setParts) . " WHERE page_key = ?";

    try {
        $stmt = $pdo->prepare($query);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        if (isMissingTableError($e)) {
            return false;
        }

        throw $e;
    }
}

/**
 * Insert or update SEO data for a page
 *
 * @param string $page_key The page key
 * @param array $data SEO data
 * @return bool Success status
 */
function setPageSEO($page_key, $data) {
    global $pdo;

    $fields = ['page_key', 'page_title', 'meta_description', 'meta_keywords', 'canonical_url', 'robots_meta',
               'og_title', 'og_description', 'og_image', 'og_type',
               'twitter_title', 'twitter_description', 'twitter_image', 'twitter_card',
               'schema_markup'];

    $columns = [];
    $placeholders = [];
    $params = [];

    foreach ($fields as $field) {
        if (isset($data[$field]) || $field === 'page_key') {
            $columns[] = $field;
            $placeholders[] = '?';
            $params[] = $field === 'page_key' ? $page_key : ($data[$field] ?? null);
        }
    }

    $query = "INSERT INTO page_seo (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")
              ON DUPLICATE KEY UPDATE " . implode(', ', array_map(function($col) {
                  return "$col = VALUES($col)";
              }, array_diff($columns, ['page_key'])));

    try {
        $stmt = $pdo->prepare($query);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        if (isMissingTableError($e)) {
            return false;
        }

        throw $e;
    }
}

/**
 * Get all page SEO data for admin management
 *
 * @return array Array of all page SEO records
 */
function getAllPageSEO() {
    global $pdo;

    try {
        $stmt = $pdo->query("SELECT * FROM page_seo ORDER BY page_key");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        if (isMissingTableError($e)) {
            return [];
        }

        throw $e;
    }
}

/**
 * Check if admin is logged in
 */
function checkAdminLogin() {
    if (!isset($_SESSION['admin_id'])) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
            exit;
        } else {
            header('Location: login.php');
            exit;
        }
    }
}

/**
 * INTERNAL: Start session only when it's safe to do so.
 */
function startSessionIfPossible() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return true;
    }

    if (headers_sent()) {
        return false;
    }

    session_start();
    return session_status() === PHP_SESSION_ACTIVE;
}

/**
 * SECURITY: Generate CSRF token
 */
function generateCSRFToken() {
    if (!startSessionIfPossible()) {
        return '';
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * SECURITY: Verify CSRF token from POST
 */
function verifyCSRFToken($token = null) {
    if (!startSessionIfPossible()) {
        return false;
    }
    $token = $token ?? ($_POST['csrf_token'] ?? '');
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * SECURITY: Sanitize HTML input (allow safe HTML tags)
 */
function sanitizeHTML($input) {
    $allowed_tags = '<p><br><strong><em><u><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><code><pre><a>';
    return strip_tags($input, $allowed_tags);
}

/**
 * SECURITY: Validate file upload (MIME type and extension)
 */
function validateFileUpload($file_array, $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], $max_size = 5242880) {
    if (!is_array($file_array) || !isset($file_array['tmp_name'])) {
        return ['valid' => false, 'error' => 'Invalid file upload'];
    }

    // Check file size
    if ($file_array['size'] > $max_size) {
        return ['valid' => false, 'error' => 'File size exceeds maximum allowed size'];
    }

    // Check for upload errors
    if ($file_array['error'] !== UPLOAD_ERR_OK) {
        return ['valid' => false, 'error' => 'File upload error: ' . $file_array['error']];
    }

    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file_array['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        return ['valid' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowed_types)];
    }

    // Validate extension matches MIME type
    $ext = strtolower(pathinfo($file_array['name'], PATHINFO_EXTENSION));
    $valid_extensions = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/gif' => ['gif'],
        'image/webp' => ['webp']
    ];

    if (!isset($valid_extensions[$mime_type]) || !in_array($ext, $valid_extensions[$mime_type])) {
        return ['valid' => false, 'error' => 'File extension does not match file type'];
    }

    return ['valid' => true, 'mime_type' => $mime_type, 'extension' => $ext];
}

/**
 * SECURITY: Sanitize filename to prevent directory traversal and injection
 */
function sanitizeFilename($filename) {
    // Remove path components
    $filename = basename($filename);
    // Remove special characters, keep only alphanumeric, dash, underscore, dot
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    // Remove leading dots
    $filename = ltrim($filename, '.');
    return $filename;
}

/**
 * PERFORMANCE: Get cached settings with session fallback safety
 */
function getSettingCached($key, $use_cache = true) {
    global $pdo;
    static $memory_cache = [];

    $session_available = startSessionIfPossible();

    if ($session_available && !isset($_SESSION['_settings_cache'])) {
        $_SESSION['_settings_cache'] = [];
    }

    if ($use_cache) {
        if ($session_available && isset($_SESSION['_settings_cache'][$key])) {
            return $_SESSION['_settings_cache'][$key];
        }
        if (array_key_exists($key, $memory_cache)) {
            return $memory_cache[$key];
        }
    }

    // Fetch from database
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $value = $stmt->fetchColumn();

    // Cache the result
    if ($session_available) {
        $_SESSION['_settings_cache'][$key] = $value;
    }
    $memory_cache[$key] = $value;

    return $value;
}

/**
 * PERFORMANCE: Clear settings cache (call after updating settings)
 */
function clearSettingsCache() {
    if (startSessionIfPossible()) {
        $_SESSION['_settings_cache'] = [];
    }
}

/**
 * PERFORMANCE: Add pagination helper
 */
function getPaginationInfo($current_page, $total_items, $per_page = 20) {
    $per_page = max(1, (int)$per_page);
    $total_items = max(0, (int)$total_items);
    $total_pages = max(1, (int)ceil($total_items / $per_page));
    $current_page = max(1, min((int)$current_page, $total_pages));
    $offset = ($current_page - 1) * $per_page;
    
    return [
        'current_page' => max(1, $current_page),
        'per_page' => $per_page,
        'total_items' => $total_items,
        'total_pages' => $total_pages,
        'offset' => max(0, $offset),
        'has_previous' => $current_page > 1,
        'has_next' => $current_page < $total_pages
    ];
}

?>
