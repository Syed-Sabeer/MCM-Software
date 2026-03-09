-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2026 at 10:46 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel-crm`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `comment` text DEFAULT NULL,
  `additional` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`additional`)),
  `schedule_from` datetime DEFAULT NULL,
  `schedule_to` datetime DEFAULT NULL,
  `is_done` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `entity_type` varchar(255) DEFAULT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `title`, `type`, `comment`, `additional`, `schedule_from`, `schedule_to`, `is_done`, `user_id`, `entity_type`, `entity_id`, `created_at`, `updated_at`, `location`) VALUES
(7, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:31:02', '2025-11-15 14:31:02', NULL),
(8, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Syed Sabeer\",\"label\":\"Syed Sabeer\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:31:02', '2025-11-15 14:31:02', NULL),
(9, 'Updated Emails', 'system', NULL, '{\"attribute\":\"Emails\",\"new\":{\"value\":[{\"label\":\"work\",\"value\":\"syedsabeer6198@gmail.com\"}],\"label\":\"syedsabeer6198@gmail.com (work)\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:31:02', '2025-11-15 14:31:02', NULL),
(10, 'Updated Contact Numbers', 'system', NULL, '{\"attribute\":\"Contact Numbers\",\"new\":{\"value\":[{\"label\":\"work\",\"value\":\"20094008480\"}],\"label\":\"20094008480 (work)\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:31:02', '2025-11-15 14:31:02', NULL),
(11, 'Updated Job Title', 'system', NULL, '{\"attribute\":\"Job Title\",\"new\":{\"value\":\"Employee\",\"label\":\"Employee\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:31:02', '2025-11-15 14:31:02', NULL),
(12, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:36:48', '2025-11-15 14:36:48', NULL),
(13, 'Updated Title', 'system', NULL, '{\"attribute\":\"Title\",\"new\":{\"value\":\"Accusamus atque earu\",\"label\":\"Accusamus atque earu\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:36:48', '2025-11-15 14:36:48', NULL),
(14, 'Updated Description', 'system', NULL, '{\"attribute\":\"Description\",\"new\":{\"value\":\"Necessitatibus reici\",\"label\":\"Necessitatibus reici\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:36:48', '2025-11-15 14:36:48', NULL),
(15, 'Updated Lead Value', 'system', NULL, '{\"attribute\":\"Lead Value\",\"new\":{\"value\":23,\"label\":\"$23.00\"},\"old\":{\"value\":null,\"label\":\"$0.00\"}}', NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:36:48', '2025-11-15 14:36:48', NULL),
(16, 'Updated Source', 'system', NULL, '{\"attribute\":\"Source\",\"new\":{\"value\":2,\"label\":\"Web\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:36:48', '2025-11-15 14:36:48', NULL),
(17, 'Updated Type', 'system', NULL, '{\"attribute\":\"Type\",\"new\":{\"value\":2,\"label\":\"Existing Business\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:36:48', '2025-11-15 14:36:48', NULL),
(18, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:36:48', '2025-11-15 14:36:48', NULL),
(19, 'Updated Expected Close Date', 'system', NULL, '{\"attribute\":\"Expected Close Date\",\"new\":{\"value\":\"2025-11-20\",\"label\":\"Thu Nov 20, 2025\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:36:48', '2025-11-15 14:36:48', NULL),
(20, 'Updated Pipeline', 'system', NULL, '{\"attribute\":\"Pipeline\",\"new\":{\"value\":1,\"label\":\"Default Pipeline\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:36:48', '2025-11-15 14:36:48', NULL),
(21, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":1,\"label\":\"New\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2025-11-15 14:36:48', '2025-11-15 14:36:48', NULL),
(22, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, '2026-02-06 10:07:01', '2026-02-06 10:07:01', NULL),
(23, NULL, 'note', 'dsf', NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-02-06 18:55:40', '2026-02-06 18:55:40', NULL),
(24, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, '2026-02-06 18:59:21', '2026-02-06 18:59:21', NULL),
(25, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-02-06 19:44:22', '2026-02-06 19:44:22', NULL),
(26, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-06 19:44:23', '2026-02-06 19:44:23', NULL),
(27, 'Updated Organization', 'system', NULL, '{\"attribute\":\"Organization\",\"new\":{\"value\":2,\"label\":\"Abdul Haynes\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-06 19:44:23', '2026-02-06 19:44:23', NULL),
(28, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, '2026-02-06 19:45:48', '2026-02-06 19:45:48', NULL),
(29, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, '2026-02-06 19:47:13', '2026-02-06 19:47:13', NULL),
(30, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, '2026-02-06 19:48:38', '2026-02-06 19:48:38', NULL),
(31, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, '2026-02-06 19:48:47', '2026-02-06 19:48:47', NULL),
(32, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, '2026-02-06 19:49:29', '2026-02-06 19:49:29', NULL),
(33, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, '2026-02-06 19:52:37', '2026-02-06 19:52:37', NULL),
(34, 'fg', 'file', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, '2026-02-06 19:53:23', '2026-02-06 19:53:23', NULL),
(35, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, '2026-02-06 19:54:28', '2026-02-06 19:54:28', NULL),
(36, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, '2026-02-06 19:55:48', '2026-02-06 19:55:48', NULL),
(37, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 18:36:10', '2026-02-16 18:36:10', NULL),
(38, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 18:36:10', '2026-02-16 18:36:10', NULL),
(39, 'Updated Organization', 'system', NULL, '{\"attribute\":\"Organization\",\"new\":{\"value\":2,\"label\":\"Abdul Haynes\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 18:36:10', '2026-02-16 18:36:10', NULL),
(52, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 18:43:13', '2026-02-16 18:43:13', NULL),
(53, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 18:43:14', '2026-02-16 18:43:14', NULL),
(54, 'Updated Organization', 'system', NULL, '{\"attribute\":\"Organization\",\"new\":{\"value\":2,\"label\":\"Abdul Haynes\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 18:43:14', '2026-02-16 18:43:14', NULL),
(55, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 18:43:53', '2026-02-16 18:43:53', NULL),
(56, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 18:43:53', '2026-02-16 18:43:53', NULL),
(57, 'Updated Organization', 'system', NULL, '{\"attribute\":\"Organization\",\"new\":{\"value\":2,\"label\":\"Abdul Haynes\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 18:43:53', '2026-02-16 18:43:53', NULL),
(58, 'Updated cell_phone', 'system', NULL, '{\"attribute\":\"cell_phone\",\"new\":{\"value\":\"0000000\",\"label\":\"0000000\"},\"old\":{\"value\":\"\",\"label\":\"\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 18:58:07', '2026-02-16 18:58:07', NULL),
(59, 'Updated email_secondary', 'system', NULL, '{\"attribute\":\"email_secondary\",\"new\":{\"value\":\"test@gmail.com\",\"label\":\"test@gmail.com\"},\"old\":{\"value\":\"\",\"label\":\"\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 18:58:07', '2026-02-16 18:58:07', NULL),
(60, 'Updated birth_date', 'system', NULL, '{\"attribute\":\"birth_date\",\"new\":{\"value\":\"1901-11-15 00:00:00\",\"label\":\"1901-11-15 00:00:00\"},\"old\":{\"value\":\"-000001-11-29T18:06:32.000000Z\",\"label\":\"Tue Nov 30, -0001 00:00 AM\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 18:58:07', '2026-02-16 18:58:07', NULL),
(61, 'f', 'file', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, '2026-02-16 18:59:31', '2026-02-16 18:59:31', NULL),
(62, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, '2026-02-16 19:01:25', '2026-02-16 19:01:25', NULL),
(63, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-16 19:07:04', '2026-02-16 19:07:04', NULL),
(64, 'nhgjfj', 'file', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-16 19:10:01', '2026-02-16 19:10:01', NULL),
(65, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 19:17:55', '2026-02-16 19:17:55', NULL),
(66, 'Updated Title', 'system', NULL, '{\"attribute\":\"Title\",\"new\":{\"value\":\"dsf\",\"label\":\"dsf\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 19:17:55', '2026-02-16 19:17:55', NULL),
(67, 'Updated Lead Value', 'system', NULL, '{\"attribute\":\"Lead Value\",\"new\":{\"value\":45,\"label\":\"$45.00\"},\"old\":{\"value\":null,\"label\":\"$0.00\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 19:17:55', '2026-02-16 19:17:55', NULL),
(68, 'Updated Source', 'system', NULL, '{\"attribute\":\"Source\",\"new\":{\"value\":1,\"label\":\"Email\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 19:17:55', '2026-02-16 19:17:55', NULL),
(69, 'Updated Type', 'system', NULL, '{\"attribute\":\"Type\",\"new\":{\"value\":1,\"label\":\"New Business\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 19:17:55', '2026-02-16 19:17:55', NULL),
(70, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 19:17:55', '2026-02-16 19:17:55', NULL),
(71, 'Updated Pipeline', 'system', NULL, '{\"attribute\":\"Pipeline\",\"new\":{\"value\":1,\"label\":\"Default Pipeline\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 19:17:55', '2026-02-16 19:17:55', NULL),
(72, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":1,\"label\":\"New\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-16 19:17:55', '2026-02-16 19:17:55', NULL),
(73, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 15:37:49', '2026-02-21 15:37:49', NULL),
(74, 'Updated Title', 'system', NULL, '{\"attribute\":\"Title\",\"new\":{\"value\":\"Occaecat quas qui al\",\"label\":\"Occaecat quas qui al\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 15:37:49', '2026-02-21 15:37:49', NULL),
(75, 'Updated Description', 'system', NULL, '{\"attribute\":\"Description\",\"new\":{\"value\":\"Amet minima ut sed\",\"label\":\"Amet minima ut sed\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 15:37:49', '2026-02-21 15:37:49', NULL),
(76, 'Updated Lead Value', 'system', NULL, '{\"attribute\":\"Lead Value\",\"new\":{\"value\":78,\"label\":\"$78.00\"},\"old\":{\"value\":null,\"label\":\"$0.00\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 15:37:49', '2026-02-21 15:37:49', NULL),
(77, 'Updated Source', 'system', NULL, '{\"attribute\":\"Source\",\"new\":{\"value\":5,\"label\":\"Direct\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 15:37:49', '2026-02-21 15:37:49', NULL),
(78, 'Updated Type', 'system', NULL, '{\"attribute\":\"Type\",\"new\":{\"value\":2,\"label\":\"Existing Business\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 15:37:49', '2026-02-21 15:37:49', NULL),
(79, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 15:37:49', '2026-02-21 15:37:49', NULL),
(80, 'Updated Expected Close Date', 'system', NULL, '{\"attribute\":\"Expected Close Date\",\"new\":{\"value\":\"2026-02-28\",\"label\":\"Sat Feb 28, 2026\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 15:37:49', '2026-02-21 15:37:49', NULL),
(81, 'Updated Pipeline', 'system', NULL, '{\"attribute\":\"Pipeline\",\"new\":{\"value\":1,\"label\":\"Default Pipeline\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 15:37:49', '2026-02-21 15:37:49', NULL),
(82, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":1,\"label\":\"New\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 15:37:49', '2026-02-21 15:37:49', NULL),
(83, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 18:16:51', '2026-02-21 18:16:51', NULL),
(84, 'Updated Title', 'system', NULL, '{\"attribute\":\"Title\",\"new\":{\"value\":\"Quis dicta id facili\",\"label\":\"Quis dicta id facili\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 18:16:51', '2026-02-21 18:16:51', NULL),
(85, 'Updated Description', 'system', NULL, '{\"attribute\":\"Description\",\"new\":{\"value\":\"Vero eum rerum bland\",\"label\":\"Vero eum rerum bland\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 18:16:51', '2026-02-21 18:16:51', NULL),
(86, 'Updated Lead Value', 'system', NULL, '{\"attribute\":\"Lead Value\",\"new\":{\"value\":6,\"label\":\"$6.00\"},\"old\":{\"value\":null,\"label\":\"$0.00\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 18:16:51', '2026-02-21 18:16:51', NULL),
(87, 'Updated Source', 'system', NULL, '{\"attribute\":\"Source\",\"new\":{\"value\":5,\"label\":\"Direct\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 18:16:51', '2026-02-21 18:16:51', NULL),
(88, 'Updated Type', 'system', NULL, '{\"attribute\":\"Type\",\"new\":{\"value\":1,\"label\":\"New Business\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 18:16:51', '2026-02-21 18:16:51', NULL),
(89, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 18:16:51', '2026-02-21 18:16:51', NULL),
(90, 'Updated Expected Close Date', 'system', NULL, '{\"attribute\":\"Expected Close Date\",\"new\":{\"value\":\"2026-02-26\",\"label\":\"Thu Feb 26, 2026\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 18:16:51', '2026-02-21 18:16:51', NULL),
(91, 'Updated Pipeline', 'system', NULL, '{\"attribute\":\"Pipeline\",\"new\":{\"value\":1,\"label\":\"Default Pipeline\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 18:16:51', '2026-02-21 18:16:51', NULL),
(92, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":1,\"label\":\"New\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 18:16:51', '2026-02-21 18:16:51', NULL),
(93, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":2,\"label\":\"Follow Up\"},\"old\":{\"value\":1,\"label\":\"New\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 18:17:17', '2026-02-21 18:17:17', NULL),
(94, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":3,\"label\":\"Prospect\"},\"old\":{\"value\":2,\"label\":\"Follow Up\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 18:17:20', '2026-02-21 18:17:20', NULL),
(95, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":2,\"label\":\"Follow Up\"},\"old\":{\"value\":1,\"label\":\"New\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-21 18:24:44', '2026-02-21 18:24:44', NULL),
(98, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 10:47:33', '2026-02-22 10:47:33', NULL),
(99, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Cotton Tote Bag\",\"label\":\"Cotton Tote Bag\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 10:47:33', '2026-02-22 10:47:33', NULL),
(100, 'Updated cover_image', 'system', NULL, '{\"attribute\":\"cover_image\",\"new\":{\"value\":\"product-images\\/pNsCYoTurKqsXyktIlfhBAIxAzqUUG8J3KCETwlG.jpg\",\"label\":\"product-images\\/pNsCYoTurKqsXyktIlfhBAIxAzqUUG8J3KCETwlG.jpg\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 11:02:27', '2026-02-22 11:02:27', NULL),
(101, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Cotton Tote Bags\",\"label\":\"Cotton Tote Bags\"},\"old\":{\"value\":\"Cotton Tote Bag\",\"label\":\"Cotton Tote Bag\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 11:55:47', '2026-02-22 11:55:47', NULL),
(102, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAGS-B77516\",\"label\":\"COTTONTOTEBAGS-B77516\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 11:55:47', '2026-02-22 11:55:47', NULL),
(103, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Cotton Tote Bag\",\"label\":\"Cotton Tote Bag\"},\"old\":{\"value\":\"Cotton Tote Bags\",\"label\":\"Cotton Tote Bags\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 11:56:04', '2026-02-22 11:56:04', NULL),
(104, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-C77BAD\",\"label\":\"COTTONTOTEBAG-C77BAD\"},\"old\":{\"value\":\"COTTONTOTEBAGS-B77516\",\"label\":\"COTTONTOTEBAGS-B77516\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 11:56:04', '2026-02-22 11:56:04', NULL),
(105, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-A7041D\",\"label\":\"COTTONTOTEBAG-A7041D\"},\"old\":{\"value\":\"COTTONTOTEBAG-C77BAD\",\"label\":\"COTTONTOTEBAG-C77BAD\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 11:56:34', '2026-02-22 11:56:34', NULL),
(106, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-4144C7\",\"label\":\"COTTONTOTEBAG-4144C7\"},\"old\":{\"value\":\"COTTONTOTEBAG-A7041D\",\"label\":\"COTTONTOTEBAG-A7041D\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 11:57:00', '2026-02-22 11:57:00', NULL),
(107, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-C901D1\",\"label\":\"COTTONTOTEBAG-C901D1\"},\"old\":{\"value\":\"COTTONTOTEBAG-4144C7\",\"label\":\"COTTONTOTEBAG-4144C7\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 11:57:40', '2026-02-22 11:57:40', NULL),
(108, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-58196F\",\"label\":\"COTTONTOTEBAG-58196F\"},\"old\":{\"value\":\"COTTONTOTEBAG-C901D1\",\"label\":\"COTTONTOTEBAG-C901D1\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 11:58:53', '2026-02-22 11:58:53', NULL),
(109, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-87E8DB\",\"label\":\"COTTONTOTEBAG-87E8DB\"},\"old\":{\"value\":\"COTTONTOTEBAG-58196F\",\"label\":\"COTTONTOTEBAG-58196F\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 12:26:08', '2026-02-22 12:26:08', NULL),
(110, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-B8D5E9\",\"label\":\"COTTONTOTEBAG-B8D5E9\"},\"old\":{\"value\":\"COTTONTOTEBAG-87E8DB\",\"label\":\"COTTONTOTEBAG-87E8DB\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 12:28:19', '2026-02-22 12:28:19', NULL),
(111, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-22 12:29:48', '2026-02-22 12:29:48', NULL),
(112, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-22 12:30:07', '2026-02-22 12:30:07', NULL),
(113, 'dsfdf', 'call', NULL, NULL, '2026-02-22 12:00:00', '2026-02-24 12:00:00', 0, 1, 'organizations', 2, '2026-02-22 12:33:29', '2026-02-22 12:33:29', NULL),
(114, NULL, 'note', 'ghghg', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-22 12:34:45', '2026-02-22 12:34:45', NULL),
(115, 'Updated additional_info', 'system', NULL, '{\"attribute\":\"additional_info\",\"new\":{\"value\":\"gfhg\",\"label\":\"gfhg\"},\"old\":{\"value\":\"\",\"label\":\"\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 12:36:31', '2026-02-22 12:36:31', NULL),
(116, 'Updated shipping_info', 'system', NULL, '{\"attribute\":\"shipping_info\",\"new\":{\"value\":\"gfh\",\"label\":\"gfh\"},\"old\":{\"value\":\"\",\"label\":\"\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 12:36:31', '2026-02-22 12:36:31', NULL),
(117, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-7AE17F\",\"label\":\"COTTONTOTEBAG-7AE17F\"},\"old\":{\"value\":\"COTTONTOTEBAG-B8D5E9\",\"label\":\"COTTONTOTEBAG-B8D5E9\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 12:36:31', '2026-02-22 12:36:31', NULL),
(118, 'Updated shipping_info', 'system', NULL, '{\"attribute\":\"shipping_info\",\"new\":{\"value\":\"<table class=\\\"pf-info-table\\\">\\r\\n<tbody>\\r\\n<tr>\\r\\n<td>Standard Lead Time<\\/td>\\r\\n<td>5-7 Business Days after artwork approval<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Rush Service<\\/td>\\r\\n<td>Available - Please contact customer service for rush options and pricing<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Shipping Methods<\\/td>\\r\\n<td>Ground, 2nd Day Air, Next Day Air, Freight for large orders<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Packaging<\\/td>\\r\\n<td>Standard bulk packaging. Individual poly bags available upon request<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Weight per Unit<\\/td>\\r\\n<td>Approximately 0.3 lbs<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Carton Dimensions<\\/td>\\r\\n<td>24\\\" x 18\\\" x 12\\\" (approx. 100 units per carton)<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>International Shipping<\\/td>\\r\\n<td>Available - Contact for international shipping quotes and lead times<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Freight Quote<\\/td>\\r\\n<td>Use the Freight Quote button to get shipping estimates<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Delivery Address<\\/td>\\r\\n<td>Residential and commercial addresses accepted. Loading dock availability may affect freight charges<\\/td>\\r\\n<\\/tr>\\r\\n<\\/tbody>\\r\\n<\\/table>\",\"label\":\"<table class=\\\"pf-info-table\\\">\\r\\n<tbody>\\r\\n<tr>\\r\\n<td>Standard Lead Time<\\/td>\\r\\n<td>5-7 Business Days after artwork approval<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Rush Service<\\/td>\\r\\n<td>Available - Please contact customer service for rush options and pricing<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Shipping Methods<\\/td>\\r\\n<td>Ground, 2nd Day Air, Next Day Air, Freight for large orders<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Packaging<\\/td>\\r\\n<td>Standard bulk packaging. Individual poly bags available upon request<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Weight per Unit<\\/td>\\r\\n<td>Approximately 0.3 lbs<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Carton Dimensions<\\/td>\\r\\n<td>24\\\" x 18\\\" x 12\\\" (approx. 100 units per carton)<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>International Shipping<\\/td>\\r\\n<td>Available - Contact for international shipping quotes and lead times<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Freight Quote<\\/td>\\r\\n<td>Use the Freight Quote button to get shipping estimates<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Delivery Address<\\/td>\\r\\n<td>Residential and commercial addresses accepted. Loading dock availability may affect freight charges<\\/td>\\r\\n<\\/tr>\\r\\n<\\/tbody>\\r\\n<\\/table>\"},\"old\":{\"value\":\"gfh\",\"label\":\"gfh\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 12:39:12', '2026-02-22 12:39:12', NULL),
(119, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-86993D\",\"label\":\"COTTONTOTEBAG-86993D\"},\"old\":{\"value\":\"COTTONTOTEBAG-7AE17F\",\"label\":\"COTTONTOTEBAG-7AE17F\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 12:39:12', '2026-02-22 12:39:12', NULL),
(120, 'Updated additional_info', 'system', NULL, '{\"attribute\":\"additional_info\",\"new\":{\"value\":\"<table class=\\\"pf-info-table\\\">\\r\\n<tbody>\\r\\n<tr>\\r\\n<td>Item No.<\\/td>\\r\\n<td>MQTB<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Item Name<\\/td>\\r\\n<td>Cotton Tote Bag<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Description<\\/td>\\r\\n<td>6oz Cotton Bag. 100% Cotton Canvas Tote self-fabric handles. Range of colors Reinforced at stress points.<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Available Colors<\\/td>\\r\\n<td>Army, Azalea, Black, Carolina Blue, Dark Grey, Forest Green, Gold, Light Grey, Hot Pink, Kelly Green, Lavender, Light Pink, Lime, Maroon, Natural, Navy, Orange, Purple, Red, Royal, Sapphire, Texas Orange, Turquoise, White, Yellow, Chocolate<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Product Size<\\/td>\\r\\n<td>15\\\"W x 16\\\"H<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Imprint Area<\\/td>\\r\\n<td>10\\\"W x 12\\\"H<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Quality \\/ Material<\\/td>\\r\\n<td>100% Cotton Canvas<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Quality Weight<\\/td>\\r\\n<td>6oz<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Handle Length<\\/td>\\r\\n<td>22\\\"<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Side Gussets<\\/td>\\r\\n<td>No<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Bottom Gussets<\\/td>\\r\\n<td>No<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Rush Available<\\/td>\\r\\n<td>Yes, Pls contact customer service<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Available Decoration<\\/td>\\r\\n<td>Spot Colors, 4 Color Process, Heat Transfers<\\/td>\\r\\n<\\/tr>\\r\\n<\\/tbody>\\r\\n<\\/table>\",\"label\":\"<table class=\\\"pf-info-table\\\">\\r\\n<tbody>\\r\\n<tr>\\r\\n<td>Item No.<\\/td>\\r\\n<td>MQTB<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Item Name<\\/td>\\r\\n<td>Cotton Tote Bag<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Description<\\/td>\\r\\n<td>6oz Cotton Bag. 100% Cotton Canvas Tote self-fabric handles. Range of colors Reinforced at stress points.<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Available Colors<\\/td>\\r\\n<td>Army, Azalea, Black, Carolina Blue, Dark Grey, Forest Green, Gold, Light Grey, Hot Pink, Kelly Green, Lavender, Light Pink, Lime, Maroon, Natural, Navy, Orange, Purple, Red, Royal, Sapphire, Texas Orange, Turquoise, White, Yellow, Chocolate<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Product Size<\\/td>\\r\\n<td>15\\\"W x 16\\\"H<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Imprint Area<\\/td>\\r\\n<td>10\\\"W x 12\\\"H<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Quality \\/ Material<\\/td>\\r\\n<td>100% Cotton Canvas<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Quality Weight<\\/td>\\r\\n<td>6oz<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Handle Length<\\/td>\\r\\n<td>22\\\"<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Side Gussets<\\/td>\\r\\n<td>No<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Bottom Gussets<\\/td>\\r\\n<td>No<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Rush Available<\\/td>\\r\\n<td>Yes, Pls contact customer service<\\/td>\\r\\n<\\/tr>\\r\\n<tr>\\r\\n<td>Available Decoration<\\/td>\\r\\n<td>Spot Colors, 4 Color Process, Heat Transfers<\\/td>\\r\\n<\\/tr>\\r\\n<\\/tbody>\\r\\n<\\/table>\"},\"old\":{\"value\":\"gfhg\",\"label\":\"gfhg\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 12:43:35', '2026-02-22 12:43:35', NULL),
(121, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-F29242\",\"label\":\"COTTONTOTEBAG-F29242\"},\"old\":{\"value\":\"COTTONTOTEBAG-86993D\",\"label\":\"COTTONTOTEBAG-86993D\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 12:43:35', '2026-02-22 12:43:35', NULL),
(122, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-B2939B\",\"label\":\"COTTONTOTEBAG-B2939B\"},\"old\":{\"value\":\"COTTONTOTEBAG-F29242\",\"label\":\"COTTONTOTEBAG-F29242\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 14:44:19', '2026-02-22 14:44:19', NULL),
(123, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-B33203\",\"label\":\"COTTONTOTEBAG-B33203\"},\"old\":{\"value\":\"COTTONTOTEBAG-B2939B\",\"label\":\"COTTONTOTEBAG-B2939B\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 14:44:35', '2026-02-22 14:44:35', NULL),
(124, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-4DF9C0\",\"label\":\"COTTONTOTEBAG-4DF9C0\"},\"old\":{\"value\":\"COTTONTOTEBAG-B33203\",\"label\":\"COTTONTOTEBAG-B33203\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 19:23:24', '2026-02-22 19:23:24', NULL),
(125, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-7E3396\",\"label\":\"COTTONTOTEBAG-7E3396\"},\"old\":{\"value\":\"COTTONTOTEBAG-4DF9C0\",\"label\":\"COTTONTOTEBAG-4DF9C0\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 19:29:03', '2026-02-22 19:29:03', NULL),
(126, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-E82D89\",\"label\":\"COTTONTOTEBAG-E82D89\"},\"old\":{\"value\":\"COTTONTOTEBAG-7E3396\",\"label\":\"COTTONTOTEBAG-7E3396\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 19:29:26', '2026-02-22 19:29:26', NULL),
(127, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-504F3D\",\"label\":\"COTTONTOTEBAG-504F3D\"},\"old\":{\"value\":\"COTTONTOTEBAG-E82D89\",\"label\":\"COTTONTOTEBAG-E82D89\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 19:30:37', '2026-02-22 19:30:37', NULL),
(128, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-79F413\",\"label\":\"COTTONTOTEBAG-79F413\"},\"old\":{\"value\":\"COTTONTOTEBAG-504F3D\",\"label\":\"COTTONTOTEBAG-504F3D\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 19:34:07', '2026-02-22 19:34:07', NULL),
(129, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-5B00A9\",\"label\":\"COTTONTOTEBAG-5B00A9\"},\"old\":{\"value\":\"COTTONTOTEBAG-79F413\",\"label\":\"COTTONTOTEBAG-79F413\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 19:35:09', '2026-02-22 19:35:09', NULL),
(130, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"COTTONTOTEBAG-13B742\",\"label\":\"COTTONTOTEBAG-13B742\"},\"old\":{\"value\":\"COTTONTOTEBAG-5B00A9\",\"label\":\"COTTONTOTEBAG-5B00A9\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-22 19:38:01', '2026-02-22 19:38:01', NULL),
(144, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, 'persons', 4, '2026-02-23 17:41:44', '2026-02-23 17:41:44', NULL),
(145, NULL, 'note', 'yhjjh', NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-02-23 17:41:59', '2026-02-23 17:41:59', NULL),
(146, 'v', 'call', 'bfghfgh', NULL, '2026-02-23 12:00:00', '2026-02-24 12:00:00', 0, 1, NULL, NULL, '2026-02-23 17:42:49', '2026-02-23 17:42:49', NULL),
(147, NULL, 'note', 'fggfg', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:21:22', '2026-02-23 18:21:22', NULL),
(150, NULL, 'note', 'dsfdsf', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:22:15', '2026-02-23 18:22:15', NULL),
(151, NULL, 'note', 'dsfdsf', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:22:21', '2026-02-23 18:22:21', NULL),
(153, NULL, 'note', 'cxcv', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:28:08', '2026-02-23 18:28:08', NULL),
(154, NULL, 'note', 'ffghfgh', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:28:11', '2026-02-23 18:28:11', NULL),
(155, NULL, 'note', 'ghfh', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:28:14', '2026-02-23 18:28:14', NULL),
(156, NULL, 'note', 'ghgfh', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:28:16', '2026-02-23 18:28:16', NULL),
(157, NULL, 'note', 'ghgfhgh', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:28:19', '2026-02-23 18:28:19', NULL),
(158, NULL, 'note', 'gfhfghgh', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:28:21', '2026-02-23 18:28:21', NULL),
(159, NULL, 'note', 'gfhgfhgfh', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:28:24', '2026-02-23 18:28:24', NULL),
(160, NULL, 'note', 'ghgfhg', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:28:28', '2026-02-23 18:28:28', NULL),
(161, NULL, 'note', 'fgfdg', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:28:41', '2026-02-23 18:28:41', NULL),
(162, 'ghgh', 'call', NULL, NULL, '2026-02-23 12:00:00', '2026-02-25 12:00:00', 0, 1, 'organizations', 2, '2026-02-23 18:28:55', '2026-02-23 18:28:55', NULL),
(163, NULL, 'note', 'trt', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:39:24', '2026-02-23 18:39:24', NULL),
(164, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-23 18:39:36', '2026-02-23 18:39:36', NULL),
(165, NULL, 'note', 'cvfdg', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:39:46', '2026-02-23 18:39:46', NULL),
(166, NULL, 'note', 'fgfg', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:39:49', '2026-02-23 18:39:49', NULL),
(167, NULL, 'note', 'fgfg', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:39:52', '2026-02-23 18:39:52', NULL),
(168, NULL, 'note', 'fdgfg', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:39:54', '2026-02-23 18:39:54', NULL),
(169, NULL, 'note', 'fdgdfg', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:39:57', '2026-02-23 18:39:57', NULL),
(170, NULL, 'note', 'fdgfdgfd', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:40:00', '2026-02-23 18:40:00', NULL),
(171, NULL, 'note', 'dfgdfg', NULL, NULL, NULL, 1, 1, 'organizations', 2, '2026-02-23 18:49:18', '2026-02-23 18:49:18', NULL),
(172, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-23 19:01:51', '2026-02-23 19:01:51', NULL),
(173, 'gfgg', 'task', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-23 19:14:21', '2026-02-23 19:14:21', NULL),
(174, 'gfgg', 'task', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-23 19:14:28', '2026-02-23 19:14:28', NULL),
(175, 'tyrry', 'task', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-23 19:22:30', '2026-02-23 19:22:30', NULL),
(176, 'dsfdf', 'task', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-23 19:22:43', '2026-02-23 19:22:43', NULL),
(177, 'Updated Industry', 'system', NULL, '{\"attribute\":\"Industry\",\"new\":{\"value\":\"\",\"label\":null},\"old\":{\"value\":0,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-23 19:25:23', '2026-02-23 19:25:23', NULL),
(178, 'Updated Employees', 'system', NULL, '{\"attribute\":\"Employees\",\"new\":{\"value\":800,\"label\":800},\"old\":{\"value\":80,\"label\":80}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-23 19:25:23', '2026-02-23 19:25:23', NULL),
(179, 'fgfg', 'task', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-23 19:26:34', '2026-02-23 19:26:34', NULL),
(180, 'hjgj', 'task', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-23 19:32:35', '2026-02-23 19:32:35', NULL),
(181, 'fghghgh', 'task', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-23 19:35:22', '2026-02-23 19:35:22', NULL),
(183, '000000000', 'task', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-23 19:42:12', '2026-02-23 19:42:12', NULL),
(184, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-02-23 20:11:53', '2026-02-23 20:11:53', NULL),
(185, 'Updated Subject', 'system', NULL, '{\"attribute\":\"Subject\",\"new\":{\"value\":\"gfdgf\",\"label\":\"gfdgf\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-23 20:11:53', '2026-02-23 20:11:53', NULL),
(186, 'Updated Description', 'system', NULL, '{\"attribute\":\"Description\",\"new\":{\"value\":\"fgfg\",\"label\":\"fgfg\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-23 20:11:53', '2026-02-23 20:11:53', NULL),
(187, 'Updated Case Origin', 'system', NULL, '{\"attribute\":\"Case Origin\",\"new\":{\"value\":3,\"label\":\"Web Form\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-23 20:11:53', '2026-02-23 20:11:53', NULL),
(188, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-23 20:11:53', '2026-02-23 20:11:53', NULL),
(189, 'Updated Pipeline', 'system', NULL, '{\"attribute\":\"Pipeline\",\"new\":{\"value\":1,\"label\":\"Default Pipeline\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-23 20:11:53', '2026-02-23 20:11:53', NULL),
(190, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":2,\"label\":\"Follow Up\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-23 20:11:53', '2026-02-23 20:11:53', NULL),
(191, 'Updated Priority', 'system', NULL, '{\"attribute\":\"Priority\",\"new\":{\"value\":\"low\",\"label\":null},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-23 20:11:53', '2026-02-23 20:11:53', NULL),
(207, NULL, 'file', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-23 20:25:51', '2026-02-23 20:25:51', NULL),
(208, 'tyfgjhj', 'task', NULL, NULL, NULL, NULL, 0, 1, 'organizations', 2, '2026-02-23 20:25:58', '2026-02-23 20:25:58', NULL),
(209, 'hjhj', 'task', NULL, NULL, '2026-02-25 12:00:00', NULL, 0, 1, 'organizations', 2, '2026-02-23 20:26:41', '2026-02-23 20:26:41', NULL),
(218, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":3,\"label\":\"Prospect\"},\"old\":{\"value\":1,\"label\":\"New\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-23 20:28:26', '2026-02-23 20:28:26', NULL),
(228, 'Updated cell_phone', 'system', NULL, '{\"attribute\":\"cell_phone\",\"new\":{\"value\":43534543543,\"label\":43534543543},\"old\":{\"value\":\"\",\"label\":\"\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-24 06:50:00', '2026-02-24 06:50:00', NULL),
(229, 'Updated email_secondary', 'system', NULL, '{\"attribute\":\"email_secondary\",\"new\":{\"value\":\"testuser@gmail.com\",\"label\":\"testuser@gmail.com\"},\"old\":{\"value\":\"\",\"label\":\"\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-24 06:50:00', '2026-02-24 06:50:00', NULL),
(230, 'Updated birth_date', 'system', NULL, '{\"attribute\":\"birth_date\",\"new\":{\"value\":\"1901-11-22 00:00:00\",\"label\":\"1901-11-22 00:00:00\"},\"old\":{\"value\":\"-000001-11-29T18:06:32.000000Z\",\"label\":\"Tue Nov 30, -0001 00:00 AM\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-24 06:50:00', '2026-02-24 06:50:00', NULL),
(231, 'Updated email', 'system', NULL, '{\"attribute\":\"email\",\"new\":{\"value\":\"testuser@gmail.com\",\"label\":\"testuser@gmail.com\"},\"old\":{\"value\":\"\",\"label\":\"\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-24 06:50:00', '2026-02-24 06:50:00', NULL),
(232, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":2,\"label\":\"Follow Up\"},\"old\":{\"value\":1,\"label\":\"New\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-24 12:18:23', '2026-02-24 12:18:23', NULL),
(233, NULL, 'note', 'its a test comment', NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-02-24 12:18:43', '2026-02-24 12:18:43', NULL),
(234, 'Call', 'call', 'I talked to this perosn and told them what we need from them.', NULL, '2026-02-24 12:00:00', '2026-02-25 12:00:00', 0, 1, NULL, NULL, '2026-02-24 12:19:30', '2026-02-24 12:19:30', 'In person online'),
(246, 'Updated Priority', 'system', NULL, '{\"attribute\":\"Priority\",\"new\":{\"value\":2,\"label\":\"Medium\"},\"old\":{\"value\":0,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-25 21:13:09', '2026-02-25 21:13:09', NULL),
(247, 'Updated case_no', 'system', NULL, '{\"attribute\":\"case_no\",\"new\":{\"value\":\"00012\",\"label\":\"00012\"},\"old\":{\"value\":\"00005\",\"label\":\"00005\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-25 22:37:08', '2026-02-25 22:37:08', NULL),
(248, 'Updated Priority', 'system', NULL, '{\"attribute\":\"Priority\",\"new\":{\"value\":1,\"label\":\"Low\"},\"old\":{\"value\":2,\"label\":\"Medium\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-25 22:55:46', '2026-02-25 22:55:46', NULL),
(249, 'Updated Expected Close Date', 'system', NULL, '{\"attribute\":\"Expected Close Date\",\"new\":{\"value\":null,\"label\":null},\"old\":{\"value\":\"2026-02-26\",\"label\":\"Thu Feb 26, 2026\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-26 13:39:02', '2026-02-26 13:39:02', NULL),
(250, NULL, 'note', 'jkjkkj', NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-02-26 13:45:56', '2026-02-26 13:45:56', NULL),
(251, 'Updated title', 'system', NULL, '{\"attribute\":\"title\",\"new\":{\"value\":\"fdkjdfjkg\",\"label\":\"fdkjdfjkg\"},\"old\":{\"value\":\"\",\"label\":\"\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-26 14:47:40', '2026-02-26 14:47:40', NULL),
(252, 'Updated description', 'system', NULL, '{\"attribute\":\"description\",\"new\":{\"value\":\"kjiljoi\",\"label\":\"kjiljoi\"},\"old\":{\"value\":\"\",\"label\":\"\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-26 14:47:40', '2026-02-26 14:47:40', NULL),
(253, 'Updated direct_phone', 'system', NULL, '{\"attribute\":\"direct_phone\",\"new\":{\"value\":\"difgkjv\",\"label\":\"difgkjv\"},\"old\":{\"value\":\"\",\"label\":\"\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-26 14:47:40', '2026-02-26 14:47:40', NULL),
(254, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-02-27 15:15:16', '2026-02-27 15:15:16', NULL),
(255, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-02-27 15:15:17', '2026-02-27 15:15:17', NULL),
(256, 'Updated Industry', 'system', NULL, '{\"attribute\":\"Industry\",\"new\":{\"value\":\"\",\"label\":null},\"old\":{\"value\":0,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-03-02 14:42:04', '2026-03-02 14:42:04', NULL),
(257, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-03-09 15:36:53', '2026-03-09 15:36:53', NULL),
(258, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Xantha Kaufman\",\"label\":\"Xantha Kaufman\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-03-09 15:36:53', '2026-03-09 15:36:53', NULL),
(259, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"Ullamco ab minus et\",\"label\":\"Ullamco ab minus et\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-03-09 15:36:53', '2026-03-09 15:36:53', NULL),
(260, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-03-09 15:37:14', '2026-03-09 15:37:14', NULL),
(261, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Xantha Kaufman (Copy)\",\"label\":\"Xantha Kaufman (Copy)\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-03-09 15:37:14', '2026-03-09 15:37:14', NULL),
(262, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"XANTHAKAUFMANCOPY-2A5DF5\",\"label\":\"XANTHAKAUFMANCOPY-2A5DF5\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-03-09 15:37:14', '2026-03-09 15:37:14', NULL),
(263, 'Updated Industry', 'system', NULL, '{\"attribute\":\"Industry\",\"new\":{\"value\":\"\",\"label\":null},\"old\":{\"value\":0,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-03-09 19:38:44', '2026-03-09 19:38:44', NULL),
(264, 'Updated Industry', 'system', NULL, '{\"attribute\":\"Industry\",\"new\":{\"value\":\"\",\"label\":null},\"old\":{\"value\":0,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-03-09 19:38:56', '2026-03-09 19:38:56', NULL),
(265, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-03-09 20:10:04', '2026-03-09 20:10:04', NULL),
(266, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Kathleen Haynes\",\"label\":\"Kathleen Haynes\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-03-09 20:10:04', '2026-03-09 20:10:04', NULL),
(267, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"Sit deserunt conseq\",\"label\":\"Sit deserunt conseq\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-03-09 20:10:04', '2026-03-09 20:10:04', NULL),
(268, 'Updated Price', 'system', NULL, '{\"attribute\":\"Price\",\"new\":{\"value\":938,\"label\":\"$938.00\"},\"old\":{\"value\":null,\"label\":\"$0.00\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-03-09 20:10:05', '2026-03-09 20:10:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `activity_files`
--

CREATE TABLE `activity_files` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `activity_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_files`
--

INSERT INTO `activity_files` (`id`, `name`, `path`, `activity_id`, `created_at`, `updated_at`) VALUES
(1, 'test image.jpeg', 'activities/22/LaMLVczohByWQi5YlSCd70Mzmq7RMDoP77VWZ5Kd.jpg', 22, '2026-02-06 10:07:03', '2026-02-06 10:07:03'),
(2, 'test image.jpeg', 'activities/24/7cWjcmCJ5xUvOlHPT61OWr7uiieBh2sDud0twi2C.jpg', 24, '2026-02-06 18:59:21', '2026-02-06 18:59:21'),
(3, 'test image.jpeg', 'activities/28/25N5s0ulu9TgkYVonu6KqhiRE4Uq9hGgqkcnjf4F.jpg', 28, '2026-02-06 19:45:49', '2026-02-06 19:45:49'),
(4, 'test image.jpeg', 'activities/29/9WnEaJdJkdpKWUlMSqeXT6cLKUfynr4BvU3RD08W.jpg', 29, '2026-02-06 19:47:13', '2026-02-06 19:47:13'),
(5, 'test image.jpeg', 'activities/30/bAbdfaxjp0Kd7Opp8orIPpAiSzgpBK9UFC92Xl2n.jpg', 30, '2026-02-06 19:48:38', '2026-02-06 19:48:38'),
(6, 'images.jpg', 'activities/31/09oOgGr6fDNycEvIWUNDfiHrAnvAlsSUQZJB6fq3.jpg', 31, '2026-02-06 19:48:48', '2026-02-06 19:48:48'),
(7, 'test image.jpeg', 'activities/32/7vlMWGitkADp0abTUSfF555cO2TaU1qy5MWrpplQ.jpg', 32, '2026-02-06 19:49:29', '2026-02-06 19:49:29'),
(8, 'test image.jpeg', 'activities/33/6QOSnfXc8wYeD3jCee9jHcLT04KuxTkzCQcDwmDa.jpg', 33, '2026-02-06 19:52:37', '2026-02-06 19:52:37'),
(9, '2023-bentley-continental-gt-s-coupe.jpg', 'activities/34/Hi51KJcFTDu0t3pJOHQvgSKnS9XVRE9mye6r1qRX.jpg', 34, '2026-02-06 19:53:23', '2026-02-06 19:53:23'),
(10, '2023-bentley-continental-gt-s-coupe.jpg', 'activities/35/VFS6hzZPD0zisguuwd4QIfHQor1p3Wta8rYs81OI.jpg', 35, '2026-02-06 19:54:28', '2026-02-06 19:54:28'),
(11, 'images.jpg', 'activities/36/nr0SgJfP1xY0QgPhVWiR1qPao5S9NpwVGKdCKtfG.jpg', 36, '2026-02-06 19:55:48', '2026-02-06 19:55:48'),
(12, '2023-bentley-continental-gt-s-coupe.jpg', 'activities/61/m8VZJ4T5ApQRf0ZiSa4VDKNwFQ8r2Mf1PjDoUKFf.jpg', 61, '2026-02-16 18:59:32', '2026-02-16 18:59:32'),
(13, 'test image.jpeg', 'activities/62/MxLiYLlXIpDLpQO6POsJW7Y7pHFobslHIxbhuyN5.jpg', 62, '2026-02-16 19:01:25', '2026-02-16 19:01:25'),
(14, 'test image.jpeg', 'activities/63/DUJuk43uqRiUbj88md7YdvcaPbRthVOPaFFxvICD.jpg', 63, '2026-02-16 19:07:04', '2026-02-16 19:07:04'),
(15, 'test image.jpeg', 'activities/64/beSMTCGBGxfGBrRcc3YI7rn1oLCfeIkHoX98s1g4.jpg', 64, '2026-02-16 19:10:01', '2026-02-16 19:10:01'),
(16, 'cover.jpg', 'activities/111/KdT4RHqylGd7O00VTuyU35WtXT1RrfqrLRw9RciL.jpg', 111, '2026-02-22 12:29:48', '2026-02-22 12:29:48'),
(17, 'cover.jpg', 'activities/112/mIUnXa8Wc8XT3Nbm7U43K39YouCAKM7ZCuyHJGfu.jpg', 112, '2026-02-22 12:30:07', '2026-02-22 12:30:07'),
(18, 'test image.jpeg', 'activities/144/pzxvZUnhA5usZnn8kZAnaLvMQJxgRVT8pR7o2Xq2.jpg', 144, '2026-02-23 17:41:44', '2026-02-23 17:41:44'),
(20, 'test image.jpeg', 'activities/164/1FG3LafXOf73WoWQbGQImiSPKmm79jSK0AuS3aqg.jpg', 164, '2026-02-23 18:39:36', '2026-02-23 18:39:36'),
(21, '📘 RENTAL WIZARD - WORKFORCE FINANCIAL MODULE – FINAL SSOT (v1.0).pdf', 'activities/172/N2eGvJPHAx1nahBGWTxuqSFUOCnvmGy7OKgVwaY7.pdf', 172, '2026-02-23 19:01:51', '2026-02-23 19:01:51'),
(22, 'test image.jpeg', 'activities/207/hfg5LTJ2avDYT32IgB5kbjMg0cAyDETm6FHDqYd2.jpg', 207, '2026-02-23 20:25:51', '2026-02-23 20:25:51');

-- --------------------------------------------------------

--
-- Table structure for table `activity_participants`
--

CREATE TABLE `activity_participants` (
  `id` int(10) UNSIGNED NOT NULL,
  `activity_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `person_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_participants`
--

INSERT INTO `activity_participants` (`id`, `activity_id`, `user_id`, `person_id`) VALUES
(1, 113, NULL, 1),
(2, 146, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `lookup_type` varchar(255) DEFAULT NULL,
  `entity_type` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `validation` varchar(255) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_unique` tinyint(1) NOT NULL DEFAULT 0,
  `quick_add` tinyint(1) NOT NULL DEFAULT 0,
  `is_user_defined` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `code`, `name`, `type`, `lookup_type`, `entity_type`, `sort_order`, `validation`, `is_required`, `is_unique`, `quick_add`, `is_user_defined`, `created_at`, `updated_at`) VALUES
(19, 'title', 'Subject', 'text', NULL, 'leads', 1, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(20, 'description', 'Description', 'textarea', NULL, 'leads', 2, NULL, 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(21, 'lead_value', 'Lead Value', 'price', NULL, 'leads', 3, 'decimal', 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(22, 'lead_source_id', 'Case Origin', 'select', 'lead_sources', 'leads', 4, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(23, 'lead_type_id', 'Type', 'select', 'lead_types', 'leads', 5, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(24, 'user_id', 'Sales Owner', 'select', 'users', 'leads', 7, NULL, 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(25, 'expected_close_date', 'Expected Close Date', 'date', NULL, 'leads', 8, NULL, 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(26, 'lead_pipeline_id', 'Pipeline', 'lookup', 'lead_pipelines', 'leads', 9, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(27, 'lead_pipeline_stage_id', 'Stage', 'lookup', 'lead_pipeline_stages', 'leads', 10, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(28, 'name', 'Name', 'text', NULL, 'persons', 1, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(29, 'emails', 'Emails', 'email', NULL, 'persons', 2, NULL, 1, 1, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(30, 'contact_numbers', 'Contact Numbers', 'phone', NULL, 'persons', 3, 'numeric', 0, 1, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(31, 'job_title', 'Job Title', 'text', NULL, 'persons', 4, NULL, 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(32, 'user_id', 'Sales Owner', 'lookup', 'users', 'persons', 5, NULL, 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(33, 'organization_id', 'Organization', 'lookup', 'organizations', 'persons', 6, NULL, 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(34, 'name', 'Name', 'text', NULL, 'organizations', 1, NULL, 1, 1, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(35, 'address', 'Billing Address', 'address', NULL, 'organizations', 2, NULL, 0, 0, 1, 0, '2025-11-15 14:24:53', '2026-02-05 09:20:18'),
(36, 'user_id', 'Sales Owner', 'lookup', 'users', 'organizations', 3, NULL, 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(37, 'name', 'Name', 'text', NULL, 'products', 1, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(38, 'description', 'Description', 'textarea', NULL, 'products', 2, NULL, 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(39, 'sku', 'SKU', 'text', NULL, 'products', 3, NULL, 1, 1, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(40, 'quantity', 'Quantity', 'text', NULL, 'products', 4, 'numeric', 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(41, 'price', 'Price', 'price', NULL, 'products', 5, 'decimal', 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(42, 'user_id', 'Sales Owner', 'select', 'users', 'quotes', 1, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(43, 'subject', 'Subject', 'text', NULL, 'quotes', 2, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(44, 'description', 'Description', 'textarea', NULL, 'quotes', 3, NULL, 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(45, 'billing_address', 'Billing Address', 'address', NULL, 'quotes', 4, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(46, 'shipping_address', 'Shipping Address', 'address', NULL, 'quotes', 5, NULL, 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(47, 'discount_percent', 'Discount Percent', 'text', NULL, 'quotes', 6, 'decimal', 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(48, 'discount_amount', 'Discount Amount', 'price', NULL, 'quotes', 7, 'decimal', 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(49, 'tax_amount', 'Tax Amount', 'price', NULL, 'quotes', 8, 'decimal', 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(50, 'adjustment_amount', 'Adjustment Amount', 'price', NULL, 'quotes', 9, 'decimal', 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(51, 'sub_total', 'Sub Total', 'price', NULL, 'quotes', 10, 'decimal', 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(52, 'grand_total', 'Grand Total', 'price', NULL, 'quotes', 11, 'decimal', 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(53, 'expired_at', 'Expired At', 'date', NULL, 'quotes', 12, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(54, 'person_id', 'Person', 'lookup', 'persons', 'quotes', 13, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(55, 'name', 'Name', 'text', NULL, 'warehouses', 1, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(56, 'description', 'Description', 'textarea', NULL, 'warehouses', 2, NULL, 0, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(57, 'contact_name', 'Contact Name', 'text', NULL, 'warehouses', 3, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(58, 'contact_emails', 'Contact Emails', 'email', NULL, 'warehouses', 4, NULL, 1, 1, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(59, 'contact_numbers', 'Contact Numbers', 'phone', NULL, 'warehouses', 5, 'numeric', 0, 1, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(60, 'contact_address', 'Contact Address', 'address', NULL, 'warehouses', 6, NULL, 1, 0, 1, 0, '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(61, 'about', 'About Product', 'textarea', NULL, 'products', NULL, NULL, 0, 0, 1, 1, '2025-12-07 12:19:08', '2025-12-07 12:19:08'),
(63, 'phone_org', 'Phone', 'phone', NULL, 'organizations', NULL, NULL, 0, 0, 1, 1, '2026-02-05 09:17:41', '2026-02-05 09:17:41'),
(64, 'fax_org', 'Fax', 'phone', NULL, 'organizations', NULL, NULL, 0, 0, 1, 1, '2026-02-05 09:18:30', '2026-02-05 09:18:30'),
(65, 'website_org', 'Website', 'text', NULL, 'organizations', NULL, 'url', 0, 0, 1, 1, '2026-02-05 09:19:34', '2026-02-05 09:19:34'),
(66, 'parent_organization_id', 'Parent Organization', 'lookup', 'organizations', 'organizations', 4, NULL, 0, 0, 1, 1, '2026-02-05 09:30:00', '2026-02-05 09:30:00'),
(67, 'organization_type', 'Type', 'select', NULL, 'organizations', 10, NULL, 0, 0, 1, 1, '2026-02-05 09:30:00', '2026-02-05 09:30:00'),
(68, 'industry', 'Industry', 'select', NULL, 'organizations', 11, NULL, 0, 0, 1, 1, '2026-02-05 09:30:00', '2026-02-05 09:30:00'),
(69, 'employees', 'Employees', 'text', NULL, 'organizations', 12, 'numeric', 0, 0, 1, 1, '2026-02-05 09:30:00', '2026-02-05 09:30:00'),
(70, 'annual_revenue', 'Annual Revenue', 'price', NULL, 'organizations', 13, 'decimal', 0, 0, 1, 1, '2026-02-05 09:30:00', '2026-02-05 09:30:00'),
(71, 'description_org', 'Description', 'textarea', NULL, 'organizations', 14, NULL, 0, 0, 1, 1, '2026-02-05 09:30:00', '2026-02-05 09:30:00'),
(72, 'shipping_address', 'Shipping Address', 'address', NULL, 'organizations', 15, NULL, 0, 0, 1, 1, '2026-02-05 09:30:00', '2026-02-05 09:30:00'),
(73, 'priority', 'Priority', 'select', 'lead_priorities', 'leads', 6, NULL, 0, 0, 1, 0, '2026-02-21 18:00:12', '2026-02-21 18:00:12');

-- --------------------------------------------------------

--
-- Table structure for table `attribute_options`
--

CREATE TABLE `attribute_options` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `attribute_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attribute_options`
--

INSERT INTO `attribute_options` (`id`, `name`, `sort_order`, `attribute_id`) VALUES
(1, 'Factory', 1, 67),
(2, 'Customer', 2, 67),
(3, 'Vendor', 3, 67),
(4, 'Marketing', 4, 67),
(5, 'Prospect', 5, 67),
(6, 'Sales Rep', 6, 67),
(7, 'Other', 7, 67),
(8, 'Home Apparel', 1, 68),
(9, 'Other Shipping', 2, 68);

-- --------------------------------------------------------

--
-- Table structure for table `attribute_values`
--

CREATE TABLE `attribute_values` (
  `id` int(10) UNSIGNED NOT NULL,
  `entity_type` varchar(255) NOT NULL DEFAULT 'leads',
  `text_value` text DEFAULT NULL,
  `boolean_value` tinyint(1) DEFAULT NULL,
  `integer_value` int(11) DEFAULT NULL,
  `float_value` double DEFAULT NULL,
  `datetime_value` datetime DEFAULT NULL,
  `date_value` date DEFAULT NULL,
  `json_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`json_value`)),
  `entity_id` int(10) UNSIGNED NOT NULL,
  `attribute_id` int(10) UNSIGNED NOT NULL,
  `unique_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attribute_values`
--

INSERT INTO `attribute_values` (`id`, `entity_type`, `text_value`, `boolean_value`, `integer_value`, `float_value`, `datetime_value`, `date_value`, `json_value`, `entity_id`, `attribute_id`, `unique_id`) VALUES
(6, 'persons', 'Syed Sabeer', NULL, NULL, NULL, NULL, NULL, NULL, 1, 28, NULL),
(7, 'persons', NULL, NULL, NULL, NULL, NULL, NULL, '[{\"value\":\"syedsabeer6198@gmail.com\",\"label\":\"work\"}]', 1, 29, NULL),
(8, 'persons', NULL, NULL, NULL, NULL, NULL, NULL, '[{\"value\":\"20094008480\",\"label\":\"work\"}]', 1, 30, NULL),
(9, 'persons', 'Employee', NULL, NULL, NULL, NULL, NULL, NULL, 1, 31, NULL),
(10, 'persons', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 32, NULL),
(11, 'persons', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 33, NULL),
(12, 'leads', 'Accusamus atque earu', NULL, NULL, NULL, NULL, NULL, NULL, 1, 19, NULL),
(13, 'leads', 'Necessitatibus reici', NULL, NULL, NULL, NULL, NULL, NULL, 1, 20, NULL),
(14, 'leads', NULL, NULL, NULL, 23, NULL, NULL, NULL, 1, 21, NULL),
(15, 'leads', NULL, NULL, 2, NULL, NULL, NULL, NULL, 1, 22, NULL),
(16, 'leads', NULL, NULL, 2, NULL, NULL, NULL, NULL, 1, 23, NULL),
(17, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 24, NULL),
(18, 'leads', NULL, NULL, NULL, NULL, NULL, '2025-11-20', NULL, 1, 25, NULL),
(19, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 26, NULL),
(20, 'leads', NULL, NULL, 2, NULL, NULL, NULL, NULL, 1, 27, NULL),
(21, 'organizations', 'Abdul Haynes', NULL, NULL, NULL, NULL, NULL, NULL, 2, 34, NULL),
(22, 'organizations', NULL, NULL, 0, NULL, NULL, NULL, NULL, 2, 68, NULL),
(23, 'organizations', '800', NULL, NULL, NULL, NULL, NULL, NULL, 2, 69, NULL),
(24, 'organizations', NULL, NULL, NULL, 72, NULL, NULL, NULL, 2, 70, NULL),
(25, 'persons', NULL, NULL, 1, NULL, NULL, NULL, NULL, 2, 32, NULL),
(26, 'persons', NULL, NULL, 2, NULL, NULL, NULL, NULL, 2, 33, NULL),
(27, 'persons', NULL, NULL, 1, NULL, NULL, NULL, NULL, 4, 32, NULL),
(28, 'persons', NULL, NULL, 2, NULL, NULL, NULL, NULL, 4, 33, NULL),
(37, 'persons', NULL, NULL, 1, NULL, NULL, NULL, NULL, 9, 32, NULL),
(38, 'persons', NULL, NULL, 2, NULL, NULL, NULL, NULL, 9, 33, NULL),
(39, 'persons', NULL, NULL, 1, NULL, NULL, NULL, NULL, 10, 32, NULL),
(40, 'persons', NULL, NULL, 2, NULL, NULL, NULL, NULL, 10, 33, NULL),
(41, 'leads', 'dsf', NULL, NULL, NULL, NULL, NULL, NULL, 2, 19, NULL),
(42, 'leads', '', NULL, NULL, NULL, NULL, NULL, NULL, 2, 20, NULL),
(43, 'leads', NULL, NULL, NULL, 45, NULL, NULL, NULL, 2, 21, NULL),
(44, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 2, 22, NULL),
(45, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 2, 23, NULL),
(46, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 2, 24, NULL),
(47, 'leads', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 25, NULL),
(48, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 2, 26, NULL),
(49, 'leads', NULL, NULL, 2, NULL, NULL, NULL, NULL, 2, 27, NULL),
(50, 'leads', 'Occaecat quas qui al', NULL, NULL, NULL, NULL, NULL, NULL, 3, 19, NULL),
(51, 'leads', 'Amet minima ut sed', NULL, NULL, NULL, NULL, NULL, NULL, 3, 20, NULL),
(52, 'leads', NULL, NULL, NULL, 78, NULL, NULL, NULL, 3, 21, NULL),
(53, 'leads', NULL, NULL, 5, NULL, NULL, NULL, NULL, 3, 22, NULL),
(54, 'leads', NULL, NULL, 2, NULL, NULL, NULL, NULL, 3, 23, NULL),
(55, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 3, 24, NULL),
(56, 'leads', NULL, NULL, NULL, NULL, NULL, '2026-02-28', NULL, 3, 25, NULL),
(57, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 3, 26, NULL),
(58, 'leads', NULL, NULL, 3, NULL, NULL, NULL, NULL, 3, 27, NULL),
(59, 'leads', 'Quis dicta id facili', NULL, NULL, NULL, NULL, NULL, NULL, 4, 19, NULL),
(60, 'leads', 'Vero eum rerum bland', NULL, NULL, NULL, NULL, NULL, NULL, 4, 20, NULL),
(61, 'leads', NULL, NULL, NULL, 6, NULL, NULL, NULL, 4, 21, NULL),
(62, 'leads', NULL, NULL, 5, NULL, NULL, NULL, NULL, 4, 22, NULL),
(63, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 4, 23, NULL),
(64, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 4, 24, NULL),
(65, 'leads', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, 25, NULL),
(66, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 4, 26, NULL),
(67, 'leads', NULL, NULL, 3, NULL, NULL, NULL, NULL, 4, 27, NULL),
(69, 'products', 'Cotton Tote Bag', NULL, NULL, NULL, NULL, NULL, NULL, 3, 37, NULL),
(70, 'products', 'COTTONTOTEBAG-13B742', NULL, NULL, NULL, NULL, NULL, NULL, 3, 39, NULL),
(86, 'leads', 'gfdgf', NULL, NULL, NULL, NULL, NULL, NULL, 5, 19, NULL),
(87, 'leads', 'fgfg', NULL, NULL, NULL, NULL, NULL, NULL, 5, 20, NULL),
(88, 'leads', NULL, NULL, 3, NULL, NULL, NULL, NULL, 5, 22, NULL),
(89, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 5, 24, NULL),
(90, 'leads', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, 25, NULL),
(91, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 5, 26, NULL),
(92, 'leads', NULL, NULL, 2, NULL, NULL, NULL, NULL, 5, 27, NULL),
(93, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 5, 73, NULL),
(137, 'quotes', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 42, NULL),
(138, 'quotes', 'test', NULL, NULL, NULL, NULL, NULL, NULL, 1, 43, NULL),
(139, 'quotes', 'test', NULL, NULL, NULL, NULL, NULL, NULL, 1, 44, NULL),
(140, 'quotes', NULL, NULL, NULL, NULL, NULL, NULL, '{\"address\":\"hjfhdsfhsfhj\",\"country\":\"AT\",\"state\":\"KN\",\"city\":\"dsfhsdhj\",\"postcode\":\"8889\"}', 1, 45, NULL),
(141, 'quotes', NULL, NULL, NULL, NULL, NULL, NULL, '{\"address\":\"\",\"country\":\"\",\"state\":\"\",\"city\":\"\",\"postcode\":\"\"}', 1, 46, NULL),
(142, 'quotes', NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 48, NULL),
(143, 'quotes', NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 49, NULL),
(144, 'quotes', NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 50, NULL),
(145, 'quotes', NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 51, NULL),
(146, 'quotes', NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 52, NULL),
(147, 'quotes', NULL, NULL, NULL, NULL, NULL, '2026-02-27', NULL, 1, 53, NULL),
(148, 'quotes', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, 54, NULL),
(149, 'persons', NULL, NULL, 1, NULL, NULL, NULL, NULL, 11, 32, NULL),
(150, 'persons', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 11, 33, NULL),
(151, 'products', 'Xantha Kaufman', NULL, NULL, NULL, NULL, NULL, NULL, 18, 37, NULL),
(152, 'products', 'Ullamco ab minus et', NULL, NULL, NULL, NULL, NULL, NULL, 18, 39, NULL),
(153, 'products', 'Xantha Kaufman (Copy)', NULL, NULL, NULL, NULL, NULL, NULL, 19, 37, NULL),
(154, 'products', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 19, 38, NULL),
(155, 'products', 'XANTHAKAUFMANCOPY-2A5DF5', NULL, NULL, NULL, NULL, NULL, NULL, 19, 39, NULL),
(156, 'products', '0', NULL, NULL, NULL, NULL, NULL, NULL, 19, 40, NULL),
(157, 'products', NULL, NULL, NULL, 0, NULL, NULL, NULL, 19, 41, NULL),
(158, 'products', 'Kathleen Haynes', NULL, NULL, NULL, NULL, NULL, NULL, 20, 37, NULL),
(159, 'products', 'Sit deserunt conseq', NULL, NULL, NULL, NULL, NULL, NULL, 20, 39, NULL),
(160, 'products', NULL, NULL, NULL, 938, NULL, NULL, NULL, 20, 41, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `core_config`
--

CREATE TABLE `core_config` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `core_config`
--

INSERT INTO `core_config` (`id`, `code`, `value`, `created_at`, `updated_at`) VALUES
(1, 'general.settings.menu.mail.mail', 'Mail', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(2, 'general.settings.menu.mail.inbox', 'Inbox', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(3, 'general.settings.menu.mail.draft', 'Draft', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(4, 'general.settings.menu.mail.outbox', 'Outbox', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(5, 'general.settings.menu.mail.sent', 'Sent', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(6, 'general.settings.menu.mail.trash', 'Trash', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(7, 'general.settings.menu.contacts.contacts', 'Contacts', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(8, 'general.settings.menu.contacts.persons', 'Persons', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(9, 'general.settings.menu.contacts.organizations', 'Organizations', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(10, 'general.settings.footer.label', '<!-- Footer Copyright Section -->\r\n<div style=\"text-align: center; padding: 18px 10px; font-size: 14px; color: #777; border-top: 1px solid #eee; line-height: 1.7;\">\r\n<p style=\"margin: 0;\">&copy; 2026 <a style=\"color: #aa1e25; text-decoration: none; font-weight: 600;\" href=\"https://metrocottonmill.com/\"> Metro Cotton Mill (Pvt) Ltd. </a> All Rights Reserved.</p>\r\n<p style=\"margin: 4px 0 0 0; font-size: 13px;\">Powered by <a style=\"color: #aa1e25; text-decoration: none; font-weight: 600;\" href=\"https://deveoninc.com/\" target=\"_blank\" rel=\"noopener\"> Deveon Inc </a></p>\r\n</div>', '2025-11-15 14:42:22', '2026-02-22 14:50:19'),
(11, 'general.settings.menu.dashboard', 'Dashboard', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(12, 'general.settings.menu.leads', 'Case', '2025-11-15 14:42:22', '2025-12-12 22:17:06'),
(13, 'general.settings.menu.quotes', 'Quotes', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(14, 'general.settings.menu.activities', 'Activities', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(15, 'general.settings.menu.products', 'Products', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(16, 'general.settings.menu.settings', 'Settings', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(17, 'general.settings.menu.configuration', 'Configuration', '2025-11-15 14:42:22', '2025-11-15 14:42:22'),
(18, 'general.settings.menu_color.brand_color', '#aa1e25', '2025-11-15 14:42:22', '2025-11-18 08:44:16'),
(19, 'general.general.locale_settings.locale', 'en', '2025-11-15 14:45:20', '2025-11-15 14:45:20'),
(20, 'general.general.admin_logo.logo_image', 'configuration/dcgu03AMYeGFkvyI1cq9EMwwOxZr4TSfhSX2oxx4.webp', '2025-11-15 14:45:20', '2026-02-27 14:32:38'),
(21, 'email.imap.account.host', 'mail.deveoninc.com', '2026-02-22 11:52:20', '2026-02-22 11:52:20'),
(22, 'email.imap.account.port', '993', '2026-02-22 11:52:20', '2026-02-22 11:52:20'),
(23, 'email.imap.account.encryption', 'ssl', '2026-02-22 11:52:20', '2026-02-22 11:53:37'),
(24, 'email.imap.account.validate_cert', '1', '2026-02-22 11:52:20', '2026-02-22 11:52:20'),
(25, 'email.imap.account.username', 'test@deveoninc.com', '2026-02-22 11:52:20', '2026-02-22 11:52:20'),
(26, 'email.imap.account.password', 'JDu$]w;FhnP;[r4w', '2026-02-22 11:52:20', '2026-02-22 11:52:20'),
(27, 'general.general.company_info.company_name', 'Metro Cotton Mill (PVT) LTD', '2026-02-27 14:31:35', '2026-02-27 14:31:43'),
(28, 'general.general.company_info.address', '4327 Summerfield Blvd', '2026-02-27 14:31:35', '2026-02-27 14:32:04'),
(29, 'general.general.company_info.telephone', '03176198517', '2026-02-27 14:31:35', '2026-02-27 14:32:04'),
(30, 'general.general.company_info.cell', '03176198517', '2026-02-27 14:31:35', '2026-02-27 14:32:04'),
(31, 'general.general.company_info.email', 'syedsabeer6198@gmail.com', '2026-02-27 14:31:35', '2026-02-27 14:32:04'),
(32, 'general.general.company_info.website', 'deveoninc.com', '2026-02-27 14:31:35', '2026-02-27 14:32:04');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `code`, `name`) VALUES
(1, 'AF', 'Afghanistan'),
(2, 'AX', 'Åland Islands'),
(3, 'AL', 'Albania'),
(4, 'DZ', 'Algeria'),
(5, 'AS', 'American Samoa'),
(6, 'AD', 'Andorra'),
(7, 'AO', 'Angola'),
(8, 'AI', 'Anguilla'),
(9, 'AQ', 'Antarctica'),
(10, 'AG', 'Antigua & Barbuda'),
(11, 'AR', 'Argentina'),
(12, 'AM', 'Armenia'),
(13, 'AW', 'Aruba'),
(14, 'AC', 'Ascension Island'),
(15, 'AU', 'Australia'),
(16, 'AT', 'Austria'),
(17, 'AZ', 'Azerbaijan'),
(18, 'BS', 'Bahamas'),
(19, 'BH', 'Bahrain'),
(20, 'BD', 'Bangladesh'),
(21, 'BB', 'Barbados'),
(22, 'BY', 'Belarus'),
(23, 'BE', 'Belgium'),
(24, 'BZ', 'Belize'),
(25, 'BJ', 'Benin'),
(26, 'BM', 'Bermuda'),
(27, 'BT', 'Bhutan'),
(28, 'BO', 'Bolivia'),
(29, 'BA', 'Bosnia & Herzegovina'),
(30, 'BW', 'Botswana'),
(31, 'BR', 'Brazil'),
(32, 'IO', 'British Indian Ocean Territory'),
(33, 'VG', 'British Virgin Islands'),
(34, 'BN', 'Brunei'),
(35, 'BG', 'Bulgaria'),
(36, 'BF', 'Burkina Faso'),
(37, 'BI', 'Burundi'),
(38, 'KH', 'Cambodia'),
(39, 'CM', 'Cameroon'),
(40, 'CA', 'Canada'),
(41, 'IC', 'Canary Islands'),
(42, 'CV', 'Cape Verde'),
(43, 'BQ', 'Caribbean Netherlands'),
(44, 'KY', 'Cayman Islands'),
(45, 'CF', 'Central African Republic'),
(46, 'EA', 'Ceuta & Melilla'),
(47, 'TD', 'Chad'),
(48, 'CL', 'Chile'),
(49, 'CN', 'China'),
(50, 'CX', 'Christmas Island'),
(51, 'CC', 'Cocos (Keeling) Islands'),
(52, 'CO', 'Colombia'),
(53, 'KM', 'Comoros'),
(54, 'CG', 'Congo - Brazzaville'),
(55, 'CD', 'Congo - Kinshasa'),
(56, 'CK', 'Cook Islands'),
(57, 'CR', 'Costa Rica'),
(58, 'CI', 'Côte d’Ivoire'),
(59, 'HR', 'Croatia'),
(60, 'CU', 'Cuba'),
(61, 'CW', 'Curaçao'),
(62, 'CY', 'Cyprus'),
(63, 'CZ', 'Czechia'),
(64, 'DK', 'Denmark'),
(65, 'DG', 'Diego Garcia'),
(66, 'DJ', 'Djibouti'),
(67, 'DM', 'Dominica'),
(68, 'DO', 'Dominican Republic'),
(69, 'EC', 'Ecuador'),
(70, 'EG', 'Egypt'),
(71, 'SV', 'El Salvador'),
(72, 'GQ', 'Equatorial Guinea'),
(73, 'ER', 'Eritrea'),
(74, 'EE', 'Estonia'),
(75, 'ET', 'Ethiopia'),
(76, 'EZ', 'Eurozone'),
(77, 'FK', 'Falkland Islands'),
(78, 'FO', 'Faroe Islands'),
(79, 'FJ', 'Fiji'),
(80, 'FI', 'Finland'),
(81, 'FR', 'France'),
(82, 'GF', 'French Guiana'),
(83, 'PF', 'French Polynesia'),
(84, 'TF', 'French Southern Territories'),
(85, 'GA', 'Gabon'),
(86, 'GM', 'Gambia'),
(87, 'GE', 'Georgia'),
(88, 'DE', 'Germany'),
(89, 'GH', 'Ghana'),
(90, 'GI', 'Gibraltar'),
(91, 'GR', 'Greece'),
(92, 'GL', 'Greenland'),
(93, 'GD', 'Grenada'),
(94, 'GP', 'Guadeloupe'),
(95, 'GU', 'Guam'),
(96, 'GT', 'Guatemala'),
(97, 'GG', 'Guernsey'),
(98, 'GN', 'Guinea'),
(99, 'GW', 'Guinea-Bissau'),
(100, 'GY', 'Guyana'),
(101, 'HT', 'Haiti'),
(102, 'HN', 'Honduras'),
(103, 'HK', 'Hong Kong SAR China'),
(104, 'HU', 'Hungary'),
(105, 'IS', 'Iceland'),
(106, 'IN', 'India'),
(107, 'ID', 'Indonesia'),
(108, 'IR', 'Iran'),
(109, 'IQ', 'Iraq'),
(110, 'IE', 'Ireland'),
(111, 'IM', 'Isle of Man'),
(112, 'IL', 'Israel'),
(113, 'IT', 'Italy'),
(114, 'JM', 'Jamaica'),
(115, 'JP', 'Japan'),
(116, 'JE', 'Jersey'),
(117, 'JO', 'Jordan'),
(118, 'KZ', 'Kazakhstan'),
(119, 'KE', 'Kenya'),
(120, 'KI', 'Kiribati'),
(121, 'XK', 'Kosovo'),
(122, 'KW', 'Kuwait'),
(123, 'KG', 'Kyrgyzstan'),
(124, 'LA', 'Laos'),
(125, 'LV', 'Latvia'),
(126, 'LB', 'Lebanon'),
(127, 'LS', 'Lesotho'),
(128, 'LR', 'Liberia'),
(129, 'LY', 'Libya'),
(130, 'LI', 'Liechtenstein'),
(131, 'LT', 'Lithuania'),
(132, 'LU', 'Luxembourg'),
(133, 'MO', 'Macau SAR China'),
(134, 'MK', 'Macedonia'),
(135, 'MG', 'Madagascar'),
(136, 'MW', 'Malawi'),
(137, 'MY', 'Malaysia'),
(138, 'MV', 'Maldives'),
(139, 'ML', 'Mali'),
(140, 'MT', 'Malta'),
(141, 'MH', 'Marshall Islands'),
(142, 'MQ', 'Martinique'),
(143, 'MR', 'Mauritania'),
(144, 'MU', 'Mauritius'),
(145, 'YT', 'Mayotte'),
(146, 'MX', 'Mexico'),
(147, 'FM', 'Micronesia'),
(148, 'MD', 'Moldova'),
(149, 'MC', 'Monaco'),
(150, 'MN', 'Mongolia'),
(151, 'ME', 'Montenegro'),
(152, 'MS', 'Montserrat'),
(153, 'MA', 'Morocco'),
(154, 'MZ', 'Mozambique'),
(155, 'MM', 'Myanmar (Burma)'),
(156, 'NA', 'Namibia'),
(157, 'NR', 'Nauru'),
(158, 'NP', 'Nepal'),
(159, 'NL', 'Netherlands'),
(160, 'NC', 'New Caledonia'),
(161, 'NZ', 'New Zealand'),
(162, 'NI', 'Nicaragua'),
(163, 'NE', 'Niger'),
(164, 'NG', 'Nigeria'),
(165, 'NU', 'Niue'),
(166, 'NF', 'Norfolk Island'),
(167, 'KP', 'North Korea'),
(168, 'MP', 'Northern Mariana Islands'),
(169, 'NO', 'Norway'),
(170, 'OM', 'Oman'),
(171, 'PK', 'Pakistan'),
(172, 'PW', 'Palau'),
(173, 'PS', 'Palestinian Territories'),
(174, 'PA', 'Panama'),
(175, 'PG', 'Papua New Guinea'),
(176, 'PY', 'Paraguay'),
(177, 'PE', 'Peru'),
(178, 'PH', 'Philippines'),
(179, 'PN', 'Pitcairn Islands'),
(180, 'PL', 'Poland'),
(181, 'PT', 'Portugal'),
(182, 'PR', 'Puerto Rico'),
(183, 'QA', 'Qatar'),
(184, 'RE', 'Réunion'),
(185, 'RO', 'Romania'),
(186, 'RU', 'Russia'),
(187, 'RW', 'Rwanda'),
(188, 'WS', 'Samoa'),
(189, 'SM', 'San Marino'),
(190, 'ST', 'São Tomé & Príncipe'),
(191, 'SA', 'Saudi Arabia'),
(192, 'SN', 'Senegal'),
(193, 'RS', 'Serbia'),
(194, 'SC', 'Seychelles'),
(195, 'SL', 'Sierra Leone'),
(196, 'SG', 'Singapore'),
(197, 'SX', 'Sint Maarten'),
(198, 'SK', 'Slovakia'),
(199, 'SI', 'Slovenia'),
(200, 'SB', 'Solomon Islands'),
(201, 'SO', 'Somalia'),
(202, 'ZA', 'South Africa'),
(203, 'GS', 'South Georgia & South Sandwich Islands'),
(204, 'KR', 'South Korea'),
(205, 'SS', 'South Sudan'),
(206, 'ES', 'Spain'),
(207, 'LK', 'Sri Lanka'),
(208, 'BL', 'St. Barthélemy'),
(209, 'SH', 'St. Helena'),
(210, 'KN', 'St. Kitts & Nevis'),
(211, 'LC', 'St. Lucia'),
(212, 'MF', 'St. Martin'),
(213, 'PM', 'St. Pierre & Miquelon'),
(214, 'VC', 'St. Vincent & Grenadines'),
(215, 'SD', 'Sudan'),
(216, 'SR', 'Suriname'),
(217, 'SJ', 'Svalbard & Jan Mayen'),
(218, 'SZ', 'Swaziland'),
(219, 'SE', 'Sweden'),
(220, 'CH', 'Switzerland'),
(221, 'SY', 'Syria'),
(222, 'TW', 'Taiwan'),
(223, 'TJ', 'Tajikistan'),
(224, 'TZ', 'Tanzania'),
(225, 'TH', 'Thailand'),
(226, 'TL', 'Timor-Leste'),
(227, 'TG', 'Togo'),
(228, 'TK', 'Tokelau'),
(229, 'TO', 'Tonga'),
(230, 'TT', 'Trinidad & Tobago'),
(231, 'TA', 'Tristan da Cunha'),
(232, 'TN', 'Tunisia'),
(233, 'TR', 'Turkey'),
(234, 'TM', 'Turkmenistan'),
(235, 'TC', 'Turks & Caicos Islands'),
(236, 'TV', 'Tuvalu'),
(237, 'UM', 'U.S. Outlying Islands'),
(238, 'VI', 'U.S. Virgin Islands'),
(239, 'UG', 'Uganda'),
(240, 'UA', 'Ukraine'),
(241, 'AE', 'United Arab Emirates'),
(242, 'GB', 'United Kingdom'),
(243, 'UN', 'United Nations'),
(244, 'US', 'United States'),
(245, 'UY', 'Uruguay'),
(246, 'UZ', 'Uzbekistan'),
(247, 'VU', 'Vanuatu'),
(248, 'VA', 'Vatican City'),
(249, 'VE', 'Venezuela'),
(250, 'VN', 'Vietnam'),
(251, 'WF', 'Wallis & Futuna'),
(252, 'EH', 'Western Sahara'),
(253, 'YE', 'Yemen'),
(254, 'ZM', 'Zambia'),
(255, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `country_states`
--

CREATE TABLE `country_states` (
  `id` int(10) UNSIGNED NOT NULL,
  `country_code` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `country_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `country_states`
--

INSERT INTO `country_states` (`id`, `country_code`, `code`, `name`, `country_id`) VALUES
(1, 'US', 'AL', 'Alabama', 244),
(2, 'US', 'AK', 'Alaska', 244),
(3, 'US', 'AS', 'American Samoa', 244),
(4, 'US', 'AZ', 'Arizona', 244),
(5, 'US', 'AR', 'Arkansas', 244),
(6, 'US', 'AE', 'Armed Forces Africa', 244),
(7, 'US', 'AA', 'Armed Forces Americas', 244),
(8, 'US', 'AE', 'Armed Forces Canada', 244),
(9, 'US', 'AE', 'Armed Forces Europe', 244),
(10, 'US', 'AE', 'Armed Forces Middle East', 244),
(11, 'US', 'AP', 'Armed Forces Pacific', 244),
(12, 'US', 'CA', 'California', 244),
(13, 'US', 'CO', 'Colorado', 244),
(14, 'US', 'CT', 'Connecticut', 244),
(15, 'US', 'DE', 'Delaware', 244),
(16, 'US', 'DC', 'District of Columbia', 244),
(17, 'US', 'FM', 'Federated States Of Micronesia', 244),
(18, 'US', 'FL', 'Florida', 244),
(19, 'US', 'GA', 'Georgia', 244),
(20, 'US', 'GU', 'Guam', 244),
(21, 'US', 'HI', 'Hawaii', 244),
(22, 'US', 'ID', 'Idaho', 244),
(23, 'US', 'IL', 'Illinois', 244),
(24, 'US', 'IN', 'Indiana', 244),
(25, 'US', 'IA', 'Iowa', 244),
(26, 'US', 'KS', 'Kansas', 244),
(27, 'US', 'KY', 'Kentucky', 244),
(28, 'US', 'LA', 'Louisiana', 244),
(29, 'US', 'ME', 'Maine', 244),
(30, 'US', 'MH', 'Marshall Islands', 244),
(31, 'US', 'MD', 'Maryland', 244),
(32, 'US', 'MA', 'Massachusetts', 244),
(33, 'US', 'MI', 'Michigan', 244),
(34, 'US', 'MN', 'Minnesota', 244),
(35, 'US', 'MS', 'Mississippi', 244),
(36, 'US', 'MO', 'Missouri', 244),
(37, 'US', 'MT', 'Montana', 244),
(38, 'US', 'NE', 'Nebraska', 244),
(39, 'US', 'NV', 'Nevada', 244),
(40, 'US', 'NH', 'New Hampshire', 244),
(41, 'US', 'NJ', 'New Jersey', 244),
(42, 'US', 'NM', 'New Mexico', 244),
(43, 'US', 'NY', 'New York', 244),
(44, 'US', 'NC', 'North Carolina', 244),
(45, 'US', 'ND', 'North Dakota', 244),
(46, 'US', 'MP', 'Northern Mariana Islands', 244),
(47, 'US', 'OH', 'Ohio', 244),
(48, 'US', 'OK', 'Oklahoma', 244),
(49, 'US', 'OR', 'Oregon', 244),
(50, 'US', 'PW', 'Palau', 244),
(51, 'US', 'PA', 'Pennsylvania', 244),
(52, 'US', 'PR', 'Puerto Rico', 244),
(53, 'US', 'RI', 'Rhode Island', 244),
(54, 'US', 'SC', 'South Carolina', 244),
(55, 'US', 'SD', 'South Dakota', 244),
(56, 'US', 'TN', 'Tennessee', 244),
(57, 'US', 'TX', 'Texas', 244),
(58, 'US', 'UT', 'Utah', 244),
(59, 'US', 'VT', 'Vermont', 244),
(60, 'US', 'VI', 'Virgin Islands', 244),
(61, 'US', 'VA', 'Virginia', 244),
(62, 'US', 'WA', 'Washington', 244),
(63, 'US', 'WV', 'West Virginia', 244),
(64, 'US', 'WI', 'Wisconsin', 244),
(65, 'US', 'WY', 'Wyoming', 244),
(66, 'CA', 'AB', 'Alberta', 40),
(67, 'CA', 'BC', 'British Columbia', 40),
(68, 'CA', 'MB', 'Manitoba', 40),
(69, 'CA', 'NL', 'Newfoundland and Labrador', 40),
(70, 'CA', 'NB', 'New Brunswick', 40),
(71, 'CA', 'NS', 'Nova Scotia', 40),
(72, 'CA', 'NT', 'Northwest Territories', 40),
(73, 'CA', 'NU', 'Nunavut', 40),
(74, 'CA', 'ON', 'Ontario', 40),
(75, 'CA', 'PE', 'Prince Edward Island', 40),
(76, 'CA', 'QC', 'Quebec', 40),
(77, 'CA', 'SK', 'Saskatchewan', 40),
(78, 'CA', 'YT', 'Yukon Territory', 40),
(79, 'DE', 'NDS', 'Niedersachsen', 88),
(80, 'DE', 'BAW', 'Baden-Württemberg', 88),
(81, 'DE', 'BAY', 'Bayern', 88),
(82, 'DE', 'BER', 'Berlin', 88),
(83, 'DE', 'BRG', 'Brandenburg', 88),
(84, 'DE', 'BRE', 'Bremen', 88),
(85, 'DE', 'HAM', 'Hamburg', 88),
(86, 'DE', 'HES', 'Hessen', 88),
(87, 'DE', 'MEC', 'Mecklenburg-Vorpommern', 88),
(88, 'DE', 'NRW', 'Nordrhein-Westfalen', 88),
(89, 'DE', 'RHE', 'Rheinland-Pfalz', 88),
(90, 'DE', 'SAR', 'Saarland', 88),
(91, 'DE', 'SAS', 'Sachsen', 88),
(92, 'DE', 'SAC', 'Sachsen-Anhalt', 88),
(93, 'DE', 'SCN', 'Schleswig-Holstein', 88),
(94, 'DE', 'THE', 'Thüringen', 88),
(95, 'AT', 'WI', 'Wien', 16),
(96, 'AT', 'NO', 'Niederösterreich', 16),
(97, 'AT', 'OO', 'Oberösterreich', 16),
(98, 'AT', 'SB', 'Salzburg', 16),
(99, 'AT', 'KN', 'Kärnten', 16),
(100, 'AT', 'ST', 'Steiermark', 16),
(101, 'AT', 'TI', 'Tirol', 16),
(102, 'AT', 'BL', 'Burgenland', 16),
(103, 'AT', 'VB', 'Vorarlberg', 16),
(104, 'CH', 'AG', 'Aargau', 220),
(105, 'CH', 'AI', 'Appenzell Innerrhoden', 220),
(106, 'CH', 'AR', 'Appenzell Ausserrhoden', 220),
(107, 'CH', 'BE', 'Bern', 220),
(108, 'CH', 'BL', 'Basel-Landschaft', 220),
(109, 'CH', 'BS', 'Basel-Stadt', 220),
(110, 'CH', 'FR', 'Freiburg', 220),
(111, 'CH', 'GE', 'Genf', 220),
(112, 'CH', 'GL', 'Glarus', 220),
(113, 'CH', 'GR', 'Graubünden', 220),
(114, 'CH', 'JU', 'Jura', 220),
(115, 'CH', 'LU', 'Luzern', 220),
(116, 'CH', 'NE', 'Neuenburg', 220),
(117, 'CH', 'NW', 'Nidwalden', 220),
(118, 'CH', 'OW', 'Obwalden', 220),
(119, 'CH', 'SG', 'St. Gallen', 220),
(120, 'CH', 'SH', 'Schaffhausen', 220),
(121, 'CH', 'SO', 'Solothurn', 220),
(122, 'CH', 'SZ', 'Schwyz', 220),
(123, 'CH', 'TG', 'Thurgau', 220),
(124, 'CH', 'TI', 'Tessin', 220),
(125, 'CH', 'UR', 'Uri', 220),
(126, 'CH', 'VD', 'Waadt', 220),
(127, 'CH', 'VS', 'Wallis', 220),
(128, 'CH', 'ZG', 'Zug', 220),
(129, 'CH', 'ZH', 'Zürich', 220),
(130, 'ES', 'A Coruсa', 'A Coruña', 206),
(131, 'ES', 'Alava', 'Alava', 206),
(132, 'ES', 'Albacete', 'Albacete', 206),
(133, 'ES', 'Alicante', 'Alicante', 206),
(134, 'ES', 'Almeria', 'Almeria', 206),
(135, 'ES', 'Asturias', 'Asturias', 206),
(136, 'ES', 'Avila', 'Avila', 206),
(137, 'ES', 'Badajoz', 'Badajoz', 206),
(138, 'ES', 'Baleares', 'Baleares', 206),
(139, 'ES', 'Barcelona', 'Barcelona', 206),
(140, 'ES', 'Burgos', 'Burgos', 206),
(141, 'ES', 'Caceres', 'Caceres', 206),
(142, 'ES', 'Cadiz', 'Cadiz', 206),
(143, 'ES', 'Cantabria', 'Cantabria', 206),
(144, 'ES', 'Castellon', 'Castellon', 206),
(145, 'ES', 'Ceuta', 'Ceuta', 206),
(146, 'ES', 'Ciudad Real', 'Ciudad Real', 206),
(147, 'ES', 'Cordoba', 'Cordoba', 206),
(148, 'ES', 'Cuenca', 'Cuenca', 206),
(149, 'ES', 'Girona', 'Girona', 206),
(150, 'ES', 'Granada', 'Granada', 206),
(151, 'ES', 'Guadalajara', 'Guadalajara', 206),
(152, 'ES', 'Guipuzcoa', 'Guipuzcoa', 206),
(153, 'ES', 'Huelva', 'Huelva', 206),
(154, 'ES', 'Huesca', 'Huesca', 206),
(155, 'ES', 'Jaen', 'Jaen', 206),
(156, 'ES', 'La Rioja', 'La Rioja', 206),
(157, 'ES', 'Las Palmas', 'Las Palmas', 206),
(158, 'ES', 'Leon', 'Leon', 206),
(159, 'ES', 'Lleida', 'Lleida', 206),
(160, 'ES', 'Lugo', 'Lugo', 206),
(161, 'ES', 'Madrid', 'Madrid', 206),
(162, 'ES', 'Malaga', 'Malaga', 206),
(163, 'ES', 'Melilla', 'Melilla', 206),
(164, 'ES', 'Murcia', 'Murcia', 206),
(165, 'ES', 'Navarra', 'Navarra', 206),
(166, 'ES', 'Ourense', 'Ourense', 206),
(167, 'ES', 'Palencia', 'Palencia', 206),
(168, 'ES', 'Pontevedra', 'Pontevedra', 206),
(169, 'ES', 'Salamanca', 'Salamanca', 206),
(170, 'ES', 'Santa Cruz de Tenerife', 'Santa Cruz de Tenerife', 206),
(171, 'ES', 'Segovia', 'Segovia', 206),
(172, 'ES', 'Sevilla', 'Sevilla', 206),
(173, 'ES', 'Soria', 'Soria', 206),
(174, 'ES', 'Tarragona', 'Tarragona', 206),
(175, 'ES', 'Teruel', 'Teruel', 206),
(176, 'ES', 'Toledo', 'Toledo', 206),
(177, 'ES', 'Valencia', 'Valencia', 206),
(178, 'ES', 'Valladolid', 'Valladolid', 206),
(179, 'ES', 'Vizcaya', 'Vizcaya', 206),
(180, 'ES', 'Zamora', 'Zamora', 206),
(181, 'ES', 'Zaragoza', 'Zaragoza', 206),
(182, 'FR', '1', 'Ain', 81),
(183, 'FR', '2', 'Aisne', 81),
(184, 'FR', '3', 'Allier', 81),
(185, 'FR', '4', 'Alpes-de-Haute-Provence', 81),
(186, 'FR', '5', 'Hautes-Alpes', 81),
(187, 'FR', '6', 'Alpes-Maritimes', 81),
(188, 'FR', '7', 'Ardèche', 81),
(189, 'FR', '8', 'Ardennes', 81),
(190, 'FR', '9', 'Ariège', 81),
(191, 'FR', '10', 'Aube', 81),
(192, 'FR', '11', 'Aude', 81),
(193, 'FR', '12', 'Aveyron', 81),
(194, 'FR', '13', 'Bouches-du-Rhône', 81),
(195, 'FR', '14', 'Calvados', 81),
(196, 'FR', '15', 'Cantal', 81),
(197, 'FR', '16', 'Charente', 81),
(198, 'FR', '17', 'Charente-Maritime', 81),
(199, 'FR', '18', 'Cher', 81),
(200, 'FR', '19', 'Corrèze', 81),
(201, 'FR', '2A', 'Corse-du-Sud', 81),
(202, 'FR', '2B', 'Haute-Corse', 81),
(203, 'FR', '21', 'Côte-d\'Or', 81),
(204, 'FR', '22', 'Côtes-d\'Armor', 81),
(205, 'FR', '23', 'Creuse', 81),
(206, 'FR', '24', 'Dordogne', 81),
(207, 'FR', '25', 'Doubs', 81),
(208, 'FR', '26', 'Drôme', 81),
(209, 'FR', '27', 'Eure', 81),
(210, 'FR', '28', 'Eure-et-Loir', 81),
(211, 'FR', '29', 'Finistère', 81),
(212, 'FR', '30', 'Gard', 81),
(213, 'FR', '31', 'Haute-Garonne', 81),
(214, 'FR', '32', 'Gers', 81),
(215, 'FR', '33', 'Gironde', 81),
(216, 'FR', '34', 'Hérault', 81),
(217, 'FR', '35', 'Ille-et-Vilaine', 81),
(218, 'FR', '36', 'Indre', 81),
(219, 'FR', '37', 'Indre-et-Loire', 81),
(220, 'FR', '38', 'Isère', 81),
(221, 'FR', '39', 'Jura', 81),
(222, 'FR', '40', 'Landes', 81),
(223, 'FR', '41', 'Loir-et-Cher', 81),
(224, 'FR', '42', 'Loire', 81),
(225, 'FR', '43', 'Haute-Loire', 81),
(226, 'FR', '44', 'Loire-Atlantique', 81),
(227, 'FR', '45', 'Loiret', 81),
(228, 'FR', '46', 'Lot', 81),
(229, 'FR', '47', 'Lot-et-Garonne', 81),
(230, 'FR', '48', 'Lozère', 81),
(231, 'FR', '49', 'Maine-et-Loire', 81),
(232, 'FR', '50', 'Manche', 81),
(233, 'FR', '51', 'Marne', 81),
(234, 'FR', '52', 'Haute-Marne', 81),
(235, 'FR', '53', 'Mayenne', 81),
(236, 'FR', '54', 'Meurthe-et-Moselle', 81),
(237, 'FR', '55', 'Meuse', 81),
(238, 'FR', '56', 'Morbihan', 81),
(239, 'FR', '57', 'Moselle', 81),
(240, 'FR', '58', 'Nièvre', 81),
(241, 'FR', '59', 'Nord', 81),
(242, 'FR', '60', 'Oise', 81),
(243, 'FR', '61', 'Orne', 81),
(244, 'FR', '62', 'Pas-de-Calais', 81),
(245, 'FR', '63', 'Puy-de-Dôme', 81),
(246, 'FR', '64', 'Pyrénées-Atlantiques', 81),
(247, 'FR', '65', 'Hautes-Pyrénées', 81),
(248, 'FR', '66', 'Pyrénées-Orientales', 81),
(249, 'FR', '67', 'Bas-Rhin', 81),
(250, 'FR', '68', 'Haut-Rhin', 81),
(251, 'FR', '69', 'Rhône', 81),
(252, 'FR', '70', 'Haute-Saône', 81),
(253, 'FR', '71', 'Saône-et-Loire', 81),
(254, 'FR', '72', 'Sarthe', 81),
(255, 'FR', '73', 'Savoie', 81),
(256, 'FR', '74', 'Haute-Savoie', 81),
(257, 'FR', '75', 'Paris', 81),
(258, 'FR', '76', 'Seine-Maritime', 81),
(259, 'FR', '77', 'Seine-et-Marne', 81),
(260, 'FR', '78', 'Yvelines', 81),
(261, 'FR', '79', 'Deux-Sèvres', 81),
(262, 'FR', '80', 'Somme', 81),
(263, 'FR', '81', 'Tarn', 81),
(264, 'FR', '82', 'Tarn-et-Garonne', 81),
(265, 'FR', '83', 'Var', 81),
(266, 'FR', '84', 'Vaucluse', 81),
(267, 'FR', '85', 'Vendée', 81),
(268, 'FR', '86', 'Vienne', 81),
(269, 'FR', '87', 'Haute-Vienne', 81),
(270, 'FR', '88', 'Vosges', 81),
(271, 'FR', '89', 'Yonne', 81),
(272, 'FR', '90', 'Territoire-de-Belfort', 81),
(273, 'FR', '91', 'Essonne', 81),
(274, 'FR', '92', 'Hauts-de-Seine', 81),
(275, 'FR', '93', 'Seine-Saint-Denis', 81),
(276, 'FR', '94', 'Val-de-Marne', 81),
(277, 'FR', '95', 'Val-d\'Oise', 81),
(278, 'RO', 'AB', 'Alba', 185),
(279, 'RO', 'AR', 'Arad', 185),
(280, 'RO', 'AG', 'Argeş', 185),
(281, 'RO', 'BC', 'Bacău', 185),
(282, 'RO', 'BH', 'Bihor', 185),
(283, 'RO', 'BN', 'Bistriţa-Năsăud', 185),
(284, 'RO', 'BT', 'Botoşani', 185),
(285, 'RO', 'BV', 'Braşov', 185),
(286, 'RO', 'BR', 'Brăila', 185),
(287, 'RO', 'B', 'Bucureşti', 185),
(288, 'RO', 'BZ', 'Buzău', 185),
(289, 'RO', 'CS', 'Caraş-Severin', 185),
(290, 'RO', 'CL', 'Călăraşi', 185),
(291, 'RO', 'CJ', 'Cluj', 185),
(292, 'RO', 'CT', 'Constanţa', 185),
(293, 'RO', 'CV', 'Covasna', 185),
(294, 'RO', 'DB', 'Dâmboviţa', 185),
(295, 'RO', 'DJ', 'Dolj', 185),
(296, 'RO', 'GL', 'Galaţi', 185),
(297, 'RO', 'GR', 'Giurgiu', 185),
(298, 'RO', 'GJ', 'Gorj', 185),
(299, 'RO', 'HR', 'Harghita', 185),
(300, 'RO', 'HD', 'Hunedoara', 185),
(301, 'RO', 'IL', 'Ialomiţa', 185),
(302, 'RO', 'IS', 'Iaşi', 185),
(303, 'RO', 'IF', 'Ilfov', 185),
(304, 'RO', 'MM', 'Maramureş', 185),
(305, 'RO', 'MH', 'Mehedinţi', 185),
(306, 'RO', 'MS', 'Mureş', 185),
(307, 'RO', 'NT', 'Neamţ', 185),
(308, 'RO', 'OT', 'Olt', 185),
(309, 'RO', 'PH', 'Prahova', 185),
(310, 'RO', 'SM', 'Satu-Mare', 185),
(311, 'RO', 'SJ', 'Sălaj', 185),
(312, 'RO', 'SB', 'Sibiu', 185),
(313, 'RO', 'SV', 'Suceava', 185),
(314, 'RO', 'TR', 'Teleorman', 185),
(315, 'RO', 'TM', 'Timiş', 185),
(316, 'RO', 'TL', 'Tulcea', 185),
(317, 'RO', 'VS', 'Vaslui', 185),
(318, 'RO', 'VL', 'Vâlcea', 185),
(319, 'RO', 'VN', 'Vrancea', 185),
(320, 'FI', 'Lappi', 'Lappi', 80),
(321, 'FI', 'Pohjois-Pohjanmaa', 'Pohjois-Pohjanmaa', 80),
(322, 'FI', 'Kainuu', 'Kainuu', 80),
(323, 'FI', 'Pohjois-Karjala', 'Pohjois-Karjala', 80),
(324, 'FI', 'Pohjois-Savo', 'Pohjois-Savo', 80),
(325, 'FI', 'Etelä-Savo', 'Etelä-Savo', 80),
(326, 'FI', 'Etelä-Pohjanmaa', 'Etelä-Pohjanmaa', 80),
(327, 'FI', 'Pohjanmaa', 'Pohjanmaa', 80),
(328, 'FI', 'Pirkanmaa', 'Pirkanmaa', 80),
(329, 'FI', 'Satakunta', 'Satakunta', 80),
(330, 'FI', 'Keski-Pohjanmaa', 'Keski-Pohjanmaa', 80),
(331, 'FI', 'Keski-Suomi', 'Keski-Suomi', 80),
(332, 'FI', 'Varsinais-Suomi', 'Varsinais-Suomi', 80),
(333, 'FI', 'Etelä-Karjala', 'Etelä-Karjala', 80),
(334, 'FI', 'Päijät-Häme', 'Päijät-Häme', 80),
(335, 'FI', 'Kanta-Häme', 'Kanta-Häme', 80),
(336, 'FI', 'Uusimaa', 'Uusimaa', 80),
(337, 'FI', 'Itä-Uusimaa', 'Itä-Uusimaa', 80),
(338, 'FI', 'Kymenlaakso', 'Kymenlaakso', 80),
(339, 'FI', 'Ahvenanmaa', 'Ahvenanmaa', 80),
(340, 'EE', 'EE-37', 'Harjumaa', 74),
(341, 'EE', 'EE-39', 'Hiiumaa', 74),
(342, 'EE', 'EE-44', 'Ida-Virumaa', 74),
(343, 'EE', 'EE-49', 'Jõgevamaa', 74),
(344, 'EE', 'EE-51', 'Järvamaa', 74),
(345, 'EE', 'EE-57', 'Läänemaa', 74),
(346, 'EE', 'EE-59', 'Lääne-Virumaa', 74),
(347, 'EE', 'EE-65', 'Põlvamaa', 74),
(348, 'EE', 'EE-67', 'Pärnumaa', 74),
(349, 'EE', 'EE-70', 'Raplamaa', 74),
(350, 'EE', 'EE-74', 'Saaremaa', 74),
(351, 'EE', 'EE-78', 'Tartumaa', 74),
(352, 'EE', 'EE-82', 'Valgamaa', 74),
(353, 'EE', 'EE-84', 'Viljandimaa', 74),
(354, 'EE', 'EE-86', 'Võrumaa', 74),
(355, 'LV', 'LV-DGV', 'Daugavpils', 125),
(356, 'LV', 'LV-JEL', 'Jelgava', 125),
(357, 'LV', 'Jēkabpils', 'Jēkabpils', 125),
(358, 'LV', 'LV-JUR', 'Jūrmala', 125),
(359, 'LV', 'LV-LPX', 'Liepāja', 125),
(360, 'LV', 'LV-LE', 'Liepājas novads', 125),
(361, 'LV', 'LV-REZ', 'Rēzekne', 125),
(362, 'LV', 'LV-RIX', 'Rīga', 125),
(363, 'LV', 'LV-RI', 'Rīgas novads', 125),
(364, 'LV', 'Valmiera', 'Valmiera', 125),
(365, 'LV', 'LV-VEN', 'Ventspils', 125),
(366, 'LV', 'Aglonas novads', 'Aglonas novads', 125),
(367, 'LV', 'LV-AI', 'Aizkraukles novads', 125),
(368, 'LV', 'Aizputes novads', 'Aizputes novads', 125),
(369, 'LV', 'Aknīstes novads', 'Aknīstes novads', 125),
(370, 'LV', 'Alojas novads', 'Alojas novads', 125),
(371, 'LV', 'Alsungas novads', 'Alsungas novads', 125),
(372, 'LV', 'LV-AL', 'Alūksnes novads', 125),
(373, 'LV', 'Amatas novads', 'Amatas novads', 125),
(374, 'LV', 'Apes novads', 'Apes novads', 125),
(375, 'LV', 'Auces novads', 'Auces novads', 125),
(376, 'LV', 'Babītes novads', 'Babītes novads', 125),
(377, 'LV', 'Baldones novads', 'Baldones novads', 125),
(378, 'LV', 'Baltinavas novads', 'Baltinavas novads', 125),
(379, 'LV', 'LV-BL', 'Balvu novads', 125),
(380, 'LV', 'LV-BU', 'Bauskas novads', 125),
(381, 'LV', 'Beverīnas novads', 'Beverīnas novads', 125),
(382, 'LV', 'Brocēnu novads', 'Brocēnu novads', 125),
(383, 'LV', 'Burtnieku novads', 'Burtnieku novads', 125),
(384, 'LV', 'Carnikavas novads', 'Carnikavas novads', 125),
(385, 'LV', 'Cesvaines novads', 'Cesvaines novads', 125),
(386, 'LV', 'Ciblas novads', 'Ciblas novads', 125),
(387, 'LV', 'LV-CE', 'Cēsu novads', 125),
(388, 'LV', 'Dagdas novads', 'Dagdas novads', 125),
(389, 'LV', 'LV-DA', 'Daugavpils novads', 125),
(390, 'LV', 'LV-DO', 'Dobeles novads', 125),
(391, 'LV', 'Dundagas novads', 'Dundagas novads', 125),
(392, 'LV', 'Durbes novads', 'Durbes novads', 125),
(393, 'LV', 'Engures novads', 'Engures novads', 125),
(394, 'LV', 'Garkalnes novads', 'Garkalnes novads', 125),
(395, 'LV', 'Grobiņas novads', 'Grobiņas novads', 125),
(396, 'LV', 'LV-GU', 'Gulbenes novads', 125),
(397, 'LV', 'Iecavas novads', 'Iecavas novads', 125),
(398, 'LV', 'Ikšķiles novads', 'Ikšķiles novads', 125),
(399, 'LV', 'Ilūkstes novads', 'Ilūkstes novads', 125),
(400, 'LV', 'Inčukalna novads', 'Inčukalna novads', 125),
(401, 'LV', 'Jaunjelgavas novads', 'Jaunjelgavas novads', 125),
(402, 'LV', 'Jaunpiebalgas novads', 'Jaunpiebalgas novads', 125),
(403, 'LV', 'Jaunpils novads', 'Jaunpils novads', 125),
(404, 'LV', 'LV-JL', 'Jelgavas novads', 125),
(405, 'LV', 'LV-JK', 'Jēkabpils novads', 125),
(406, 'LV', 'Kandavas novads', 'Kandavas novads', 125),
(407, 'LV', 'Kokneses novads', 'Kokneses novads', 125),
(408, 'LV', 'Krimuldas novads', 'Krimuldas novads', 125),
(409, 'LV', 'Krustpils novads', 'Krustpils novads', 125),
(410, 'LV', 'LV-KR', 'Krāslavas novads', 125),
(411, 'LV', 'LV-KU', 'Kuldīgas novads', 125),
(412, 'LV', 'Kārsavas novads', 'Kārsavas novads', 125),
(413, 'LV', 'Lielvārdes novads', 'Lielvārdes novads', 125),
(414, 'LV', 'LV-LM', 'Limbažu novads', 125),
(415, 'LV', 'Lubānas novads', 'Lubānas novads', 125),
(416, 'LV', 'LV-LU', 'Ludzas novads', 125),
(417, 'LV', 'Līgatnes novads', 'Līgatnes novads', 125),
(418, 'LV', 'Līvānu novads', 'Līvānu novads', 125),
(419, 'LV', 'LV-MA', 'Madonas novads', 125),
(420, 'LV', 'Mazsalacas novads', 'Mazsalacas novads', 125),
(421, 'LV', 'Mālpils novads', 'Mālpils novads', 125),
(422, 'LV', 'Mārupes novads', 'Mārupes novads', 125),
(423, 'LV', 'Naukšēnu novads', 'Naukšēnu novads', 125),
(424, 'LV', 'Neretas novads', 'Neretas novads', 125),
(425, 'LV', 'Nīcas novads', 'Nīcas novads', 125),
(426, 'LV', 'LV-OG', 'Ogres novads', 125),
(427, 'LV', 'Olaines novads', 'Olaines novads', 125),
(428, 'LV', 'Ozolnieku novads', 'Ozolnieku novads', 125),
(429, 'LV', 'LV-PR', 'Preiļu novads', 125),
(430, 'LV', 'Priekules novads', 'Priekules novads', 125),
(431, 'LV', 'Priekuļu novads', 'Priekuļu novads', 125),
(432, 'LV', 'Pārgaujas novads', 'Pārgaujas novads', 125),
(433, 'LV', 'Pāvilostas novads', 'Pāvilostas novads', 125),
(434, 'LV', 'Pļaviņu novads', 'Pļaviņu novads', 125),
(435, 'LV', 'Raunas novads', 'Raunas novads', 125),
(436, 'LV', 'Riebiņu novads', 'Riebiņu novads', 125),
(437, 'LV', 'Rojas novads', 'Rojas novads', 125),
(438, 'LV', 'Ropažu novads', 'Ropažu novads', 125),
(439, 'LV', 'Rucavas novads', 'Rucavas novads', 125),
(440, 'LV', 'Rugāju novads', 'Rugāju novads', 125),
(441, 'LV', 'Rundāles novads', 'Rundāles novads', 125),
(442, 'LV', 'LV-RE', 'Rēzeknes novads', 125),
(443, 'LV', 'Rūjienas novads', 'Rūjienas novads', 125),
(444, 'LV', 'Salacgrīvas novads', 'Salacgrīvas novads', 125),
(445, 'LV', 'Salas novads', 'Salas novads', 125),
(446, 'LV', 'Salaspils novads', 'Salaspils novads', 125),
(447, 'LV', 'LV-SA', 'Saldus novads', 125),
(448, 'LV', 'Saulkrastu novads', 'Saulkrastu novads', 125),
(449, 'LV', 'Siguldas novads', 'Siguldas novads', 125),
(450, 'LV', 'Skrundas novads', 'Skrundas novads', 125),
(451, 'LV', 'Skrīveru novads', 'Skrīveru novads', 125),
(452, 'LV', 'Smiltenes novads', 'Smiltenes novads', 125),
(453, 'LV', 'Stopiņu novads', 'Stopiņu novads', 125),
(454, 'LV', 'Strenču novads', 'Strenču novads', 125),
(455, 'LV', 'Sējas novads', 'Sējas novads', 125),
(456, 'LV', 'LV-TA', 'Talsu novads', 125),
(457, 'LV', 'LV-TU', 'Tukuma novads', 125),
(458, 'LV', 'Tērvetes novads', 'Tērvetes novads', 125),
(459, 'LV', 'Vaiņodes novads', 'Vaiņodes novads', 125),
(460, 'LV', 'LV-VK', 'Valkas novads', 125),
(461, 'LV', 'LV-VM', 'Valmieras novads', 125),
(462, 'LV', 'Varakļānu novads', 'Varakļānu novads', 125),
(463, 'LV', 'Vecpiebalgas novads', 'Vecpiebalgas novads', 125),
(464, 'LV', 'Vecumnieku novads', 'Vecumnieku novads', 125),
(465, 'LV', 'LV-VE', 'Ventspils novads', 125),
(466, 'LV', 'Viesītes novads', 'Viesītes novads', 125),
(467, 'LV', 'Viļakas novads', 'Viļakas novads', 125),
(468, 'LV', 'Viļānu novads', 'Viļānu novads', 125),
(469, 'LV', 'Vārkavas novads', 'Vārkavas novads', 125),
(470, 'LV', 'Zilupes novads', 'Zilupes novads', 125),
(471, 'LV', 'Ādažu novads', 'Ādažu novads', 125),
(472, 'LV', 'Ērgļu novads', 'Ērgļu novads', 125),
(473, 'LV', 'Ķeguma novads', 'Ķeguma novads', 125),
(474, 'LV', 'Ķekavas novads', 'Ķekavas novads', 125),
(475, 'LT', 'LT-AL', 'Alytaus Apskritis', 131),
(476, 'LT', 'LT-KU', 'Kauno Apskritis', 131),
(477, 'LT', 'LT-KL', 'Klaipėdos Apskritis', 131),
(478, 'LT', 'LT-MR', 'Marijampolės Apskritis', 131),
(479, 'LT', 'LT-PN', 'Panevėžio Apskritis', 131),
(480, 'LT', 'LT-SA', 'Šiaulių Apskritis', 131),
(481, 'LT', 'LT-TA', 'Tauragės Apskritis', 131),
(482, 'LT', 'LT-TE', 'Telšių Apskritis', 131),
(483, 'LT', 'LT-UT', 'Utenos Apskritis', 131),
(484, 'LT', 'LT-VL', 'Vilniaus Apskritis', 131),
(485, 'BR', 'AC', 'Acre', 31),
(486, 'BR', 'AL', 'Alagoas', 31),
(487, 'BR', 'AP', 'Amapá', 31),
(488, 'BR', 'AM', 'Amazonas', 31),
(489, 'BR', 'BA', 'Bahia', 31),
(490, 'BR', 'CE', 'Ceará', 31),
(491, 'BR', 'ES', 'Espírito Santo', 31),
(492, 'BR', 'GO', 'Goiás', 31),
(493, 'BR', 'MA', 'Maranhão', 31),
(494, 'BR', 'MT', 'Mato Grosso', 31),
(495, 'BR', 'MS', 'Mato Grosso do Sul', 31),
(496, 'BR', 'MG', 'Minas Gerais', 31),
(497, 'BR', 'PA', 'Pará', 31),
(498, 'BR', 'PB', 'Paraíba', 31),
(499, 'BR', 'PR', 'Paraná', 31),
(500, 'BR', 'PE', 'Pernambuco', 31),
(501, 'BR', 'PI', 'Piauí', 31),
(502, 'BR', 'RJ', 'Rio de Janeiro', 31),
(503, 'BR', 'RN', 'Rio Grande do Norte', 31),
(504, 'BR', 'RS', 'Rio Grande do Sul', 31),
(505, 'BR', 'RO', 'Rondônia', 31),
(506, 'BR', 'RR', 'Roraima', 31),
(507, 'BR', 'SC', 'Santa Catarina', 31),
(508, 'BR', 'SP', 'São Paulo', 31),
(509, 'BR', 'SE', 'Sergipe', 31),
(510, 'BR', 'TO', 'Tocantins', 31),
(511, 'BR', 'DF', 'Distrito Federal', 31),
(512, 'HR', 'HR-01', 'Zagrebačka županija', 59),
(513, 'HR', 'HR-02', 'Krapinsko-zagorska županija', 59),
(514, 'HR', 'HR-03', 'Sisačko-moslavačka županija', 59),
(515, 'HR', 'HR-04', 'Karlovačka županija', 59),
(516, 'HR', 'HR-05', 'Varaždinska županija', 59),
(517, 'HR', 'HR-06', 'Koprivničko-križevačka županija', 59),
(518, 'HR', 'HR-07', 'Bjelovarsko-bilogorska županija', 59),
(519, 'HR', 'HR-08', 'Primorsko-goranska županija', 59),
(520, 'HR', 'HR-09', 'Ličko-senjska županija', 59),
(521, 'HR', 'HR-10', 'Virovitičko-podravska županija', 59),
(522, 'HR', 'HR-11', 'Požeško-slavonska županija', 59),
(523, 'HR', 'HR-12', 'Brodsko-posavska županija', 59),
(524, 'HR', 'HR-13', 'Zadarska županija', 59),
(525, 'HR', 'HR-14', 'Osječko-baranjska županija', 59),
(526, 'HR', 'HR-15', 'Šibensko-kninska županija', 59),
(527, 'HR', 'HR-16', 'Vukovarsko-srijemska županija', 59),
(528, 'HR', 'HR-17', 'Splitsko-dalmatinska županija', 59),
(529, 'HR', 'HR-18', 'Istarska županija', 59),
(530, 'HR', 'HR-19', 'Dubrovačko-neretvanska županija', 59),
(531, 'HR', 'HR-20', 'Međimurska županija', 59),
(532, 'HR', 'HR-21', 'Grad Zagreb', 59),
(533, 'IN', 'AN', 'Andaman and Nicobar Islands', 106),
(534, 'IN', 'AP', 'Andhra Pradesh', 106),
(535, 'IN', 'AR', 'Arunachal Pradesh', 106),
(536, 'IN', 'AS', 'Assam', 106),
(537, 'IN', 'BR', 'Bihar', 106),
(538, 'IN', 'CH', 'Chandigarh', 106),
(539, 'IN', 'CT', 'Chhattisgarh', 106),
(540, 'IN', 'DN', 'Dadra and Nagar Haveli', 106),
(541, 'IN', 'DD', 'Daman and Diu', 106),
(542, 'IN', 'DL', 'Delhi', 106),
(543, 'IN', 'GA', 'Goa', 106),
(544, 'IN', 'GJ', 'Gujarat', 106),
(545, 'IN', 'HR', 'Haryana', 106),
(546, 'IN', 'HP', 'Himachal Pradesh', 106),
(547, 'IN', 'JK', 'Jammu and Kashmir', 106),
(548, 'IN', 'JH', 'Jharkhand', 106),
(549, 'IN', 'KA', 'Karnataka', 106),
(550, 'IN', 'KL', 'Kerala', 106),
(551, 'IN', 'LD', 'Lakshadweep', 106),
(552, 'IN', 'MP', 'Madhya Pradesh', 106),
(553, 'IN', 'MH', 'Maharashtra', 106),
(554, 'IN', 'MN', 'Manipur', 106),
(555, 'IN', 'ML', 'Meghalaya', 106),
(556, 'IN', 'MZ', 'Mizoram', 106),
(557, 'IN', 'NL', 'Nagaland', 106),
(558, 'IN', 'OR', 'Odisha', 106),
(559, 'IN', 'PY', 'Puducherry', 106),
(560, 'IN', 'PB', 'Punjab', 106),
(561, 'IN', 'RJ', 'Rajasthan', 106),
(562, 'IN', 'SK', 'Sikkim', 106),
(563, 'IN', 'TN', 'Tamil Nadu', 106),
(564, 'IN', 'TG', 'Telangana', 106),
(565, 'IN', 'TR', 'Tripura', 106),
(566, 'IN', 'UP', 'Uttar Pradesh', 106),
(567, 'IN', 'UT', 'Uttarakhand', 106),
(568, 'IN', 'WB', 'West Bengal', 106);

-- --------------------------------------------------------

--
-- Table structure for table `datagrid_saved_filters`
--

CREATE TABLE `datagrid_saved_filters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `src` varchar(255) NOT NULL,
  `applied` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`applied`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE `emails` (
  `id` int(10) UNSIGNED NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `source` varchar(255) NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `reply` text DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `folders` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`folders`)),
  `from` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`from`)),
  `sender` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sender`)),
  `reply_to` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`reply_to`)),
  `cc` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cc`)),
  `bcc` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`bcc`)),
  `unique_id` varchar(255) DEFAULT NULL,
  `message_id` varchar(255) NOT NULL,
  `reference_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`reference_ids`)),
  `person_id` int(10) UNSIGNED DEFAULT NULL,
  `lead_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `emails`
--

INSERT INTO `emails` (`id`, `subject`, `source`, `user_type`, `name`, `reply`, `is_read`, `folders`, `from`, `sender`, `reply_to`, `cc`, `bcc`, `unique_id`, `message_id`, `reference_ids`, `person_id`, `lead_id`, `created_at`, `updated_at`, `parent_id`) VALUES
(1, 'fuck off', 'web', 'admin', NULL, '<p>fuck off</p>', 0, '[\"draft\"]', '\"laravel@krayincrm.com\"', NULL, '[\"syedmuaz6198@gmail.com\"]', NULL, NULL, '1771527247@deveoninc.com', '1771527247@deveoninc.com', '[\"1771527247@deveoninc.com\"]', NULL, NULL, '2026-02-19 19:24:07', '2026-02-19 19:24:07', NULL),
(2, 'test', 'web', 'admin', NULL, 'test', 0, '[\"outbox\"]', '\"laravel@krayincrm.com\"', NULL, '[\"finalgamers67@gmail.com\"]', NULL, NULL, '1771700261@deveoninc.com', '1771700261@deveoninc.com', '[\"1771700261@deveoninc.com\"]', NULL, NULL, '2026-02-21 19:27:41', '2026-02-21 19:27:41', NULL),
(3, 'test', 'web', 'admin', NULL, 'tesdt', 0, '[\"outbox\"]', '\"laravel@krayincrm.com\"', NULL, '[\"finalgamers67@gmail.com\"]', NULL, NULL, '1771866804@deveoninc.com', '1771866804@deveoninc.com', '[\"1771866804@deveoninc.com\"]', NULL, 4, '2026-02-23 17:43:24', '2026-02-23 17:43:24', NULL),
(4, 'yeds', 'web', 'admin', NULL, 'dfdf', 0, '[\"outbox\"]', '\"laravel@krayincrm.com\"', NULL, '[\"dfdf@dsff.com\"]', NULL, NULL, '1771869060@deveoninc.com', '1771869060@deveoninc.com', '[\"1771869060@deveoninc.com\"]', NULL, NULL, '2026-02-23 18:21:00', '2026-02-23 18:21:00', NULL),
(5, 'test email', 'web', 'admin', NULL, 'this is a test email. \r\n\r\nregards; \r\nZubair', 0, '[\"outbox\"]', '\"laravel@krayincrm.com\"', NULL, '[\"zubairmaya@gmail.com\"]', NULL, NULL, '1771897891@deveoninc.com', '1771897891@deveoninc.com', '[\"1771897891@deveoninc.com\"]', NULL, 2, '2026-02-24 12:21:31', '2026-02-24 12:21:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_attachments`
--

CREATE TABLE `email_attachments` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `path` varchar(255) NOT NULL,
  `size` int(11) DEFAULT NULL,
  `content_type` varchar(255) DEFAULT NULL,
  `content_id` varchar(255) DEFAULT NULL,
  `email_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_tags`
--

CREATE TABLE `email_tags` (
  `tag_id` int(10) UNSIGNED NOT NULL,
  `email_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `name`, `subject`, `content`, `created_at`, `updated_at`) VALUES
(1, 'Activity created', 'Activity created: {%activities.title%}', '<p style=\"font-size: 16px; color: #5e5e5e;\">You have a new activity, please find the details bellow:</p>\n                                <p><strong style=\"font-size: 16px;\">Details</strong></p>\n                                <table style=\"height: 97px; width: 952px;\">\n                                    <tbody>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px;\">Title</td>\n                                            <td style=\"width: 770.047px; font-size: 16px;\">{%activities.title%}</td>\n                                        </tr>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px;\">Type</td>\n                                                <td style=\"width: 770.047px; font-size: 16px;\">{%activities.type%}</td>\n                                        </tr>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px;\">Date</td>\n                                            <td style=\"width: 770.047px; font-size: 16px;\">{%activities.schedule_from%} to&nbsp;{%activities.schedule_to%}</td>\n                                        </tr>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px; vertical-align: text-top;\">Participants</td>\n                                            <td style=\"width: 770.047px; font-size: 16px;\">{%activities.participants%}</td>\n                                        </tr>\n                                    </tbody>\n                                </table>', '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(2, 'Activity modified', 'Activity modified: {%activities.title%}', '<p style=\"font-size: 16px; color: #5e5e5e;\">You have a new activity modified, please find the details bellow:</p>\n                                <p><strong style=\"font-size: 16px;\">Details</strong></p>\n                                <table style=\"height: 97px; width: 952px;\">\n                                    <tbody>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px;\">Title</td>\n                                            <td style=\"width: 770.047px; font-size: 16px;\">{%activities.title%}</td>\n                                        </tr>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px;\">Type</td>\n                                            <td style=\"width: 770.047px; font-size: 16px;\">{%activities.type%}</td>\n                                        </tr>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px;\">Date</td>\n                                            <td style=\"width: 770.047px; font-size: 16px;\">{%activities.schedule_from%} to&nbsp;{%activities.schedule_to%}</td>\n                                        </tr>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px; vertical-align: text-top;\">Participants</td>\n                                            <td style=\"width: 770.047px; font-size: 16px;\">{%activities.participants%}</td>\n                                        </tr>\n                                    </tbody>\n                                </table>', '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(3, 'test', 'This is a test Subject', '<p>This is a test description</p>', '2025-11-15 14:43:49', '2025-11-15 14:43:49');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `imports`
--

CREATE TABLE `imports` (
  `id` int(10) UNSIGNED NOT NULL,
  `state` varchar(255) NOT NULL DEFAULT 'pending',
  `process_in_queue` tinyint(1) NOT NULL DEFAULT 1,
  `type` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `validation_strategy` varchar(255) NOT NULL,
  `allowed_errors` int(11) NOT NULL DEFAULT 0,
  `processed_rows_count` int(11) NOT NULL DEFAULT 0,
  `invalid_rows_count` int(11) NOT NULL DEFAULT 0,
  `errors_count` int(11) NOT NULL DEFAULT 0,
  `errors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`errors`)),
  `field_separator` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `error_file_path` varchar(255) DEFAULT NULL,
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`summary`)),
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `import_batches`
--

CREATE TABLE `import_batches` (
  `id` int(10) UNSIGNED NOT NULL,
  `state` varchar(255) NOT NULL DEFAULT 'pending',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`summary`)),
  `import_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` text NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` int(10) UNSIGNED NOT NULL,
  `case_no` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `lead_value` decimal(12,4) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `lost_reason` text DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `person_id` int(10) UNSIGNED DEFAULT NULL,
  `organization_id` int(10) UNSIGNED DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `lead_source_id` int(10) UNSIGNED DEFAULT NULL,
  `lead_type_id` int(10) UNSIGNED DEFAULT NULL,
  `lead_pipeline_id` int(10) UNSIGNED DEFAULT NULL,
  `lead_pipeline_stage_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expected_close_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `case_no`, `title`, `description`, `lead_value`, `status`, `lost_reason`, `closed_at`, `user_id`, `person_id`, `organization_id`, `priority`, `lead_source_id`, `lead_type_id`, `lead_pipeline_id`, `lead_pipeline_stage_id`, `created_at`, `updated_at`, `expected_close_date`) VALUES
(1, '00001', 'Accusamus atque earu', 'Necessitatibus reici', 23.0000, 1, NULL, NULL, 1, 1, NULL, 'medium', 2, 2, 1, 2, '2025-11-15 14:36:48', '2026-02-21 18:24:44', NULL),
(2, '00002', 'dsf', '', 45.0000, 1, NULL, NULL, 1, 1, NULL, 'medium', 1, 1, 1, 2, '2026-02-16 19:17:55', '2026-02-24 12:18:23', NULL),
(3, '00003', 'Occaecat quas qui al', 'Amet minima ut sed', 78.0000, 1, NULL, NULL, 1, 1, NULL, 'medium', 5, 2, 1, 3, '2026-02-21 15:37:49', '2026-02-23 20:28:26', NULL),
(4, '00004', '\"Quis dicta id facili\"\"Quis dicta id facili\"\"Quis dicta id facili\"\"Quis dicta id facili\"\"Quis dicta id facili\"\"Quis dicta id facili\"\"Quis dicta id facili\"\"Quis dicta id facili\"\"Quis dicta id facili\"\"Quis dicta id facili\"\"Quis dicta id facili\"\"Quis dicta i', 'Vero eum rerum bland', 6.0000, 1, NULL, NULL, 1, 1, 2, 'medium', 5, 1, 1, 3, '2026-02-21 18:16:51', '2026-02-26 13:39:02', NULL),
(5, '00012', '\"gfdgfgfdgfgfd', 'gfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgfgfdgf', NULL, 1, NULL, NULL, 1, 1, 2, 'low', 3, NULL, 1, 2, '2026-02-23 20:11:53', '2026-02-26 12:06:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_activities`
--

CREATE TABLE `lead_activities` (
  `activity_id` int(10) UNSIGNED NOT NULL,
  `lead_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_activities`
--

INSERT INTO `lead_activities` (`activity_id`, `lead_id`) VALUES
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(65, 2),
(66, 2),
(67, 2),
(68, 2),
(69, 2),
(70, 2),
(71, 2),
(72, 2),
(73, 3),
(74, 3),
(75, 3),
(76, 3),
(77, 3),
(78, 3),
(79, 3),
(80, 3),
(81, 3),
(82, 3),
(83, 4),
(84, 4),
(85, 4),
(86, 4),
(87, 4),
(88, 4),
(89, 4),
(90, 4),
(91, 4),
(92, 4),
(93, 4),
(94, 4),
(95, 1),
(144, 4),
(145, 4),
(146, 4),
(184, 5),
(185, 5),
(186, 5),
(187, 5),
(188, 5),
(189, 5),
(190, 5),
(191, 5),
(218, 3),
(232, 2),
(233, 2),
(234, 2),
(246, 5),
(247, 5),
(248, 5),
(249, 4);

-- --------------------------------------------------------

--
-- Table structure for table `lead_pipelines`
--

CREATE TABLE `lead_pipelines` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `rotten_days` int(11) NOT NULL DEFAULT 30,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_pipelines`
--

INSERT INTO `lead_pipelines` (`id`, `name`, `is_default`, `rotten_days`, `created_at`, `updated_at`) VALUES
(1, 'Default Pipeline', 1, 30, '2025-11-15 14:24:53', '2025-11-15 14:24:53');

-- --------------------------------------------------------

--
-- Table structure for table `lead_pipeline_stages`
--

CREATE TABLE `lead_pipeline_stages` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `probability` int(11) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `lead_pipeline_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_pipeline_stages`
--

INSERT INTO `lead_pipeline_stages` (`id`, `code`, `name`, `probability`, `sort_order`, `lead_pipeline_id`) VALUES
(1, 'new', 'New', 100, 1, 1),
(2, 'follow-up', 'Follow Up', 100, 2, 1),
(3, 'prospect', 'Prospect', 100, 3, 1),
(4, 'negotiation', 'Negotiation', 100, 4, 1),
(5, 'won', 'Won', 100, 5, 1),
(6, 'lost', 'Lost', 0, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `lead_priorities`
--

CREATE TABLE `lead_priorities` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_priorities`
--

INSERT INTO `lead_priorities` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Low', '2026-02-21 18:01:04', '2026-02-21 18:01:04'),
(2, 'Medium', '2026-02-21 18:01:04', '2026-02-21 18:01:04'),
(3, 'High', '2026-02-21 18:01:04', '2026-02-21 18:01:04'),
(4, 'Urgent', '2026-02-21 18:01:04', '2026-02-21 18:01:04');

-- --------------------------------------------------------

--
-- Table structure for table `lead_products`
--

CREATE TABLE `lead_products` (
  `id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `price` decimal(12,4) DEFAULT NULL,
  `amount` decimal(12,4) DEFAULT NULL,
  `lead_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_quotes`
--

CREATE TABLE `lead_quotes` (
  `quote_id` int(10) UNSIGNED NOT NULL,
  `lead_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_sources`
--

CREATE TABLE `lead_sources` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_sources`
--

INSERT INTO `lead_sources` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Email', '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(2, 'Web', '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(3, 'Web Form', '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(4, 'Phone', '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(5, 'Direct', '2025-11-15 14:24:53', '2025-11-15 14:24:53');

-- --------------------------------------------------------

--
-- Table structure for table `lead_stages`
--

CREATE TABLE `lead_stages` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_user_defined` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_tags`
--

CREATE TABLE `lead_tags` (
  `tag_id` int(10) UNSIGNED NOT NULL,
  `lead_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_types`
--

CREATE TABLE `lead_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_types`
--

INSERT INTO `lead_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'New Business', '2025-11-15 14:24:53', '2025-11-15 14:24:53'),
(2, 'Existing Business', '2025-11-15 14:24:53', '2025-11-15 14:24:53');

-- --------------------------------------------------------

--
-- Table structure for table `marketing_campaigns`
--

CREATE TABLE `marketing_campaigns` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL,
  `mail_to` varchar(255) NOT NULL,
  `spooling` varchar(255) DEFAULT NULL,
  `marketing_template_id` int(10) UNSIGNED DEFAULT NULL,
  `marketing_event_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_events`
--

CREATE TABLE `marketing_events` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_08_19_000000_create_failed_jobs_table', 1),
(2, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(3, '2021_03_12_060658_create_core_config_table', 1),
(4, '2021_03_12_074578_create_groups_table', 1),
(5, '2021_03_12_074597_create_roles_table', 1),
(6, '2021_03_12_074857_create_users_table', 1),
(7, '2021_03_12_074867_create_user_groups_table', 1),
(8, '2021_03_12_074957_create_user_password_resets_table', 1),
(9, '2021_04_02_080709_create_attributes_table', 1),
(10, '2021_04_02_080837_create_attribute_options_table', 1),
(11, '2021_04_06_122751_create_attribute_values_table', 1),
(12, '2021_04_09_051326_create_organizations_table', 1),
(13, '2021_04_09_065617_create_persons_table', 1),
(14, '2021_04_09_065617_create_products_table', 1),
(15, '2021_04_12_173232_create_countries_table', 1),
(16, '2021_04_12_173344_create_country_states_table', 1),
(17, '2021_04_21_172825_create_lead_sources_table', 1),
(18, '2021_04_21_172847_create_lead_types_table', 1),
(19, '2021_04_22_153258_create_lead_stages_table', 1),
(20, '2021_04_22_155706_create_lead_pipelines_table', 1),
(21, '2021_04_22_155838_create_lead_pipeline_stages_table', 1),
(22, '2021_04_22_164215_create_leads_table', 1),
(23, '2021_04_22_171805_create_lead_products_table', 1),
(24, '2021_05_12_150329_create_activities_table', 1),
(25, '2021_05_12_150329_create_lead_activities_table', 1),
(26, '2021_05_15_151855_create_activity_files_table', 1),
(27, '2021_05_20_141230_create_tags_table', 1),
(28, '2021_05_20_141240_create_lead_tags_table', 1),
(29, '2021_05_24_075618_create_emails_table', 1),
(30, '2021_05_25_072700_create_email_attachments_table', 1),
(31, '2021_06_07_162808_add_lead_view_permission_column_in_users_table', 1),
(32, '2021_07_01_230345_create_quotes_table', 1),
(33, '2021_07_01_231317_create_quote_items_table', 1),
(34, '2021_07_02_201822_create_lead_quotes_table', 1),
(35, '2021_07_28_142453_create_activity_participants_table', 1),
(36, '2021_08_26_133538_create_workflows_table', 1),
(37, '2021_09_03_172713_create_email_templates_table', 1),
(38, '2021_09_22_194103_add_unique_index_to_name_in_organizations_table', 1),
(39, '2021_09_22_194622_add_unique_index_to_name_in_groups_table', 1),
(40, '2021_09_23_221138_add_column_expected_close_date_in_leads_table', 1),
(41, '2021_09_30_135857_add_column_rotten_days_in_lead_pipelines_table', 1),
(42, '2021_09_30_154222_alter_lead_pipeline_stages_table', 1),
(43, '2021_09_30_161722_alter_leads_table', 1),
(44, '2021_09_30_183825_change_user_id_to_nullable_in_leads_table', 1),
(45, '2021_10_02_170105_insert_expected_closed_date_column_in_attributes_table', 1),
(46, '2021_11_11_180804_change_lead_pipeline_stage_id_constraint_in_leads_table', 1),
(47, '2021_11_12_171510_add_image_column_in_users_table', 1),
(48, '2021_11_17_190943_add_location_column_in_activities_table', 1),
(49, '2021_12_14_213049_create_web_forms_table', 1),
(50, '2021_12_14_214923_create_web_form_attributes_table', 1),
(51, '2024_01_11_154640_create_imports_table', 1),
(52, '2024_01_11_154741_create_import_batches_table', 1),
(53, '2024_05_10_152848_create_saved_filters_table', 1),
(54, '2024_06_21_160707_create_warehouses_table', 1),
(55, '2024_06_21_160735_create_warehouse_locations_table', 1),
(56, '2024_06_24_174241_insert_warehouse_attributes_in_attributes_table', 1),
(57, '2024_06_28_154009_create_product_inventories_table', 1),
(58, '2024_07_24_150821_create_webhooks_table', 1),
(59, '2024_07_31_092951_add_job_title_in_persons_table', 1),
(60, '2024_07_31_093603_add_organization_sales_owner_attribute_in_attributes_table', 1),
(61, '2024_07_31_093605_add_person_job_title_attribute_in_attributes_table', 1),
(62, '2024_07_31_093605_add_person_sales_owner_attribute_in_attributes_table', 1),
(63, '2024_08_06_145943_create_person_tags_table', 1),
(64, '2024_08_06_161212_create_person_activities_table', 1),
(65, '2024_08_10_100329_create_warehouse_activities_table', 1),
(66, '2024_08_10_100340_create_warehouse_tags_table', 1),
(67, '2024_08_10_150329_create_product_activities_table', 1),
(68, '2024_08_10_150340_create_product_tags_table', 1),
(69, '2024_08_14_102116_add_user_id_column_in_persons_table', 1),
(70, '2024_08_14_102136_add_user_id_column_in_organizations_table', 1),
(71, '2024_08_21_153011_add_leads_stage_and_pipeline_attributes', 1),
(72, '2024_08_27_091619_create_email_tags_table', 1),
(73, '2024_09_06_065808_alter_product_inventories_table', 1),
(74, '2024_09_09_094040_create_job_batches_table', 1),
(75, '2024_09_09_094042_create_jobs_table', 1),
(76, '2024_09_09_112201_add_unique_id_to_person_table', 1),
(77, '2024_10_29_044744_create_marketing_events_table', 1),
(78, '2024_11_04_122500_create_marketing_campaigns_table', 1),
(79, '2024_11_29_120302_modify_foreign_keys_in_leads_table', 1),
(80, '2025_01_17_151632_alter_activities_table', 1),
(81, '2025_01_29_133500_update_text_column_type_in_core_config_table', 1),
(82, '2025_03_19_132236_update_organization_id_column_in_persons_table', 1),
(83, '2025_07_01_133612_alter_lead_pipelines_table', 1),
(84, '2025_07_02_191710_alter_attribute_values_table', 1),
(85, '2025_07_09_133553_alter_email_templates_table', 1),
(86, '2025_02_24_000001_add_case_no_to_leads_table', 2),
(87, '2026_02_27_000001_create_purchase_orders_table', 3),
(88, '2026_02_27_000002_create_purchase_order_items_table', 3),
(89, '2026_02_27_000003_add_type_to_persons_table', 4),
(90, '2026_02_27_000004_add_additional_fields_to_persons_table', 5),
(91, '2026_02_27_000004_add_additional_fields_to_purchase_orders_table', 6),
(92, '2026_02_27_000005_add_organization_to_purchase_orders_table', 7),
(93, '2026_03_08_000001_add_catalog_fields_to_products_table', 8),
(94, '2026_03_09_000001_add_sales_flow_fields_to_quotes_table', 9),
(95, '2026_03_09_000003_create_proforma_invoices_tables', 10),
(96, '2026_03_08_000002_create_product_consumptions_table', 11),
(97, '2026_03_08_000003_create_product_production_sections_tables', 12),
(98, '2026_03_09_000002_add_extended_fields_to_quote_items_table', 13),
(99, '2026_03_09_000004_make_person_id_nullable_in_quotes_table', 14),
(100, '2026_03_10_000001_add_pricing_fields_to_products_table', 15),
(101, '2026_03_10_000001_add_color_and_image_fields_to_quote_items_table', 16);

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_organization_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`address`)),
  `phone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `industry` varchar(255) DEFAULT NULL,
  `employees` int(11) DEFAULT NULL,
  `annual_revenue` decimal(12,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `billing_street` varchar(255) DEFAULT NULL,
  `billing_city` varchar(255) DEFAULT NULL,
  `billing_state` varchar(255) DEFAULT NULL,
  `billing_postcode` varchar(255) DEFAULT NULL,
  `billing_country` varchar(255) DEFAULT NULL,
  `shipping_street` varchar(255) DEFAULT NULL,
  `shipping_city` varchar(255) DEFAULT NULL,
  `shipping_state` varchar(255) DEFAULT NULL,
  `shipping_postcode` varchar(255) DEFAULT NULL,
  `shipping_country` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`id`, `parent_organization_id`, `name`, `address`, `phone`, `fax`, `website`, `type`, `industry`, `employees`, `annual_revenue`, `description`, `billing_street`, `billing_city`, `billing_state`, `billing_postcode`, `billing_country`, `shipping_street`, `shipping_city`, `shipping_state`, `shipping_postcode`, `shipping_country`, `created_at`, `updated_at`, `user_id`) VALUES
(2, NULL, 'Abdul Haynes', NULL, '+1 (475) 445-8871', '+1 (112) 105-5157', '', 'customer', '', 800, 72.00, '• Its Organization and Then Contacts “not Persons” • Can I add the type of customer', 'Odit est sed perspi', 'Ducimus ea dicta au', 'Quia dolor exercitat', 'Nostrum ut aliquip s', 'Minima similique eve', 'Blanditiis omnis ut', 'Ea debitis quisquam', 'Id quo quis minim si', 'Fugit non ipsam quo', 'Maiores doloremque e', '2026-02-06 07:07:40', '2026-03-09 19:38:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `organization_activities`
--

CREATE TABLE `organization_activities` (
  `activity_id` int(10) UNSIGNED NOT NULL,
  `organization_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `organization_activities`
--

INSERT INTO `organization_activities` (`activity_id`, `organization_id`) VALUES
(163, 2),
(164, 2),
(165, 2),
(166, 2),
(167, 2),
(168, 2),
(169, 2),
(170, 2),
(171, 2),
(172, 2),
(173, 2),
(174, 2),
(175, 2),
(176, 2),
(177, 2),
(178, 2),
(179, 2),
(180, 2),
(181, 2),
(183, 2),
(207, 2),
(208, 2),
(209, 2),
(256, 2),
(263, 2),
(264, 2);

-- --------------------------------------------------------

--
-- Table structure for table `organization_files`
--

CREATE TABLE `organization_files` (
  `id` int(10) UNSIGNED NOT NULL,
  `organization_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `organization_files`
--

INSERT INTO `organization_files` (`id`, `organization_id`, `user_id`, `title`, `description`, `path`, `original_name`, `mime_type`, `size`, `created_at`, `updated_at`) VALUES
(0, 2, 1, '', '', 'organization-files/hQyBbDjG1Mcly8WQgY16MMviT12duFBWMfIHDYIW.jpg', 'test image.jpeg', 'image/jpeg', 7443, '2026-02-06 18:53:59', '2026-02-06 18:53:59'),
(0, 2, 1, '', '', 'organization-files/PZVE2f1cUchVykxcn6F6TlbLiSQdANyK49pCBOaz.jpg', 'carolina.jpg', 'image/jpeg', 25911, '2026-02-22 12:24:34', '2026-02-22 12:24:34');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `persons`
--

CREATE TABLE `persons` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT 'customer',
  `notes` text DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `last_delivery_date` date DEFAULT NULL,
  `payment_term` varchar(255) DEFAULT NULL,
  `shipping_method` varchar(255) DEFAULT NULL,
  `emails` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`emails`)),
  `contact_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`contact_numbers`)),
  `organization_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `unique_id` varchar(255) DEFAULT NULL,
  `salutation` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cell_phone` varchar(255) DEFAULT NULL,
  `direct_phone` varchar(255) DEFAULT NULL,
  `email_secondary` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mailing_street` varchar(255) DEFAULT NULL,
  `mailing_city` varchar(255) DEFAULT NULL,
  `mailing_state` varchar(255) DEFAULT NULL,
  `mailing_postcode` varchar(255) DEFAULT NULL,
  `mailing_country` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `persons`
--

INSERT INTO `persons` (`id`, `name`, `type`, `notes`, `completion_date`, `last_delivery_date`, `payment_term`, `shipping_method`, `emails`, `contact_numbers`, `organization_id`, `created_at`, `updated_at`, `job_title`, `user_id`, `unique_id`, `salutation`, `first_name`, `last_name`, `title`, `description`, `cell_phone`, `direct_phone`, `email_secondary`, `birth_date`, `phone`, `email`, `mailing_street`, `mailing_city`, `mailing_state`, `mailing_postcode`, `mailing_country`) VALUES
(1, 'Syed Sabeer', 'customer', NULL, NULL, NULL, NULL, NULL, '[{\"value\":\"syedsabeer6198@gmail.com\",\"label\":\"work\"}]', '[{\"value\":\"20094008480\",\"label\":\"work\"}]', NULL, '2025-11-15 14:31:02', '2025-11-15 14:31:02', 'Employee', NULL, 'syedsabeer6198@gmail.com|20094008480', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, '', 'customer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-02-06 19:44:22', '2026-02-06 19:44:22', NULL, 1, '1|2', '', 'Kuame', 'Trevino', 'Tempor ullamco magna', 'Assumenda proident', '+1 (855) 618-2783', '+1 (297) 115-4107', '+1 (297) 115-4107', '1972-01-01', '+1 (753) 794-9162', 'sihava@mailinator.com', 'Ut voluptatibus libe', 'Impedit consequuntu', 'Voluptates hic nulla', 'Consequatur recusan', 'Esse est quis maior'),
(4, '', 'customer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-02-16 18:36:10', '2026-02-16 18:36:10', NULL, 1, NULL, '', 'dfg', 'dfg', '', '', '', '', '', '0000-00-00', '+1 (475) 445-8871', '', 'Odit est sed perspi', 'Ducimus ea dicta au', 'Quia dolor exercitat', 'Nostrum ut aliquip s', 'Minima similique eve'),
(9, '', 'customer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-02-16 18:43:13', '2026-02-26 14:47:40', NULL, 1, NULL, '', 'fdg', 'dfsgfg', 'fdkjdfjkg', 'kjiljoi', '43534543543', 'difgkjv', 'testuser@gmail.com', '1901-11-22', '+1 (475) 445-8871', 'testuser@gmail.com', 'Odit est sed perspi', 'Ducimus ea dicta au', 'Quia dolor exercitat', 'Nostrum ut aliquip s', 'Minima similique eve'),
(10, '', 'customer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-02-16 18:43:53', '2026-02-16 18:58:07', NULL, 1, NULL, '', 'dfds', 'dsfdf', '', '', '0000000', '', 'test@gmail.com', '1901-11-15', '+1 (475) 445-8871', '', 'Odit est sed perspi', 'Ducimus ea dicta au', 'Quia dolor exercitat', 'Nostrum ut aliquip s', 'Minima similique eve'),
(11, '', 'vendor', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-27 15:15:16', '2026-02-27 15:15:16', NULL, 1, NULL, '', 'Steel', 'Wong', 'Aliqua Rerum culpa', 'Debitis officia est', '+1 (242) 653-2194', '+1 (861) 453-7789', '+1 (861) 453-7789', '1997-01-01', '+1 (846) 853-3684', 'xybinohexu@mailinator.com', 'Non delectus adipis', 'Consectetur pariatu', 'Ex eveniet sit debi', 'Omnis consectetur d', 'Eligendi debitis par');

-- --------------------------------------------------------

--
-- Table structure for table `person_activities`
--

CREATE TABLE `person_activities` (
  `activity_id` int(10) UNSIGNED NOT NULL,
  `person_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `person_activities`
--

INSERT INTO `person_activities` (`activity_id`, `person_id`) VALUES
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(37, 4),
(38, 4),
(39, 4),
(52, 9),
(53, 9),
(54, 9),
(55, 10),
(56, 10),
(57, 10),
(58, 10),
(59, 10),
(60, 10),
(228, 9),
(229, 9),
(230, 9),
(231, 9),
(250, 9),
(251, 9),
(252, 9),
(253, 9),
(254, 11),
(255, 11);

-- --------------------------------------------------------

--
-- Table structure for table `person_tags`
--

CREATE TABLE `person_tags` (
  `tag_id` int(10) UNSIGNED NOT NULL,
  `person_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `sku` varchar(255) NOT NULL,
  `internal_code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `customer_organization_id` int(10) UNSIGNED DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `publish_on_website` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `price` decimal(12,4) DEFAULT NULL,
  `cost_price` decimal(12,4) DEFAULT NULL,
  `selling_price` decimal(12,4) DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `style` varchar(255) DEFAULT NULL,
  `size` varchar(100) DEFAULT NULL,
  `cover_image` varchar(500) DEFAULT NULL,
  `additional_info` longtext DEFAULT NULL,
  `shipping_info` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `internal_code`, `name`, `customer_organization_id`, `slug`, `description`, `quantity`, `publish_on_website`, `price`, `cost_price`, `selling_price`, `category_id`, `style`, `size`, `cover_image`, `additional_info`, `shipping_info`, `created_at`, `updated_at`) VALUES
(3, 'COTTONTOTEBAG-13B742', NULL, 'Cotton Tote Bag', NULL, 'cotton-tote-bag', NULL, 0, 0, NULL, NULL, NULL, 0, 'MQTB', '15\"W x 16\"H', 'product-images/pNsCYoTurKqsXyktIlfhBAIxAzqUUG8J3KCETwlG.jpg', '<table class=\"pf-info-table\">\r\n<tbody>\r\n<tr>\r\n<td>Item No.</td>\r\n<td>MQTB</td>\r\n</tr>\r\n<tr>\r\n<td>Item Name</td>\r\n<td>Cotton Tote Bag</td>\r\n</tr>\r\n<tr>\r\n<td>Description</td>\r\n<td>6oz Cotton Bag. 100% Cotton Canvas Tote self-fabric handles. Range of colors Reinforced at stress points.</td>\r\n</tr>\r\n<tr>\r\n<td>Available Colors</td>\r\n<td>Army, Azalea, Black, Carolina Blue, Dark Grey, Forest Green, Gold, Light Grey, Hot Pink, Kelly Green, Lavender, Light Pink, Lime, Maroon, Natural, Navy, Orange, Purple, Red, Royal, Sapphire, Texas Orange, Turquoise, White, Yellow, Chocolate</td>\r\n</tr>\r\n<tr>\r\n<td>Product Size</td>\r\n<td>15\"W x 16\"H</td>\r\n</tr>\r\n<tr>\r\n<td>Imprint Area</td>\r\n<td>10\"W x 12\"H</td>\r\n</tr>\r\n<tr>\r\n<td>Quality / Material</td>\r\n<td>100% Cotton Canvas</td>\r\n</tr>\r\n<tr>\r\n<td>Quality Weight</td>\r\n<td>6oz</td>\r\n</tr>\r\n<tr>\r\n<td>Handle Length</td>\r\n<td>22\"</td>\r\n</tr>\r\n<tr>\r\n<td>Side Gussets</td>\r\n<td>No</td>\r\n</tr>\r\n<tr>\r\n<td>Bottom Gussets</td>\r\n<td>No</td>\r\n</tr>\r\n<tr>\r\n<td>Rush Available</td>\r\n<td>Yes, Pls contact customer service</td>\r\n</tr>\r\n<tr>\r\n<td>Available Decoration</td>\r\n<td>Spot Colors, 4 Color Process, Heat Transfers</td>\r\n</tr>\r\n</tbody>\r\n</table>', '<table class=\"pf-info-table\">\r\n<tbody>\r\n<tr>\r\n<td>Standard Lead Time</td>\r\n<td>5-7 Business Days after artwork approval</td>\r\n</tr>\r\n<tr>\r\n<td>Rush Service</td>\r\n<td>Available - Please contact customer service for rush options and pricing</td>\r\n</tr>\r\n<tr>\r\n<td>Shipping Methods</td>\r\n<td>Ground, 2nd Day Air, Next Day Air, Freight for large orders</td>\r\n</tr>\r\n<tr>\r\n<td>Packaging</td>\r\n<td>Standard bulk packaging. Individual poly bags available upon request</td>\r\n</tr>\r\n<tr>\r\n<td>Weight per Unit</td>\r\n<td>Approximately 0.3 lbs</td>\r\n</tr>\r\n<tr>\r\n<td>Carton Dimensions</td>\r\n<td>24\" x 18\" x 12\" (approx. 100 units per carton)</td>\r\n</tr>\r\n<tr>\r\n<td>International Shipping</td>\r\n<td>Available - Contact for international shipping quotes and lead times</td>\r\n</tr>\r\n<tr>\r\n<td>Freight Quote</td>\r\n<td>Use the Freight Quote button to get shipping estimates</td>\r\n</tr>\r\n<tr>\r\n<td>Delivery Address</td>\r\n<td>Residential and commercial addresses accepted. Loading dock availability may affect freight charges</td>\r\n</tr>\r\n</tbody>\r\n</table>', '2026-02-22 10:47:33', '2026-02-22 19:38:01'),
(18, 'Ullamco ab minus et', 'Dolores ex in quia a', 'Xantha Kaufman', NULL, 'xantha-kaufman', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'Esse ab anim omnis v', NULL, NULL, NULL, '2026-03-09 15:36:53', '2026-03-09 15:36:53'),
(19, 'XANTHAKAUFMANCOPY-2A5DF5', 'Dolores ex in quia a', 'Xantha Kaufman (Copy)', NULL, 'xantha-kaufman-copy', NULL, 0, 0, 0.0000, NULL, NULL, NULL, NULL, 'Esse ab anim omnis v', NULL, NULL, NULL, '2026-03-09 15:37:14', '2026-03-09 15:37:14'),
(20, 'Sit deserunt conseq', 'Et molestiae ut ipsu', 'Kathleen Haynes', 2, 'kathleen-haynes', NULL, 0, 0, 938.0000, 301.0000, 938.0000, NULL, NULL, 'Dolorem et dolor ex', 'product-images/seRbGO90doe8xva2k6ElTGQ2U4sJYzArLx1Lu62A.jpg', NULL, NULL, '2026-03-09 20:10:04', '2026-03-09 20:10:04');

-- --------------------------------------------------------

--
-- Table structure for table `product_activities`
--

CREATE TABLE `product_activities` (
  `activity_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_activities`
--

INSERT INTO `product_activities` (`activity_id`, `product_id`) VALUES
(98, 3),
(99, 3),
(100, 3),
(101, 3),
(102, 3),
(103, 3),
(104, 3),
(105, 3),
(106, 3),
(107, 3),
(108, 3),
(109, 3),
(110, 3),
(115, 3),
(116, 3),
(117, 3),
(118, 3),
(119, 3),
(120, 3),
(121, 3),
(122, 3),
(123, 3),
(124, 3),
(125, 3),
(126, 3),
(127, 3),
(128, 3),
(129, 3),
(130, 3),
(257, 18),
(258, 18),
(259, 18),
(260, 19),
(261, 19),
(262, 19),
(265, 20),
(266, 20),
(267, 20),
(268, 20);

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(0, 'Bags', '2026-02-21 21:52:31', '2026-02-22 10:46:37');

-- --------------------------------------------------------

--
-- Table structure for table `product_colors`
--

CREATE TABLE `product_colors` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `color_code` varchar(20) NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_colors`
--

INSERT INTO `product_colors` (`id`, `product_id`, `name`, `color_code`, `sort_order`, `created_at`, `updated_at`) VALUES
(654, 3, 'Army', '#4B5320', 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(655, 3, 'Azalea', '#F19CBB', 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(656, 3, 'Black', '#000000', 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(657, 3, 'Carolina Blue', '#56A0D3', 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(658, 3, 'Chocolate', '#D2691E', 4, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(659, 3, 'Dark Grey', '#A9A9A9', 5, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(660, 3, 'Forest Green', '#228B22', 6, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(661, 3, 'Gold', '#FFD700', 7, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(662, 3, 'Hot Pink', '#FF69B4', 8, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(663, 3, 'Kelly', '#4CBB17', 9, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(664, 3, 'Lavender', '#E6E6FA', 10, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(665, 3, 'Light Grey', '#D3D3D3', 11, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(666, 3, 'Light Pink', '#FFB6C1', 12, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(667, 3, 'Lime', '#00FF00', 13, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(668, 3, 'Maroon', '#800000', 14, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(669, 3, 'Natural', '#F5F5DC', 15, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(670, 3, 'Navy', '#000080', 16, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(671, 3, 'Orange', '#FFA500', 17, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(672, 3, 'Purple', '#800080', 18, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(673, 3, 'Red', '#FF0000', 19, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(674, 3, 'Royal', '#4169E1', 20, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(675, 3, 'Sapphire', '#0F52BA', 21, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(676, 3, 'Texas Orange', '#BF5700', 22, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(677, 3, 'Turquoise', '#40E0D0', 23, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(678, 3, 'White', '#FFFFFF', 24, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(679, 3, 'Yellow', '#FFFF00', 25, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(810, 18, 'Bradley Vasquez', '#90f51a', 0, '2026-03-09 15:36:53', '2026-03-09 15:36:53'),
(811, 18, 'Declan Moran', '#94f42f', 1, '2026-03-09 15:36:53', '2026-03-09 15:36:53'),
(812, 19, 'Bradley Vasquez', '#90f51a', 0, '2026-03-09 15:37:14', '2026-03-09 15:37:14'),
(813, 19, 'Declan Moran', '#94f42f', 1, '2026-03-09 15:37:14', '2026-03-09 15:37:14'),
(816, 20, 'Ryder Clark', '#cfc6b7', 0, '2026-03-09 20:17:26', '2026-03-09 20:17:26'),
(817, 20, 'Doris Pierce', '#90a7ad', 1, '2026-03-09 20:17:26', '2026-03-09 20:17:26');

-- --------------------------------------------------------

--
-- Table structure for table `product_consumptions`
--

CREATE TABLE `product_consumptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `qty` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `unit` varchar(255) NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_consumptions`
--

INSERT INTO `product_consumptions` (`id`, `product_id`, `name`, `qty`, `unit`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 18, 'Dolan Odonnell', 850.0000, 'Anim est dolore plac', 0, '2026-03-09 15:36:53', '2026-03-09 15:36:53'),
(2, 18, 'Ishmael Ross', 254.0000, 'Eaque laborum Persp', 1, '2026-03-09 15:36:53', '2026-03-09 15:36:53'),
(3, 19, 'Dolan Odonnell', 850.0000, 'Anim est dolore plac', 0, '2026-03-09 15:37:14', '2026-03-09 15:37:14'),
(4, 19, 'Ishmael Ross', 254.0000, 'Eaque laborum Persp', 1, '2026-03-09 15:37:14', '2026-03-09 15:37:14'),
(6, 20, 'Chaim Luna', 102.0000, 'Voluptatem eos nihi', 0, '2026-03-09 20:17:26', '2026-03-09 20:17:26');

-- --------------------------------------------------------

--
-- Table structure for table `product_inventories`
--

CREATE TABLE `product_inventories` (
  `id` int(10) UNSIGNED NOT NULL,
  `in_stock` int(11) NOT NULL DEFAULT 0,
  `allocated` int(11) NOT NULL DEFAULT 0,
  `product_id` int(10) UNSIGNED NOT NULL,
  `warehouse_id` int(10) UNSIGNED DEFAULT NULL,
  `warehouse_location_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_key_points`
--

CREATE TABLE `product_key_points` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `key_heading` varchar(255) NOT NULL,
  `key_point` text NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_key_points`
--

INSERT INTO `product_key_points` (`id`, `product_id`, `key_heading`, `key_point`, `sort_order`, `created_at`, `updated_at`) VALUES
(170, 3, 'Price Includes', '1 Color, 1 Location', 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(171, 3, 'Rush Available', 'Contact Customer Service', 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(172, 3, 'Setup Charge', '$56.25 (V)', 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(173, 3, 'Repeat Setup', '$25.00 (V)', 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(174, 3, 'Flash Charge', '$0.31 (V)', 4, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(175, 3, 'PMS Match', '$25.00 (V)', 5, '2026-02-22 19:38:01', '2026-02-22 19:38:01');

-- --------------------------------------------------------

--
-- Table structure for table `product_other_images`
--

CREATE TABLE `product_other_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `path` varchar(500) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `color_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_other_images`
--

INSERT INTO `product_other_images` (`id`, `product_id`, `path`, `original_name`, `sort_order`, `color_id`, `created_at`, `updated_at`) VALUES
(4, 3, 'product-other-images/ThIThoOwmRlhT0e9xba7iT2epdCpb3A87TLHnBGR.jpg', 'carolina.jpg', 1, 657, '2026-02-22 11:56:04', '2026-02-22 19:38:01'),
(5, 3, 'product-other-images/mQVkPe9YguE3yzRc8tBq6z8rg9ZgRzJkNPHn9Dbx.jpg', 'gold.jpg', 2, 661, '2026-02-22 11:56:34', '2026-02-22 19:38:01'),
(6, 3, 'product-other-images/dGaYsCk4XF3gL3jo0Z8as6Juf6QqRrA8MXnsg3k0.jpg', 'grey.jpg', 4, 659, '2026-02-22 11:56:34', '2026-02-22 19:38:01'),
(7, 3, 'product-other-images/WjsNpdenWnwVL3Dyg24a8jWTD5D0rmSV5aPvjxQT.jpg', 'maroon.jpg', 5, 668, '2026-02-22 11:57:00', '2026-02-22 19:38:01'),
(8, 3, 'product-other-images/BJkP8jNZoDhkl15udrSdD7DNR7BolCoPIC5gWCW5.jpg', 'navy.jpg', 6, 670, '2026-02-22 11:57:40', '2026-02-22 19:38:01'),
(9, 3, 'product-other-images/sasjiKKSU0om3bNYxxXLSOMRK85SOQWV44dXc4Q7.jpg', 'purple.jpg', 8, 672, '2026-02-22 11:57:40', '2026-02-22 19:38:01'),
(10, 3, 'product-other-images/BRfAEiQ0pXOYDzat6eNgY909wBl8CWSEp5aD5EPZ.jpg', 'red.jpg', 11, 673, '2026-02-22 11:57:40', '2026-02-22 19:38:01'),
(11, 3, 'product-other-images/G32GmSHqR7unmHS9mlzlusnya6mMZbQukQzHvgVK.jpg', 'royal.jpg', 15, 674, '2026-02-22 11:57:40', '2026-02-22 19:38:01'),
(28, 20, 'product-other-images/sil9BFWOhULCxuohCl8glhyvU6Napdb2ysWpWCYl.jpg', 'images.jpg', 1, 816, '2026-03-09 20:17:26', '2026-03-09 20:17:26');

-- --------------------------------------------------------

--
-- Table structure for table `product_pricing_charts`
--

CREATE TABLE `product_pricing_charts` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `heading` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_pricing_charts`
--

INSERT INTO `product_pricing_charts` (`id`, `product_id`, `heading`, `type`, `sort_order`, `created_at`, `updated_at`) VALUES
(31, 3, 'Spot Printing', '', 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(32, 3, 'Heat Transfer', '', 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(33, 3, 'Blank', '', 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01');

-- --------------------------------------------------------

--
-- Table structure for table `product_pricing_chart_tiers`
--

CREATE TABLE `product_pricing_chart_tiers` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_pricing_chart_id` int(10) UNSIGNED NOT NULL,
  `product_pricing_chart_type_id` int(10) UNSIGNED DEFAULT NULL,
  `quantity` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `price` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_pricing_chart_tiers`
--

INSERT INTO `product_pricing_chart_tiers` (`id`, `product_pricing_chart_id`, `product_pricing_chart_type_id`, `quantity`, `price`, `sort_order`, `created_at`, `updated_at`) VALUES
(74, 31, 12, 72.0000, 4.0200, 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(75, 31, 12, 288.0000, 3.2700, 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(76, 31, 12, 500.0000, 2.9400, 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(77, 31, 12, 1000.0000, 2.7700, 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(78, 31, 12, 2000.0000, 2.5800, 4, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(79, 31, 12, 3000.0000, 2.4800, 5, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(80, 31, 13, 72.0000, 5.0000, 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(81, 31, 13, 288.0000, 4.2300, 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(82, 31, 13, 500.0000, 3.9000, 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(83, 31, 13, 1000.0000, 3.7300, 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(84, 31, 13, 2000.0000, 3.5400, 4, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(85, 31, 13, 3000.0000, 3.4400, 5, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(86, 31, 14, 72.0000, 1.8100, 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(87, 31, 14, 288.0000, 1.3100, 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(88, 31, 14, 500.0000, 1.0600, 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(89, 31, 14, 1000.0000, 0.9400, 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(90, 31, 14, 2000.0000, 0.8100, 4, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(91, 31, 14, 3000.0000, 0.7100, 5, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(92, 31, 15, 72.0000, 0.5000, 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(93, 31, 15, 288.0000, 0.3800, 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(94, 31, 15, 500.0000, 0.3500, 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(95, 31, 15, 1000.0000, 0.3000, 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(96, 31, 15, 2000.0000, 0.2500, 4, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(97, 31, 15, 3000.0000, 0.2000, 5, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(98, 32, 16, 72.0000, 4.0200, 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(99, 32, 16, 288.0000, 3.2700, 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(100, 32, 16, 500.0000, 2.9400, 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(101, 32, 16, 1000.0000, 2.7700, 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(102, 32, 16, 2000.0000, 2.5800, 4, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(103, 32, 16, 3000.0000, 2.4800, 5, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(104, 32, 17, 72.0000, 5.0000, 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(105, 32, 17, 288.0000, 4.2300, 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(106, 32, 17, 500.0000, 3.9000, 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(107, 32, 17, 1000.0000, 3.7300, 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(108, 32, 17, 2000.0000, 3.5400, 4, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(109, 32, 17, 3000.0000, 3.4400, 5, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(110, 32, 18, 72.0000, 1.8100, 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(111, 32, 18, 288.0000, 1.3100, 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(112, 32, 18, 500.0000, 1.0600, 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(113, 32, 18, 1000.0000, 0.9400, 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(114, 32, 18, 2000.0000, 0.8100, 4, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(115, 32, 18, 3000.0000, 0.7100, 5, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(116, 32, 19, 72.0000, 0.5000, 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(117, 32, 19, 288.0000, 0.3800, 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(118, 32, 19, 500.0000, 0.3500, 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(119, 32, 19, 1000.0000, 0.3000, 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(120, 32, 19, 2000.0000, 0.2500, 4, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(121, 32, 19, 3000.0000, 0.2000, 5, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(122, 33, 20, 72.0000, 4.0200, 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(123, 33, 20, 288.0000, 3.2700, 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(124, 33, 20, 500.0000, 2.9400, 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(125, 33, 20, 1000.0000, 2.7700, 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(126, 33, 20, 2000.0000, 2.5800, 4, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(127, 33, 20, 3000.0000, 2.4800, 5, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(128, 33, 21, 72.0000, 5.0000, 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(129, 33, 21, 288.0000, 4.2300, 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(130, 33, 21, 500.0000, 3.9000, 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(131, 33, 21, 1000.0000, 3.7300, 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(132, 33, 21, 2000.0000, 3.5400, 4, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(133, 33, 21, 3000.0000, 3.4400, 5, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(134, 33, 22, 72.0000, 1.8100, 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(135, 33, 22, 288.0000, 1.3100, 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(136, 33, 22, 500.0000, 1.0600, 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(137, 33, 22, 1000.0000, 0.9400, 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(138, 33, 22, 2000.0000, 0.8100, 4, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(139, 33, 22, 3000.0000, 0.7100, 5, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(140, 33, 23, 72.0000, 0.5000, 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(141, 33, 23, 288.0000, 0.3800, 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(142, 33, 23, 500.0000, 0.3500, 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(143, 33, 23, 1000.0000, 0.3000, 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(144, 33, 23, 2000.0000, 0.2500, 4, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(145, 33, 23, 3000.0000, 0.2000, 5, '2026-02-22 19:38:01', '2026-02-22 19:38:01');

-- --------------------------------------------------------

--
-- Table structure for table `product_pricing_chart_types`
--

CREATE TABLE `product_pricing_chart_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_pricing_chart_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(100) NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_pricing_chart_types`
--

INSERT INTO `product_pricing_chart_types` (`id`, `product_pricing_chart_id`, `type`, `sort_order`, `created_at`, `updated_at`) VALUES
(12, 31, 'Natural', 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(13, 31, 'Colors', 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(14, 31, 'Add Location', 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(15, 31, 'Add Color', 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(16, 32, 'Natural', 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(17, 32, 'Colors', 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(18, 32, 'Add Location', 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(19, 32, 'Add Color', 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(20, 33, 'Natural', 0, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(21, 33, 'Colors', 1, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(22, 33, 'Add Location', 2, '2026-02-22 19:38:01', '2026-02-22 19:38:01'),
(23, 33, 'Add Color', 3, '2026-02-22 19:38:01', '2026-02-22 19:38:01');

-- --------------------------------------------------------

--
-- Table structure for table `product_production_sections`
--

CREATE TABLE `product_production_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `section_name` varchar(255) NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_production_sections`
--

INSERT INTO `product_production_sections` (`id`, `product_id`, `section_name`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 18, 'Calvin Fisher', 0, '2026-03-09 15:36:53', '2026-03-09 15:36:53'),
(2, 18, 'Chastity Goodwin', 1, '2026-03-09 15:36:53', '2026-03-09 15:36:53'),
(3, 19, 'Calvin Fisher', 0, '2026-03-09 15:37:14', '2026-03-09 15:37:14'),
(4, 19, 'Chastity Goodwin', 1, '2026-03-09 15:37:14', '2026-03-09 15:37:14'),
(6, 20, 'Charlotte Brock', 0, '2026-03-09 20:17:26', '2026-03-09 20:17:26');

-- --------------------------------------------------------

--
-- Table structure for table `product_production_section_items`
--

CREATE TABLE `product_production_section_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_production_section_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `qty` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `unit` varchar(255) NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_production_section_items`
--

INSERT INTO `product_production_section_items` (`id`, `product_production_section_id`, `name`, `qty`, `unit`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Fay Oneil', 774.0000, 'Atque consequatur E', 0, '2026-03-09 15:36:53', '2026-03-09 15:36:53'),
(2, 2, 'Cooper Battle', 416.0000, 'Proident laboris ip', 0, '2026-03-09 15:36:53', '2026-03-09 15:36:53'),
(3, 3, 'Fay Oneil', 774.0000, 'Atque consequatur E', 0, '2026-03-09 15:37:14', '2026-03-09 15:37:14'),
(4, 4, 'Cooper Battle', 416.0000, 'Proident laboris ip', 0, '2026-03-09 15:37:14', '2026-03-09 15:37:14'),
(6, 6, 'Hilary Frazier', 516.0000, 'Magnam voluptate quo', 0, '2026-03-09 20:17:26', '2026-03-09 20:17:26');

-- --------------------------------------------------------

--
-- Table structure for table `product_tags`
--

CREATE TABLE `product_tags` (
  `tag_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proforma_invoices`
--

CREATE TABLE `proforma_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proforma_number` varchar(255) NOT NULL,
  `quote_id` int(10) UNSIGNED DEFAULT NULL,
  `organization_id` int(10) UNSIGNED NOT NULL,
  `person_id` int(10) UNSIGNED DEFAULT NULL,
  `sales_owner_id` int(10) UNSIGNED DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `issue_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`billing_address`)),
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`shipping_address`)),
  `subtotal` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `discount_percent` decimal(12,4) DEFAULT 0.0000,
  `discount_amount` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `tax_amount` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `adjustment_amount` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `grand_total` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `received_amount` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `remaining_amount` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `terms` text DEFAULT NULL,
  `customer_po_reference` varchar(255) DEFAULT NULL,
  `source_type` varchar(255) DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `approved_by` int(10) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proforma_invoice_items`
--

CREATE TABLE `proforma_invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proforma_invoice_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `qty` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `unit` varchar(255) DEFAULT NULL,
  `unit_price` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `discount_percent` decimal(12,4) DEFAULT 0.0000,
  `discount_amount` decimal(12,4) DEFAULT 0.0000,
  `tax_percent` decimal(12,4) DEFAULT 0.0000,
  `tax_amount` decimal(12,4) DEFAULT 0.0000,
  `line_subtotal` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `line_total` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `sort_order` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proforma_receipts`
--

CREATE TABLE `proforma_receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proforma_invoice_id` bigint(20) UNSIGNED NOT NULL,
  `receipt_number` varchar(255) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,4) NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `received_by` int(10) UNSIGNED DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `po_number` varchar(255) NOT NULL,
  `job_number` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `last_delivery_date` date DEFAULT NULL,
  `payment_term` varchar(255) DEFAULT NULL,
  `shipping_method` varchar(255) DEFAULT NULL,
  `sales_tax_percent` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `freight` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `sub_total` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `tax_amount` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `grand_total` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `person_id` int(10) UNSIGNED DEFAULT NULL,
  `organization_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `po_number`, `job_number`, `description`, `notes`, `completion_date`, `last_delivery_date`, `payment_term`, `shipping_method`, `sales_tax_percent`, `freight`, `sub_total`, `tax_amount`, `grand_total`, `person_id`, `organization_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '00001', '', NULL, NULL, NULL, NULL, NULL, NULL, 12.0000, 100.0000, 1200.0000, 144.0000, 1444.0000, NULL, NULL, NULL, '2026-02-27 13:52:32', '2026-02-27 13:52:32');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `item` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `total` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `purchase_order_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `item`, `description`, `quantity`, `price`, `total`, `purchase_order_id`, `created_at`, `updated_at`) VALUES
(1, 'hjhhjhjhjhhjhj', 'jjkjk', 12, 100.0000, 1200.0000, 1, '2026-02-27 13:52:32', '2026-02-27 13:52:32'),
(2, 'dshfdjk', 'djfdjfjk', 1, 0.0000, 0.0000, 1, '2026-02-27 13:52:32', '2026-02-27 13:52:32');

-- --------------------------------------------------------

--
-- Table structure for table `quotes`
--

CREATE TABLE `quotes` (
  `id` int(10) UNSIGNED NOT NULL,
  `quote_number` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `quote_date` date DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `terms` text DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`billing_address`)),
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`shipping_address`)),
  `discount_percent` decimal(12,4) DEFAULT 0.0000,
  `discount_amount` decimal(12,4) DEFAULT NULL,
  `tax_amount` decimal(12,4) DEFAULT NULL,
  `adjustment_amount` decimal(12,4) DEFAULT NULL,
  `sub_total` decimal(12,4) DEFAULT NULL,
  `grand_total` decimal(12,4) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `expired_at` datetime DEFAULT NULL,
  `person_id` int(10) UNSIGNED DEFAULT NULL,
  `organization_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quote_items`
--

CREATE TABLE `quote_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `item_code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `unit` varchar(255) DEFAULT NULL,
  `price` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `unit_price` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `coupon_code` varchar(255) DEFAULT NULL,
  `discount_percent` decimal(12,4) DEFAULT 0.0000,
  `discount_amount` decimal(12,4) DEFAULT 0.0000,
  `tax_percent` decimal(12,4) DEFAULT 0.0000,
  `tax_amount` decimal(12,4) DEFAULT 0.0000,
  `line_subtotal` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `line_total` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `sort_order` int(11) DEFAULT NULL,
  `total` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `color_variant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `color_variant_name` varchar(255) DEFAULT NULL,
  `preview_image` varchar(255) DEFAULT NULL,
  `quote_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `permission_type` varchar(255) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `permission_type`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'Administrator Role', 'all', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `view_permission` varchar(255) DEFAULT 'global',
  `role_id` int(10) UNSIGNED NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `status`, `view_permission`, `role_id`, `remember_token`, `created_at`, `updated_at`, `image`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$DeTM8iUvVZBsVgxH3OnT9OYVTTZqTNAop4Yjpmrb0lqfbQ/kXcmEG', 1, 'global', 1, NULL, '2025-11-15 14:24:54', '2025-11-15 14:24:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE `user_groups` (
  `group_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_password_resets`
--

CREATE TABLE `user_password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `contact_name` varchar(255) NOT NULL,
  `contact_emails` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`contact_emails`)),
  `contact_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`contact_numbers`)),
  `contact_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`contact_address`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_activities`
--

CREATE TABLE `warehouse_activities` (
  `activity_id` int(10) UNSIGNED NOT NULL,
  `warehouse_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_locations`
--

CREATE TABLE `warehouse_locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `warehouse_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_tags`
--

CREATE TABLE `warehouse_tags` (
  `tag_id` int(10) UNSIGNED NOT NULL,
  `warehouse_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `webhooks`
--

CREATE TABLE `webhooks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `method` varchar(255) NOT NULL,
  `end_point` varchar(255) NOT NULL,
  `query_params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`query_params`)),
  `headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`headers`)),
  `payload_type` varchar(255) NOT NULL,
  `raw_payload_type` varchar(255) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `web_forms`
--

CREATE TABLE `web_forms` (
  `id` int(10) UNSIGNED NOT NULL,
  `form_id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `submit_button_label` text NOT NULL,
  `submit_success_action` varchar(255) NOT NULL,
  `submit_success_content` varchar(255) NOT NULL,
  `create_lead` tinyint(1) NOT NULL DEFAULT 0,
  `background_color` varchar(255) DEFAULT NULL,
  `form_background_color` varchar(255) DEFAULT NULL,
  `form_title_color` varchar(255) DEFAULT NULL,
  `form_submit_button_color` varchar(255) DEFAULT NULL,
  `attribute_label_color` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `web_form_attributes`
--

CREATE TABLE `web_form_attributes` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) DEFAULT NULL,
  `attribute_id` int(10) UNSIGNED NOT NULL,
  `web_form_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workflows`
--

CREATE TABLE `workflows` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `entity_type` varchar(255) NOT NULL,
  `event` varchar(255) NOT NULL,
  `condition_type` varchar(255) NOT NULL DEFAULT 'and',
  `conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`conditions`)),
  `actions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`actions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workflows`
--

INSERT INTO `workflows` (`id`, `name`, `description`, `entity_type`, `event`, `condition_type`, `conditions`, `actions`, `created_at`, `updated_at`) VALUES
(1, 'Emails to participants after activity creation', 'Emails to participants after activity creation', 'activities', 'activity.create.after', 'and', '[{\"value\": [\"call\", \"meeting\", \"lunch\"], \"operator\": \"{}\", \"attribute\": \"type\", \"attribute_type\": \"multiselect\"}]', '[{\"id\": \"send_email_to_participants\", \"value\": \"1\"}]', '2025-11-15 14:24:54', '2025-11-15 14:24:54'),
(2, 'Emails to participants after activity updation', 'Emails to participants after activity updation', 'activities', 'activity.update.after', 'and', '[{\"value\": [\"call\", \"meeting\", \"lunch\"], \"operator\": \"{}\", \"attribute\": \"type\", \"attribute_type\": \"multiselect\"}]', '[{\"id\": \"send_email_to_participants\", \"value\": \"2\"}]', '2025-11-15 14:24:54', '2025-11-15 14:24:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activities_user_id_foreign` (`user_id`),
  ADD KEY `activities_entity_type_entity_id_index` (`entity_type`,`entity_id`);

--
-- Indexes for table `activity_files`
--
ALTER TABLE `activity_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_files_activity_id_foreign` (`activity_id`);

--
-- Indexes for table `activity_participants`
--
ALTER TABLE `activity_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_participants_activity_id_foreign` (`activity_id`),
  ADD KEY `activity_participants_user_id_foreign` (`user_id`),
  ADD KEY `activity_participants_person_id_foreign` (`person_id`);

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attributes_code_entity_type_unique` (`code`,`entity_type`);

--
-- Indexes for table `attribute_options`
--
ALTER TABLE `attribute_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attribute_options_attribute_id_foreign` (`attribute_id`);

--
-- Indexes for table `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entity_type_attribute_value_index_unique` (`entity_type`,`entity_id`,`attribute_id`),
  ADD UNIQUE KEY `attribute_values_unique_id_unique` (`unique_id`),
  ADD KEY `attribute_values_attribute_id_foreign` (`attribute_id`);

--
-- Indexes for table `core_config`
--
ALTER TABLE `core_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `country_states`
--
ALTER TABLE `country_states`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country_states_country_id_foreign` (`country_id`);

--
-- Indexes for table `datagrid_saved_filters`
--
ALTER TABLE `datagrid_saved_filters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `datagrid_saved_filters_user_id_name_src_unique` (`user_id`,`name`,`src`);

--
-- Indexes for table `emails`
--
ALTER TABLE `emails`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emails_message_id_unique` (`message_id`),
  ADD UNIQUE KEY `emails_unique_id_unique` (`unique_id`),
  ADD KEY `emails_person_id_foreign` (`person_id`),
  ADD KEY `emails_lead_id_foreign` (`lead_id`),
  ADD KEY `emails_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `email_attachments`
--
ALTER TABLE `email_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_attachments_email_id_foreign` (`email_id`);

--
-- Indexes for table `email_tags`
--
ALTER TABLE `email_tags`
  ADD KEY `email_tags_tag_id_foreign` (`tag_id`),
  ADD KEY `email_tags_email_id_foreign` (`email_id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_templates_name_unique` (`name`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `groups_name_unique` (`name`);

--
-- Indexes for table `imports`
--
ALTER TABLE `imports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `import_batches`
--
ALTER TABLE `import_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `import_batches_import_id_foreign` (`import_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `leads_case_no_unique` (`case_no`),
  ADD KEY `leads_lead_pipeline_id_foreign` (`lead_pipeline_id`),
  ADD KEY `leads_lead_pipeline_stage_id_foreign` (`lead_pipeline_stage_id`),
  ADD KEY `leads_user_id_foreign` (`user_id`),
  ADD KEY `leads_person_id_foreign` (`person_id`),
  ADD KEY `leads_lead_source_id_foreign` (`lead_source_id`),
  ADD KEY `leads_lead_type_id_foreign` (`lead_type_id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `lead_activities`
--
ALTER TABLE `lead_activities`
  ADD KEY `lead_activities_activity_id_foreign` (`activity_id`),
  ADD KEY `lead_activities_lead_id_foreign` (`lead_id`);

--
-- Indexes for table `lead_pipelines`
--
ALTER TABLE `lead_pipelines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lead_pipelines_name_unique` (`name`);

--
-- Indexes for table `lead_pipeline_stages`
--
ALTER TABLE `lead_pipeline_stages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lead_pipeline_stages_code_lead_pipeline_id_unique` (`code`,`lead_pipeline_id`),
  ADD UNIQUE KEY `lead_pipeline_stages_name_lead_pipeline_id_unique` (`name`,`lead_pipeline_id`),
  ADD KEY `lead_pipeline_stages_lead_pipeline_id_foreign` (`lead_pipeline_id`);

--
-- Indexes for table `lead_priorities`
--
ALTER TABLE `lead_priorities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lead_products`
--
ALTER TABLE `lead_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_products_lead_id_foreign` (`lead_id`),
  ADD KEY `lead_products_product_id_foreign` (`product_id`);

--
-- Indexes for table `lead_quotes`
--
ALTER TABLE `lead_quotes`
  ADD KEY `lead_quotes_quote_id_foreign` (`quote_id`),
  ADD KEY `lead_quotes_lead_id_foreign` (`lead_id`);

--
-- Indexes for table `lead_sources`
--
ALTER TABLE `lead_sources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lead_stages`
--
ALTER TABLE `lead_stages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lead_tags`
--
ALTER TABLE `lead_tags`
  ADD KEY `lead_tags_tag_id_foreign` (`tag_id`),
  ADD KEY `lead_tags_lead_id_foreign` (`lead_id`);

--
-- Indexes for table `lead_types`
--
ALTER TABLE `lead_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marketing_campaigns`
--
ALTER TABLE `marketing_campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marketing_campaigns_marketing_template_id_foreign` (`marketing_template_id`),
  ADD KEY `marketing_campaigns_marketing_event_id_foreign` (`marketing_event_id`);

--
-- Indexes for table `marketing_events`
--
ALTER TABLE `marketing_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `organizations_name_unique` (`name`),
  ADD KEY `organizations_user_id_foreign` (`user_id`),
  ADD KEY `organizations_parent_organization_id_foreign` (`parent_organization_id`);

--
-- Indexes for table `organization_activities`
--
ALTER TABLE `organization_activities`
  ADD PRIMARY KEY (`activity_id`,`organization_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `persons`
--
ALTER TABLE `persons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `persons_unique_id_unique` (`unique_id`),
  ADD KEY `persons_user_id_foreign` (`user_id`),
  ADD KEY `persons_organization_id_foreign` (`organization_id`);

--
-- Indexes for table `person_activities`
--
ALTER TABLE `person_activities`
  ADD KEY `person_activities_activity_id_foreign` (`activity_id`),
  ADD KEY `person_activities_person_id_foreign` (`person_id`);

--
-- Indexes for table `person_tags`
--
ALTER TABLE `person_tags`
  ADD KEY `person_tags_tag_id_foreign` (`tag_id`),
  ADD KEY `person_tags_person_id_foreign` (`person_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`),
  ADD KEY `products_customer_organization_id_foreign` (`customer_organization_id`);

--
-- Indexes for table `product_activities`
--
ALTER TABLE `product_activities`
  ADD KEY `product_activities_activity_id_foreign` (`activity_id`),
  ADD KEY `product_activities_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_colors_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_consumptions`
--
ALTER TABLE `product_consumptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_consumptions_product_id_index` (`product_id`);

--
-- Indexes for table `product_inventories`
--
ALTER TABLE `product_inventories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_inventories_product_id_foreign` (`product_id`),
  ADD KEY `product_inventories_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `product_inventories_warehouse_location_id_foreign` (`warehouse_location_id`);

--
-- Indexes for table `product_key_points`
--
ALTER TABLE `product_key_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_key_points_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_other_images`
--
ALTER TABLE `product_other_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_other_images_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_pricing_charts`
--
ALTER TABLE `product_pricing_charts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_pricing_charts_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_pricing_chart_tiers`
--
ALTER TABLE `product_pricing_chart_tiers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_pricing_chart_tiers_chart_id_foreign` (`product_pricing_chart_id`);

--
-- Indexes for table `product_pricing_chart_types`
--
ALTER TABLE `product_pricing_chart_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_pricing_chart_types_chart_id_foreign` (`product_pricing_chart_id`);

--
-- Indexes for table `product_production_sections`
--
ALTER TABLE `product_production_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_production_sections_product_id_index` (`product_id`);

--
-- Indexes for table `product_production_section_items`
--
ALTER TABLE `product_production_section_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ppsi_section_id_index` (`product_production_section_id`);

--
-- Indexes for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD KEY `product_tags_tag_id_foreign` (`tag_id`),
  ADD KEY `product_tags_product_id_foreign` (`product_id`);

--
-- Indexes for table `proforma_invoices`
--
ALTER TABLE `proforma_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `proforma_invoices_proforma_number_unique` (`proforma_number`),
  ADD KEY `proforma_invoices_quote_id_foreign` (`quote_id`),
  ADD KEY `proforma_invoices_organization_id_foreign` (`organization_id`),
  ADD KEY `proforma_invoices_person_id_foreign` (`person_id`),
  ADD KEY `proforma_invoices_sales_owner_id_foreign` (`sales_owner_id`),
  ADD KEY `proforma_invoices_created_by_foreign` (`created_by`),
  ADD KEY `proforma_invoices_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `proforma_invoice_items`
--
ALTER TABLE `proforma_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proforma_invoice_items_proforma_invoice_id_foreign` (`proforma_invoice_id`),
  ADD KEY `proforma_invoice_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `proforma_receipts`
--
ALTER TABLE `proforma_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proforma_receipts_proforma_invoice_id_foreign` (`proforma_invoice_id`),
  ADD KEY `proforma_receipts_received_by_foreign` (`received_by`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`),
  ADD KEY `purchase_orders_person_id_foreign` (`person_id`),
  ADD KEY `purchase_orders_user_id_foreign` (`user_id`),
  ADD KEY `purchase_orders_organization_id_foreign` (`organization_id`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`);

--
-- Indexes for table `quotes`
--
ALTER TABLE `quotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotes_quote_number_unique` (`quote_number`),
  ADD KEY `quotes_person_id_foreign` (`person_id`),
  ADD KEY `quotes_user_id_foreign` (`user_id`),
  ADD KEY `quotes_organization_id_foreign` (`organization_id`);

--
-- Indexes for table `quote_items`
--
ALTER TABLE `quote_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quote_items_quote_id_foreign` (`quote_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tags_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- Indexes for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD KEY `user_groups_group_id_foreign` (`group_id`),
  ADD KEY `user_groups_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_password_resets`
--
ALTER TABLE `user_password_resets`
  ADD KEY `user_password_resets_email_index` (`email`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warehouse_activities`
--
ALTER TABLE `warehouse_activities`
  ADD KEY `warehouse_activities_activity_id_foreign` (`activity_id`),
  ADD KEY `warehouse_activities_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `warehouse_locations`
--
ALTER TABLE `warehouse_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warehouse_locations_warehouse_id_name_unique` (`warehouse_id`,`name`);

--
-- Indexes for table `warehouse_tags`
--
ALTER TABLE `warehouse_tags`
  ADD KEY `warehouse_tags_tag_id_foreign` (`tag_id`),
  ADD KEY `warehouse_tags_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `webhooks`
--
ALTER TABLE `webhooks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `web_forms`
--
ALTER TABLE `web_forms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `web_forms_form_id_unique` (`form_id`);

--
-- Indexes for table `web_form_attributes`
--
ALTER TABLE `web_form_attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `web_form_attributes_attribute_id_foreign` (`attribute_id`),
  ADD KEY `web_form_attributes_web_form_id_foreign` (`web_form_id`);

--
-- Indexes for table `workflows`
--
ALTER TABLE `workflows`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=269;

--
-- AUTO_INCREMENT for table `activity_files`
--
ALTER TABLE `activity_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `activity_participants`
--
ALTER TABLE `activity_participants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `attribute_options`
--
ALTER TABLE `attribute_options`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `attribute_values`
--
ALTER TABLE `attribute_values`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `core_config`
--
ALTER TABLE `core_config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=256;

--
-- AUTO_INCREMENT for table `country_states`
--
ALTER TABLE `country_states`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=569;

--
-- AUTO_INCREMENT for table `datagrid_saved_filters`
--
ALTER TABLE `datagrid_saved_filters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emails`
--
ALTER TABLE `emails`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `email_attachments`
--
ALTER TABLE `email_attachments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imports`
--
ALTER TABLE `imports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `import_batches`
--
ALTER TABLE `import_batches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lead_pipelines`
--
ALTER TABLE `lead_pipelines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lead_pipeline_stages`
--
ALTER TABLE `lead_pipeline_stages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lead_priorities`
--
ALTER TABLE `lead_priorities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lead_products`
--
ALTER TABLE `lead_products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_sources`
--
ALTER TABLE `lead_sources`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lead_stages`
--
ALTER TABLE `lead_stages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_types`
--
ALTER TABLE `lead_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `marketing_campaigns`
--
ALTER TABLE `marketing_campaigns`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketing_events`
--
ALTER TABLE `marketing_events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `persons`
--
ALTER TABLE `persons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `product_colors`
--
ALTER TABLE `product_colors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=818;

--
-- AUTO_INCREMENT for table `product_consumptions`
--
ALTER TABLE `product_consumptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product_inventories`
--
ALTER TABLE `product_inventories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_key_points`
--
ALTER TABLE `product_key_points`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT for table `product_other_images`
--
ALTER TABLE `product_other_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `product_pricing_charts`
--
ALTER TABLE `product_pricing_charts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `product_pricing_chart_tiers`
--
ALTER TABLE `product_pricing_chart_tiers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=506;

--
-- AUTO_INCREMENT for table `product_pricing_chart_types`
--
ALTER TABLE `product_pricing_chart_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `product_production_sections`
--
ALTER TABLE `product_production_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product_production_section_items`
--
ALTER TABLE `product_production_section_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `proforma_invoices`
--
ALTER TABLE `proforma_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proforma_invoice_items`
--
ALTER TABLE `proforma_invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proforma_receipts`
--
ALTER TABLE `proforma_receipts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `quotes`
--
ALTER TABLE `quotes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quote_items`
--
ALTER TABLE `quote_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouse_locations`
--
ALTER TABLE `warehouse_locations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `webhooks`
--
ALTER TABLE `webhooks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_forms`
--
ALTER TABLE `web_forms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_form_attributes`
--
ALTER TABLE `web_form_attributes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workflows`
--
ALTER TABLE `workflows`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_files`
--
ALTER TABLE `activity_files`
  ADD CONSTRAINT `activity_files_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_participants`
--
ALTER TABLE `activity_participants`
  ADD CONSTRAINT `activity_participants_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_participants_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attribute_options`
--
ALTER TABLE `attribute_options`
  ADD CONSTRAINT `attribute_options_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD CONSTRAINT `attribute_values_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `country_states`
--
ALTER TABLE `country_states`
  ADD CONSTRAINT `country_states_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emails`
--
ALTER TABLE `emails`
  ADD CONSTRAINT `emails_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `emails_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `emails` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `emails_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `email_attachments`
--
ALTER TABLE `email_attachments`
  ADD CONSTRAINT `email_attachments_email_id_foreign` FOREIGN KEY (`email_id`) REFERENCES `emails` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `email_tags`
--
ALTER TABLE `email_tags`
  ADD CONSTRAINT `email_tags_email_id_foreign` FOREIGN KEY (`email_id`) REFERENCES `emails` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `email_tags_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `import_batches`
--
ALTER TABLE `import_batches`
  ADD CONSTRAINT `import_batches_import_id_foreign` FOREIGN KEY (`import_id`) REFERENCES `imports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_lead_pipeline_id_foreign` FOREIGN KEY (`lead_pipeline_id`) REFERENCES `lead_pipelines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leads_lead_pipeline_stage_id_foreign` FOREIGN KEY (`lead_pipeline_stage_id`) REFERENCES `lead_pipeline_stages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_lead_source_id_foreign` FOREIGN KEY (`lead_source_id`) REFERENCES `lead_sources` (`id`),
  ADD CONSTRAINT `leads_lead_type_id_foreign` FOREIGN KEY (`lead_type_id`) REFERENCES `lead_types` (`id`),
  ADD CONSTRAINT `leads_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`),
  ADD CONSTRAINT `leads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lead_activities`
--
ALTER TABLE `lead_activities`
  ADD CONSTRAINT `lead_activities_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_activities_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lead_pipeline_stages`
--
ALTER TABLE `lead_pipeline_stages`
  ADD CONSTRAINT `lead_pipeline_stages_lead_pipeline_id_foreign` FOREIGN KEY (`lead_pipeline_id`) REFERENCES `lead_pipelines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lead_products`
--
ALTER TABLE `lead_products`
  ADD CONSTRAINT `lead_products_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lead_quotes`
--
ALTER TABLE `lead_quotes`
  ADD CONSTRAINT `lead_quotes_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_quotes_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lead_tags`
--
ALTER TABLE `lead_tags`
  ADD CONSTRAINT `lead_tags_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_tags_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marketing_campaigns`
--
ALTER TABLE `marketing_campaigns`
  ADD CONSTRAINT `marketing_campaigns_marketing_event_id_foreign` FOREIGN KEY (`marketing_event_id`) REFERENCES `marketing_events` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `marketing_campaigns_marketing_template_id_foreign` FOREIGN KEY (`marketing_template_id`) REFERENCES `email_templates` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `organizations`
--
ALTER TABLE `organizations`
  ADD CONSTRAINT `organizations_parent_organization_id_foreign` FOREIGN KEY (`parent_organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `organizations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `organization_activities`
--
ALTER TABLE `organization_activities`
  ADD CONSTRAINT `organization_activities_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `persons`
--
ALTER TABLE `persons`
  ADD CONSTRAINT `persons_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `persons_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `person_activities`
--
ALTER TABLE `person_activities`
  ADD CONSTRAINT `person_activities_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `person_activities_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `person_tags`
--
ALTER TABLE `person_tags`
  ADD CONSTRAINT `person_tags_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `person_tags_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_customer_organization_id_foreign` FOREIGN KEY (`customer_organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_activities`
--
ALTER TABLE `product_activities`
  ADD CONSTRAINT `product_activities_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_activities_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD CONSTRAINT `product_colors_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_consumptions`
--
ALTER TABLE `product_consumptions`
  ADD CONSTRAINT `product_consumptions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_inventories`
--
ALTER TABLE `product_inventories`
  ADD CONSTRAINT `product_inventories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_inventories_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_inventories_warehouse_location_id_foreign` FOREIGN KEY (`warehouse_location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_key_points`
--
ALTER TABLE `product_key_points`
  ADD CONSTRAINT `product_key_points_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_other_images`
--
ALTER TABLE `product_other_images`
  ADD CONSTRAINT `product_other_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_pricing_charts`
--
ALTER TABLE `product_pricing_charts`
  ADD CONSTRAINT `product_pricing_charts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_pricing_chart_tiers`
--
ALTER TABLE `product_pricing_chart_tiers`
  ADD CONSTRAINT `product_pricing_chart_tiers_chart_id_foreign` FOREIGN KEY (`product_pricing_chart_id`) REFERENCES `product_pricing_charts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_pricing_chart_types`
--
ALTER TABLE `product_pricing_chart_types`
  ADD CONSTRAINT `product_pricing_chart_types_chart_id_foreign` FOREIGN KEY (`product_pricing_chart_id`) REFERENCES `product_pricing_charts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_production_sections`
--
ALTER TABLE `product_production_sections`
  ADD CONSTRAINT `product_production_sections_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_production_section_items`
--
ALTER TABLE `product_production_section_items`
  ADD CONSTRAINT `pps_items_section_id_foreign` FOREIGN KEY (`product_production_section_id`) REFERENCES `product_production_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_tags`
--
ALTER TABLE `product_tags`
  ADD CONSTRAINT `product_tags_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_tags_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `proforma_invoices`
--
ALTER TABLE `proforma_invoices`
  ADD CONSTRAINT `proforma_invoices_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `proforma_invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `proforma_invoices_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `proforma_invoices_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `proforma_invoices_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `proforma_invoices_sales_owner_id_foreign` FOREIGN KEY (`sales_owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `proforma_invoice_items`
--
ALTER TABLE `proforma_invoice_items`
  ADD CONSTRAINT `proforma_invoice_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `proforma_invoice_items_proforma_invoice_id_foreign` FOREIGN KEY (`proforma_invoice_id`) REFERENCES `proforma_invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `proforma_receipts`
--
ALTER TABLE `proforma_receipts`
  ADD CONSTRAINT `proforma_receipts_proforma_invoice_id_foreign` FOREIGN KEY (`proforma_invoice_id`) REFERENCES `proforma_invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `proforma_receipts_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotes`
--
ALTER TABLE `quotes`
  ADD CONSTRAINT `quotes_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quotes_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quotes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quote_items`
--
ALTER TABLE `quote_items`
  ADD CONSTRAINT `quote_items_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tags`
--
ALTER TABLE `tags`
  ADD CONSTRAINT `tags_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD CONSTRAINT `user_groups_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_groups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warehouse_activities`
--
ALTER TABLE `warehouse_activities`
  ADD CONSTRAINT `warehouse_activities_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warehouse_activities_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warehouse_locations`
--
ALTER TABLE `warehouse_locations`
  ADD CONSTRAINT `warehouse_locations_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warehouse_tags`
--
ALTER TABLE `warehouse_tags`
  ADD CONSTRAINT `warehouse_tags_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warehouse_tags_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `web_form_attributes`
--
ALTER TABLE `web_form_attributes`
  ADD CONSTRAINT `web_form_attributes_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `web_form_attributes_web_form_id_foreign` FOREIGN KEY (`web_form_id`) REFERENCES `web_forms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
