-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 12, 2026 at 07:48 PM
-- Server version: 8.4.3
-- PHP Version: 8.2.28

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
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `additional` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `schedule_from` datetime DEFAULT NULL,
  `schedule_to` datetime DEFAULT NULL,
  `is_done` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int UNSIGNED DEFAULT NULL,
  `entity_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `title`, `type`, `comment`, `additional`, `schedule_from`, `schedule_to`, `is_done`, `user_id`, `entity_type`, `entity_id`, `created_at`, `updated_at`, `location`) VALUES
(393, 'abc', 'file', '', NULL, NULL, NULL, 0, 1, 'persons', 12, '2026-05-17 11:33:59', '2026-05-17 11:33:59', NULL),
(394, 'abc', 'file', '', NULL, NULL, NULL, 0, 1, 'organizations', 3, '2026-05-17 11:34:57', '2026-05-17 11:34:57', NULL),
(402, 'hh', 'file', '', NULL, NULL, NULL, 0, 1, 'organizations', 3, '2026-05-17 11:39:50', '2026-05-17 11:39:50', NULL),
(403, 'fdjkfkj', 'file', '', NULL, NULL, NULL, 0, 1, 'organizations', 3, '2026-05-17 11:40:05', '2026-05-17 11:40:05', NULL),
(404, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 11:52:35', '2026-05-17 11:52:35', NULL),
(405, 'Updated Title', 'system', NULL, '{\"attribute\":\"Title\",\"new\":{\"value\":\"ABC\",\"label\":\"ABC\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 11:52:35', '2026-05-17 11:52:35', NULL),
(406, 'Updated Description', 'system', NULL, '{\"attribute\":\"Description\",\"new\":{\"value\":\"ABC\",\"label\":\"ABC\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 11:52:35', '2026-05-17 11:52:35', NULL),
(407, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Example Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 11:52:35', '2026-05-17 11:52:35', NULL),
(408, 'Updated Pipeline', 'system', NULL, '{\"attribute\":\"Pipeline\",\"new\":{\"value\":1,\"label\":\"Default Pipeline\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 11:52:35', '2026-05-17 11:52:35', NULL),
(409, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":1,\"label\":\"New\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 11:52:35', '2026-05-17 11:52:35', NULL),
(410, 'Updated Source', 'system', NULL, '{\"attribute\":\"Source\",\"new\":{\"value\":3,\"label\":\"Web Form\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 12:08:16', '2026-05-17 12:08:16', NULL),
(419, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 12:23:08', '2026-05-17 12:23:08', NULL),
(420, 'Updated Title', 'system', NULL, '{\"attribute\":\"Title\",\"new\":{\"value\":\"fdf\",\"label\":\"fdf\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 12:23:08', '2026-05-17 12:23:08', NULL),
(421, 'Updated Description', 'system', NULL, '{\"attribute\":\"Description\",\"new\":{\"value\":\"df\",\"label\":\"df\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 12:23:08', '2026-05-17 12:23:08', NULL),
(422, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Example Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 12:23:08', '2026-05-17 12:23:08', NULL),
(423, 'Updated Pipeline', 'system', NULL, '{\"attribute\":\"Pipeline\",\"new\":{\"value\":1,\"label\":\"Default Pipeline\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 12:23:08', '2026-05-17 12:23:08', NULL),
(424, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":1,\"label\":\"New\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 12:23:08', '2026-05-17 12:23:08', NULL),
(425, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 12:25:45', '2026-05-17 12:25:45', NULL),
(426, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Zahir Rocha\",\"label\":\"Zahir Rocha\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 12:25:45', '2026-05-17 12:25:45', NULL),
(427, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Example Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 12:25:45', '2026-05-17 12:25:45', NULL),
(434, 'Updated organization_id', 'system', NULL, '{\"attribute\":\"organization_id\",\"new\":{\"value\":13,\"label\":13},\"old\":{\"value\":3,\"label\":3}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 12:43:27', '2026-05-17 12:43:27', NULL),
(435, 'Updated organization_id', 'system', NULL, '{\"attribute\":\"organization_id\",\"new\":{\"value\":13,\"label\":13},\"old\":{\"value\":3,\"label\":3}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 12:43:49', '2026-05-17 12:43:49', NULL),
(436, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:16:15', '2026-05-17 15:16:15', NULL),
(437, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Supplier Product\",\"label\":\"Supplier Product\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:16:15', '2026-05-17 15:16:15', NULL),
(438, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"ST00\",\"label\":\"ST00\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:16:15', '2026-05-17 15:16:15', NULL),
(439, 'Updated Price', 'system', NULL, '{\"attribute\":\"Price\",\"new\":{\"value\":14,\"label\":\"$14.00\"},\"old\":{\"value\":null,\"label\":\"$0.00\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:16:15', '2026-05-17 15:16:15', NULL),
(440, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Anika Dale\",\"label\":\"Anika Dale\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:17:23', '2026-05-17 15:17:23', NULL),
(441, 'Updated Address', 'system', NULL, '{\"attribute\":\"Address\",\"new\":{\"value\":[{\"city\":\"Ratione laborum quis\",\"country\":\"Modi est molestias d\",\"postcode\":\"Ad debitis similique\",\"state\":\"Culpa vitae aut pro\",\"street\":\"Molestias est sint e\",\"type\":\"billing\"},{\"city\":\"Ratione laborum quis\",\"country\":\"Modi est molestias d\",\"postcode\":\"Ad debitis similique\",\"state\":\"Culpa vitae aut pro\",\"street\":\"Molestias est sint e\",\"type\":\"shipping\"},{\"city\":\"Alias accusamus non\",\"country\":\"Tempore quia optio\",\"postcode\":\"Maiores non dolores\",\"state\":\"Quis reiciendis ulla\",\"street\":\"Facilis sed esse und\",\"type\":\"shipping\"},{\"city\":\"Fugit maxime incidi\",\"country\":\"Non exercitationem l\",\"postcode\":\"Quo incidunt libero\",\"state\":\"Irure officia quod q\",\"street\":\"Laudantium ipsam vo\",\"type\":\"billing\"}],\"label\":\"<br>  <br><br><br>\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:17:23', '2026-05-17 15:17:23', NULL),
(442, 'hh', 'call', '', NULL, NULL, NULL, 0, 1, 'organizations', 14, '2026-05-17 15:17:40', '2026-05-17 15:17:40', NULL),
(443, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:20', '2026-05-17 15:18:20', NULL),
(444, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Liberty Matthews\",\"label\":\"Liberty Matthews\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:20', '2026-05-17 15:18:20', NULL),
(445, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Example Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:20', '2026-05-17 15:18:20', NULL),
(446, 'Updated Organization', 'system', NULL, '{\"attribute\":\"Organization\",\"new\":{\"value\":14,\"label\":\"Anika Dale\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:20', '2026-05-17 15:18:20', NULL),
(447, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:29', '2026-05-17 15:18:29', NULL),
(448, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Candice Bullock\",\"label\":\"Candice Bullock\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:29', '2026-05-17 15:18:29', NULL),
(449, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Example Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:29', '2026-05-17 15:18:29', NULL),
(450, 'Updated Organization', 'system', NULL, '{\"attribute\":\"Organization\",\"new\":{\"value\":14,\"label\":\"Anika Dale\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:29', '2026-05-17 15:18:29', NULL),
(451, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:34', '2026-05-17 15:18:34', NULL),
(452, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Rinah Christian\",\"label\":\"Rinah Christian\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:34', '2026-05-17 15:18:34', NULL),
(453, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Example Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:34', '2026-05-17 15:18:34', NULL),
(454, 'Updated Organization', 'system', NULL, '{\"attribute\":\"Organization\",\"new\":{\"value\":14,\"label\":\"Anika Dale\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:34', '2026-05-17 15:18:34', NULL),
(455, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:41', '2026-05-17 15:18:41', NULL),
(456, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Ulysses Velazquez\",\"label\":\"Ulysses Velazquez\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:41', '2026-05-17 15:18:41', NULL),
(457, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Example Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:41', '2026-05-17 15:18:41', NULL),
(458, 'Updated Organization', 'system', NULL, '{\"attribute\":\"Organization\",\"new\":{\"value\":14,\"label\":\"Anika Dale\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:18:41', '2026-05-17 15:18:41', NULL),
(459, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:19:08', '2026-05-17 15:19:08', NULL),
(460, 'Updated Title', 'system', NULL, '{\"attribute\":\"Title\",\"new\":{\"value\":\"dsf\",\"label\":\"dsf\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:19:08', '2026-05-17 15:19:08', NULL),
(461, 'Updated Description', 'system', NULL, '{\"attribute\":\"Description\",\"new\":{\"value\":\"sdf\",\"label\":\"sdf\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:19:08', '2026-05-17 15:19:08', NULL),
(462, 'Updated Source', 'system', NULL, '{\"attribute\":\"Source\",\"new\":{\"value\":2,\"label\":\"Web\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:19:08', '2026-05-17 15:19:08', NULL),
(463, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Example Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:19:08', '2026-05-17 15:19:08', NULL),
(464, 'Updated Pipeline', 'system', NULL, '{\"attribute\":\"Pipeline\",\"new\":{\"value\":1,\"label\":\"Default Pipeline\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:19:08', '2026-05-17 15:19:08', NULL),
(465, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":4,\"label\":\"Negotiation\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:19:08', '2026-05-17 15:19:08', NULL),
(466, 'yu', 'file', '', NULL, NULL, NULL, 0, 1, 'leads', 21, '2026-05-17 15:19:35', '2026-05-17 15:19:35', NULL),
(467, '', 'file', '', NULL, NULL, NULL, 0, 1, 'persons', 18, '2026-05-17 15:20:37', '2026-05-17 15:20:37', NULL),
(468, 'task new', 'task', NULL, NULL, '2026-05-18 00:00:00', '2026-05-18 00:00:00', 0, 1, 'organizations', 14, '2026-05-17 15:21:20', '2026-05-17 15:21:20', NULL),
(469, NULL, 'note', 'dfdf', NULL, NULL, NULL, 1, 1, 'organizations', 14, '2026-05-17 15:21:35', '2026-05-17 15:21:35', NULL),
(470, 'New upcoming Event Calendar Check', 'meeting', '', NULL, '2026-05-18 19:51:00', '2026-05-20 19:51:00', 0, 1, 'organizations', 14, '2026-05-17 15:22:29', '2026-05-17 15:22:29', NULL),
(471, 'Created', 'system', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:24:56', '2026-05-17 15:24:56', NULL),
(472, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Xandra Frazier\",\"label\":\"Xandra Frazier\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:24:56', '2026-05-17 15:24:56', NULL),
(473, 'Updated Sales Owner', 'system', NULL, '{\"attribute\":\"Sales Owner\",\"new\":{\"value\":1,\"label\":\"Example Admin\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:24:56', '2026-05-17 15:24:56', NULL),
(474, 'Updated Organization', 'system', NULL, '{\"attribute\":\"Organization\",\"new\":{\"value\":12,\"label\":\"Vendor 10\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:24:56', '2026-05-17 15:24:56', NULL),
(475, '', 'file', '', NULL, NULL, NULL, 0, 1, 'persons', 19, '2026-05-17 15:25:07', '2026-05-17 15:25:07', NULL),
(476, 'Updated weight', 'system', NULL, '{\"attribute\":\"weight\",\"new\":{\"value\":13,\"label\":13},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:30:54', '2026-05-17 15:30:54', NULL),
(477, 'Updated weight_unit', 'system', NULL, '{\"attribute\":\"weight_unit\",\"new\":{\"value\":\"gsm\",\"label\":\"gsm\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:30:54', '2026-05-17 15:30:54', NULL),
(478, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Cotton Tote Bag\",\"label\":\"Cotton Tote Bag\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:30:54', '2026-05-17 15:30:54', NULL),
(479, 'Updated SKU', 'system', NULL, '{\"attribute\":\"SKU\",\"new\":{\"value\":\"MQTB\",\"label\":\"MQTB\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:30:54', '2026-05-17 15:30:54', NULL),
(480, 'Updated Price', 'system', NULL, '{\"attribute\":\"Price\",\"new\":{\"value\":4.72,\"label\":\"$4.72\"},\"old\":{\"value\":null,\"label\":\"$0.00\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-17 15:30:54', '2026-05-17 15:30:54', NULL),
(481, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":2,\"label\":\"Follow Up\"},\"old\":{\"value\":1,\"label\":\"New\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:05', '2026-05-19 13:55:05', NULL),
(482, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":2,\"label\":\"Follow Up\"},\"old\":{\"value\":1,\"label\":\"New\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:07', '2026-05-19 13:55:07', NULL),
(483, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":1,\"label\":\"New\"},\"old\":{\"value\":2,\"label\":\"Follow Up\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:08', '2026-05-19 13:55:08', NULL),
(484, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":1,\"label\":\"New\"},\"old\":{\"value\":2,\"label\":\"Follow Up\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:09', '2026-05-19 13:55:09', NULL),
(485, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":2,\"label\":\"Follow Up\"},\"old\":{\"value\":1,\"label\":\"New\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:11', '2026-05-19 13:55:11', NULL),
(486, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":2,\"label\":\"Follow Up\"},\"old\":{\"value\":1,\"label\":\"New\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:12', '2026-05-19 13:55:12', NULL),
(487, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":1,\"label\":\"New\"},\"old\":{\"value\":2,\"label\":\"Follow Up\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:13', '2026-05-19 13:55:13', NULL),
(488, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":1,\"label\":\"New\"},\"old\":{\"value\":2,\"label\":\"Follow Up\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:14', '2026-05-19 13:55:14', NULL),
(489, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":2,\"label\":\"Follow Up\"},\"old\":{\"value\":1,\"label\":\"New\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:17', '2026-05-19 13:55:17', NULL),
(490, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":2,\"label\":\"Follow Up\"},\"old\":{\"value\":1,\"label\":\"New\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:18', '2026-05-19 13:55:18', NULL),
(491, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":1,\"label\":\"New\"},\"old\":{\"value\":2,\"label\":\"Follow Up\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:20', '2026-05-19 13:55:20', NULL),
(492, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":3,\"label\":\"Prospect\"},\"old\":{\"value\":2,\"label\":\"Follow Up\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:21', '2026-05-19 13:55:21', NULL),
(493, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":2,\"label\":\"Follow Up\"},\"old\":{\"value\":4,\"label\":\"Negotiation\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:22', '2026-05-19 13:55:22', NULL),
(494, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":4,\"label\":\"Negotiation\"},\"old\":{\"value\":2,\"label\":\"Follow Up\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:24', '2026-05-19 13:55:24', NULL),
(495, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":2,\"label\":\"Follow Up\"},\"old\":{\"value\":4,\"label\":\"Negotiation\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:25', '2026-05-19 13:55:25', NULL),
(496, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":4,\"label\":\"Negotiation\"},\"old\":{\"value\":3,\"label\":\"Prospect\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:26', '2026-05-19 13:55:26', NULL),
(497, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":3,\"label\":\"Prospect\"},\"old\":{\"value\":4,\"label\":\"Negotiation\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:27', '2026-05-19 13:55:27', NULL),
(498, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":1,\"label\":\"New\"},\"old\":{\"value\":2,\"label\":\"Follow Up\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:30', '2026-05-19 13:55:30', NULL),
(499, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":1,\"label\":\"New\"},\"old\":{\"value\":3,\"label\":\"Prospect\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:31', '2026-05-19 13:55:31', NULL),
(500, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":2,\"label\":\"Follow Up\"},\"old\":{\"value\":1,\"label\":\"New\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:33', '2026-05-19 13:55:33', NULL),
(501, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":2,\"label\":\"Follow Up\"},\"old\":{\"value\":1,\"label\":\"New\"}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 13:55:35', '2026-05-19 13:55:35', NULL),
(502, 'Updated Stage', 'system', NULL, '{\"attribute\":\"Stage\",\"new\":{\"value\":4,\"label\":\"Negotiation\"},\"old\":{\"value\":1,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-19 15:18:29', '2026-05-19 15:18:29', NULL),
(503, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Deveon\",\"label\":\"Deveon\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-20 04:18:10', '2026-05-20 04:18:10', NULL),
(504, 'Updated Address', 'system', NULL, '{\"attribute\":\"Address\",\"new\":{\"value\":\"[]\",\"label\":null},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-05-20 04:18:10', '2026-05-20 04:18:10', NULL),
(505, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"Testttttt\",\"label\":\"Testttttt\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-07-12 19:10:16', '2026-07-12 19:10:16', NULL),
(506, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"fgfg\",\"label\":\"fgfg\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-07-12 19:11:27', '2026-07-12 19:11:27', NULL),
(507, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"sdsd\",\"label\":\"sdsd\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-07-12 19:12:09', '2026-07-12 19:12:09', NULL),
(508, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"fdsfdsff\",\"label\":\"fdsfdsff\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-07-12 19:49:14', '2026-07-12 19:49:14', NULL),
(509, 'Updated Name', 'system', NULL, '{\"attribute\":\"Name\",\"new\":{\"value\":\"fhtutfhdfhrtu\",\"label\":\"fhtutfhdfhrtu\"},\"old\":{\"value\":null,\"label\":null}}', NULL, NULL, 1, 1, NULL, NULL, '2026-07-12 19:49:26', '2026-07-12 19:49:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `activity_files`
--

CREATE TABLE `activity_files` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activity_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_files`
--

INSERT INTO `activity_files` (`id`, `name`, `path`, `activity_id`, `created_at`, `updated_at`) VALUES
(26, 'abc.jpg', 'activities/393/3qZUIsTOw2ifILbdQFlIuNXGzMiFMNmHrvrmxKYd.jpg', 393, '2026-05-17 11:33:59', '2026-05-17 12:05:51'),
(27, '', 'activities/394/iiD4AbCicEPGGe79ZAHtoB8AXS4xlrAVZCYWK3Wt.jpg', 394, '2026-05-17 11:34:57', '2026-05-17 11:34:57'),
(28, 'test image.jpeg', 'activities/402/UhHMlznEXbBYLqK5LZNUZvXrI7qDXWf4ZNAIEym1.jpg', 402, '2026-05-17 11:39:50', '2026-05-17 11:39:50'),
(29, 'test image.jpeg', 'activities/403/QrALpOh87yHeaJgJS7ImKi9WBGY0rCMeCCJVWiVd.jpg', 403, '2026-05-17 11:40:05', '2026-05-17 11:40:05'),
(30, 'abc.jpg', 'activities/466/AcoNf74VCAW2WDM1qFS0jYMVhSyGDyQ6XYDyPUbx.jpg', 466, '2026-05-17 15:19:35', '2026-05-17 15:19:35'),
(31, 'abc.jpg', 'activities/467/OaWxDM7HwLfbvilG5POfwla4aLl1oEojYVpbPs84.jpg', 467, '2026-05-17 15:20:37', '2026-05-17 15:20:37'),
(32, 'abc (1).jpg', 'activities/475/cKMPMhD9PJ5eiKzXThs1UHdm3L4RHo9vzanEM4s3.jpg', 475, '2026-05-17 15:25:07', '2026-05-17 15:25:07');

-- --------------------------------------------------------

--
-- Table structure for table `activity_participants`
--

CREATE TABLE `activity_participants` (
  `id` int UNSIGNED NOT NULL,
  `activity_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `person_id` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_participants`
--

INSERT INTO `activity_participants` (`id`, `activity_id`, `user_id`, `person_id`) VALUES
(10, 442, NULL, 14),
(11, 468, 1, NULL),
(12, 470, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `id` int UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lookup_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int DEFAULT NULL,
  `validation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_unique` tinyint(1) NOT NULL DEFAULT '0',
  `quick_add` tinyint(1) NOT NULL DEFAULT '0',
  `is_user_defined` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `code`, `name`, `type`, `lookup_type`, `entity_type`, `sort_order`, `validation`, `is_required`, `is_unique`, `quick_add`, `is_user_defined`, `created_at`, `updated_at`) VALUES
(75, 'title', 'Title', 'text', NULL, 'leads', 1, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(76, 'description', 'Description', 'textarea', NULL, 'leads', 2, NULL, 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(77, 'lead_value', 'Lead Value', 'price', NULL, 'leads', 3, 'decimal', 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(78, 'lead_source_id', 'Source', 'select', 'lead_sources', 'leads', 4, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(79, 'lead_type_id', 'Type', 'select', 'lead_types', 'leads', 5, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(80, 'user_id', 'Sales Owner', 'select', 'users', 'leads', 7, NULL, 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(81, 'expected_close_date', 'Expected Close Date', 'date', NULL, 'leads', 8, NULL, 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(82, 'lead_pipeline_id', 'Pipeline', 'lookup', 'lead_pipelines', 'leads', 9, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(83, 'lead_pipeline_stage_id', 'Stage', 'lookup', 'lead_pipeline_stages', 'leads', 10, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(84, 'name', 'Name', 'text', NULL, 'persons', 1, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(85, 'emails', 'Emails', 'email', NULL, 'persons', 2, NULL, 1, 1, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(86, 'contact_numbers', 'Contact Numbers', 'phone', NULL, 'persons', 3, 'numeric', 0, 1, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(87, 'job_title', 'Job Title', 'text', NULL, 'persons', 4, NULL, 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(88, 'user_id', 'Sales Owner', 'lookup', 'users', 'persons', 5, NULL, 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(89, 'organization_id', 'Organization', 'lookup', 'organizations', 'persons', 6, NULL, 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(90, 'name', 'Name', 'text', NULL, 'organizations', 1, NULL, 1, 1, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(91, 'address', 'Address', 'address', NULL, 'organizations', 2, NULL, 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(92, 'user_id', 'Sales Owner', 'lookup', 'users', 'organizations', 3, NULL, 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(93, 'name', 'Name', 'text', NULL, 'products', 1, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(94, 'description', 'Description', 'textarea', NULL, 'products', 2, NULL, 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(95, 'sku', 'SKU', 'text', NULL, 'products', 3, NULL, 1, 1, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(96, 'quantity', 'Quantity', 'text', NULL, 'products', 4, 'numeric', 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(97, 'price', 'Price', 'price', NULL, 'products', 5, 'decimal', 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(98, 'user_id', 'Sales Owner', 'select', 'users', 'quotes', 1, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(99, 'subject', 'Subject', 'text', NULL, 'quotes', 2, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(100, 'description', 'Description', 'textarea', NULL, 'quotes', 3, NULL, 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(101, 'billing_address', 'Billing Address', 'address', NULL, 'quotes', 4, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(102, 'shipping_address', 'Shipping Address', 'address', NULL, 'quotes', 5, NULL, 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(103, 'discount_percent', 'Discount Percent', 'text', NULL, 'quotes', 6, 'decimal', 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(104, 'discount_amount', 'Discount Amount', 'price', NULL, 'quotes', 7, 'decimal', 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(105, 'tax_amount', 'Tax Amount', 'price', NULL, 'quotes', 8, 'decimal', 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(106, 'adjustment_amount', 'Adjustment Amount', 'price', NULL, 'quotes', 9, 'decimal', 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(107, 'sub_total', 'Sub Total', 'price', NULL, 'quotes', 10, 'decimal', 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(108, 'grand_total', 'Grand Total', 'price', NULL, 'quotes', 11, 'decimal', 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(109, 'expired_at', 'Expired At', 'date', NULL, 'quotes', 12, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(110, 'person_id', 'Person', 'lookup', 'persons', 'quotes', 13, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(111, 'name', 'Name', 'text', NULL, 'warehouses', 1, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(112, 'description', 'Description', 'textarea', NULL, 'warehouses', 2, NULL, 0, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(113, 'contact_name', 'Contact Name', 'text', NULL, 'warehouses', 3, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(114, 'contact_emails', 'Contact Emails', 'email', NULL, 'warehouses', 4, NULL, 1, 1, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(115, 'contact_numbers', 'Contact Numbers', 'phone', NULL, 'warehouses', 5, 'numeric', 0, 1, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(116, 'contact_address', 'Contact Address', 'address', NULL, 'warehouses', 6, NULL, 1, 0, 1, 0, '2026-05-17 11:32:05', '2026-05-17 11:32:05');

-- --------------------------------------------------------

--
-- Table structure for table `attribute_options`
--

CREATE TABLE `attribute_options` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT NULL,
  `attribute_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attribute_values`
--

CREATE TABLE `attribute_values` (
  `id` int UNSIGNED NOT NULL,
  `entity_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'leads',
  `text_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `boolean_value` tinyint(1) DEFAULT NULL,
  `integer_value` int DEFAULT NULL,
  `float_value` double DEFAULT NULL,
  `datetime_value` datetime DEFAULT NULL,
  `date_value` date DEFAULT NULL,
  `json_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `entity_id` int UNSIGNED NOT NULL,
  `attribute_id` int UNSIGNED NOT NULL,
  `unique_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attribute_values`
--

INSERT INTO `attribute_values` (`id`, `entity_type`, `text_value`, `boolean_value`, `integer_value`, `float_value`, `datetime_value`, `date_value`, `json_value`, `entity_id`, `attribute_id`, `unique_id`) VALUES
(404, 'leads', 'ABC', NULL, NULL, NULL, NULL, NULL, NULL, 16, 75, NULL),
(405, 'leads', 'ABC', NULL, NULL, NULL, NULL, NULL, NULL, 16, 76, NULL),
(406, 'leads', NULL, NULL, 3, NULL, NULL, NULL, NULL, 16, 78, NULL),
(407, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 16, 80, NULL),
(408, 'leads', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 16, 81, NULL),
(409, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 16, 82, NULL),
(410, 'leads', NULL, NULL, 4, NULL, NULL, NULL, NULL, 16, 83, NULL),
(416, 'leads', 'fdf', NULL, NULL, NULL, NULL, NULL, NULL, 19, 75, NULL),
(417, 'leads', 'df', NULL, NULL, NULL, NULL, NULL, NULL, 19, 76, NULL),
(418, 'leads', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 19, 78, NULL),
(419, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 19, 80, NULL),
(420, 'leads', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 19, 81, NULL),
(421, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 19, 82, NULL),
(422, 'leads', NULL, NULL, 2, NULL, NULL, NULL, NULL, 19, 83, NULL),
(423, 'persons', 'Zahir Rocha', NULL, NULL, NULL, NULL, NULL, NULL, 14, 84, NULL),
(424, 'persons', NULL, NULL, 1, NULL, NULL, NULL, NULL, 14, 88, NULL),
(425, 'persons', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 14, 89, NULL),
(431, 'products', 'Supplier Product', NULL, NULL, NULL, NULL, NULL, NULL, 34, 93, NULL),
(432, 'products', 'ST00', NULL, NULL, NULL, NULL, NULL, NULL, 34, 95, NULL),
(433, 'products', NULL, NULL, NULL, 14, NULL, NULL, NULL, 34, 97, NULL),
(434, 'organizations', 'Anika Dale', NULL, NULL, NULL, NULL, NULL, NULL, 14, 90, NULL),
(435, 'organizations', NULL, NULL, NULL, NULL, NULL, NULL, '[{\"type\":\"billing\",\"street\":\"Molestias est sint e\",\"city\":\"Ratione laborum quis\",\"state\":\"Culpa vitae aut pro\",\"postcode\":\"Ad debitis similique\",\"country\":\"Modi est molestias d\"},{\"type\":\"shipping\",\"street\":\"Molestias est sint e\",\"city\":\"Ratione laborum quis\",\"state\":\"Culpa vitae aut pro\",\"postcode\":\"Ad debitis similique\",\"country\":\"Modi est molestias d\"},{\"type\":\"shipping\",\"street\":\"Facilis sed esse und\",\"city\":\"Alias accusamus non\",\"state\":\"Quis reiciendis ulla\",\"postcode\":\"Maiores non dolores\",\"country\":\"Tempore quia optio\"},{\"type\":\"billing\",\"street\":\"Laudantium ipsam vo\",\"city\":\"Fugit maxime incidi\",\"state\":\"Irure officia quod q\",\"postcode\":\"Quo incidunt libero\",\"country\":\"Non exercitationem l\"}]', 14, 91, NULL),
(436, 'persons', 'Liberty Matthews', NULL, NULL, NULL, NULL, NULL, NULL, 15, 84, NULL),
(437, 'persons', NULL, NULL, 1, NULL, NULL, NULL, NULL, 15, 88, NULL),
(438, 'persons', NULL, NULL, 14, NULL, NULL, NULL, NULL, 15, 89, NULL),
(439, 'persons', 'Candice Bullock', NULL, NULL, NULL, NULL, NULL, NULL, 16, 84, NULL),
(440, 'persons', NULL, NULL, 1, NULL, NULL, NULL, NULL, 16, 88, NULL),
(441, 'persons', NULL, NULL, 14, NULL, NULL, NULL, NULL, 16, 89, NULL),
(442, 'persons', 'Rinah Christian', NULL, NULL, NULL, NULL, NULL, NULL, 17, 84, NULL),
(443, 'persons', NULL, NULL, 1, NULL, NULL, NULL, NULL, 17, 88, NULL),
(444, 'persons', NULL, NULL, 14, NULL, NULL, NULL, NULL, 17, 89, NULL),
(445, 'persons', 'Ulysses Velazquez', NULL, NULL, NULL, NULL, NULL, NULL, 18, 84, NULL),
(446, 'persons', NULL, NULL, 1, NULL, NULL, NULL, NULL, 18, 88, NULL),
(447, 'persons', NULL, NULL, 14, NULL, NULL, NULL, NULL, 18, 89, NULL),
(448, 'leads', 'dsf', NULL, NULL, NULL, NULL, NULL, NULL, 21, 75, NULL),
(449, 'leads', 'sdf', NULL, NULL, NULL, NULL, NULL, NULL, 21, 76, NULL),
(450, 'leads', NULL, NULL, 2, NULL, NULL, NULL, NULL, 21, 78, NULL),
(451, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 21, 80, NULL),
(452, 'leads', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 21, 81, NULL),
(453, 'leads', NULL, NULL, 1, NULL, NULL, NULL, NULL, 21, 82, NULL),
(454, 'leads', NULL, NULL, 2, NULL, NULL, NULL, NULL, 21, 83, NULL),
(455, 'persons', 'Xandra Frazier', NULL, NULL, NULL, NULL, NULL, NULL, 19, 84, NULL),
(456, 'persons', NULL, NULL, 1, NULL, NULL, NULL, NULL, 19, 88, NULL),
(457, 'persons', NULL, NULL, 12, NULL, NULL, NULL, NULL, 19, 89, NULL),
(458, 'quotes', NULL, NULL, 1, NULL, NULL, NULL, NULL, 20, 98, NULL),
(459, 'quotes', 'Quote 000001', NULL, NULL, NULL, NULL, NULL, NULL, 20, 99, NULL),
(460, 'quotes', NULL, NULL, NULL, NULL, NULL, NULL, '{\"key\":\"billing\",\"label\":\"Billing Address\",\"type\":\"billing\",\"address\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\",\"street\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\",\"city\":\"Ottawa\",\"state\":\"Ontario\",\"postcode\":\"K1Z 7T1\",\"country\":\"Canada\"}', 20, 101, NULL),
(461, 'quotes', NULL, NULL, NULL, NULL, NULL, NULL, '{\"key\":\"shipping\",\"label\":\"Shipping Address\",\"type\":\"shipping\",\"address\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\",\"street\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\",\"city\":\"Ottawa\",\"state\":\"Ontario\",\"postcode\":\"K1Z 7T1\",\"country\":\"Canada\"}', 20, 102, NULL),
(462, 'quotes', NULL, NULL, NULL, 0, NULL, NULL, NULL, 20, 104, NULL),
(463, 'quotes', NULL, NULL, NULL, 1537.56, NULL, NULL, NULL, 20, 105, NULL),
(464, 'quotes', NULL, NULL, NULL, 200, NULL, NULL, NULL, 20, 106, NULL),
(465, 'quotes', NULL, NULL, NULL, 12813, NULL, NULL, NULL, 20, 107, NULL),
(466, 'quotes', NULL, NULL, NULL, 14550.56, NULL, NULL, NULL, 20, 108, NULL),
(467, 'products', 'Cotton Tote Bag', NULL, NULL, NULL, NULL, NULL, NULL, 21, 93, NULL),
(468, 'products', 'MQTB', NULL, NULL, NULL, NULL, NULL, NULL, 21, 95, NULL),
(469, 'products', NULL, NULL, NULL, 4.72, NULL, NULL, NULL, 21, 97, NULL),
(479, 'organizations', 'Deveon', NULL, NULL, NULL, NULL, NULL, NULL, 3, 90, NULL),
(480, 'organizations', NULL, NULL, NULL, NULL, NULL, NULL, '[]', 3, 91, NULL),
(499, 'organizations', 'Testttttt', NULL, NULL, NULL, NULL, NULL, NULL, 15, 90, NULL),
(500, 'organizations', 'fgfg', NULL, NULL, NULL, NULL, NULL, NULL, 16, 90, NULL),
(501, 'organizations', 'sdsd', NULL, NULL, NULL, NULL, NULL, NULL, 17, 90, NULL),
(502, 'organizations', 'fdsfdsff', NULL, NULL, NULL, NULL, NULL, NULL, 18, 90, NULL),
(503, 'organizations', 'fhtutfhdfhrtu', NULL, NULL, NULL, NULL, NULL, NULL, 19, 90, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `color_references`
--

CREATE TABLE `color_references` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `color_references`
--

INSERT INTO `color_references` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 'red', '#FF0000', '2026-04-16 19:39:29', '2026-04-16 19:39:29');

-- --------------------------------------------------------

--
-- Table structure for table `core_config`
--

CREATE TABLE `core_config` (
  `id` int UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
(19, 'general.general.locale_settings.locale', 'en', '2025-11-15 14:45:20', '2026-05-19 16:16:48'),
(20, 'general.general.admin_logo.logo_image', 'configuration/zp258im205mII7LJfOIOGHABxtqXKE2z2DTHht6o.webp', '2025-11-15 14:45:20', '2026-04-18 09:47:27'),
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
  `id` int UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
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
  `id` int UNSIGNED NOT NULL,
  `country_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` int UNSIGNED NOT NULL
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
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `applied` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_charges`
--

CREATE TABLE `document_charges` (
  `id` bigint UNSIGNED NOT NULL,
  `chargeable_id` bigint UNSIGNED NOT NULL,
  `chargeable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_charges`
--

INSERT INTO `document_charges` (`id`, `chargeable_id`, `chargeable_type`, `name`, `type`, `value`, `amount`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 8, 'Webkul\\Quote\\Models\\ProformaInvoice', 'Tariff', 'percentage', 5.0000, 0.0000, 0, '2026-04-16 20:02:54', '2026-04-16 20:02:54'),
(2, 8, 'Webkul\\Quote\\Models\\ProformaInvoice', 'Freight', 'percentage', 9.0000, 0.0000, 1, '2026-04-16 20:02:54', '2026-04-16 20:02:54'),
(3, 9, 'Webkul\\Quote\\Models\\ProformaInvoice', 'Tariff', 'percentage', 5.0000, 2.8320, 0, '2026-04-16 20:29:37', '2026-04-16 20:29:37'),
(4, 9, 'Webkul\\Quote\\Models\\ProformaInvoice', 'Freight', 'percentage', 9.0000, 5.0976, 1, '2026-04-16 20:29:37', '2026-04-16 20:29:37'),
(5, 17, 'quotes', 'ere', 'percentage', 34.0000, 14803.6000, 0, '2026-05-16 19:41:21', '2026-05-16 19:41:21'),
(6, 18, 'quotes', 'Shipping', 'value', 450.0000, 450.0000, 0, '2026-05-16 19:44:33', '2026-05-16 19:44:33'),
(7, 18, 'quotes', 'Tarrif', 'percentage', 12.0000, 142.2000, 1, '2026-05-16 19:44:33', '2026-05-16 19:44:33'),
(8, 10, 'Webkul\\Quote\\Models\\ProformaInvoice', 'Shipping', 'value', 450.0000, 450.0000, 0, '2026-05-16 19:57:25', '2026-05-16 19:57:25'),
(9, 10, 'Webkul\\Quote\\Models\\ProformaInvoice', 'Tarrif', 'percentage', 12.0000, 142.2000, 1, '2026-05-16 19:57:25', '2026-05-16 19:57:25'),
(10, 13, 'Webkul\\PurchaseOrder\\Models\\PurchaseOrder', 'Shipping', 'value', 20000.0000, 20000.0000, 0, '2026-05-17 09:57:34', '2026-05-17 09:57:34'),
(11, 14, 'Webkul\\PurchaseOrder\\Models\\PurchaseOrder', 'Shipping', 'value', 20000.0000, 20000.0000, 0, '2026-05-17 09:57:34', '2026-05-17 09:57:34'),
(12, 7, 'Webkul\\PurchaseOrder\\Models\\VendorQuote', 'Shipping', 'percentage', 2000.0000, 1749912.0000, 0, '2026-05-17 09:59:03', '2026-05-17 09:59:03'),
(13, 20, 'quotes', 'Shipping', 'value', 200.0000, 200.0000, 0, '2026-05-17 15:28:56', '2026-05-17 15:28:56'),
(14, 20, 'quotes', 'Tarrif', 'percentage', 12.0000, 1537.5600, 1, '2026-05-17 15:28:56', '2026-05-17 15:28:56'),
(15, 12, 'Webkul\\Quote\\Models\\ProformaInvoice', 'Shipping', 'value', 200.0000, 200.0000, 0, '2026-05-17 15:29:35', '2026-05-17 15:29:35'),
(16, 12, 'Webkul\\Quote\\Models\\ProformaInvoice', 'Tarrif', 'percentage', 12.0000, 1537.5600, 1, '2026-05-17 15:29:35', '2026-05-17 15:29:35');

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE `emails` (
  `id` int UNSIGNED NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reply` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `folders` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `from` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `sender` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `reply_to` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `cc` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `bcc` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `unique_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `person_id` int UNSIGNED DEFAULT NULL,
  `lead_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `parent_id` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `emails`
--

INSERT INTO `emails` (`id`, `subject`, `source`, `user_type`, `name`, `reply`, `is_read`, `folders`, `from`, `sender`, `reply_to`, `cc`, `bcc`, `unique_id`, `message_id`, `reference_ids`, `person_id`, `lead_id`, `created_at`, `updated_at`, `parent_id`) VALUES
(1, 'fuck off', 'web', 'admin', NULL, '<p>fuck off</p>', 0, '[\"draft\"]', '\"laravel@krayincrm.com\"', NULL, '[\"syedmuaz6198@gmail.com\"]', NULL, NULL, '1771527247@deveoninc.com', '1771527247@deveoninc.com', '[\"1771527247@deveoninc.com\"]', NULL, NULL, '2026-02-19 19:24:07', '2026-02-19 19:24:07', NULL),
(2, 'test', 'web', 'admin', NULL, 'test', 0, '[\"outbox\"]', '\"laravel@krayincrm.com\"', NULL, '[\"finalgamers67@gmail.com\"]', NULL, NULL, '1771700261@deveoninc.com', '1771700261@deveoninc.com', '[\"1771700261@deveoninc.com\"]', NULL, NULL, '2026-02-21 19:27:41', '2026-02-21 19:27:41', NULL),
(3, 'test', 'web', 'admin', NULL, 'tesdt', 0, '[\"outbox\"]', '\"laravel@krayincrm.com\"', NULL, '[\"finalgamers67@gmail.com\"]', NULL, NULL, '1771866804@deveoninc.com', '1771866804@deveoninc.com', '[\"1771866804@deveoninc.com\"]', NULL, NULL, '2026-02-23 17:43:24', '2026-02-23 17:43:24', NULL),
(4, 'yeds', 'web', 'admin', NULL, 'dfdf', 0, '[\"outbox\"]', '\"laravel@krayincrm.com\"', NULL, '[\"dfdf@dsff.com\"]', NULL, NULL, '1771869060@deveoninc.com', '1771869060@deveoninc.com', '[\"1771869060@deveoninc.com\"]', NULL, NULL, '2026-02-23 18:21:00', '2026-02-23 18:21:00', NULL),
(5, 'test email', 'web', 'admin', NULL, 'this is a test email. \r\n\r\nregards; \r\nZubair', 0, '[\"outbox\"]', '\"laravel@krayincrm.com\"', NULL, '[\"zubairmaya@gmail.com\"]', NULL, NULL, '1771897891@deveoninc.com', '1771897891@deveoninc.com', '[\"1771897891@deveoninc.com\"]', NULL, NULL, '2026-02-24 12:21:31', '2026-02-24 12:21:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_attachments`
--

CREATE TABLE `email_attachments` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` int DEFAULT NULL,
  `content_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_tags`
--

CREATE TABLE `email_tags` (
  `tag_id` int UNSIGNED NOT NULL,
  `email_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `name`, `subject`, `content`, `created_at`, `updated_at`) VALUES
(1, 'Activity created', 'Activity created: {%activities.title%}', '<p style=\"font-size: 16px; color: #5e5e5e;\">You have a new activity, please find the details bellow:</p>\n                                <p><strong style=\"font-size: 16px;\">Details</strong></p>\n                                <table style=\"height: 97px; width: 952px;\">\n                                    <tbody>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px;\">Title</td>\n                                            <td style=\"width: 770.047px; font-size: 16px;\">{%activities.title%}</td>\n                                        </tr>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px;\">Type</td>\n                                                <td style=\"width: 770.047px; font-size: 16px;\">{%activities.type%}</td>\n                                        </tr>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px;\">Date</td>\n                                            <td style=\"width: 770.047px; font-size: 16px;\">{%activities.schedule_from%} to&nbsp;{%activities.schedule_to%}</td>\n                                        </tr>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px; vertical-align: text-top;\">Participants</td>\n                                            <td style=\"width: 770.047px; font-size: 16px;\">{%activities.participants%}</td>\n                                        </tr>\n                                    </tbody>\n                                </table>', '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(2, 'Activity modified', 'Activity modified: {%activities.title%}', '<p style=\"font-size: 16px; color: #5e5e5e;\">You have a new activity modified, please find the details bellow:</p>\n                                <p><strong style=\"font-size: 16px;\">Details</strong></p>\n                                <table style=\"height: 97px; width: 952px;\">\n                                    <tbody>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px;\">Title</td>\n                                            <td style=\"width: 770.047px; font-size: 16px;\">{%activities.title%}</td>\n                                        </tr>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px;\">Type</td>\n                                            <td style=\"width: 770.047px; font-size: 16px;\">{%activities.type%}</td>\n                                        </tr>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px;\">Date</td>\n                                            <td style=\"width: 770.047px; font-size: 16px;\">{%activities.schedule_from%} to&nbsp;{%activities.schedule_to%}</td>\n                                        </tr>\n                                        <tr>\n                                            <td style=\"width: 116.953px; color: #546e7a; font-size: 16px; vertical-align: text-top;\">Participants</td>\n                                            <td style=\"width: 770.047px; font-size: 16px;\">{%activities.participants%}</td>\n                                        </tr>\n                                    </tbody>\n                                </table>', '2026-05-17 11:32:05', '2026-05-17 11:32:05');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipts`
--

CREATE TABLE `goods_receipts` (
  `id` int UNSIGNED NOT NULL,
  `goods_receipt_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_order_id` int UNSIGNED NOT NULL,
  `vendor_id` int UNSIGNED DEFAULT NULL,
  `receipt_date` date NOT NULL,
  `received_by` int UNSIGNED DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `attachment_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'posted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `goods_receipts`
--

INSERT INTO `goods_receipts` (`id`, `goods_receipt_number`, `purchase_order_id`, `vendor_id`, `receipt_date`, `received_by`, `notes`, `attachment_path`, `status`, `created_at`, `updated_at`) VALUES
(3, 'GR-00001', 17, 4, '2026-05-19', 1, '', NULL, 'posted', '2026-05-19 15:25:51', '2026-05-19 15:25:51');

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipt_items`
--

CREATE TABLE `goods_receipt_items` (
  `id` int UNSIGNED NOT NULL,
  `goods_receipt_id` int UNSIGNED NOT NULL,
  `purchase_order_item_id` int UNSIGNED NOT NULL,
  `requirement_id` int UNSIGNED DEFAULT NULL,
  `material_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `received_qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `unit` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `line_total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `goods_receipt_items`
--

INSERT INTO `goods_receipt_items` (`id`, `goods_receipt_id`, `purchase_order_item_id`, `requirement_id`, `material_name`, `received_qty`, `unit`, `unit_price`, `line_total`, `created_at`, `updated_at`) VALUES
(9, 3, 60, NULL, 'Oekotex Label', 0.0762, 'PCS', 0.0000, 0.0000, '2026-05-19 15:25:51', '2026-05-19 15:25:51');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `imports`
--

CREATE TABLE `imports` (
  `id` int UNSIGNED NOT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `process_in_queue` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `validation_strategy` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `allowed_errors` int NOT NULL DEFAULT '0',
  `processed_rows_count` int NOT NULL DEFAULT '0',
  `invalid_rows_count` int NOT NULL DEFAULT '0',
  `errors_count` int NOT NULL DEFAULT '0',
  `errors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `field_separator` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `import_batches`
--

CREATE TABLE `import_batches` (
  `id` int UNSIGNED NOT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `import_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `industries`
--

CREATE TABLE `industries` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `industries`
--

INSERT INTO `industries` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Home Apparel', '2026-07-09 18:39:07', '2026-07-09 18:39:07'),
(2, 'Other Shipping', '2026-07-09 18:39:07', '2026-07-09 18:39:07'),
(3, 'kjjk', '2026-07-12 19:45:58', '2026-07-12 19:45:58'),
(4, 'hjhk', '2026-07-12 19:46:09', '2026-07-12 19:46:09');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_cards`
--

CREATE TABLE `job_cards` (
  `id` int UNSIGNED NOT NULL,
  `job_order_id` int UNSIGNED NOT NULL,
  `job_order_item_id` int UNSIGNED DEFAULT NULL,
  `product_id` int UNSIGNED DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_cards`
--

INSERT INTO `job_cards` (`id`, `job_order_id`, `job_order_item_id`, `product_id`, `title`, `status`, `remarks`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 'JO-00001 - Cotton Tote Bag', 'open', NULL, 1, '2026-03-11 21:00:44', '2026-03-11 21:00:44'),
(2, 2, 2, 21, 'JO-00001 - Cotton Tote Bag', 'open', NULL, 1, '2026-03-18 17:15:19', '2026-03-18 17:15:19'),
(3, 3, 3, 21, 'JO-00001 - MQTB', 'open', NULL, 1, '2026-03-18 18:45:03', '2026-03-18 18:45:03'),
(4, 4, 4, 21, 'JO-00001 - MQTB', 'open', NULL, 1, '2026-03-18 21:46:53', '2026-03-18 21:46:53'),
(9, 7, 9, 31, 'JO-00001 - Test2', 'open', NULL, 1, '2026-04-16 19:58:00', '2026-04-16 19:58:00'),
(10, 7, 10, 30, 'JO-00001 - Test1', 'open', NULL, 1, '2026-04-16 19:58:00', '2026-04-16 19:58:00'),
(11, 8, 11, 31, 'JO-00002 - Test2', 'open', NULL, 1, '2026-04-16 20:03:13', '2026-04-16 20:03:13'),
(12, 8, 12, 30, 'JO-00002 - Test1', 'open', NULL, 1, '2026-04-16 20:03:13', '2026-04-16 20:03:13'),
(13, 9, 13, 21, 'JO-00003 - MQTB', 'open', NULL, 1, '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(18, 10, 14, 21, 'JO-00004 - MQTB', 'open', NULL, NULL, '2026-05-16 20:16:47', '2026-05-16 20:16:47'),
(19, 11, 16, 21, 'JO-00005 - MQTB', 'open', NULL, 1, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(20, 11, 18, 34, 'JO-00005 - ST00', 'open', NULL, 1, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(21, 12, 19, 21, 'JO-00006 - MQTB', 'open', NULL, 1, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(22, 12, 21, 34, 'JO-00006 - ST00', 'open', NULL, 1, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(23, 13, 22, 21, 'JO-00007 - MQTB', 'open', NULL, 1, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(24, 13, 24, 34, 'JO-00007 - ST00', 'open', NULL, 1, '2026-07-12 20:03:09', '2026-07-12 20:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `job_card_sections`
--

CREATE TABLE `job_card_sections` (
  `id` int UNSIGNED NOT NULL,
  `job_card_id` int UNSIGNED NOT NULL,
  `source_product_section_id` int UNSIGNED DEFAULT NULL,
  `section_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_started',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_card_sections`
--

INSERT INTO `job_card_sections` (`id`, `job_card_id`, `source_product_section_id`, `section_name`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 7, 'Cutting', 0, 'not_started', '2026-03-18 17:15:19', '2026-03-18 17:15:19'),
(2, 2, 8, 'Stitching', 1, 'not_started', '2026-03-18 17:15:19', '2026-03-18 17:15:19'),
(3, 2, 9, 'Finishing', 2, 'not_started', '2026-03-18 17:15:19', '2026-03-18 17:15:19'),
(4, 2, 10, 'Quality', 3, 'not_started', '2026-03-18 17:15:19', '2026-03-18 17:15:19'),
(5, 2, 11, 'Packing', 4, 'not_started', '2026-03-18 17:15:19', '2026-03-18 17:15:19'),
(6, 3, 7, 'Cutting', 0, 'not_started', '2026-03-18 18:45:03', '2026-03-18 18:45:03'),
(7, 3, 8, 'Stitching', 1, 'not_started', '2026-03-18 18:45:03', '2026-03-18 18:45:03'),
(8, 3, 9, 'Finishing', 2, 'not_started', '2026-03-18 18:45:03', '2026-03-18 18:45:03'),
(9, 3, 10, 'Quality', 3, 'not_started', '2026-03-18 18:45:03', '2026-03-18 18:45:03'),
(10, 3, 11, 'Packing', 4, 'not_started', '2026-03-18 18:45:03', '2026-03-18 18:45:03'),
(11, 4, 7, 'Cutting', 0, 'not_started', '2026-03-18 21:46:53', '2026-03-18 21:46:53'),
(12, 4, 8, 'Stitching', 1, 'not_started', '2026-03-18 21:46:53', '2026-03-18 21:46:53'),
(13, 4, 9, 'Finishing', 2, 'not_started', '2026-03-18 21:46:53', '2026-03-18 21:46:53'),
(14, 4, 10, 'Quality', 3, 'not_started', '2026-03-18 21:46:53', '2026-03-18 21:46:53'),
(15, 4, 11, 'Packing', 4, 'not_started', '2026-03-18 21:46:53', '2026-03-18 21:46:53'),
(16, 13, 7, 'Cutting', 0, 'not_started', '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(17, 13, 8, 'Stitching', 1, 'not_started', '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(18, 13, 9, 'Finishing', 2, 'not_started', '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(19, 13, 10, 'Quality', 3, 'not_started', '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(20, 13, 11, 'Packing', 4, 'not_started', '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(21, 14, 7, 'Cutting', 0, 'not_started', '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(22, 14, 8, 'Stitching', 1, 'not_started', '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(23, 14, 9, 'Finishing', 2, 'not_started', '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(24, 14, 10, 'Quality', 3, 'not_started', '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(25, 14, 11, 'Packing', 4, 'not_started', '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(26, 15, 7, 'Cutting', 0, 'not_started', '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(27, 15, 8, 'Stitching', 1, 'not_started', '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(28, 15, 9, 'Finishing', 2, 'not_started', '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(29, 15, 10, 'Quality', 3, 'not_started', '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(30, 15, 11, 'Packing', 4, 'not_started', '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(31, 16, 7, 'Cutting', 0, 'not_started', '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(32, 16, 8, 'Stitching', 1, 'not_started', '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(33, 16, 9, 'Finishing', 2, 'not_started', '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(34, 16, 10, 'Quality', 3, 'not_started', '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(35, 16, 11, 'Packing', 4, 'not_started', '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(36, 17, 7, 'Cutting', 0, 'not_started', '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(37, 17, 8, 'Stitching', 1, 'not_started', '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(38, 17, 9, 'Finishing', 2, 'not_started', '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(39, 17, 10, 'Quality', 3, 'not_started', '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(40, 17, 11, 'Packing', 4, 'not_started', '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(41, 18, 7, 'Cutting', 0, 'not_started', '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(42, 18, 8, 'Stitching', 1, 'not_started', '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(43, 18, 9, 'Finishing', 2, 'not_started', '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(44, 18, 10, 'Quality', 3, 'not_started', '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(45, 18, 11, 'Packing', 4, 'not_started', '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(46, 19, 7, 'Cutting', 0, 'not_started', '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(47, 19, 8, 'Stitching', 1, 'not_started', '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(48, 19, 9, 'Finishing', 2, 'not_started', '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(49, 19, 10, 'Quality', 3, 'not_started', '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(50, 19, 11, 'Packing', 4, 'not_started', '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(51, 21, 33, 'Cutting', 0, 'not_started', '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(52, 21, 34, 'Stitching', 1, 'not_started', '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(53, 21, 35, 'Finishing', 2, 'not_started', '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(54, 21, 36, 'Quality', 3, 'not_started', '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(55, 21, 37, 'Packing', 4, 'not_started', '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(56, 23, 33, 'Cutting', 0, 'not_started', '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(57, 23, 34, 'Stitching', 1, 'not_started', '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(58, 23, 35, 'Finishing', 2, 'not_started', '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(59, 23, 36, 'Quality', 3, 'not_started', '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(60, 23, 37, 'Packing', 4, 'not_started', '2026-07-12 20:03:09', '2026-07-12 20:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `job_card_section_items`
--

CREATE TABLE `job_card_section_items` (
  `id` int UNSIGNED NOT NULL,
  `job_card_section_id` int UNSIGNED NOT NULL,
  `source_product_section_item_id` int UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(12,4) DEFAULT NULL,
  `unit` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_card_section_items`
--

INSERT INTO `job_card_section_items` (`id`, `job_card_section_id`, `source_product_section_item_id`, `name`, `qty`, `unit`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 7, 'Fabric Width', 66.0000, 'Inch', 0, '2026-03-18 17:15:19', '2026-03-18 17:15:19'),
(2, 1, 8, 'Cutting', 40.0000, 'Inch', 1, '2026-03-18 17:15:19', '2026-03-18 17:15:19'),
(3, 2, 9, 'Thread Usage', 5.0000, 'Meter', 0, '2026-03-18 17:15:19', '2026-03-18 17:15:19'),
(4, 2, 10, 'Machine Type', 1.0000, 'Unit', 1, '2026-03-18 17:15:19', '2026-03-18 17:15:19'),
(5, 3, 11, 'Iron Pass', 1.0000, 'Pass', 0, '2026-03-18 17:15:19', '2026-03-18 17:15:19'),
(6, 4, 12, 'QC Check', 1.0000, 'Check', 0, '2026-03-18 17:15:19', '2026-03-18 17:15:19'),
(7, 5, 13, 'Pieces Per Carton', 100.0000, 'Piece', 0, '2026-03-18 17:15:19', '2026-03-18 17:15:19'),
(8, 6, 7, 'Fabric Width', 66.0000, 'Inch', 0, '2026-03-18 18:45:03', '2026-03-18 18:45:03'),
(9, 6, 8, 'Cutting', 40.0000, 'Inch', 1, '2026-03-18 18:45:03', '2026-03-18 18:45:03'),
(10, 7, 9, 'Thread Usage', 5.0000, 'Meter', 0, '2026-03-18 18:45:03', '2026-03-18 18:45:03'),
(11, 7, 10, 'Machine Type', 1.0000, 'Unit', 1, '2026-03-18 18:45:03', '2026-03-18 18:45:03'),
(12, 8, 11, 'Iron Pass', 1.0000, 'Pass', 0, '2026-03-18 18:45:03', '2026-03-18 18:45:03'),
(13, 9, 12, 'QC Check', 1.0000, 'Check', 0, '2026-03-18 18:45:03', '2026-03-18 18:45:03'),
(14, 10, 13, 'Pieces Per Carton', 100.0000, 'Piece', 0, '2026-03-18 18:45:03', '2026-03-18 18:45:03'),
(15, 11, 7, 'Fabric Width', 66.0000, 'Inch', 0, '2026-03-18 21:46:53', '2026-03-18 21:46:53'),
(16, 11, 8, 'Cutting', 40.0000, 'Inch', 1, '2026-03-18 21:46:53', '2026-03-18 21:46:53'),
(17, 12, 9, 'Thread Usage', 5.0000, 'Meter', 0, '2026-03-18 21:46:53', '2026-03-18 21:46:53'),
(18, 12, 10, 'Machine Type', 1.0000, 'Unit', 1, '2026-03-18 21:46:53', '2026-03-18 21:46:53'),
(19, 13, 11, 'Iron Pass', 1.0000, 'Pass', 0, '2026-03-18 21:46:53', '2026-03-18 21:46:53'),
(20, 14, 12, 'QC Check', 1.0000, 'Check', 0, '2026-03-18 21:46:53', '2026-03-18 21:46:53'),
(21, 15, 13, 'Pieces Per Carton', 100.0000, 'Piece', 0, '2026-03-18 21:46:53', '2026-03-18 21:46:53'),
(22, 16, 7, 'Fabric Width', 66.0000, 'Inch', 0, '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(23, 16, 8, 'Cutting', 40.0000, 'Inch', 1, '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(24, 17, 9, 'Thread Usage', 5.0000, 'Meter', 0, '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(25, 17, 10, 'Machine Type', 1.0000, 'Unit', 1, '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(26, 18, 11, 'Iron Pass', 1.0000, 'Pass', 0, '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(27, 19, 12, 'QC Check', 1.0000, 'Check', 0, '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(28, 20, 13, 'Pieces Per Carton', 100.0000, 'Piece', 0, '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(29, 21, 7, 'Fabric Width', 66.0000, 'Inch', 0, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(30, 21, 8, 'Cutting', 40.0000, 'Inch', 1, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(31, 22, 9, 'Thread Usage', 5.0000, 'Meter', 0, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(32, 22, 10, 'Machine Type', 1.0000, 'Unit', 1, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(33, 23, 11, 'Iron Pass', 1.0000, 'Pass', 0, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(34, 24, 12, 'QC Check', 1.0000, 'Check', 0, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(35, 25, 13, 'Pieces Per Carton', 100.0000, 'Piece', 0, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(36, 26, 7, 'Fabric Width', 66.0000, 'Inch', 0, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(37, 26, 8, 'Cutting', 40.0000, 'Inch', 1, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(38, 27, 9, 'Thread Usage', 5.0000, 'Meter', 0, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(39, 27, 10, 'Machine Type', 1.0000, 'Unit', 1, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(40, 28, 11, 'Iron Pass', 1.0000, 'Pass', 0, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(41, 29, 12, 'QC Check', 1.0000, 'Check', 0, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(42, 30, 13, 'Pieces Per Carton', 100.0000, 'Piece', 0, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(43, 31, 7, 'Fabric Width', 66.0000, 'Inch', 0, '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(44, 31, 8, 'Cutting', 40.0000, 'Inch', 1, '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(45, 32, 9, 'Thread Usage', 5.0000, 'Meter', 0, '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(46, 32, 10, 'Machine Type', 1.0000, 'Unit', 1, '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(47, 33, 11, 'Iron Pass', 1.0000, 'Pass', 0, '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(48, 34, 12, 'QC Check', 1.0000, 'Check', 0, '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(49, 35, 13, 'Pieces Per Carton', 100.0000, 'Piece', 0, '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(50, 36, 7, 'Fabric Width', 66.0000, 'Inch', 0, '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(51, 36, 8, 'Cutting', 40.0000, 'Inch', 1, '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(52, 37, 9, 'Thread Usage', 5.0000, 'Meter', 0, '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(53, 37, 10, 'Machine Type', 1.0000, 'Unit', 1, '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(54, 38, 11, 'Iron Pass', 1.0000, 'Pass', 0, '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(55, 39, 12, 'QC Check', 1.0000, 'Check', 0, '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(56, 40, 13, 'Pieces Per Carton', 100.0000, 'Piece', 0, '2026-05-16 20:15:20', '2026-05-16 20:15:20'),
(57, 41, 7, 'Fabric Width', 66.0000, 'Inch', 0, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(58, 41, 8, 'Cutting', 40.0000, 'Inch', 1, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(59, 42, 9, 'Thread Usage', 5.0000, 'Meter', 0, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(60, 42, 10, 'Machine Type', 1.0000, 'Unit', 1, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(61, 43, 11, 'Iron Pass', 1.0000, 'Pass', 0, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(62, 44, 12, 'QC Check', 1.0000, 'Check', 0, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(63, 45, 13, 'Pieces Per Carton', 100.0000, 'Piece', 0, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(64, 46, 7, 'Fabric Width', 66.0000, 'Inch', 0, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(65, 46, 8, 'Cutting', 40.0000, 'Inch', 1, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(66, 47, 9, 'Thread Usage', 5.0000, 'Meter', 0, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(67, 47, 10, 'Machine Type', 1.0000, 'Unit', 1, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(68, 48, 11, 'Iron Pass', 1.0000, 'Pass', 0, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(69, 49, 12, 'QC Check', 1.0000, 'Check', 0, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(70, 50, 13, 'Pieces Per Carton', 100.0000, 'Piece', 0, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(71, 51, 43, 'Fabric Width', 66.0000, 'Inch', 0, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(72, 51, 44, 'Cutting', 40.0000, 'Inch', 1, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(73, 52, 45, 'Thread Usage', 5.0000, 'Meter', 0, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(74, 52, 46, 'Machine Type', 1.0000, 'Unit', 1, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(75, 53, 47, 'Iron Pass', 1.0000, 'Pass', 0, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(76, 54, 48, 'QC Check', 1.0000, 'Check', 0, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(77, 55, 49, 'Pieces Per Carton', 100.0000, 'Piece', 0, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(78, 56, 43, 'Fabric Width', 66.0000, 'Inch', 0, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(79, 56, 44, 'Cutting', 40.0000, 'Inch', 1, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(80, 57, 45, 'Thread Usage', 5.0000, 'Meter', 0, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(81, 57, 46, 'Machine Type', 1.0000, 'Unit', 1, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(82, 58, 47, 'Iron Pass', 1.0000, 'Pass', 0, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(83, 59, 48, 'QC Check', 1.0000, 'Check', 0, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(84, 60, 49, 'Pieces Per Carton', 100.0000, 'Piece', 0, '2026-07-12 20:03:09', '2026-07-12 20:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `job_orders`
--

CREATE TABLE `job_orders` (
  `id` int UNSIGNED NOT NULL,
  `job_order_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `proforma_invoice_id` int UNSIGNED NOT NULL,
  `organization_id` int UNSIGNED NOT NULL,
  `person_id` int UNSIGNED DEFAULT NULL,
  `customer_po_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_date` date NOT NULL,
  `required_delivery_date` date DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `total_order_qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `approved_by` int UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_orders`
--

INSERT INTO `job_orders` (`id`, `job_order_number`, `proforma_invoice_id`, `organization_id`, `person_id`, `customer_po_reference`, `subject`, `issue_date`, `required_delivery_date`, `status`, `total_order_qty`, `remarks`, `created_by`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(10, 'JO-00004', 11, 3, 0, NULL, 'Quote 000002', '2026-05-17', '0000-00-00', 'open', 1644.0000, '', 1, NULL, NULL, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(11, 'JO-00005', 12, 3, 0, NULL, 'Quote 000001', '2026-05-17', '0000-00-00', 'open', 2703.0000, 'this is a test remarks', 1, NULL, NULL, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(12, 'JO-00006', 12, 3, 0, NULL, 'Quote 000001', '2026-05-17', '0000-00-00', 'open', 2703.0000, 'this is a test remarks', 1, NULL, NULL, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(13, 'JO-00007', 12, 3, 0, NULL, 'Quote 000001', '2026-07-13', '0000-00-00', 'open', 2703.0000, 'this is a test remarks', 1, NULL, NULL, '2026-07-12 20:03:09', '2026-07-12 20:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `job_order_items`
--

CREATE TABLE `job_order_items` (
  `id` int UNSIGNED NOT NULL,
  `job_order_id` int UNSIGNED NOT NULL,
  `proforma_invoice_item_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` int UNSIGNED DEFAULT NULL,
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `unit` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(12,4) DEFAULT NULL,
  `line_total` decimal(12,4) DEFAULT NULL,
  `sort_order` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_order_items`
--

INSERT INTO `job_order_items` (`id`, `job_order_id`, `proforma_invoice_item_id`, `product_id`, `item_name`, `item_code`, `description`, `qty`, `unit`, `unit_price`, `line_total`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 3, 'Cotton Tote Bag', NULL, NULL, 1.0000, NULL, NULL, NULL, 0, '2026-03-11 21:00:44', '2026-03-11 21:00:44'),
(2, 2, NULL, 21, 'Cotton Tote Bag', NULL, NULL, 12.0000, NULL, NULL, NULL, 0, '2026-03-18 17:15:19', '2026-03-18 17:15:19'),
(3, 3, NULL, 21, 'Cotton Tote Bag', NULL, NULL, 1.0000, NULL, NULL, NULL, 0, '2026-03-18 18:45:03', '2026-03-18 18:45:03'),
(4, 4, NULL, 21, 'Cotton Tote Bag', NULL, NULL, 12.0000, NULL, NULL, NULL, 0, '2026-03-18 21:46:53', '2026-03-18 21:46:53'),
(9, 7, 6, 31, 'test22', 'Test2', '', 1.0000, 'PCS', 0.0000, 0.0000, 0, '2026-04-16 19:58:00', '2026-04-16 19:58:00'),
(10, 7, 7, 30, 'testtt', 'Test1', '', 1.0000, 'PCS', 0.0000, 0.0000, 1, '2026-04-16 19:58:00', '2026-04-16 19:58:00'),
(11, 8, 8, 31, 'test22', 'Test2', '', 1.0000, 'PCS', 0.0000, 0.0000, 0, '2026-04-16 20:03:13', '2026-04-16 20:03:13'),
(12, 8, 9, 30, 'testtt', 'Test1', '', 1.0000, 'PCS', 0.0000, 0.0000, 1, '2026-04-16 20:03:13', '2026-04-16 20:03:13'),
(13, 9, 11, 21, 'Cotton Tote Bag', 'MQTB', '', 1500.0000, 'PCS', 0.7900, 1185.0000, 0, '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(14, 10, 12, 21, 'Cotton Tote Bag', 'MQTB', '', 144.0000, 'PCS', 4.7200, 679.6800, 0, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(15, 10, 13, 21, 'Cotton Tote Bag', 'MQTB', '', 1500.0000, 'PCS', 4.7200, 7080.0000, 1, '2026-05-16 20:06:40', '2026-05-16 20:06:40'),
(16, 11, 14, 21, 'Cotton Tote Bag', 'MQTB', '', 1500.0000, 'PCS', 4.7200, 7080.0000, 0, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(17, 11, 15, 21, 'Cotton Tote Bag', 'MQTB', '', 1200.0000, 'PCS', 4.7200, 5664.0000, 1, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(18, 11, 16, 34, 'Supplier Product', 'ST00', '', 3.0000, 'PCS', 23.0000, 69.0000, 2, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(19, 12, 14, 21, 'Cotton Tote Bag', 'MQTB', '', 1500.0000, 'PCS', 4.7200, 7080.0000, 0, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(20, 12, 15, 21, 'Cotton Tote Bag', 'MQTB', '', 1200.0000, 'PCS', 4.7200, 5664.0000, 1, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(21, 12, 16, 34, 'Supplier Product', 'ST00', '', 3.0000, 'PCS', 23.0000, 69.0000, 2, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(22, 13, 14, 21, 'Cotton Tote Bag', 'MQTB', '', 1500.0000, 'PCS', 4.7200, 7080.0000, 0, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(23, 13, 15, 21, 'Cotton Tote Bag', 'MQTB', '', 1200.0000, 'PCS', 4.7200, 5664.0000, 1, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(24, 13, 16, 34, 'Supplier Product', 'ST00', '', 3.0000, 'PCS', 23.0000, 69.0000, 2, '2026-07-12 20:03:09', '2026-07-12 20:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `job_order_requirements`
--

CREATE TABLE `job_order_requirements` (
  `id` int UNSIGNED NOT NULL,
  `job_order_id` int UNSIGNED NOT NULL,
  `job_order_item_id` int UNSIGNED DEFAULT NULL,
  `product_id` int UNSIGNED DEFAULT NULL,
  `item_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `material_reference_id` bigint UNSIGNED DEFAULT NULL,
  `material_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_ids` json DEFAULT NULL,
  `color_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty_per_unit` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `ordered_qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `required_qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `received_qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `balance_qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `sort_order` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_order_requirements`
--

INSERT INTO `job_order_requirements` (`id`, `job_order_id`, `job_order_item_id`, `product_id`, `item_codes`, `material_reference_id`, `material_name`, `unit`, `vendor_ids`, `color_name`, `color_code`, `qty_per_unit`, `ordered_qty`, `required_qty`, `received_qty`, `balance_qty`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(15, 8, 11, 31, 'Test2, Test1', 3, '20x20 / 60x66 - 66\" | 150gsm', 'METER', '[6, 9]', 'red', '#FF0000', 0.3120, 2.0000, 0.6240, 0.0000, 0.6240, 'pending', 0, '2026-04-16 20:03:13', '2026-04-16 20:03:13'),
(16, 8, 11, 31, 'Test2', 7, 'Cartons (240 pcs / Box)', 'INCH', NULL, 'green', NULL, 0.0420, 1.0000, 0.0420, 0.0000, 0.0420, 'pending', 1, '2026-04-16 20:03:13', '2026-04-16 20:03:13'),
(17, 8, 12, 30, 'Test1', 7, 'Cartons (240 pcs / Box)', 'INCH', '[10]', 'yellow', NULL, 0.0420, 1.0000, 0.0420, 0.0000, 0.0420, 'pending', 2, '2026-04-16 20:03:13', '2026-04-16 20:03:13'),
(18, 7, 9, 31, 'Test2, Test1', 3, '20x20 / 60x66 - 66\" | 150gsm', 'METER', '[6, 9]', 'red', '#FF0000', 0.3120, 2.0000, 0.6240, 0.0000, 0.6240, 'pending', 0, '2026-04-16 20:03:41', '2026-04-16 20:03:41'),
(19, 7, 9, 31, 'Test2', 7, 'Cartons (240 pcs / Box)', 'INCH', NULL, 'green', NULL, 0.0420, 1.0000, 0.0420, 0.0000, 0.0420, 'pending', 1, '2026-04-16 20:03:41', '2026-04-16 20:03:41'),
(20, 7, 10, 30, 'Test1', 7, 'Cartons (240 pcs / Box)', 'INCH', '[10]', 'yellow', NULL, 0.0420, 1.0000, 0.0420, 0.0000, 0.0420, 'pending', 2, '2026-04-16 20:03:41', '2026-04-16 20:03:41'),
(21, 9, 13, 21, 'MQTB', NULL, 'Cotton Fabric', 'Meter', NULL, NULL, NULL, 0.3500, 1500.0000, 525.0000, 0.0000, 525.0000, 'pending', 0, '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(22, 9, 13, 21, 'MQTB', NULL, 'Handle', 'Inch', NULL, NULL, NULL, 20.0000, 1500.0000, 30000.0000, 0.0000, 30000.0000, 'pending', 1, '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(23, 9, 13, 21, 'MQTB', NULL, 'Label', 'Piece', NULL, NULL, NULL, 1.0000, 1500.0000, 1500.0000, 0.0000, 1500.0000, 'pending', 2, '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(24, 9, 13, 21, 'MQTB', NULL, 'Thread', 'Meter', NULL, NULL, NULL, 5.0000, 1500.0000, 7500.0000, 0.0000, 7500.0000, 'pending', 3, '2026-05-16 19:57:53', '2026-05-16 19:57:53'),
(37, 10, 14, 21, 'MQTB', NULL, 'Cotton Fabric', 'Meter', NULL, 'Hot Pink', NULL, 0.3500, 144.0000, 50.4000, 0.0000, 50.4000, 'pending', 0, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(38, 10, 14, 21, 'MQTB', NULL, 'Handle', 'Inch', NULL, 'Hot Pink', NULL, 20.0000, 144.0000, 2880.0000, 0.0000, 2880.0000, 'pending', 1, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(39, 10, 14, 21, 'MQTB', NULL, 'Label', 'Piece', NULL, 'Hot Pink', NULL, 1.0000, 144.0000, 144.0000, 0.0000, 144.0000, 'pending', 2, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(40, 10, 14, 21, 'MQTB', NULL, 'Thread', 'Meter', NULL, 'Hot Pink', NULL, 5.0000, 144.0000, 720.0000, 0.0000, 720.0000, 'pending', 3, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(41, 10, 15, 21, 'MQTB', NULL, 'Cotton Fabric', 'Meter', NULL, 'red', NULL, 0.3500, 1500.0000, 525.0000, 0.0000, 525.0000, 'pending', 4, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(42, 10, 15, 21, 'MQTB', NULL, 'Handle', 'Inch', NULL, 'red', NULL, 20.0000, 1500.0000, 30000.0000, 0.0000, 30000.0000, 'pending', 5, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(43, 10, 15, 21, 'MQTB', NULL, 'Label', 'Piece', NULL, 'red', NULL, 1.0000, 1500.0000, 1500.0000, 0.0000, 1500.0000, 'pending', 6, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(44, 10, 15, 21, 'MQTB', NULL, 'Thread', 'Meter', NULL, 'red', NULL, 5.0000, 1500.0000, 7500.0000, 0.0000, 7500.0000, 'pending', 7, '2026-05-16 20:16:48', '2026-05-16 20:16:48'),
(45, 11, 16, 21, 'MQTB', NULL, 'Cotton Fabric', 'Meter', NULL, 'red', NULL, 0.3500, 1500.0000, 525.0000, 0.0000, 525.0000, 'pending', 0, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(46, 11, 16, 21, 'MQTB', NULL, 'Handle', 'Inch', NULL, 'red', NULL, 20.0000, 1500.0000, 30000.0000, 0.0000, 30000.0000, 'pending', 1, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(47, 11, 16, 21, 'MQTB', NULL, 'Label', 'Piece', NULL, 'red', NULL, 1.0000, 1500.0000, 1500.0000, 0.0000, 1500.0000, 'pending', 2, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(48, 11, 16, 21, 'MQTB', NULL, 'Thread', 'Meter', NULL, 'red', NULL, 5.0000, 1500.0000, 7500.0000, 0.0000, 7500.0000, 'pending', 3, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(49, 11, 17, 21, 'MQTB', NULL, 'Cotton Fabric', 'Meter', NULL, 'Navy', NULL, 0.3500, 1200.0000, 420.0000, 0.0000, 420.0000, 'pending', 4, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(50, 11, 17, 21, 'MQTB', NULL, 'Handle', 'Inch', NULL, 'Navy', NULL, 20.0000, 1200.0000, 24000.0000, 0.0000, 24000.0000, 'pending', 5, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(51, 11, 17, 21, 'MQTB', NULL, 'Label', 'Piece', NULL, 'Navy', NULL, 1.0000, 1200.0000, 1200.0000, 0.0000, 1200.0000, 'pending', 6, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(52, 11, 17, 21, 'MQTB', NULL, 'Thread', 'Meter', NULL, 'Navy', NULL, 5.0000, 1200.0000, 6000.0000, 0.0000, 6000.0000, 'pending', 7, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(53, 11, 18, 34, 'ST00', 3, '20x20 / 60x66 - 66\" | 150gsm', 'METER', '[6, 9]', 'red', NULL, 0.3120, 3.0000, 0.9360, 0.0000, 0.9360, 'pending', 8, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(54, 11, 18, 34, 'ST00', 7, 'Cartons (240 pcs / Box)', 'INCH', '[10]', 'red', NULL, 0.0420, 3.0000, 0.1260, 0.0000, 0.1260, 'pending', 9, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(55, 11, 18, 34, 'ST00', 5, 'Oekotex Label', 'INCH', '[4]', 'red', '#FF0000', 1.0000, 3.0000, 3.0000, 0.0000, 3.0000, 'pending', 10, '2026-05-17 15:29:59', '2026-05-17 15:29:59'),
(56, 12, 19, 21, 'MQTB', 7, 'Cartons (240 pcs / Box)', 'Meter', NULL, 'red', '#FF0000', 0.3500, 2700.0000, 945.0000, 0.0000, 945.0000, 'pending', 0, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(57, 12, 19, 21, 'MQTB', 3, '20x20 / 60x66 - 66\" | 150gsm', 'Inch', '[6, 9]', 'red', NULL, 20.0000, 1500.0000, 30000.0000, 0.0000, 30000.0000, 'pending', 1, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(58, 12, 19, 21, 'MQTB', 5, 'Oekotex Label', 'Piece', NULL, 'red', NULL, 1.0000, 1500.0000, 1500.0000, 0.0000, 1500.0000, 'pending', 2, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(59, 12, 19, 21, 'MQTB', 7, 'Cartons (240 pcs / Box)', 'Meter', NULL, 'red', '#FF0000', 5.0000, 2700.0000, 13500.0000, 0.0000, 13500.0000, 'pending', 3, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(60, 12, 20, 21, 'MQTB', 3, '20x20 / 60x66 - 66\" | 150gsm', 'Inch', '[6, 9]', 'Navy', NULL, 20.0000, 1200.0000, 24000.0000, 0.0000, 24000.0000, 'pending', 4, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(61, 12, 20, 21, 'MQTB', 5, 'Oekotex Label', 'Piece', NULL, 'Navy', NULL, 1.0000, 1200.0000, 1200.0000, 0.0000, 1200.0000, 'pending', 5, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(62, 12, 21, 34, 'ST00', 3, '20x20 / 60x66 - 66\" | 150gsm', 'METER', '[6, 9]', 'red', NULL, 0.3120, 3.0000, 0.9360, 0.0000, 0.9360, 'pending', 6, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(63, 12, 21, 34, 'ST00', 7, 'Cartons (240 pcs / Box)', 'INCH', '[10]', 'red', NULL, 0.0420, 3.0000, 0.1260, 0.0000, 0.1260, 'pending', 7, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(64, 12, 21, 34, 'ST00', 5, 'Oekotex Label', 'INCH', '[4]', 'red', '#FF0000', 1.0000, 3.0000, 3.0000, 0.0000, 3.0000, 'pending', 8, '2026-05-17 15:31:16', '2026-05-17 15:31:16'),
(65, 13, 22, 21, 'MQTB', 7, 'Cartons (240 pcs / Box)', 'Meter', NULL, 'red', '#FF0000', 0.3500, 2700.0000, 945.0000, 0.0000, 945.0000, 'pending', 0, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(66, 13, 22, 21, 'MQTB', 3, '20x20 / 60x66 - 66\" | 150gsm', 'Inch', '[6, 9]', 'red', NULL, 20.0000, 1500.0000, 30000.0000, 0.0000, 30000.0000, 'pending', 1, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(67, 13, 22, 21, 'MQTB', 5, 'Oekotex Label', 'Piece', NULL, 'red', NULL, 1.0000, 1500.0000, 1500.0000, 0.0000, 1500.0000, 'pending', 2, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(68, 13, 22, 21, 'MQTB', 7, 'Cartons (240 pcs / Box)', 'Meter', NULL, 'red', '#FF0000', 5.0000, 2700.0000, 13500.0000, 0.0000, 13500.0000, 'pending', 3, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(69, 13, 23, 21, 'MQTB', 3, '20x20 / 60x66 - 66\" | 150gsm', 'Inch', '[6, 9]', 'Navy', NULL, 20.0000, 1200.0000, 24000.0000, 0.0000, 24000.0000, 'pending', 4, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(70, 13, 23, 21, 'MQTB', 5, 'Oekotex Label', 'Piece', NULL, 'Navy', NULL, 1.0000, 1200.0000, 1200.0000, 0.0000, 1200.0000, 'pending', 5, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(71, 13, 24, 34, 'ST00', 3, '20x20 / 60x66 - 66\" | 150gsm', 'METER', '[6, 9]', 'red', NULL, 0.3120, 3.0000, 0.9360, 0.0000, 0.9360, 'pending', 6, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(72, 13, 24, 34, 'ST00', 7, 'Cartons (240 pcs / Box)', 'INCH', '[10]', 'red', NULL, 0.0420, 3.0000, 0.1260, 0.0000, 0.1260, 'pending', 7, '2026-07-12 20:03:09', '2026-07-12 20:03:09'),
(73, 13, 24, 34, 'ST00', 5, 'Oekotex Label', 'INCH', '[4]', 'red', '#FF0000', 1.0000, 3.0000, 3.0000, 0.0000, 3.0000, 'pending', 8, '2026-07-12 20:03:09', '2026-07-12 20:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` int UNSIGNED NOT NULL,
  `case_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `lead_value` decimal(12,4) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `lost_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `closed_at` datetime DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `person_id` int UNSIGNED DEFAULT NULL,
  `organization_id` int UNSIGNED DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
  `lead_source_id` int UNSIGNED DEFAULT NULL,
  `lead_type_id` int UNSIGNED DEFAULT NULL,
  `lead_pipeline_id` int UNSIGNED DEFAULT NULL,
  `lead_pipeline_stage_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expected_close_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `case_no`, `title`, `description`, `lead_value`, `status`, `lost_reason`, `closed_at`, `user_id`, `person_id`, `organization_id`, `priority`, `lead_source_id`, `lead_type_id`, `lead_pipeline_id`, `lead_pipeline_stage_id`, `created_at`, `updated_at`, `expected_close_date`) VALUES
(16, '00002', 'ABC', 'ABC', NULL, 1, NULL, NULL, 1, 12, 13, NULL, 3, NULL, 1, 4, '2026-05-17 11:52:35', '2026-05-19 15:18:29', NULL),
(19, '00003', 'fdf', 'df', NULL, 1, NULL, NULL, 1, 12, 13, NULL, NULL, NULL, 1, 2, '2026-05-17 12:23:08', '2026-05-19 13:55:35', NULL),
(21, '00004', 'dsf', 'sdf', NULL, 1, NULL, NULL, 1, 18, 14, 'medium', 2, NULL, 1, 2, '2026-05-17 15:19:08', '2026-05-19 13:55:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_activities`
--

CREATE TABLE `lead_activities` (
  `activity_id` int UNSIGNED NOT NULL,
  `lead_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_activities`
--

INSERT INTO `lead_activities` (`activity_id`, `lead_id`) VALUES
(404, 16),
(405, 16),
(406, 16),
(407, 16),
(408, 16),
(409, 16),
(410, 16),
(419, 19),
(420, 19),
(421, 19),
(422, 19),
(423, 19),
(424, 19),
(434, 19),
(435, 16),
(459, 21),
(460, 21),
(461, 21),
(462, 21),
(463, 21),
(464, 21),
(465, 21),
(466, 21),
(481, 16),
(482, 19),
(483, 16),
(484, 19),
(485, 19),
(486, 16),
(487, 16),
(488, 19),
(489, 16),
(490, 19),
(491, 19),
(492, 16),
(493, 21),
(494, 21),
(495, 21),
(496, 16),
(497, 16),
(498, 21),
(499, 16),
(500, 21),
(501, 19),
(502, 16);

-- --------------------------------------------------------

--
-- Table structure for table `lead_persons`
--

CREATE TABLE `lead_persons` (
  `id` int UNSIGNED NOT NULL,
  `lead_id` int UNSIGNED NOT NULL,
  `person_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_persons`
--

INSERT INTO `lead_persons` (`id`, `lead_id`, `person_id`, `created_at`, `updated_at`) VALUES
(2, 16, 12, '2026-05-17 11:52:35', '2026-05-17 12:08:16'),
(9, 19, 12, '2026-05-17 12:23:08', '2026-05-17 12:23:08'),
(13, 19, 13, '2026-05-17 12:43:27', '2026-05-17 12:43:27'),
(14, 16, 13, '2026-05-17 12:43:49', '2026-05-17 12:43:49'),
(15, 21, 18, '2026-05-17 15:19:08', '2026-05-17 15:19:08');

-- --------------------------------------------------------

--
-- Table structure for table `lead_pipelines`
--

CREATE TABLE `lead_pipelines` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `rotten_days` int NOT NULL DEFAULT '30',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_pipelines`
--

INSERT INTO `lead_pipelines` (`id`, `name`, `is_default`, `rotten_days`, `created_at`, `updated_at`) VALUES
(1, 'ABC', 1, 30, '2026-05-17 11:32:05', '2026-05-19 17:09:11');

-- --------------------------------------------------------

--
-- Table structure for table `lead_pipeline_stages`
--

CREATE TABLE `lead_pipeline_stages` (
  `id` int UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `probability` int NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `lead_pipeline_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_pipeline_stages`
--

INSERT INTO `lead_pipeline_stages` (`id`, `code`, `name`, `probability`, `sort_order`, `lead_pipeline_id`) VALUES
(2, 'follow-up', 'Follow Up', 100, 1, 1),
(3, 'prospect', 'Prospect', 100, 3, 1),
(4, 'negotiation', 'Negotiation', 100, 2, 1),
(5, 'won', 'Won', 100, 5, 1),
(6, 'lost', 'Lost', 0, 6, 1),
(8, 'new-stage-7', 'New Stage 7', 100, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `lead_priorities`
--

CREATE TABLE `lead_priorities` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` int UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `price` decimal(12,4) DEFAULT NULL,
  `amount` decimal(12,4) DEFAULT NULL,
  `lead_id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_quotes`
--

CREATE TABLE `lead_quotes` (
  `quote_id` int UNSIGNED NOT NULL,
  `lead_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_sources`
--

CREATE TABLE `lead_sources` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_sources`
--

INSERT INTO `lead_sources` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Email', '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(2, 'Web', '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(3, 'Web Form', '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(4, 'Phone', '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(5, 'Direct', '2026-05-17 11:32:05', '2026-05-17 11:32:05');

-- --------------------------------------------------------

--
-- Table structure for table `lead_stages`
--

CREATE TABLE `lead_stages` (
  `id` int UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_user_defined` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_tags`
--

CREATE TABLE `lead_tags` (
  `tag_id` int UNSIGNED NOT NULL,
  `lead_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_types`
--

CREATE TABLE `lead_types` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_types`
--

INSERT INTO `lead_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'New Business', '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(2, 'Existing Business', '2026-05-17 11:32:05', '2026-05-17 11:32:05');

-- --------------------------------------------------------

--
-- Table structure for table `marketing_campaigns`
--

CREATE TABLE `marketing_campaigns` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `spooling` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marketing_template_id` int UNSIGNED DEFAULT NULL,
  `marketing_event_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_events`
--

CREATE TABLE `marketing_events` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `material_references`
--

CREATE TABLE `material_references` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `unit` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `material_references`
--

INSERT INTO `material_references` (`id`, `name`, `qty`, `unit`, `color_name`, `color_code`, `created_at`, `updated_at`) VALUES
(3, '20x20 / 60x66 - 66\" | 150gsm', 0.3120, 'METER', NULL, NULL, '2026-04-16 19:46:21', '2026-04-16 19:46:21'),
(4, 'Thread 40/2 - 30 Meters', 0.0060, 'METER', NULL, NULL, '2026-04-16 19:46:57', '2026-04-16 19:46:57'),
(5, 'Oekotex Label', 1.0000, 'INCH', NULL, NULL, '2026-04-16 19:47:18', '2026-04-16 19:47:18'),
(6, 'Main Label (2 sided)', 1.0000, 'INCH', NULL, NULL, '2026-04-16 19:47:33', '2026-04-16 19:47:33'),
(7, 'Cartons (240 pcs / Box)', 0.0420, 'INCH', NULL, NULL, '2026-04-16 19:48:35', '2026-04-16 19:48:35');

-- --------------------------------------------------------

--
-- Table structure for table `material_reference_vendor`
--

CREATE TABLE `material_reference_vendor` (
  `id` bigint UNSIGNED NOT NULL,
  `material_reference_id` bigint UNSIGNED NOT NULL,
  `organization_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `material_reference_vendor`
--

INSERT INTO `material_reference_vendor` (`id`, `material_reference_id`, `organization_id`, `created_at`, `updated_at`) VALUES
(7, 3, 6, '2026-04-16 19:46:21', '2026-04-16 19:46:21'),
(8, 3, 9, '2026-04-16 19:46:21', '2026-04-16 19:46:21'),
(9, 4, 10, '2026-04-16 19:46:57', '2026-04-16 19:46:57'),
(10, 4, 12, '2026-04-16 19:46:57', '2026-04-16 19:46:57'),
(11, 5, 4, '2026-04-16 19:47:18', '2026-04-16 19:47:18'),
(12, 6, 12, '2026-04-16 19:47:33', '2026-04-16 19:47:33'),
(13, 6, 8, '2026-04-16 19:47:33', '2026-04-16 19:47:33'),
(14, 7, 10, '2026-04-16 19:48:35', '2026-04-16 19:48:35'),
(15, 6, 17, '2026-07-12 20:14:45', '2026-07-12 20:14:45'),
(16, 6, 19, '2026-07-12 20:14:45', '2026-07-12 20:14:45');

-- --------------------------------------------------------

--
-- Table structure for table `material_units`
--

CREATE TABLE `material_units` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
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
(101, '2026_03_10_000001_add_color_and_image_fields_to_quote_items_table', 16),
(102, '2026_03_11_000001_add_converted_to_invoice_id_to_proforma_invoices_table', 17),
(103, '2026_03_11_000002_add_payment_term_and_item_media_to_proformas', 18),
(104, '2026_03_12_000012_extend_purchase_orders_for_erp', 19),
(105, '2026_03_12_000013_create_missing_erp_tables', 20),
(106, '2026_03_18_000001_add_commercial_fields_to_quotes_table', 21),
(107, '2026_03_19_000001_extend_vendor_quotes_for_document_flow', 22),
(108, '2026_03_19_000002_add_terms_to_purchase_orders', 23),
(109, '2026_04_16_000003_create_material_units_table', 24),
(110, '2025_02_23_create_organization_activities_table', 99),
(111, '2026_02_05_000000_add_extended_fields_to_organizations_table', 100),
(112, '2026_02_05_100000_create_product_categories_table', 100),
(113, '2026_02_05_100001_add_extra_columns_to_products_table', 100),
(114, '2026_02_05_100002_create_product_other_images_table', 100),
(115, '2026_02_05_100003_create_product_colors_table', 100),
(116, '2026_02_06_100000_add_contact_fields_to_persons_table', 100),
(117, '2026_02_06_100001_make_emails_nullable_in_persons_table', 100),
(118, '2026_02_07_add_polymorphic_relations_to_activities', 100),
(119, '2026_02_21_000001_add_organization_id_to_leads_table', 100),
(120, '2026_03_12_000010_create_job_order_tables', 100),
(121, '2026_03_12_000011_create_procurement_tables', 100),
(122, '2026_04_15_000001_add_pricing_fields_to_product_colors_table', 100),
(123, '2026_04_15_000001_create_document_charges_table', 100),
(124, '2026_04_15_000002_add_weight_fields_to_products_table', 100),
(125, '2026_04_15_000003_create_color_references_table', 100),
(126, '2026_04_16_000001_create_material_references_table', 100),
(127, '2026_04_16_000001_extend_job_order_requirements_for_material_aggregation', 100),
(128, '2026_04_16_000002_extend_product_consumptions_for_material_references', 100),
(129, '2026_04_16_000004_create_unit_references_table', 100),
(130, '2026_04_17_000001_add_address_fields_to_procurement_documents', 101),
(131, '2026_05_17_000001_create_lead_persons_table', 102),
(132, '2026_05_17_000002_add_meter_conversion_to_unit_references_table', 103),
(133, '2026_05_19_000001_add_vendor_and_color_to_vendor_quote_items_table', 104),
(134, '2026_07_09_000001_create_industries_table', 105);

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `id` int UNSIGNED NOT NULL,
  `parent_organization_id` int UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `industry` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employees` int DEFAULT NULL,
  `annual_revenue` decimal(12,2) DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `billing_street` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_postcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_street` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_postcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`id`, `parent_organization_id`, `name`, `address`, `phone`, `fax`, `website`, `type`, `industry`, `employees`, `annual_revenue`, `description`, `billing_street`, `billing_city`, `billing_state`, `billing_postcode`, `billing_country`, `shipping_street`, `shipping_city`, `shipping_state`, `shipping_postcode`, `shipping_country`, `created_at`, `updated_at`, `user_id`) VALUES
(3, NULL, 'Deveon', '[]', '(905)514-8474', '', 'https://deveoninc.com', 'customer', '', 50, 10000.00, 'Software Company', '', '', '', '', '', '', '', '', '', '', '2026-03-18 16:49:24', '2026-05-20 04:18:10', NULL),
(4, NULL, 'XYS Enterprise', NULL, '(123) 456-4783', '', 'https://www.google.com', 'vendor', 'home_apparel', 0, 0.00, 'One of out best vendors in Canada', '4327 Summerfield Blvd', 'Toronto', 'Ontario', 'M5V 3L9', 'Canada', '4327 Summerfield Blvd', 'Toronto', 'Ontario', 'M5V 3L9', 'Canada', '2026-03-18 17:22:38', '2026-03-18 17:22:38', NULL),
(5, NULL, 'Company 2', '[{\"type\":\"billing\",\"street\":\"sad\",\"city\":\"sd\",\"state\":\"sad\",\"postcode\":\"sd\",\"country\":\"sad\"},{\"type\":\"shipping\",\"street\":\"sad\",\"city\":\"sd\",\"state\":\"sad\",\"postcode\":\"sd\",\"country\":\"sad\"}]', '', '', '', 'customer', '', 0, 0.00, '', 'sad', 'sd', 'sad', 'sd', 'sad', 'sad', 'sd', 'sad', 'sd', 'sad', '2026-04-14 20:42:57', '2026-04-18 09:41:33', NULL),
(6, NULL, 'Vendor 4', '[{\"type\":\"billing\",\"street\":\"sadsa\",\"city\":\"sad\",\"state\":\"sad\",\"postcode\":\"sad\",\"country\":\"sad\"},{\"type\":\"shipping\",\"street\":\"sadsa\",\"city\":\"sad\",\"state\":\"sad\",\"postcode\":\"sad\",\"country\":\"sad\"}]', '', '', '', 'vendor', '', 0, 0.00, '', 'sadsa', 'sad', 'sad', 'sad', 'sad', 'sadsa', 'sad', 'sad', 'sad', 'sad', '2026-04-14 21:21:42', '2026-04-14 21:21:42', NULL),
(7, NULL, 'Company 5', '[{\"type\":\"billing\",\"street\":\"sd\",\"city\":\"sd\",\"state\":\"sd\",\"postcode\":\"sd\",\"country\":\"sd\"},{\"type\":\"shipping\",\"street\":\"sd\",\"city\":\"sd\",\"state\":\"sd\",\"postcode\":\"sd\",\"country\":\"sd\"},{\"type\":\"other\",\"street\":\"sd\",\"city\":\"sd\",\"state\":\"dsfdsfds\",\"postcode\":\"dsfdsf\",\"country\":\"dsf\"}]', '', '', '', 'customer', '', 0, 0.00, '', 'sd', 'sd', 'sd', 'sd', 'sd', 'sd', 'sd', 'sd', 'sd', 'sd', '2026-04-14 21:50:54', '2026-04-18 09:41:56', NULL),
(8, NULL, 'Vendor 6', '[]', '', '', '', 'vendor', '', 0, 0.00, '', '', '', '', '', '', '', '', '', '', '', '2026-04-16 18:54:08', '2026-04-16 18:54:08', NULL),
(9, NULL, 'Vendor 7', '[{\"type\":\"billing\",\"street\":\"Excepteur porro simi\",\"city\":\"Ratione et deserunt\",\"state\":\"Laboriosam perspici\",\"postcode\":\"Consequuntur in aut\",\"country\":\"Rerum qui commodo ra\"},{\"type\":\"shipping\",\"street\":\"Est consequat Dolor\",\"city\":\"In odit ut quisquam\",\"state\":\"Elit illum consequ\",\"postcode\":\"Adipisci omnis cupid\",\"country\":\"Aut atque cillum con\"},{\"type\":\"shipping\",\"street\":\"Eius sit eveniet s\",\"city\":\"Enim nesciunt quo d\",\"state\":\"Aliquam modi saepe c\",\"postcode\":\"Reprehenderit aut n\",\"country\":\"Vel qui sunt laboris\"},{\"type\":\"shipping\",\"street\":\"Totam natus consequa\",\"city\":\"Inventore nostrud di\",\"state\":\"Error esse quis sed\",\"postcode\":\"Et eum minima quas d\",\"country\":\"Eius eaque alias ad\"}]', '+1 (779) 617-5941', '+1 (241) 659-1575', 'https://www.xugutepenos.co', 'vendor', 'home_apparel', 46, 30.00, 'Fugit et esse eiusm', 'Excepteur porro simi', 'Ratione et deserunt', 'Laboriosam perspici', 'Consequuntur in aut', 'Rerum qui commodo ra', 'Est consequat Dolor', 'In odit ut quisquam', 'Elit illum consequ', 'Adipisci omnis cupid', 'Aut atque cillum con', '2026-04-16 18:57:24', '2026-04-16 18:57:24', NULL),
(10, NULL, 'Vendor 8', '[]', '', '', '', 'vendor', '', 0, 0.00, '', '', '', '', '', '', '', '', '', '', '', '2026-04-16 18:57:46', '2026-04-16 18:57:46', NULL),
(11, NULL, 'Vendor 9', '[]', '', '', '', 'vendor', '', 0, 0.00, '', '', '', '', '', '', '', '', '', '', '', '2026-04-16 18:57:56', '2026-04-16 18:57:56', NULL),
(12, NULL, 'Vendor 10', '[]', '', '', '', 'vendor', '', 0, 0.00, '', '', '', '', '', '', '', '', '', '', '', '2026-04-16 18:58:12', '2026-04-16 18:58:12', NULL),
(13, NULL, 'Metro Cotton Mill (PVT) LTD', NULL, '03176198517', NULL, 'deveoninc.com', NULL, NULL, NULL, NULL, 'Software company', '4327 Summerfield Blvd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-18 11:40:15', '2026-04-18 11:40:15', NULL),
(14, NULL, 'Anika Dale', '[{\"type\":\"billing\",\"street\":\"Molestias est sint e\",\"city\":\"Ratione laborum quis\",\"state\":\"Culpa vitae aut pro\",\"postcode\":\"Ad debitis similique\",\"country\":\"Modi est molestias d\"},{\"type\":\"shipping\",\"street\":\"Molestias est sint e\",\"city\":\"Ratione laborum quis\",\"state\":\"Culpa vitae aut pro\",\"postcode\":\"Ad debitis similique\",\"country\":\"Modi est molestias d\"},{\"type\":\"shipping\",\"street\":\"Facilis sed esse und\",\"city\":\"Alias accusamus non\",\"state\":\"Quis reiciendis ulla\",\"postcode\":\"Maiores non dolores\",\"country\":\"Tempore quia optio\"},{\"type\":\"billing\",\"street\":\"Laudantium ipsam vo\",\"city\":\"Fugit maxime incidi\",\"state\":\"Irure officia quod q\",\"postcode\":\"Quo incidunt libero\",\"country\":\"Non exercitationem l\"}]', '+1 (623) 116-1432', '+1 (245) 541-2562', 'https://www.nirigotynebe.biz', 'customer', 'other_shipping', 52, 79.00, 'Cupiditate amet cum', 'Molestias est sint e', 'Ratione laborum quis', 'Culpa vitae aut pro', 'Ad debitis similique', 'Modi est molestias d', 'Molestias est sint e', 'Ratione laborum quis', 'Culpa vitae aut pro', 'Ad debitis similique', 'Modi est molestias d', '2026-05-17 15:17:23', '2026-05-17 15:17:23', NULL),
(15, NULL, 'Testttttt', NULL, NULL, NULL, NULL, 'customer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-12 19:10:16', '2026-07-12 19:10:16', NULL),
(16, NULL, 'fgfg', NULL, NULL, NULL, NULL, 'customer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-12 19:11:27', '2026-07-12 19:11:27', NULL),
(17, NULL, 'sdsd', NULL, NULL, NULL, NULL, 'vendor', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-12 19:12:09', '2026-07-12 19:12:09', NULL),
(18, NULL, 'fdsfdsff', NULL, NULL, NULL, NULL, 'customer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-12 19:49:14', '2026-07-12 19:49:14', NULL),
(19, NULL, 'fhtutfhdfhrtu', NULL, NULL, NULL, NULL, 'vendor', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-12 19:49:26', '2026-07-12 19:49:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `organization_activities`
--

CREATE TABLE `organization_activities` (
  `activity_id` int UNSIGNED NOT NULL,
  `organization_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `organization_activities`
--

INSERT INTO `organization_activities` (`activity_id`, `organization_id`) VALUES
(394, 3),
(402, 3),
(403, 3),
(440, 14),
(441, 14),
(442, 14),
(468, 14),
(469, 14),
(470, 14),
(503, 3),
(504, 3),
(505, 15),
(506, 16),
(507, 17),
(508, 18),
(509, 19);

-- --------------------------------------------------------

--
-- Table structure for table `organization_files`
--

CREATE TABLE `organization_files` (
  `id` int UNSIGNED NOT NULL,
  `organization_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` int DEFAULT NULL,
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
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'customer',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `completion_date` date DEFAULT NULL,
  `last_delivery_date` date DEFAULT NULL,
  `payment_term` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emails` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `contact_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `organization_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `job_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `unique_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salutation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cell_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direct_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_secondary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mailing_street` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mailing_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mailing_state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mailing_postcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mailing_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `persons`
--

INSERT INTO `persons` (`id`, `name`, `type`, `notes`, `completion_date`, `last_delivery_date`, `payment_term`, `shipping_method`, `emails`, `contact_numbers`, `organization_id`, `created_at`, `updated_at`, `job_title`, `user_id`, `unique_id`, `salutation`, `first_name`, `last_name`, `title`, `description`, `cell_phone`, `direct_phone`, `email_secondary`, `birth_date`, `phone`, `email`, `mailing_street`, `mailing_city`, `mailing_state`, `mailing_postcode`, `mailing_country`) VALUES
(12, '', 'customer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, '2026-04-14 18:59:02', '2026-04-14 18:59:02', NULL, NULL, NULL, '', 'Freya', 'Benjamin', 'Magna obcaecati accu', 'Aliquip dolor quia d', '+1 (399) 709-7948', '+1 (231) 592-5775', '+1 (231) 592-5775', '2009-01-01', '+1 (525) 299-3773', 'tuhymys@mailinator.com', 'Est esse omnis ea e', 'Velit architecto inc', 'Odio alias atque dig', 'Hic similique ut tem', 'Quia do aut dolore p'),
(13, 'Ian Ford', 'employee', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 13, '2026-04-18 11:40:16', '2026-04-18 11:40:16', NULL, NULL, NULL, NULL, 'Ian', 'Ford', NULL, NULL, NULL, NULL, NULL, '1990-01-01', '+1 (738) 226-9395', 'kogik@mailinator.com', 'Sapiente ipsum praes', 'Est qui labore conse', 'Est voluptates sint', 'Ratione aliquid reru', 'Reprehenderit enim i'),
(14, 'Zahir Rocha', 'customer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-17 12:25:45', '2026-05-17 12:25:45', NULL, 1, NULL, '', 'Zahir', 'Rocha', 'Esse nihil vitae a', 'Recusandae Delectus', '+1 (944) 881-5267', '+1 (369) 815-5036', '+1 (369) 815-5036', '2010-01-01', '+1 (138) 869-9962', 'tihagol@mailinator.com', 'Deleniti nobis ea to', 'Id dolore culpa aut', 'Consectetur sunt is', 'Iure reiciendis cons', 'Corporis qui tempora'),
(15, 'Liberty Matthews', 'customer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 14, '2026-05-17 15:18:20', '2026-05-17 15:18:20', NULL, 1, NULL, '', 'Liberty', 'Matthews', 'Culpa facilis velit', 'Occaecat nobis debit', '+1 (558) 124-7179', '+1 (753) 185-4192', '+1 (753) 185-4192', '2010-01-01', '+1 (166) 693-9116', 'gyxul@mailinator.com', 'In dolor dolores nih', 'Molestias tempore q', 'Adipisicing et sed i', 'Laboriosam ullam ni', 'Exercitationem nemo'),
(16, 'Candice Bullock', 'customer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 14, '2026-05-17 15:18:29', '2026-05-17 15:18:29', NULL, 1, NULL, '', 'Candice', 'Bullock', 'Harum voluptatem vol', 'Minim qui quaerat re', '+1 (529) 196-4568', '+1 (511) 937-9721', '+1 (511) 937-9721', '2018-01-01', '+1 (474) 823-2914', 'five@mailinator.com', 'Dolor rerum ut moles', 'Duis ab dolores offi', 'Occaecat inventore n', 'Dolores nulla molest', 'Architecto tempora a'),
(17, 'Rinah Christian', 'customer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 14, '2026-05-17 15:18:34', '2026-05-17 15:18:34', NULL, 1, NULL, '', 'Rinah', 'Christian', 'Inventore eiusmod un', 'In voluptatem sunt s', '+1 (186) 759-1375', '+1 (228) 625-6623', '+1 (228) 625-6623', '1974-01-01', '+1 (844) 974-5879', 'wure@mailinator.com', 'Atque ut fugiat con', 'Cumque perspiciatis', 'Perferendis adipisci', 'Corrupti at totam e', 'Est sed debitis ea'),
(18, 'Ulysses Velazquez', 'customer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 14, '2026-05-17 15:18:41', '2026-05-17 15:18:41', NULL, 1, NULL, '', 'Ulysses', 'Velazquez', 'Sed quisquam dolor e', 'Sint qui odit est r', '+1 (857) 737-8862', '+1 (799) 667-2394', '+1 (799) 667-2394', '1975-01-01', '+1 (458) 215-3134', 'wexifyw@mailinator.com', 'Culpa aperiam rerum', 'Et sit consectetur', 'Repellendus Ipsum', 'Ea laboris quisquam', 'Irure enim laboris n'),
(19, 'Xandra Frazier', 'vendor', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, '2026-05-17 15:24:56', '2026-05-17 15:24:56', NULL, 1, NULL, '', 'Xandra', 'Frazier', 'Consectetur dolor s', 'Tempore placeat qu', '+1 (235) 676-7948', '+1 (568) 174-1763', '+1 (568) 174-1763', '1999-01-01', '+1 (585) 596-1481', 'vazun@mailinator.com', 'Consectetur omnis ev', 'Iure sed qui et esse', 'Aute consectetur inc', 'Ut asperiores except', 'Eos eaque tempor ci');

-- --------------------------------------------------------

--
-- Table structure for table `person_activities`
--

CREATE TABLE `person_activities` (
  `activity_id` int UNSIGNED NOT NULL,
  `person_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `person_activities`
--

INSERT INTO `person_activities` (`activity_id`, `person_id`) VALUES
(393, 12),
(425, 14),
(426, 14),
(427, 14),
(443, 15),
(444, 15),
(445, 15),
(446, 15),
(447, 16),
(448, 16),
(449, 16),
(450, 16),
(451, 17),
(452, 17),
(453, 17),
(454, 17),
(455, 18),
(456, 18),
(457, 18),
(458, 18),
(467, 18),
(471, 19),
(472, 19),
(473, 19),
(474, 19),
(475, 19);

-- --------------------------------------------------------

--
-- Table structure for table `person_tags`
--

CREATE TABLE `person_tags` (
  `tag_id` int UNSIGNED NOT NULL,
  `person_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int UNSIGNED NOT NULL,
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_organization_id` int UNSIGNED DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `publish_on_website` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `price` decimal(12,4) DEFAULT NULL,
  `cost_price` decimal(12,4) DEFAULT NULL,
  `selling_price` decimal(12,4) DEFAULT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `style` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` decimal(12,4) DEFAULT NULL,
  `weight_unit` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `additional_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `shipping_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `internal_code`, `name`, `customer_organization_id`, `slug`, `description`, `quantity`, `publish_on_website`, `price`, `cost_price`, `selling_price`, `category_id`, `style`, `size`, `weight`, `weight_unit`, `cover_image`, `additional_info`, `shipping_info`, `created_at`, `updated_at`) VALUES
(21, 'MQTB', 'MQTB', 'Cotton Tote Bag', 3, 'cotton-tote-bag', NULL, 0, 0, 4.7200, 3.5000, 4.7200, NULL, NULL, '15\"W x 16\"H', 13.0000, 'gsm', 'product-images/Gt8rXILv7TUouhxluLD2waYPDC9FxQYtVfHpff6j.jpg', NULL, NULL, '2026-03-18 16:50:41', '2026-05-17 15:30:54'),
(32, 'Product 2', 'Product 2', 'Cotton Tote Bag', 3, 'cotton-tote-bag-1', NULL, 0, 0, 4.7200, 3.5000, 4.7200, NULL, NULL, '15\"W x 16\"H', NULL, NULL, 'product-images/69e3e16473635.jpg', NULL, NULL, '2026-04-18 20:24:12', '2026-04-18 20:24:12'),
(33, 'Nesciunt eiusmod in', 'Magnam exercitatione', 'Sacha Daugherty', NULL, 'sacha-daugherty', NULL, 0, 0, 28.0000, 488.0000, 28.0000, NULL, NULL, 'Quis ea incididunt d', 97.0000, 'oz', NULL, NULL, NULL, '2026-05-16 14:41:06', '2026-05-16 14:41:06'),
(34, 'ST00', 'ST00', 'Supplier Product', 3, 'supplier-product', NULL, 0, 0, 14.0000, 12.0000, 14.0000, NULL, NULL, '63 hh', 34.0000, 'oz', 'product-images/Ov1PO6FiuackB6wHhhB0JLmBZt9TJtOOWtjMm4sq.jpg', NULL, NULL, '2026-05-17 15:16:15', '2026-05-17 15:16:15');

-- --------------------------------------------------------

--
-- Table structure for table `product_activities`
--

CREATE TABLE `product_activities` (
  `activity_id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_activities`
--

INSERT INTO `product_activities` (`activity_id`, `product_id`) VALUES
(436, 34),
(437, 34),
(438, 34),
(439, 34),
(476, 21),
(477, 21),
(478, 21),
(479, 21),
(480, 21);

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost_price` decimal(12,4) DEFAULT NULL,
  `selling_price` decimal(12,4) DEFAULT NULL,
  `sort_order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_colors`
--

INSERT INTO `product_colors` (`id`, `product_id`, `name`, `color_code`, `cost_price`, `selling_price`, `sort_order`, `created_at`, `updated_at`) VALUES
(894, 32, 'red', '#FF0000', NULL, NULL, 0, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(895, 32, 'Navy', '#4b00d6', NULL, NULL, 1, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(896, 32, 'Hot Pink', '#d6009d', NULL, NULL, 2, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(897, 33, 'Allistair Avila', '#72f60b', 241.0000, 753.0000, 0, '2026-05-16 14:41:07', '2026-05-16 14:41:07'),
(898, 34, 'red', '#FF0000', 12.0000, 23.0000, 0, '2026-05-17 15:16:15', '2026-05-17 15:16:15'),
(899, 21, 'red', '#FF0000', NULL, NULL, 0, '2026-05-17 15:30:54', '2026-05-17 15:30:54'),
(900, 21, 'Navy', '#4b00d6', NULL, NULL, 1, '2026-05-17 15:30:54', '2026-05-17 15:30:54'),
(901, 21, 'Hot Pink', '#d6009d', NULL, NULL, 2, '2026-05-17 15:30:54', '2026-05-17 15:30:54');

-- --------------------------------------------------------

--
-- Table structure for table `product_consumptions`
--

CREATE TABLE `product_consumptions` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `material_reference_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor_ids` json DEFAULT NULL,
  `color_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_consumptions`
--

INSERT INTO `product_consumptions` (`id`, `product_id`, `material_reference_id`, `name`, `qty`, `unit`, `vendor_ids`, `color_name`, `color_code`, `sort_order`, `created_at`, `updated_at`) VALUES
(32, 32, NULL, 'Cotton Fabric', 0.3500, 'Meter', NULL, NULL, NULL, 0, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(33, 32, NULL, 'Handle', 20.0000, 'Inch', NULL, NULL, NULL, 1, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(34, 32, NULL, 'Label', 1.0000, 'Piece', NULL, NULL, NULL, 2, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(35, 32, NULL, 'Thread', 5.0000, 'Meter', NULL, NULL, NULL, 3, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(36, 33, NULL, 'Orli Daniel', 76.0000, 'Vel aute dolorem tem', NULL, 'Kristen Fulton', '#BB13AB', 0, '2026-05-16 14:41:07', '2026-05-16 14:41:07'),
(37, 34, 3, '20x20 / 60x66 - 66\" | 150gsm', 0.3120, 'METER', '[6, 9]', NULL, NULL, 0, '2026-05-17 15:16:15', '2026-05-17 15:16:15'),
(38, 34, 7, 'Cartons (240 pcs / Box)', 0.0420, 'INCH', '[10]', NULL, NULL, 1, '2026-05-17 15:16:15', '2026-05-17 15:16:15'),
(39, 34, 5, 'Oekotex Label', 1.0000, 'INCH', '[4]', 'red', '#FF0000', 2, '2026-05-17 15:16:15', '2026-05-17 15:16:15'),
(40, 21, 7, 'Cartons (240 pcs / Box)', 0.3500, 'Meter', NULL, 'red', '#FF0000', 0, '2026-05-17 15:30:54', '2026-05-17 15:30:54'),
(41, 21, 3, '20x20 / 60x66 - 66\" | 150gsm', 20.0000, 'Inch', '[6, 9]', NULL, NULL, 1, '2026-05-17 15:30:55', '2026-05-17 15:30:55'),
(42, 21, 5, 'Oekotex Label', 1.0000, 'Piece', NULL, NULL, NULL, 2, '2026-05-17 15:30:55', '2026-05-17 15:30:55'),
(43, 21, 7, 'Cartons (240 pcs / Box)', 5.0000, 'Meter', NULL, 'red', '#FF0000', 3, '2026-05-17 15:30:55', '2026-05-17 15:30:55');

-- --------------------------------------------------------

--
-- Table structure for table `product_inventories`
--

CREATE TABLE `product_inventories` (
  `id` int UNSIGNED NOT NULL,
  `in_stock` int NOT NULL DEFAULT '0',
  `allocated` int NOT NULL DEFAULT '0',
  `product_id` int UNSIGNED NOT NULL,
  `warehouse_id` int UNSIGNED DEFAULT NULL,
  `warehouse_location_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_key_points`
--

CREATE TABLE `product_key_points` (
  `id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `key_heading` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_point` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_other_images`
--

CREATE TABLE `product_other_images` (
  `id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `color_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_other_images`
--

INSERT INTO `product_other_images` (`id`, `product_id`, `path`, `original_name`, `sort_order`, `color_id`, `created_at`, `updated_at`) VALUES
(29, 21, 'product-other-images/G1IkWq1s5Cf8jwRm2Av13rGcgpzQv3ku5X6dRMo5.jpg', 'MQTB-Red (1).jpg', 1, 899, '2026-03-18 16:53:27', '2026-05-17 15:30:54'),
(30, 21, 'product-other-images/YlTgoP11V8dXT6nSAM0KkHZuwZ3LgdkbBUo5e4BI.jpg', 'MQTB-Navy (1).jpg', 3, 900, '2026-03-18 16:53:27', '2026-05-17 15:30:54'),
(31, 21, 'product-other-images/p9KkHMcVDvS4235OcskCJENrwJLDoTsHOP2D60Sq.jpg', 'MQTB-Hot-Pink.jpg', 4, 901, '2026-03-18 16:55:06', '2026-05-17 15:30:54'),
(41, 32, 'product-other-images/69e3e164a1c96.jpg', 'MQTB-Red (1).jpg', 0, 894, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(42, 32, 'product-other-images/69e3e164ad5e9.jpg', 'MQTB-Navy (1).jpg', 1, 895, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(43, 32, 'product-other-images/69e3e164b8538.jpg', 'MQTB-Hot-Pink.jpg', 2, 896, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(44, 34, 'product-other-images/FTdi0TnmLaNJo2g9g9Sf2xr7XTvSCU1XvZPUwFaX.jpg', 'Br0J60BxLu7DpxC5765zAku58KkqipTzEtMgpmFE.jpg', 0, 898, '2026-05-17 15:16:15', '2026-05-17 15:16:15');

-- --------------------------------------------------------

--
-- Table structure for table `product_pricing_charts`
--

CREATE TABLE `product_pricing_charts` (
  `id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `heading` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_pricing_chart_tiers`
--

CREATE TABLE `product_pricing_chart_tiers` (
  `id` int UNSIGNED NOT NULL,
  `product_pricing_chart_id` int UNSIGNED NOT NULL,
  `product_pricing_chart_type_id` int UNSIGNED DEFAULT NULL,
  `quantity` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `sort_order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_pricing_chart_types`
--

CREATE TABLE `product_pricing_chart_types` (
  `id` int UNSIGNED NOT NULL,
  `product_pricing_chart_id` int UNSIGNED NOT NULL,
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_production_sections`
--

CREATE TABLE `product_production_sections` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `section_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_production_sections`
--

INSERT INTO `product_production_sections` (`id`, `product_id`, `section_name`, `sort_order`, `created_at`, `updated_at`) VALUES
(27, 32, 'Cutting', 0, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(28, 32, 'Stitching', 1, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(29, 32, 'Finishing', 2, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(30, 32, 'Quality', 3, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(31, 32, 'Packing', 4, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(32, 33, 'Connor Weeks', 0, '2026-05-16 14:41:07', '2026-05-16 14:41:07'),
(33, 21, 'Cutting', 0, '2026-05-17 15:30:55', '2026-05-17 15:30:55'),
(34, 21, 'Stitching', 1, '2026-05-17 15:30:55', '2026-05-17 15:30:55'),
(35, 21, 'Finishing', 2, '2026-05-17 15:30:55', '2026-05-17 15:30:55'),
(36, 21, 'Quality', 3, '2026-05-17 15:30:55', '2026-05-17 15:30:55'),
(37, 21, 'Packing', 4, '2026-05-17 15:30:55', '2026-05-17 15:30:55');

-- --------------------------------------------------------

--
-- Table structure for table `product_production_section_items`
--

CREATE TABLE `product_production_section_items` (
  `id` bigint UNSIGNED NOT NULL,
  `product_production_section_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_production_section_items`
--

INSERT INTO `product_production_section_items` (`id`, `product_production_section_id`, `name`, `qty`, `unit`, `sort_order`, `created_at`, `updated_at`) VALUES
(35, 27, 'Fabric Width', 66.0000, 'Inch', 0, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(36, 27, 'Cutting', 40.0000, 'Inch', 1, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(37, 28, 'Thread Usage', 5.0000, 'Meter', 0, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(38, 28, 'Machine Type', 1.0000, 'Unit', 1, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(39, 29, 'Iron Pass', 1.0000, 'Pass', 0, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(40, 30, 'QC Check', 1.0000, 'Check', 0, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(41, 31, 'Pieces Per Carton', 100.0000, 'Piece', 0, '2026-04-18 20:24:13', '2026-04-18 20:24:13'),
(42, 32, 'Jonas Underwood', 341.0000, 'Consectetur ut laud', 0, '2026-05-16 14:41:07', '2026-05-16 14:41:07'),
(43, 33, 'Fabric Width', 66.0000, 'Inch', 0, '2026-05-17 15:30:55', '2026-05-17 15:30:55'),
(44, 33, 'Cutting', 40.0000, 'Inch', 1, '2026-05-17 15:30:55', '2026-05-17 15:30:55'),
(45, 34, 'Thread Usage', 5.0000, 'Meter', 0, '2026-05-17 15:30:55', '2026-05-17 15:30:55'),
(46, 34, 'Machine Type', 1.0000, 'Unit', 1, '2026-05-17 15:30:55', '2026-05-17 15:30:55'),
(47, 35, 'Iron Pass', 1.0000, 'Pass', 0, '2026-05-17 15:30:55', '2026-05-17 15:30:55'),
(48, 36, 'QC Check', 1.0000, 'Check', 0, '2026-05-17 15:30:55', '2026-05-17 15:30:55'),
(49, 37, 'Pieces Per Carton', 100.0000, 'Piece', 0, '2026-05-17 15:30:55', '2026-05-17 15:30:55');

-- --------------------------------------------------------

--
-- Table structure for table `product_tags`
--

CREATE TABLE `product_tags` (
  `tag_id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proforma_invoices`
--

CREATE TABLE `proforma_invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `proforma_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quote_id` int UNSIGNED DEFAULT NULL,
  `organization_id` int UNSIGNED NOT NULL,
  `person_id` int UNSIGNED DEFAULT NULL,
  `sales_owner_id` int UNSIGNED DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `subtotal` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `discount_percent` decimal(12,4) DEFAULT '0.0000',
  `discount_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `tax_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `adjustment_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `grand_total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `received_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `remaining_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payment_term` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_po_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `converted_to_invoice_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `approved_by` int UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `attachment_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `proforma_invoices`
--

INSERT INTO `proforma_invoices` (`id`, `proforma_number`, `quote_id`, `organization_id`, `person_id`, `sales_owner_id`, `subject`, `issue_date`, `due_date`, `billing_address`, `shipping_address`, `subtotal`, `discount_percent`, `discount_amount`, `tax_amount`, `adjustment_amount`, `grand_total`, `received_amount`, `remaining_amount`, `status`, `notes`, `terms`, `payment_term`, `customer_po_reference`, `source_type`, `converted_to_invoice_id`, `created_by`, `approved_by`, `approved_at`, `attachment_path`, `created_at`, `updated_at`) VALUES
(10, 'PF-00001', NULL, 3, NULL, NULL, 'Quote 000001', '2026-05-17', NULL, '{\"key\":\"billing\",\"label\":\"Billing Address\",\"type\":\"billing\",\"address\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\",\"street\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\",\"city\":\"Ottawa\",\"state\":\"Ontario\",\"postcode\":\"K1Z 7T1\",\"country\":\"Canada\"}', '{\"key\":\"shipping\",\"label\":\"Shipping Address\",\"type\":\"shipping\",\"address\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\",\"street\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\",\"city\":\"Ottawa\",\"state\":\"Ontario\",\"postcode\":\"K1Z 7T1\",\"country\":\"Canada\"}', 1185.0000, 0.0000, 0.0000, 142.2000, 450.0000, 1777.2000, 0.0000, 1777.2000, 'issued', 'this is test', 'this is test', '30 days', NULL, 'quote', NULL, NULL, NULL, NULL, NULL, '2026-05-16 19:57:25', '2026-05-16 19:57:25'),
(11, 'PF-00002', NULL, 3, NULL, NULL, 'Quote 000002', '2026-05-17', NULL, '{\"key\":\"billing\",\"label\":\"Billing Address\",\"type\":\"billing\",\"address\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\",\"street\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\",\"city\":\"Ottawa\",\"state\":\"Ontario\",\"postcode\":\"K1Z 7T1\",\"country\":\"Canada\"}', '{\"key\":\"shipping\",\"label\":\"Shipping Address\",\"type\":\"shipping\",\"address\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\",\"street\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\",\"city\":\"Ottawa\",\"state\":\"Ontario\",\"postcode\":\"K1Z 7T1\",\"country\":\"Canada\"}', 7759.6800, 0.0000, 0.0000, 0.0000, 0.0000, 7759.6800, 0.0000, 7759.6800, 'issued', '', '', '', NULL, 'quote', NULL, NULL, NULL, NULL, NULL, '2026-05-16 20:06:26', '2026-05-16 20:06:26'),
(12, 'PF-00003', 20, 3, NULL, 1, 'Quote 000001', '2026-05-17', NULL, '{\"key\":\"billing\",\"label\":\"Billing Address\",\"type\":\"billing\",\"address\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\",\"street\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\",\"city\":\"Ottawa\",\"state\":\"Ontario\",\"postcode\":\"K1Z 7T1\",\"country\":\"Canada\"}', '{\"key\":\"shipping\",\"label\":\"Shipping Address\",\"type\":\"shipping\",\"address\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\",\"street\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\",\"city\":\"Ottawa\",\"state\":\"Ontario\",\"postcode\":\"K1Z 7T1\",\"country\":\"Canada\"}', 12813.0000, 0.0000, 0.0000, 1537.5600, 200.0000, 14550.5600, 0.0000, 14550.5600, 'issued', 'this is a test remarks', 'this is a test term & condition', '30 days', NULL, 'quote', NULL, 1, NULL, NULL, NULL, '2026-05-17 15:29:34', '2026-05-17 15:29:35');

-- --------------------------------------------------------

--
-- Table structure for table `proforma_invoice_items`
--

CREATE TABLE `proforma_invoice_items` (
  `id` bigint UNSIGNED NOT NULL,
  `proforma_invoice_id` bigint UNSIGNED NOT NULL,
  `product_id` int UNSIGNED DEFAULT NULL,
  `color_variant_id` bigint UNSIGNED DEFAULT NULL,
  `color_variant_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preview_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `discount_percent` decimal(12,4) DEFAULT '0.0000',
  `discount_amount` decimal(12,4) DEFAULT '0.0000',
  `tax_percent` decimal(12,4) DEFAULT '0.0000',
  `tax_amount` decimal(12,4) DEFAULT '0.0000',
  `line_subtotal` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `line_total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `sort_order` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `proforma_invoice_items`
--

INSERT INTO `proforma_invoice_items` (`id`, `proforma_invoice_id`, `product_id`, `color_variant_id`, `color_variant_name`, `preview_image`, `item_name`, `item_code`, `description`, `qty`, `unit`, `unit_price`, `discount_percent`, `discount_amount`, `tax_percent`, `tax_amount`, `line_subtotal`, `line_total`, `sort_order`, `created_at`, `updated_at`) VALUES
(11, 10, 21, 879, 'red', 'https://localhost/mcm-software/public/storage/product-other-images/G1IkWq1s5Cf8jwRm2Av13rGcgpzQv3ku5X6dRMo5.jpg', 'Cotton Tote Bag', 'MQTB', NULL, 1500.0000, '', 0.7900, 0.0000, 0.0000, 0.0000, 0.0000, 1185.0000, 1185.0000, 0, '2026-05-16 19:57:25', '2026-05-16 19:57:25'),
(12, 11, 21, 881, 'Hot Pink', 'https://localhost/mcm-software/public/storage/product-images/Gt8rXILv7TUouhxluLD2waYPDC9FxQYtVfHpff6j.jpg', 'Cotton Tote Bag', 'MQTB', NULL, 144.0000, '', 4.7200, 0.0000, 0.0000, 0.0000, 0.0000, 679.6800, 679.6800, 0, '2026-05-16 20:06:26', '2026-05-16 20:06:26'),
(13, 11, 21, 879, 'red', 'https://localhost/mcm-software/public/storage/product-images/Gt8rXILv7TUouhxluLD2waYPDC9FxQYtVfHpff6j.jpg', 'Cotton Tote Bag', 'MQTB', NULL, 1500.0000, '', 4.7200, 0.0000, 0.0000, 0.0000, 0.0000, 7080.0000, 7080.0000, 1, '2026-05-16 20:06:26', '2026-05-16 20:06:26'),
(14, 12, 21, 879, 'red', 'https://localhost/mcm-software/public/storage/product-images/Gt8rXILv7TUouhxluLD2waYPDC9FxQYtVfHpff6j.jpg', 'Cotton Tote Bag', 'MQTB', NULL, 1500.0000, '', 4.7200, 0.0000, 0.0000, 0.0000, 0.0000, 7080.0000, 7080.0000, 0, '2026-05-17 15:29:35', '2026-05-17 15:29:35'),
(15, 12, 21, 880, 'Navy', 'https://localhost/mcm-software/public/storage/product-other-images/YlTgoP11V8dXT6nSAM0KkHZuwZ3LgdkbBUo5e4BI.jpg', 'Cotton Tote Bag', 'MQTB', NULL, 1200.0000, '', 4.7200, 0.0000, 0.0000, 0.0000, 0.0000, 5664.0000, 5664.0000, 1, '2026-05-17 15:29:35', '2026-05-17 15:29:35'),
(16, 12, 34, 898, 'red', 'https://localhost/mcm-software/public/storage/product-images/Ov1PO6FiuackB6wHhhB0JLmBZt9TJtOOWtjMm4sq.jpg', 'Supplier Product', 'ST00', NULL, 3.0000, '', 23.0000, 0.0000, 0.0000, 0.0000, 0.0000, 69.0000, 69.0000, 2, '2026-05-17 15:29:35', '2026-05-17 15:29:35');

-- --------------------------------------------------------

--
-- Table structure for table `proforma_receipts`
--

CREATE TABLE `proforma_receipts` (
  `id` bigint UNSIGNED NOT NULL,
  `proforma_invoice_id` bigint UNSIGNED NOT NULL,
  `receipt_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,4) NOT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `received_by` int UNSIGNED DEFAULT NULL,
  `attachment_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int UNSIGNED NOT NULL,
  `po_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_order_id` int UNSIGNED DEFAULT NULL,
  `vendor_quote_id` int UNSIGNED DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `attachment_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `closed_by` int UNSIGNED DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `last_delivery_date` date DEFAULT NULL,
  `expected_receive_date` date DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `payment_term` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sales_tax_percent` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `freight` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `sub_total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `tax_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `grand_total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `person_id` int UNSIGNED DEFAULT NULL,
  `organization_id` int UNSIGNED DEFAULT NULL,
  `billing_address` json DEFAULT NULL,
  `shipping_address` json DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `po_number`, `job_number`, `job_order_id`, `vendor_quote_id`, `description`, `notes`, `terms`, `attachment_path`, `closed_at`, `closed_by`, `completion_date`, `last_delivery_date`, `expected_receive_date`, `status`, `payment_term`, `shipping_method`, `sales_tax_percent`, `freight`, `sub_total`, `tax_amount`, `grand_total`, `person_id`, `organization_id`, `billing_address`, `shipping_address`, `user_id`, `created_at`, `updated_at`) VALUES
(13, 'PO-00001', NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'issued', 'djkfjk', 'kjjkjk', 0.0000, 20000.0000, 64902.2400, 0.0000, 84902.2400, NULL, 12, '{\"key\": null, \"city\": null, \"type\": \"billing\", \"label\": null, \"state\": null, \"street\": null, \"address\": \"\", \"country\": null, \"postcode\": null}', '{\"key\": null, \"city\": null, \"type\": \"shipping\", \"label\": null, \"state\": null, \"street\": null, \"address\": \"\", \"country\": null, \"postcode\": null}', NULL, '2026-05-17 09:57:34', '2026-05-17 09:57:34'),
(14, 'PO-00002', NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'issued', 'djkfjk', 'kjjkjk', 0.0000, 20000.0000, 78975.0000, 0.0000, 98975.0000, NULL, 6, '{\"key\": \"billing\", \"city\": \"sad\", \"type\": \"billing\", \"label\": \"Billing Address\", \"state\": \"sad\", \"street\": \"sadsa\", \"address\": \"sadsa\\nsad, sad, sad, sad\", \"country\": \"sad\", \"postcode\": \"sad\"}', '{\"key\": \"shipping\", \"city\": \"sad\", \"type\": \"shipping\", \"label\": \"Shipping Address\", \"state\": \"sad\", \"street\": \"sadsa\", \"address\": \"sadsa\\nsad, sad, sad, sad\", \"country\": \"sad\", \"postcode\": \"sad\"}', NULL, '2026-05-17 09:57:34', '2026-05-17 09:57:34'),
(15, 'PO-00003', NULL, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'issued', NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, 6, '{\"key\": \"billing\", \"city\": \"sad\", \"type\": \"billing\", \"label\": \"Billing Address\", \"state\": \"sad\", \"street\": \"sadsa\", \"address\": \"sadsa\\nsad, sad, sad, sad\", \"country\": \"sad\", \"postcode\": \"sad\"}', '{\"key\": \"shipping\", \"city\": \"sad\", \"type\": \"shipping\", \"label\": \"Shipping Address\", \"state\": \"sad\", \"street\": \"sadsa\", \"address\": \"sadsa\\nsad, sad, sad, sad\", \"country\": \"sad\", \"postcode\": \"sad\"}', 1, '2026-05-19 14:56:45', '2026-05-19 14:56:45'),
(16, 'PO-00004', NULL, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'issued', NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, 10, '{\"key\": null, \"city\": null, \"type\": \"billing\", \"label\": null, \"state\": null, \"street\": null, \"address\": \"\", \"country\": null, \"postcode\": null}', '{\"key\": null, \"city\": null, \"type\": \"shipping\", \"label\": null, \"state\": null, \"street\": null, \"address\": \"\", \"country\": null, \"postcode\": null}', 1, '2026-05-19 14:56:45', '2026-05-19 14:56:45'),
(17, 'PO-00005', NULL, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'fully_received', NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, 4, '{\"key\": \"billing\", \"city\": \"Toronto\", \"type\": \"billing\", \"label\": \"Billing Address\", \"state\": \"Ontario\", \"street\": \"4327 Summerfield Blvd\", \"address\": \"4327 Summerfield Blvd\\nToronto, Ontario, M5V 3L9, Canada\", \"country\": \"Canada\", \"postcode\": \"M5V 3L9\"}', '{\"key\": \"shipping\", \"city\": \"Toronto\", \"type\": \"shipping\", \"label\": \"Shipping Address\", \"state\": \"Ontario\", \"street\": \"4327 Summerfield Blvd\", \"address\": \"4327 Summerfield Blvd\\nToronto, Ontario, M5V 3L9, Canada\", \"country\": \"Canada\", \"postcode\": \"M5V 3L9\"}', 1, '2026-05-19 14:56:45', '2026-05-19 15:25:51');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` int UNSIGNED NOT NULL,
  `item` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `material_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `quantity` int NOT NULL DEFAULT '1',
  `ordered_quantity` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `received_quantity` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `pending_quantity` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `unit` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected_receive_date` date DEFAULT NULL,
  `line_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `purchase_order_id` int UNSIGNED NOT NULL,
  `requirement_id` int UNSIGNED DEFAULT NULL,
  `product_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `item`, `material_name`, `description`, `quantity`, `ordered_quantity`, `received_quantity`, `pending_quantity`, `unit`, `expected_receive_date`, `line_status`, `price`, `total`, `purchase_order_id`, `requirement_id`, `product_id`, `created_at`, `updated_at`) VALUES
(49, 'Cotton Fabric', 'Cotton Fabric', NULL, 50, 50.4000, 0.0000, 50.4000, 'PCS', NULL, 'open', 2.0000, 100.8000, 13, NULL, NULL, '2026-05-17 09:57:34', '2026-05-17 09:57:34'),
(50, 'Handle', 'Handle', NULL, 2880, 2880.0000, 0.0000, 2880.0000, 'PCS', NULL, 'open', 22.0000, 63360.0000, 13, NULL, NULL, '2026-05-17 09:57:34', '2026-05-17 09:57:34'),
(51, 'Label', 'Label', NULL, 144, 144.0000, 0.0000, 144.0000, 'PCS', NULL, 'open', 0.0100, 1.4400, 13, NULL, NULL, '2026-05-17 09:57:34', '2026-05-17 09:57:34'),
(52, 'Thread', 'Thread', NULL, 720, 720.0000, 0.0000, 720.0000, 'PCS', NULL, 'open', 2.0000, 1440.0000, 13, NULL, NULL, '2026-05-17 09:57:34', '2026-05-17 09:57:34'),
(53, 'Cotton Fabric', 'Cotton Fabric', NULL, 525, 525.0000, 0.0000, 525.0000, 'PCS', NULL, 'open', 2.0000, 1050.0000, 14, NULL, NULL, '2026-05-17 09:57:34', '2026-05-17 09:57:34'),
(54, 'Handle', 'Handle', NULL, 30000, 30000.0000, 0.0000, 30000.0000, 'PCS', NULL, 'open', 2.0000, 60000.0000, 14, NULL, NULL, '2026-05-17 09:57:34', '2026-05-17 09:57:34'),
(55, 'Label', 'Label', NULL, 1500, 1500.0000, 0.0000, 1500.0000, 'PCS', NULL, 'open', 2.0000, 3000.0000, 14, NULL, NULL, '2026-05-17 09:57:34', '2026-05-17 09:57:34'),
(56, 'Thread', 'Thread', NULL, 7500, 7500.0000, 0.0000, 7500.0000, 'PCS', NULL, 'open', 1.9900, 14925.0000, 14, NULL, NULL, '2026-05-17 09:57:34', '2026-05-17 09:57:34'),
(57, '20x20 / 60x66 - 66\" | 150gsm', '20x20 / 60x66 - 66\" | 150gsm', NULL, 610, 609.6000, 0.0000, 609.6000, 'PCS', NULL, 'open', 0.0000, 0.0000, 15, NULL, NULL, '2026-05-19 14:56:45', '2026-05-19 14:56:45'),
(58, '20x20 / 60x66 - 66\" | 150gsm', '20x20 / 60x66 - 66\" | 150gsm', NULL, 763, 762.9360, 0.0000, 762.9360, 'PCS', NULL, 'open', 0.0000, 0.0000, 15, NULL, NULL, '2026-05-19 14:56:45', '2026-05-19 14:56:45'),
(59, 'Cartons (240 pcs / Box)', 'Cartons (240 pcs / Box)', NULL, 14445, 14445.0032, 0.0000, 14445.0032, 'PCS', NULL, 'open', 0.0000, 0.0000, 16, NULL, NULL, '2026-05-19 14:56:45', '2026-05-19 14:56:45'),
(60, 'Oekotex Label', 'Oekotex Label', NULL, 0, 0.0762, 0.0762, 0.0000, 'PCS', NULL, 'fully_received', 0.0000, 0.0000, 17, NULL, NULL, '2026-05-19 14:56:45', '2026-05-19 15:25:51');

-- --------------------------------------------------------

--
-- Table structure for table `quotes`
--

CREATE TABLE `quotes` (
  `id` int UNSIGNED NOT NULL,
  `quote_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quote_date` date DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payment_term` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `production_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transit_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etd` date DEFAULT NULL,
  `eta` date DEFAULT NULL,
  `attachment_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `discount_percent` decimal(12,4) DEFAULT '0.0000',
  `tariff_percent` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `freight_percent` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `discount_amount` decimal(12,4) DEFAULT NULL,
  `tax_amount` decimal(12,4) DEFAULT NULL,
  `adjustment_amount` decimal(12,4) DEFAULT NULL,
  `sub_total` decimal(12,4) DEFAULT NULL,
  `grand_total` decimal(12,4) DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `expired_at` datetime DEFAULT NULL,
  `person_id` int UNSIGNED DEFAULT NULL,
  `organization_id` int UNSIGNED DEFAULT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `quotes`
--

INSERT INTO `quotes` (`id`, `quote_number`, `subject`, `quote_date`, `description`, `notes`, `terms`, `payment_term`, `shipping_method`, `production_time`, `transit_time`, `etd`, `eta`, `attachment_path`, `billing_address`, `shipping_address`, `discount_percent`, `tariff_percent`, `freight_percent`, `discount_amount`, `tax_amount`, `adjustment_amount`, `sub_total`, `grand_total`, `status`, `expired_at`, `person_id`, `organization_id`, `user_id`, `created_at`, `updated_at`) VALUES
(20, '000001', 'Quote 000001', '2026-05-17', NULL, 'this is a test remarks', 'this is a test term & condition', '30 days', '30 days', '30 days', '30 days', '2026-05-17', '2026-05-19', NULL, '{\"key\":\"billing\",\"label\":\"Billing Address\",\"type\":\"billing\",\"address\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\",\"street\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\",\"city\":\"Ottawa\",\"state\":\"Ontario\",\"postcode\":\"K1Z 7T1\",\"country\":\"Canada\"}', '{\"key\":\"shipping\",\"label\":\"Shipping Address\",\"type\":\"shipping\",\"address\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\",\"street\":\"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\",\"city\":\"Ottawa\",\"state\":\"Ontario\",\"postcode\":\"K1Z 7T1\",\"country\":\"Canada\"}', 0.0000, 12.0000, 0.0000, 0.0000, 1537.5600, 200.0000, 12813.0000, 14550.5600, 'open', NULL, NULL, 3, 1, '2026-05-17 15:28:56', '2026-05-17 15:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `quote_items`
--

CREATE TABLE `quote_items` (
  `id` int UNSIGNED NOT NULL,
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int DEFAULT '0',
  `unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `unit_price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `coupon_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_percent` decimal(12,4) DEFAULT '0.0000',
  `discount_amount` decimal(12,4) DEFAULT '0.0000',
  `tax_percent` decimal(12,4) DEFAULT '0.0000',
  `tax_amount` decimal(12,4) DEFAULT '0.0000',
  `line_subtotal` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `line_total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `sort_order` int DEFAULT NULL,
  `total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `product_id` int UNSIGNED DEFAULT NULL,
  `color_variant_id` bigint UNSIGNED DEFAULT NULL,
  `color_variant_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preview_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quote_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quote_items`
--

INSERT INTO `quote_items` (`id`, `sku`, `item_code`, `name`, `item_name`, `quantity`, `unit`, `price`, `unit_price`, `coupon_code`, `discount_percent`, `discount_amount`, `tax_percent`, `tax_amount`, `line_subtotal`, `line_total`, `sort_order`, `total`, `product_id`, `color_variant_id`, `color_variant_name`, `preview_image`, `quote_id`, `created_at`, `updated_at`) VALUES
(20, 'MQTB', 'MQTB', 'Cotton Tote Bag', 'Cotton Tote Bag', 1500, NULL, 4.7200, 4.7200, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 7080.0000, 7080.0000, NULL, 7080.0000, 21, 879, 'red', 'https://localhost/mcm-software/public/storage/product-images/Gt8rXILv7TUouhxluLD2waYPDC9FxQYtVfHpff6j.jpg', 20, '2026-05-17 15:28:56', '2026-05-17 15:28:56'),
(21, 'MQTB', 'MQTB', 'Cotton Tote Bag', 'Cotton Tote Bag', 1200, NULL, 4.7200, 4.7200, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 5664.0000, 5664.0000, NULL, 5664.0000, 21, 880, 'Navy', 'https://localhost/mcm-software/public/storage/product-images/Gt8rXILv7TUouhxluLD2waYPDC9FxQYtVfHpff6j.jpg', 20, '2026-05-17 15:28:56', '2026-05-17 15:28:56'),
(22, 'ST00', 'ST00', 'Supplier Product', 'Supplier Product', 3, NULL, 23.0000, 23.0000, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 69.0000, 69.0000, NULL, 69.0000, 34, 898, 'red', 'https://localhost/mcm-software/public/storage/product-images/Ov1PO6FiuackB6wHhhB0JLmBZt9TJtOOWtjMm4sq.jpg', 20, '2026-05-17 15:28:56', '2026-05-17 15:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unit_references`
--

CREATE TABLE `unit_references` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meter_conversion` decimal(16,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `unit_references`
--

INSERT INTO `unit_references` (`id`, `name`, `meter_conversion`, `created_at`, `updated_at`) VALUES
(1, 'METER', 1.00000000, '2026-04-16 17:25:23', '2026-05-17 15:39:56'),
(2, 'INCH', 0.02540000, '2026-04-16 17:25:31', '2026-04-16 17:25:31'),
(3, 'PCS', NULL, '2026-04-16 19:49:03', '2026-04-16 19:49:03'),
(4, 'CONES', NULL, '2026-04-16 19:49:11', '2026-04-16 19:49:11'),
(5, 'FOOT', 0.30480000, '2026-05-17 15:40:04', '2026-05-17 15:40:04'),
(6, 'CENTIMETER', 0.01000000, '2026-05-17 15:40:14', '2026-05-17 15:40:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `view_permission` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'global',
  `role_id` int UNSIGNED NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `status`, `view_permission`, `role_id`, `remember_token`, `created_at`, `updated_at`, `image`) VALUES
(1, 'Example Admin', 'admin@gmail.com', '$2y$10$DeTM8iUvVZBsVgxH3OnT9OYVTTZqTNAop4Yjpmrb0lqfbQ/kXcmEG', 1, 'global', 1, NULL, '2026-05-17 11:32:05', '2026-05-17 11:32:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE `user_groups` (
  `group_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_password_resets`
--

CREATE TABLE `user_password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_payables`
--

CREATE TABLE `vendor_payables` (
  `id` int UNSIGNED NOT NULL,
  `purchase_order_id` int UNSIGNED DEFAULT NULL,
  `goods_receipt_id` int UNSIGNED DEFAULT NULL,
  `organization_id` int UNSIGNED NOT NULL,
  `payable_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payable_date` date NOT NULL,
  `total_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `paid_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `remaining_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_payables`
--

INSERT INTO `vendor_payables` (`id`, `purchase_order_id`, `goods_receipt_id`, `organization_id`, `payable_number`, `payable_date`, `total_amount`, `paid_amount`, `remaining_amount`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(8, 17, 3, 4, 'VP-00001', '2026-05-19', 0.0000, 0.0000, 0.0000, 'open', 'Auto-created from goods receipt GR-00001', '2026-05-19 15:25:51', '2026-05-19 15:25:51');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_quotes`
--

CREATE TABLE `vendor_quotes` (
  `id` int UNSIGNED NOT NULL,
  `vendor_quote_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_order_id` int UNSIGNED DEFAULT NULL,
  `organization_id` int UNSIGNED NOT NULL,
  `person_id` int UNSIGNED DEFAULT NULL,
  `billing_address` json DEFAULT NULL,
  `shipping_address` json DEFAULT NULL,
  `issue_date` date NOT NULL,
  `expected_response_date` date DEFAULT NULL,
  `payment_term` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_delivery_date` date DEFAULT NULL,
  `last_delivery_date` date DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `subtotal` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `sales_tax_percent` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `sales_tax_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `freight` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `grand_total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `attachment_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_quotes`
--

INSERT INTO `vendor_quotes` (`id`, `vendor_quote_number`, `job_order_id`, `organization_id`, `person_id`, `billing_address`, `shipping_address`, `issue_date`, `expected_response_date`, `payment_term`, `shipping_method`, `first_delivery_date`, `last_delivery_date`, `status`, `notes`, `terms`, `subtotal`, `sales_tax_percent`, `sales_tax_amount`, `freight`, `grand_total`, `attachment_path`, `created_by`, `created_at`, `updated_at`) VALUES
(6, 'VQ-00001', 10, 3, NULL, '{\"key\": \"billing\", \"city\": \"Ottawa\", \"type\": \"billing\", \"label\": \"Billing Address\", \"state\": \"Ontario\", \"street\": \"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\", \"address\": \"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\", \"country\": \"Canada\", \"postcode\": \"K1Z 7T1\"}', '{\"key\": \"shipping\", \"city\": \"Ottawa\", \"type\": \"shipping\", \"label\": \"Shipping Address\", \"state\": \"Ontario\", \"street\": \"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\", \"address\": \"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\", \"country\": \"Canada\", \"postcode\": \"K1Z 7T1\"}', '2026-05-17', NULL, 'test', 'tet', '0000-00-00', '0000-00-00', 'draft', '', '', 294958.2000, 0.0000, 0.0000, 0.0000, 294958.2000, NULL, NULL, '2026-05-17 09:44:47', '2026-05-17 09:44:47'),
(7, 'VQ-00002', 10, 3, NULL, '{\"key\": \"billing\", \"city\": \"Ottawa\", \"type\": \"billing\", \"label\": \"Billing Address\", \"state\": \"Ontario\", \"street\": \"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\", \"address\": \"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\", \"country\": \"Canada\", \"postcode\": \"K1Z 7T1\"}', '{\"key\": \"shipping\", \"city\": \"Ottawa\", \"type\": \"shipping\", \"label\": \"Shipping Address\", \"state\": \"Ontario\", \"street\": \"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\", \"address\": \"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\", \"country\": \"Canada\", \"postcode\": \"K1Z 7T1\"}', '2026-05-17', NULL, 'dfj', 'uiuui', '0000-00-00', '0000-00-00', 'draft', '', '', 87495.6000, 0.0000, 0.0000, 1749912.0000, 1837407.6000, NULL, NULL, '2026-05-17 09:59:03', '2026-05-17 09:59:03'),
(8, 'VQ-00003', 12, 3, NULL, '{\"key\": \"billing\", \"city\": \"Ottawa\", \"type\": \"billing\", \"label\": \"Billing Address\", \"state\": \"Ontario\", \"street\": \"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\", \"address\": \"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\", \"country\": \"Canada\", \"postcode\": \"K1Z 7T1\"}', '{\"key\": \"shipping\", \"city\": \"Ottawa\", \"type\": \"shipping\", \"label\": \"Shipping Address\", \"state\": \"Ontario\", \"street\": \"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\", \"address\": \"Suite 391 - 1505 Laperriere Avenue, Ottawa, Ontario K1Z 7T1,\\nOttawa, Ontario, K1Z 7T1, Canada\", \"country\": \"Canada\", \"postcode\": \"K1Z 7T1\"}', '2026-05-19', NULL, '', '', '0000-00-00', '0000-00-00', 'draft', '', '', 70201.9662, 0.0000, 0.0000, 0.0000, 70201.9662, NULL, 1, '2026-05-19 14:15:12', '2026-05-19 14:15:12');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_quote_items`
--

CREATE TABLE `vendor_quote_items` (
  `id` int UNSIGNED NOT NULL,
  `vendor_quote_id` int UNSIGNED NOT NULL,
  `requirement_id` int UNSIGNED DEFAULT NULL,
  `vendor_id` int UNSIGNED DEFAULT NULL,
  `material_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `quantity` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `unit` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(12,4) DEFAULT NULL,
  `total` decimal(12,4) DEFAULT NULL,
  `vendor_lead_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected_receive_date` date DEFAULT NULL,
  `sort_order` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_quote_items`
--

INSERT INTO `vendor_quote_items` (`id`, `vendor_quote_id`, `requirement_id`, `vendor_id`, `material_name`, `color`, `description`, `quantity`, `unit`, `unit_price`, `total`, `vendor_lead_time`, `expected_receive_date`, `sort_order`, `created_at`, `updated_at`) VALUES
(22, 6, 37, NULL, 'Cotton Fabric', NULL, NULL, 50.4000, 'Meter', 3.0000, 151.2000, NULL, NULL, 0, '2026-05-17 09:44:47', '2026-05-17 09:44:47'),
(23, 6, 38, NULL, 'Handle', NULL, NULL, 2880.0000, 'Inch', 3.0000, 8640.0000, NULL, NULL, 1, '2026-05-17 09:44:47', '2026-05-17 09:44:47'),
(24, 6, 39, NULL, 'Label', NULL, NULL, 144.0000, 'Piece', 3.0000, 432.0000, NULL, NULL, 2, '2026-05-17 09:44:47', '2026-05-17 09:44:47'),
(25, 6, 40, NULL, 'Thread', NULL, NULL, 720.0000, 'Meter', 3.0000, 2160.0000, NULL, NULL, 3, '2026-05-17 09:44:47', '2026-05-17 09:44:47'),
(26, 6, 41, NULL, 'Cotton Fabric', NULL, NULL, 525.0000, 'Meter', 3.0000, 1575.0000, NULL, NULL, 4, '2026-05-17 09:44:47', '2026-05-17 09:44:47'),
(27, 6, 42, NULL, 'Handle', NULL, NULL, 30000.0000, 'Inch', 4.0000, 120000.0000, NULL, NULL, 5, '2026-05-17 09:44:47', '2026-05-17 09:44:47'),
(28, 6, 43, NULL, 'Label', NULL, NULL, 1500.0000, 'Piece', 3.0000, 4500.0000, NULL, NULL, 6, '2026-05-17 09:44:47', '2026-05-17 09:44:47'),
(29, 6, 44, NULL, 'Thread', NULL, NULL, 7500.0000, 'Meter', 21.0000, 157500.0000, NULL, NULL, 7, '2026-05-17 09:44:47', '2026-05-17 09:44:47'),
(30, 7, 37, NULL, 'Cotton Fabric', NULL, NULL, 50.4000, 'Meter', 19.0000, 957.6000, NULL, NULL, 0, '2026-05-17 09:59:03', '2026-05-17 09:59:03'),
(31, 7, 38, NULL, 'Handle', NULL, NULL, 2880.0000, 'Inch', 2.0000, 5760.0000, NULL, NULL, 1, '2026-05-17 09:59:03', '2026-05-17 09:59:03'),
(32, 7, 39, NULL, 'Label', NULL, NULL, 144.0000, 'Piece', 2.0000, 288.0000, NULL, NULL, 2, '2026-05-17 09:59:03', '2026-05-17 09:59:03'),
(33, 7, 40, NULL, 'Thread', NULL, NULL, 720.0000, 'Meter', 2.0000, 1440.0000, NULL, NULL, 3, '2026-05-17 09:59:03', '2026-05-17 09:59:03'),
(34, 7, 41, NULL, 'Cotton Fabric', NULL, NULL, 525.0000, 'Meter', 2.0000, 1050.0000, NULL, NULL, 4, '2026-05-17 09:59:03', '2026-05-17 09:59:03'),
(35, 7, 42, NULL, 'Handle', NULL, NULL, 30000.0000, 'Inch', 2.0000, 60000.0000, NULL, NULL, 5, '2026-05-17 09:59:03', '2026-05-17 09:59:03'),
(36, 7, 43, NULL, 'Label', NULL, NULL, 1500.0000, 'Piece', 2.0000, 3000.0000, NULL, NULL, 6, '2026-05-17 09:59:03', '2026-05-17 09:59:03'),
(37, 7, 44, NULL, 'Thread', NULL, NULL, 7500.0000, 'Meter', 2.0000, 15000.0000, NULL, NULL, 7, '2026-05-17 09:59:03', '2026-05-17 09:59:03'),
(38, 8, 60, NULL, '20x20 / 60x66 - 66\" | 150gsm', NULL, NULL, 609.6000, 'METER', 2.0000, 1219.2000, NULL, NULL, 0, '2026-05-19 14:15:12', '2026-05-19 14:15:12'),
(39, 8, 57, NULL, '20x20 / 60x66 - 66\" | 150gsm', NULL, NULL, 762.9360, 'METER', 23.0000, 17547.5280, NULL, NULL, 1, '2026-05-19 14:15:12', '2026-05-19 14:15:12'),
(40, 8, 56, NULL, 'Cartons (240 pcs / Box)', NULL, NULL, 14445.0032, 'METER', 3.0000, 43335.0096, NULL, NULL, 2, '2026-05-19 14:15:12', '2026-05-19 14:15:12'),
(41, 8, 61, NULL, 'Oekotex Label', NULL, NULL, 1200.0000, 'PIECE', 3.0000, 3600.0000, NULL, NULL, 3, '2026-05-19 14:15:12', '2026-05-19 14:15:12'),
(42, 8, 64, NULL, 'Oekotex Label', NULL, NULL, 0.0762, 'METER', 3.0000, 0.2286, NULL, NULL, 4, '2026-05-19 14:15:12', '2026-05-19 14:15:12'),
(43, 8, 58, NULL, 'Oekotex Label', NULL, NULL, 1500.0000, 'PIECE', 3.0000, 4500.0000, NULL, NULL, 5, '2026-05-19 14:15:12', '2026-05-19 14:15:12');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `contact_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_emails` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `contact_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `contact_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_activities`
--

CREATE TABLE `warehouse_activities` (
  `activity_id` int UNSIGNED NOT NULL,
  `warehouse_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_locations`
--

CREATE TABLE `warehouse_locations` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_tags`
--

CREATE TABLE `warehouse_tags` (
  `tag_id` int UNSIGNED NOT NULL,
  `warehouse_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `webhooks`
--

CREATE TABLE `webhooks` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `end_point` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `query_params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `payload_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `raw_payload_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `web_forms`
--

CREATE TABLE `web_forms` (
  `id` int UNSIGNED NOT NULL,
  `form_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `submit_button_label` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `submit_success_action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `submit_success_content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_lead` tinyint(1) NOT NULL DEFAULT '0',
  `background_color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `form_background_color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `form_title_color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `form_submit_button_color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attribute_label_color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `web_form_attributes`
--

CREATE TABLE `web_form_attributes` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `placeholder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int DEFAULT NULL,
  `attribute_id` int UNSIGNED NOT NULL,
  `web_form_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workflows`
--

CREATE TABLE `workflows` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'and',
  `conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `actions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `workflows`
--

INSERT INTO `workflows` (`id`, `name`, `description`, `entity_type`, `event`, `condition_type`, `conditions`, `actions`, `created_at`, `updated_at`) VALUES
(1, 'Emails to participants after activity creation', 'Emails to participants after activity creation', 'activities', 'activity.create.after', 'and', '[{\"value\": [\"call\", \"meeting\", \"lunch\"], \"operator\": \"{}\", \"attribute\": \"type\", \"attribute_type\": \"multiselect\"}]', '[{\"id\": \"send_email_to_participants\", \"value\": \"1\"}]', '2026-05-17 11:32:05', '2026-05-17 11:32:05'),
(2, 'Emails to participants after activity updation', 'Emails to participants after activity updation', 'activities', 'activity.update.after', 'and', '[{\"value\": [\"call\", \"meeting\", \"lunch\"], \"operator\": \"{}\", \"attribute\": \"type\", \"attribute_type\": \"multiselect\"}]', '[{\"id\": \"send_email_to_participants\", \"value\": \"2\"}]', '2026-05-17 11:32:05', '2026-05-17 11:32:05');

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
-- Indexes for table `color_references`
--
ALTER TABLE `color_references`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

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
-- Indexes for table `document_charges`
--
ALTER TABLE `document_charges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_chargeable` (`chargeable_id`,`chargeable_type`);

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
-- Indexes for table `goods_receipts`
--
ALTER TABLE `goods_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `goods_receipts_goods_receipt_number_unique` (`goods_receipt_number`);

--
-- Indexes for table `goods_receipt_items`
--
ALTER TABLE `goods_receipt_items`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `industries`
--
ALTER TABLE `industries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `industries_name_unique` (`name`);

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
-- Indexes for table `job_cards`
--
ALTER TABLE `job_cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_card_sections`
--
ALTER TABLE `job_card_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_card_section_items`
--
ALTER TABLE `job_card_section_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_orders`
--
ALTER TABLE `job_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_order_items`
--
ALTER TABLE `job_order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_order_requirements`
--
ALTER TABLE `job_order_requirements`
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
-- Indexes for table `lead_persons`
--
ALTER TABLE `lead_persons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lead_persons_lead_id_person_id_unique` (`lead_id`,`person_id`),
  ADD KEY `lead_persons_person_id_foreign` (`person_id`);

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
-- Indexes for table `material_references`
--
ALTER TABLE `material_references`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `material_reference_vendor`
--
ALTER TABLE `material_reference_vendor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `material_reference_vendor_unique` (`material_reference_id`,`organization_id`),
  ADD KEY `fk_material_reference_vendor_org` (`organization_id`);

--
-- Indexes for table `material_units`
--
ALTER TABLE `material_units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `material_units_name_unique` (`name`);

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
  ADD KEY `purchase_orders_organization_id_foreign` (`organization_id`),
  ADD KEY `purchase_orders_job_order_id_foreign` (`job_order_id`),
  ADD KEY `purchase_orders_vendor_quote_id_foreign` (`vendor_quote_id`),
  ADD KEY `purchase_orders_closed_by_foreign` (`closed_by`);

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
-- Indexes for table `unit_references`
--
ALTER TABLE `unit_references`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

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
-- Indexes for table `vendor_payables`
--
ALTER TABLE `vendor_payables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendor_payables_payable_number_unique` (`payable_number`);

--
-- Indexes for table `vendor_quotes`
--
ALTER TABLE `vendor_quotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendor_quotes_vendor_quote_number_unique` (`vendor_quote_number`),
  ADD KEY `vendor_quotes_job_order_id_foreign` (`job_order_id`),
  ADD KEY `vendor_quotes_organization_id_foreign` (`organization_id`),
  ADD KEY `vendor_quotes_person_id_foreign` (`person_id`),
  ADD KEY `vendor_quotes_created_by_foreign` (`created_by`);

--
-- Indexes for table `vendor_quote_items`
--
ALTER TABLE `vendor_quote_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_quote_items_vendor_quote_id_foreign` (`vendor_quote_id`),
  ADD KEY `vendor_quote_items_vendor_id_foreign` (`vendor_id`);

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=510;

--
-- AUTO_INCREMENT for table `activity_files`
--
ALTER TABLE `activity_files`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `activity_participants`
--
ALTER TABLE `activity_participants`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `attribute_options`
--
ALTER TABLE `attribute_options`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `attribute_values`
--
ALTER TABLE `attribute_values`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=504;

--
-- AUTO_INCREMENT for table `color_references`
--
ALTER TABLE `color_references`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `core_config`
--
ALTER TABLE `core_config`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=256;

--
-- AUTO_INCREMENT for table `country_states`
--
ALTER TABLE `country_states`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=569;

--
-- AUTO_INCREMENT for table `datagrid_saved_filters`
--
ALTER TABLE `datagrid_saved_filters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_charges`
--
ALTER TABLE `document_charges`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `emails`
--
ALTER TABLE `emails`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `email_attachments`
--
ALTER TABLE `email_attachments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goods_receipts`
--
ALTER TABLE `goods_receipts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `goods_receipt_items`
--
ALTER TABLE `goods_receipt_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imports`
--
ALTER TABLE `imports`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `import_batches`
--
ALTER TABLE `import_batches`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `industries`
--
ALTER TABLE `industries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_cards`
--
ALTER TABLE `job_cards`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `job_card_sections`
--
ALTER TABLE `job_card_sections`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `job_card_section_items`
--
ALTER TABLE `job_card_section_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `job_orders`
--
ALTER TABLE `job_orders`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `job_order_items`
--
ALTER TABLE `job_order_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `job_order_requirements`
--
ALTER TABLE `job_order_requirements`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `lead_persons`
--
ALTER TABLE `lead_persons`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `lead_pipelines`
--
ALTER TABLE `lead_pipelines`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lead_pipeline_stages`
--
ALTER TABLE `lead_pipeline_stages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lead_priorities`
--
ALTER TABLE `lead_priorities`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lead_products`
--
ALTER TABLE `lead_products`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_sources`
--
ALTER TABLE `lead_sources`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lead_stages`
--
ALTER TABLE `lead_stages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_types`
--
ALTER TABLE `lead_types`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `marketing_campaigns`
--
ALTER TABLE `marketing_campaigns`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketing_events`
--
ALTER TABLE `marketing_events`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_references`
--
ALTER TABLE `material_references`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `material_reference_vendor`
--
ALTER TABLE `material_reference_vendor`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `material_units`
--
ALTER TABLE `material_units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `persons`
--
ALTER TABLE `persons`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `product_colors`
--
ALTER TABLE `product_colors`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=902;

--
-- AUTO_INCREMENT for table `product_consumptions`
--
ALTER TABLE `product_consumptions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `product_inventories`
--
ALTER TABLE `product_inventories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_key_points`
--
ALTER TABLE `product_key_points`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT for table `product_other_images`
--
ALTER TABLE `product_other_images`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `product_pricing_charts`
--
ALTER TABLE `product_pricing_charts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `product_pricing_chart_tiers`
--
ALTER TABLE `product_pricing_chart_tiers`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=506;

--
-- AUTO_INCREMENT for table `product_pricing_chart_types`
--
ALTER TABLE `product_pricing_chart_types`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `product_production_sections`
--
ALTER TABLE `product_production_sections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `product_production_section_items`
--
ALTER TABLE `product_production_section_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `proforma_invoices`
--
ALTER TABLE `proforma_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `proforma_invoice_items`
--
ALTER TABLE `proforma_invoice_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `proforma_receipts`
--
ALTER TABLE `proforma_receipts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `quotes`
--
ALTER TABLE `quotes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `quote_items`
--
ALTER TABLE `quote_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unit_references`
--
ALTER TABLE `unit_references`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vendor_payables`
--
ALTER TABLE `vendor_payables`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vendor_quotes`
--
ALTER TABLE `vendor_quotes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vendor_quote_items`
--
ALTER TABLE `vendor_quote_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouse_locations`
--
ALTER TABLE `warehouse_locations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `webhooks`
--
ALTER TABLE `webhooks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_forms`
--
ALTER TABLE `web_forms`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `web_form_attributes`
--
ALTER TABLE `web_form_attributes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workflows`
--
ALTER TABLE `workflows`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Constraints for table `lead_persons`
--
ALTER TABLE `lead_persons`
  ADD CONSTRAINT `lead_persons_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_persons_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `material_reference_vendor`
--
ALTER TABLE `material_reference_vendor`
  ADD CONSTRAINT `fk_material_reference_vendor_material` FOREIGN KEY (`material_reference_id`) REFERENCES `material_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_material_reference_vendor_org` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `purchase_orders_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_job_order_id_foreign` FOREIGN KEY (`job_order_id`) REFERENCES `job_orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_vendor_quote_id_foreign` FOREIGN KEY (`vendor_quote_id`) REFERENCES `vendor_quotes` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `vendor_quotes`
--
ALTER TABLE `vendor_quotes`
  ADD CONSTRAINT `vendor_quotes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vendor_quotes_job_order_id_foreign` FOREIGN KEY (`job_order_id`) REFERENCES `job_orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vendor_quotes_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendor_quotes_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vendor_quote_items`
--
ALTER TABLE `vendor_quote_items`
  ADD CONSTRAINT `vendor_quote_items_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vendor_quote_items_vendor_quote_id_foreign` FOREIGN KEY (`vendor_quote_id`) REFERENCES `vendor_quotes` (`id`) ON DELETE CASCADE;

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
