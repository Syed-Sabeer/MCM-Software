-- =============================================================================
-- CRM Database updates – run in phpMyAdmin (or any MySQL client)
-- Safe to run multiple times: skips columns/constraints that already exist.
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1) Product categories table (for product create/edit)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `product_categories` (`id`, `name`, `created_at`, `updated_at`) VALUES (1, 'Uncategorized', NOW(), NOW());


-- -----------------------------------------------------------------------------
-- 2) Add new columns to `products` (only if they don't exist)
-- -----------------------------------------------------------------------------
SET @db = DATABASE();

SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'category_id') = 0,
  'ALTER TABLE `products` ADD COLUMN `category_id` int(10) UNSIGNED DEFAULT NULL AFTER `price`',
  'SELECT 1'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'style') = 0,
  'ALTER TABLE `products` ADD COLUMN `style` varchar(255) DEFAULT NULL AFTER `category_id`',
  'SELECT 1'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'size') = 0,
  'ALTER TABLE `products` ADD COLUMN `size` varchar(100) DEFAULT NULL AFTER `style`',
  'SELECT 1'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'cover_image') = 0,
  'ALTER TABLE `products` ADD COLUMN `cover_image` varchar(500) DEFAULT NULL AFTER `size`',
  'SELECT 1'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'additional_info') = 0,
  'ALTER TABLE `products` ADD COLUMN `additional_info` longtext DEFAULT NULL AFTER `cover_image`',
  'SELECT 1'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'shipping_info') = 0,
  'ALTER TABLE `products` ADD COLUMN `shipping_info` longtext DEFAULT NULL AFTER `additional_info`',
  'SELECT 1'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Ensure InnoDB (required for foreign keys)
ALTER TABLE `products` ENGINE=InnoDB;
ALTER TABLE `product_categories` ENGINE=InnoDB;

-- Ensure category_id column type matches product_categories.id (both int unsigned)
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'category_id') > 0,
  'ALTER TABLE `products` MODIFY COLUMN `category_id` int(10) UNSIGNED DEFAULT NULL',
  'SELECT 1'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Clear any category_id values that don't exist in product_categories (avoids FK error 150)
UPDATE `products` p
LEFT JOIN `product_categories` c ON p.`category_id` = c.`id`
SET p.`category_id` = NULL
WHERE p.`category_id` IS NOT NULL AND c.`id` IS NULL;

-- -----------------------------------------------------------------------------
-- Optional: add foreign key from products.category_id -> product_categories.id
-- Run the line below manually in phpMyAdmin if you want referential integrity.
-- If you get errno 150: ensure both tables are InnoDB (SHOW TABLE STATUS) and
-- column types match (both int unsigned). The app works without this FK.
-- -----------------------------------------------------------------------------
-- ALTER TABLE `products` ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL;


-- -----------------------------------------------------------------------------
-- 3) Product other images table
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_other_images` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(10) UNSIGNED NOT NULL,
  `path` varchar(500) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_other_images_product_id_foreign` (`product_id`),
  CONSTRAINT `product_other_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- 4) Product colors table
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_colors` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `color_code` varchar(20) NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_colors_product_id_foreign` (`product_id`),
  CONSTRAINT `product_colors_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- 5) Product key points (key_heading + key_point pairs)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_key_points` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(10) UNSIGNED NOT NULL,
  `key_heading` varchar(255) NOT NULL,
  `key_point` text NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_key_points_product_id_foreign` (`product_id`),
  CONSTRAINT `product_key_points_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- -----------------------------------------------------------------------------
-- 6) Product pricing charts (heading + type, each chart has multiple quantity/price tiers)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_pricing_charts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(10) UNSIGNED NOT NULL,
  `heading` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_pricing_charts_product_id_foreign` (`product_id`),
  CONSTRAINT `product_pricing_charts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_pricing_chart_tiers` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_pricing_chart_id` int(10) UNSIGNED NOT NULL,
  `quantity` decimal(12,4) NOT NULL DEFAULT 0,
  `price` decimal(12,4) NOT NULL DEFAULT 0,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_pricing_chart_tiers_chart_id_foreign` (`product_pricing_chart_id`),
  CONSTRAINT `product_pricing_chart_tiers_chart_id_foreign` FOREIGN KEY (`product_pricing_chart_id`) REFERENCES `product_pricing_charts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
