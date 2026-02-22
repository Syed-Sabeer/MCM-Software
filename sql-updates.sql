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

SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'slug') = 0,
  'ALTER TABLE `products` ADD COLUMN `slug` varchar(255) DEFAULT NULL AFTER `name`, ADD UNIQUE KEY `products_slug_unique` (`slug`)',
  'SELECT 1'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'publish_on_website') = 0,
  'ALTER TABLE `products` ADD COLUMN `publish_on_website` tinyint(1) NOT NULL DEFAULT 0 AFTER `shipping_info`',
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


-- -----------------------------------------------------------------------------
-- 8) Add color_id to product_other_images table (links images to colors)
-- -----------------------------------------------------------------------------
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'product_other_images' AND COLUMN_NAME = 'color_id') = 0,
  'ALTER TABLE `product_other_images` ADD COLUMN `color_id` int(10) UNSIGNED DEFAULT NULL AFTER `sort_order`',
  'SELECT 1'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;


-- -----------------------------------------------------------------------------
-- 9) Restructure pricing charts: Chart -> Types -> Tiers
-- -----------------------------------------------------------------------------

-- 9a) Create product_pricing_chart_types table (NEW - sits between charts and tiers)
CREATE TABLE IF NOT EXISTS `product_pricing_chart_types` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_pricing_chart_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(100) NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_pricing_chart_types_chart_id_foreign` (`product_pricing_chart_id`),
  CONSTRAINT `product_pricing_chart_types_chart_id_foreign` FOREIGN KEY (`product_pricing_chart_id`) REFERENCES `product_pricing_charts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9b) Add product_pricing_chart_type_id column to tiers table (for new structure)
SET @sql = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'product_pricing_chart_tiers' AND COLUMN_NAME = 'product_pricing_chart_type_id') = 0,
  'ALTER TABLE `product_pricing_chart_tiers` ADD COLUMN `product_pricing_chart_type_id` int(10) UNSIGNED DEFAULT NULL AFTER `product_pricing_chart_id`',
  'SELECT 1'
));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 9c) Migrate existing data: Create types from existing charts and link tiers to them
-- This creates one type per existing chart using the chart's 'type' field
INSERT INTO `product_pricing_chart_types` (`product_pricing_chart_id`, `type`, `sort_order`, `created_at`, `updated_at`)
SELECT `id`, COALESCE(`type`, 'Default'), 0, NOW(), NOW()
FROM `product_pricing_charts`
WHERE `id` NOT IN (SELECT DISTINCT `product_pricing_chart_id` FROM `product_pricing_chart_types`);

-- 9d) Update tiers to reference the new type records
UPDATE `product_pricing_chart_tiers` t
JOIN `product_pricing_chart_types` pt ON pt.`product_pricing_chart_id` = t.`product_pricing_chart_id`
SET t.`product_pricing_chart_type_id` = pt.`id`
WHERE t.`product_pricing_chart_type_id` IS NULL;
 