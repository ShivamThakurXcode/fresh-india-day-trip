<?php
require_once 'config.php';

// Check if slug columns exist and add them if not
$tables = ['tours', 'blogs'];

foreach ($tables as $table) {
    try {
        // Check if slug column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM $table LIKE 'slug'");
        $columnExists = $stmt->fetch();

        if (!$columnExists) {
            // Add slug column
            $pdo->exec("ALTER TABLE $table ADD COLUMN slug VARCHAR(255) UNIQUE NOT NULL AFTER title");
            $pdo->exec("ALTER TABLE $table ADD INDEX idx_slug (slug)");
            echo "Added slug column to $table table.\n";
        } else {
            echo "Slug column already exists in $table table.\n";
        }
    } catch (Exception $e) {
        echo "Error with $table table: " . $e->getMessage() . "\n";
    }
}

// Generate slugs for existing tours
try {
    $stmt = $pdo->query("SELECT id, title FROM tours WHERE slug IS NULL OR slug = ''");
    $tours = $stmt->fetchAll();

    foreach ($tours as $tour) {
        $slug = generateSlug($tour['title'], 'tours', $tour['id']);
        $updateStmt = $pdo->prepare("UPDATE tours SET slug = ? WHERE id = ?");
        $updateStmt->execute([$slug, $tour['id']]);
        echo "Updated tour ID {$tour['id']} with slug: {$slug}\n";
    }
} catch (Exception $e) {
    echo "Error updating tours: " . $e->getMessage() . "\n";
}

// Generate slugs for existing blogs
try {
    $stmt = $pdo->query("SELECT id, title FROM blogs WHERE slug IS NULL OR slug = ''");
    $blogs = $stmt->fetchAll();

    foreach ($blogs as $blog) {
        $slug = generateSlug($blog['title'], 'blogs', $blog['id']);
        $updateStmt = $pdo->prepare("UPDATE blogs SET slug = ? WHERE id = ?");
        $updateStmt->execute([$slug, $blog['id']]);
        echo "Updated blog ID {$blog['id']} with slug: {$slug}\n";
    }
} catch (Exception $e) {
    echo "Error updating blogs: " . $e->getMessage() . "\n";
}

echo "Migration completed!\n";

// Add new columns to tours table
try {
    $columnsToAdd = [
        'internal_items' => 'JSON',
        'included_points' => 'JSON',
        'excluded_points' => 'JSON',
        'faq' => 'JSON',
        'schema' => 'TEXT',
        'schemas' => 'LONGTEXT',
        'schema_markup' => 'LONGTEXT',
        'meta_title' => 'VARCHAR(255)',
        'meta_description' => 'TEXT',
        'meta_keywords' => 'TEXT',
        'show_price' => 'TINYINT(1) DEFAULT 1'
    ];

    foreach ($columnsToAdd as $column => $type) {
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM `tours` LIKE '$column'");
            $columnExists = $stmt->fetch();

            if (!$columnExists) {
                $pdo->exec("ALTER TABLE `tours` ADD COLUMN `$column` $type");
                echo "Added $column column to tours table.\n";
            } else {
                echo "$column column already exists in tours table.\n";
            }
        } catch (Exception $columnException) {
            echo "Error adding $column column to tours table: " . $columnException->getMessage() . "\n";
        }
    }

    // Keep legacy schema fields aligned where possible.
    try {
        $pdo->exec("UPDATE tours SET schema_markup = COALESCE(NULLIF(schema_markup, ''), NULLIF(`schemas`, ''), NULLIF(`schema`, ''), schema_markup)");
        $pdo->exec("UPDATE tours SET `schemas` = COALESCE(NULLIF(`schemas`, ''), NULLIF(schema_markup, ''), NULLIF(`schema`, ''), `schemas`)");
        $pdo->exec("UPDATE tours SET `schema` = COALESCE(NULLIF(`schema`, ''), NULLIF(schema_markup, ''), NULLIF(`schemas`, ''), `schema`)");
        echo "Synchronized legacy schema columns for tours.\n";
    } catch (Exception $schemaSyncException) {
        echo "Error synchronizing tour schema columns: " . $schemaSyncException->getMessage() . "\n";
    }
} catch (Exception $e) {
    echo "Error adding columns to tours table: " . $e->getMessage() . "\n";
}

// Update blogs table structure
try {
    $blogColumnsToAdd = [
        'excerpt' => 'TEXT',
        'category_id' => 'INT',
        'status' => "ENUM('draft', 'published') DEFAULT 'draft'",
        'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        'meta_title' => 'VARCHAR(255)',
        'meta_description' => 'TEXT',
        'meta_keywords' => 'TEXT',
        'schemas' => 'LONGTEXT',
        'schema_markup' => 'LONGTEXT'
    ];

    foreach ($blogColumnsToAdd as $column => $type) {
        $stmt = $pdo->query("SHOW COLUMNS FROM `blogs` LIKE '$column'");
        $columnExists = $stmt->fetch();

        if (!$columnExists) {
            $pdo->exec("ALTER TABLE `blogs` ADD COLUMN `$column` $type");
            echo "Added $column column to blogs table.\n";
        } else {
            echo "$column column already exists in blogs table.\n";
        }
    }

    // Add foreign key for category_id
    $stmt = $pdo->query("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = 'blogs' AND CONSTRAINT_TYPE = 'FOREIGN KEY'");
    $fkExists = $stmt->fetchAll();
    $hasCategoryFk = false;
    foreach ($fkExists as $fk) {
        if (strpos($fk['CONSTRAINT_NAME'], 'category_id') !== false) {
            $hasCategoryFk = true;
            break;
        }
    }
    if (!$hasCategoryFk) {
        $pdo->exec("ALTER TABLE blogs ADD CONSTRAINT fk_blogs_category_id FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL");
        echo "Added foreign key for category_id in blogs table.\n";
    }

    // Add indexes
    $indexesToAdd = ['idx_status' => 'status', 'idx_category' => 'category_id'];
    foreach ($indexesToAdd as $index => $column) {
        $stmt = $pdo->query("SHOW INDEX FROM blogs WHERE Key_name = '$index'");
        $indexExists = $stmt->fetch();
        if (!$indexExists) {
            $pdo->exec("ALTER TABLE `blogs` ADD INDEX `$index` (`$column`)");
            echo "Added $index index to blogs table.\n";
        }
    }

    // Remove old categories column if exists
    $stmt = $pdo->query("SHOW COLUMNS FROM blogs LIKE 'categories'");
    $oldColumnExists = $stmt->fetch();
    if ($oldColumnExists) {
        $pdo->exec("ALTER TABLE blogs DROP COLUMN categories");
        echo "Dropped old categories column from blogs table.\n";
    }

    // Keep legacy schema fields aligned where possible.
    try {
        $pdo->exec("UPDATE blogs SET schema_markup = COALESCE(NULLIF(schema_markup, ''), NULLIF(`schemas`, ''), schema_markup)");
        $pdo->exec("UPDATE blogs SET `schemas` = COALESCE(NULLIF(`schemas`, ''), NULLIF(schema_markup, ''), `schemas`)");
        echo "Synchronized legacy schema columns for blogs.\n";
    } catch (Exception $schemaSyncException) {
        echo "Error synchronizing blog schema columns: " . $schemaSyncException->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "Error updating blogs table: " . $e->getMessage() . "\n";
}

// Create blog_comments table
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS blog_comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        blog_id INT NOT NULL,
        author_name VARCHAR(100) NOT NULL,
        author_email VARCHAR(100),
        content TEXT NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
        INDEX idx_blog_id (blog_id),
        INDEX idx_status (status)
    )");
    echo "Created blog_comments table.\n";
} catch (Exception $e) {
    echo "Error creating blog_comments table: " . $e->getMessage() . "\n";
}

// Create page_seo table
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS page_seo (
        id INT AUTO_INCREMENT PRIMARY KEY,
        page_key VARCHAR(100) NOT NULL UNIQUE,
        page_title VARCHAR(255) NOT NULL,
        meta_description TEXT,
        meta_keywords TEXT,
        canonical_url VARCHAR(500),
        robots_meta VARCHAR(100) DEFAULT 'INDEX,FOLLOW',
        og_title VARCHAR(255),
        og_description TEXT,
        og_image VARCHAR(500),
        og_type VARCHAR(50) DEFAULT 'website',
        twitter_title VARCHAR(255),
        twitter_description TEXT,
        twitter_image VARCHAR(500),
        twitter_card VARCHAR(50) DEFAULT 'summary_large_image',
        schema_markup LONGTEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_page_key (page_key)
    )");
    echo "Created page_seo table.\n";
} catch (Exception $e) {
    echo "Error creating page_seo table: " . $e->getMessage() . "\n";
}
?>