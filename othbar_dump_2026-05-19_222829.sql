/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: othbar
-- ------------------------------------------------------
-- Server version	11.8.6-MariaDB-ubu2404

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES
(1,'Paro Valley Farm','paro-valley-farm',1,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(2,'Trongsa Highland','trongsa-highland',1,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(3,'Bumthang Organic','bumthang-organic',1,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(4,'Othbar Community Farm','othbar-community-farm',1,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(5,'Haa Valley Collective','haa-valley-collective',1,'2026-05-19 16:24:04','2026-05-19 16:24:04');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES
('laravel-cache-livewire-rate-limiter:4e77e1f00df39b43a8a74dad60db8ad318758936','i:1;',1779207911),
('laravel-cache-livewire-rate-limiter:4e77e1f00df39b43a8a74dad60db8ad318758936:timer','i:1779207911;',1779207911);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES
(1,NULL,'Heritage Grains','heritage-grains','Ancient grain varieties cultivated in Bhutan\'s high-altitude valleys',1,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(2,NULL,'Fresh Vegetables','fresh-vegetables','Seasonal organic produce from our mountain farms',1,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(3,NULL,'Wild Honey','wild-honey','Forest-gathered cliff honey from traditional hunters',1,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(4,NULL,'Himalayan Herbs','himalayan-herbs','Medicinal and culinary herbs from pristine highland meadows',1,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(5,NULL,'Preserved Foods','preserved-foods','Fermented, dried, and preserved Bhutanese specialities',1,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(6,NULL,'Chili & Spices','chili-spices','The backbone of Bhutanese cuisine — ema datshi and beyond',1,'2026-05-19 16:24:04','2026-05-19 16:24:04');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `category_product`
--

DROP TABLE IF EXISTS `category_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_product_category_id_product_id_unique` (`category_id`,`product_id`),
  KEY `category_product_product_id_foreign` (`product_id`),
  CONSTRAINT `category_product_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `category_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category_product`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `category_product` WRITE;
/*!40000 ALTER TABLE `category_product` DISABLE KEYS */;
INSERT INTO `category_product` VALUES
(1,1,7,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(2,3,8,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(3,1,9,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(4,6,10,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(5,4,11,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(6,1,12,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(7,5,12,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(8,2,13,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(9,6,14,'2026-05-19 16:24:04','2026-05-19 16:24:04');
/*!40000 ALTER TABLE `category_product` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `collection_product`
--

DROP TABLE IF EXISTS `collection_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `collection_product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collection_product_collection_id_product_id_unique` (`collection_id`,`product_id`),
  KEY `collection_product_product_id_foreign` (`product_id`),
  CONSTRAINT `collection_product_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collection_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collection_product`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `collection_product` WRITE;
/*!40000 ALTER TABLE `collection_product` DISABLE KEYS */;
INSERT INTO `collection_product` VALUES
(1,1,7,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(2,3,7,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(3,2,8,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(4,3,9,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(5,3,10,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(6,2,11,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(7,3,12,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(8,1,12,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(9,1,13,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(10,3,14,'2026-05-19 16:24:04','2026-05-19 16:24:04'),
(11,2,14,'2026-05-19 16:24:04','2026-05-19 16:24:04');
/*!40000 ALTER TABLE `collection_product` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `collections`
--

DROP TABLE IF EXISTS `collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `collections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collections_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collections`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `collections` WRITE;
/*!40000 ALTER TABLE `collections` DISABLE KEYS */;
INSERT INTO `collections` VALUES
(1,'Valley Harvest','valley-harvest','Seasonal picks from western Bhutan — red rice, highland vegetables, and small-batch preserves.','2026-05-19 16:24:04','2026-05-19 16:24:04'),
(2,'Forest & Cliff','forest-cliff','Wild honey, medicinal herbs, and forest aromatics gathered by Bhutanese harvesters.','2026-05-19 16:24:04','2026-05-19 16:24:04'),
(3,'Staples & Spices','staples-spices','Grains, noodles, ema, and timur — the everyday heart of Bhutanese cooking.','2026-05-19 16:24:04','2026-05-19 16:24:04');
/*!40000 ALTER TABLE `collections` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL,
  `value` int(10) unsigned NOT NULL,
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `max_uses` int(10) unsigned DEFAULT NULL,
  `uses_count` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES
(1,'WELCOME10','percent',10,'2026-05-18 16:24:04',NULL,NULL,0,1,'2026-05-19 16:24:04','2026-05-19 16:24:04');
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `journal_posts`
--

DROP TABLE IF EXISTS `journal_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `journal_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `body` longtext NOT NULL,
  `author_name` varchar(255) DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `journal_posts_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_posts`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `journal_posts` WRITE;
/*!40000 ALTER TABLE `journal_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `journal_posts` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `collection_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `disk` varchar(255) NOT NULL,
  `conversions_disk` varchar(255) DEFAULT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`manipulations`)),
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`custom_properties`)),
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`generated_conversions`)),
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`responsive_images`)),
  `order_column` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_03_05_164438_create_media_table',1),
(5,'2026_05_15_191407_create_permission_tables',1),
(6,'2026_05_16_100000_create_catalog_and_coupons_tables',1),
(7,'2026_05_16_100100_create_orders_tables',1),
(8,'2026_05_19_100000_create_site_content_tables',1),
(9,'2026_05_19_120000_add_created_by_user_id_to_orders_table',1),
(10,'2026_05_19_120000_add_payment_settings_to_site_settings',1),
(11,'2026_05_19_140000_add_gst_percentage_to_site_settings',1),
(12,'2026_05_19_180000_add_payment_merchant_account_to_site_settings',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES
(17,'App\\Models\\User',7),
(18,'App\\Models\\User',8);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `order_addresses`
--

DROP TABLE IF EXISTS `order_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `country_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_addresses`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `order_addresses` WRITE;
/*!40000 ALTER TABLE `order_addresses` DISABLE KEYS */;
INSERT INTO `order_addresses` VALUES
(4,'Nima','Tenzin','Babesa truck parking','Thimphu','110011','+97517263764','Bhutan','2026-05-19 16:24:38','2026-05-19 16:24:38');
/*!40000 ALTER TABLE `order_addresses` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `unit_price_minor` bigint(20) unsigned NOT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES
(4,4,7,'Bhutanese Red Rice',1,28000,'','2026-05-19 16:24:38','2026-05-19 16:24:38');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(255) NOT NULL,
  `total_minor` bigint(20) unsigned NOT NULL DEFAULT 0,
  `currency_code` varchar(8) NOT NULL DEFAULT 'BTN',
  `status` varchar(32) NOT NULL DEFAULT 'new',
  `payment_status` varchar(32) NOT NULL DEFAULT 'pending',
  `shipping_status` varchar(32) NOT NULL DEFAULT 'unsent',
  `notes` text DEFAULT NULL,
  `payment_proof_path` varchar(255) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `payment_access_token` varchar(64) DEFAULT NULL,
  `fulfillment_method` varchar(32) NOT NULL DEFAULT 'delivery',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `shipping_address_id` bigint(20) unsigned DEFAULT NULL,
  `created_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_number_unique` (`number`),
  KEY `orders_shipping_address_id_foreign` (`shipping_address_id`),
  KEY `orders_created_by_user_id_foreign` (`created_by_user_id`),
  CONSTRAINT `orders_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_shipping_address_id_foreign` FOREIGN KEY (`shipping_address_id`) REFERENCES `order_addresses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES
(4,'OTH-68B8C4',29400,'BTN','new','paid','unsent',NULL,'payment-proofs/yZueACjdHUg10hyn1gKIDZK2rs1TYfT6Mc3t02Xu.jpg','1232131232','AXka7DMjuqBBRyM13CTNQH7dOJa7vb2ZXqYwckkaxYV4nhZT','delivery','{\"email\":\"nytenzin@moit.gov.bt\",\"payment_method\":\"bank-transfer\",\"coupon_code\":null,\"source\":\"storefront\",\"fulfillment_method\":\"delivery\",\"subtotal_minor\":28000,\"discount_minor\":0,\"gst_minor\":1400,\"gst_percentage\":5,\"payment_bank\":\"mpay\"}',4,NULL,'2026-05-19 16:24:38','2026-05-19 16:25:08');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES
(81,'orders.view','web','2026-05-19 16:24:03','2026-05-19 16:24:03'),
(82,'orders.create','web','2026-05-19 16:24:03','2026-05-19 16:24:03'),
(83,'orders.fulfill','web','2026-05-19 16:24:03','2026-05-19 16:24:03'),
(84,'products.view','web','2026-05-19 16:24:03','2026-05-19 16:24:03'),
(85,'products.manage','web','2026-05-19 16:24:03','2026-05-19 16:24:03'),
(86,'catalog.manage','web','2026-05-19 16:24:03','2026-05-19 16:24:03'),
(87,'content.manage','web','2026-05-19 16:24:03','2026-05-19 16:24:03'),
(88,'settings.manage','web','2026-05-19 16:24:03','2026-05-19 16:24:03'),
(89,'reports.view','web','2026-05-19 16:24:03','2026-05-19 16:24:03'),
(90,'users.manage','web','2026-05-19 16:24:03','2026-05-19 16:24:03');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `summary` varchar(512) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `stock_quantity` int(10) unsigned NOT NULL DEFAULT 0,
  `allow_backorder` tinyint(1) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 0,
  `price_minor` bigint(20) unsigned NOT NULL DEFAULT 0,
  `currency_code` varchar(8) NOT NULL DEFAULT 'BTN',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_brand_id_foreign` (`brand_id`),
  CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES
(7,1,'Bhutanese Red Rice','bhutanese-red-rice','A nutritious, nutty-flavoured medium-grain rice with a distinctive reddish-brown colour, cultivated in the terraced fields of Paro Valley for over a millennium.','<p>Bhutanese Red Rice is a cultural treasure — a medium-grain rice with a beautiful deep red colour and a rich, nutty flavour. Cultivated in the traditional terraced fields of Paro Valley, it has sustained Bhutanese families for over a thousand years.</p><p>Rich in antioxidants, fibre, manganese, and magnesium, this rice retains its full bran layer, making it far more nutritious than polished white rice. The high-altitude clay soils and pure glacial water of Paro impart a subtle minerality and sweetness that cannot be replicated elsewhere.</p>',NULL,999,0,1,28000,'BTN','2026-05-19 16:24:04','2026-05-19 16:24:04'),
(8,2,'Wild Forest Honey','wild-forest-honey','Rare cliff honey collected by traditional hunters from wild hives in the old-growth forests of Trongsa. Dark, complex, and intensely aromatic.','<p>Harvested once a year by traditional cliff hunters using bamboo ropes and smoke in the old-growth forests of Trongsa, this wild honey is unlike any commercial product. The bees forage across vast, pristine highland meadows and rhododendron forests, producing a honey of extraordinary depth and complexity.</p><p>Dark amber in colour with notes of wildflower, beeswax, and subtle smokiness, each batch is unique to its harvest season. Raw, unfiltered, and never heated.</p>',NULL,80,0,1,65000,'BTN','2026-05-19 16:24:04','2026-05-19 16:24:04'),
(9,3,'Highland Buckwheat Flour','highland-buckwheat-flour','Stone-ground from ancient buckwheat varieties grown in the cool plateau valleys of Bumthang at 2,700m. The foundation of traditional Bhutanese pancakes and noodles.','<p>Bumthang buckwheat has been the backbone of highland Bhutanese cuisine for centuries. Grown at 2,700 metres in the Bumthang plateau — one of the highest cultivated valleys in the Himalayas — these ancient varieties develop exceptional flavour intensity in the short, cool growing season.</p><p>Stone-milled to order in small batches, this flour retains its full nutritional profile and distinctive earthy, slightly bitter character. Use it for traditional Bhutanese buckwheat pancakes (<em>khuli</em>) or pasta.</p>',NULL,300,0,1,18000,'BTN','2026-05-19 16:24:04','2026-05-19 16:24:04'),
(10,4,'Sun-Dried Ema (Chili Peppers)','sun-dried-ema','The essential ingredient of Bhutan\'s national dish. Whole dried chilies from our farm, sun-dried on bamboo mats to preserve their smoky heat and fruity depth.','<p>In Bhutan, chili is not a condiment — it is a vegetable, a staple, and the heart of the cuisine. Ema datshi (chili and cheese stew) is the national dish, and the quality of the chili determines everything.</p><p>Our ema are grown from traditional Bhutanese varieties, harvested ripe, and sun-dried on bamboo mats over two to three weeks. The result is a deeply aromatic dried chili with smoky, fruity heat and a complexity that fresh chilies cannot match. Essential for ema datshi, shakam paa, and Bhutanese meat dishes.</p>',NULL,400,0,1,22000,'BTN','2026-05-19 16:24:04','2026-05-19 16:24:04'),
(11,5,'Himalayan Nettle Tea','himalayan-nettle-tea','Hand-picked young nettle leaves from wild highland meadows in Haa Valley, gently dried to preserve their rich mineral content and deep, earthy flavour.','<p>Stinging nettle has been used in Bhutanese traditional medicine for centuries. The young spring leaves, gathered from wild meadows above 3,000 metres in Haa Valley, are at their most potent and flavourful before the plant flowers.</p><p>Our harvest team hand-picks only the top two leaves of each plant in April and May, then gently air-dries them in shade to preserve colour, aroma, and nutritional content. The resulting tea is deep green, rich in iron and vitamins, with a clean, slightly herbaceous flavour. Consumed daily by many Bhutanese for general vitality.</p>',NULL,150,0,1,34000,'BTN','2026-05-19 16:24:04','2026-05-19 16:24:04'),
(12,3,'Handmade Buckwheat Noodles','buckwheat-noodles','Traditional Bhutanese noodles made by hand from our Bumthang buckwheat flour. Nutty, earthy, and satisfying — a highland pantry staple.','<p>Made by hand in small batches by cooperative members in Bumthang, these noodles represent a living culinary tradition. The dough is made from nothing but our stone-ground buckwheat flour and water, pressed and cut by hand, then sun-dried on wooden racks.</p><p>They cook in 4–5 minutes and have a pleasantly chewy texture with a robust, earthy flavour that pairs beautifully with Bhutanese curries, simple miso broth, or stir-fried with highland vegetables.</p>',NULL,200,0,1,19500,'BTN','2026-05-19 16:24:04','2026-05-19 16:24:04'),
(13,4,'Organic Asparagus (Seasonal)','organic-asparagus','Tender asparagus spears grown without pesticides in the fertile alluvial soils of the Punakha valley. Available March through May.','<p>Asparagus thrives in the deep, rich alluvial soils deposited by the Pho Chhu and Mo Chhu rivers in Punakha. Our variety produces thick, tender spears with exceptional sweetness, due to the cool nights and warm days of the lower Himalayan foothills during spring.</p><p>Harvested each morning before 6am and packed immediately. Available only during the March–May season — order early, as quantities are strictly limited by the natural growing cycle.</p>',NULL,100,0,1,16000,'BTN','2026-05-19 16:24:04','2026-05-19 16:24:04'),
(14,2,'Sichuan Pepper (Timur)','timur-sichuan-pepper','Wild-harvested timur (Sichuan pepper) from the forests of Eastern Bhutan. Intensely aromatic with a distinctive citrusy tingle that numbs the palate.','<p>Known as <em>timur</em> in Bhutan, this is the wild-harvested Zanthoxylum species that grows in the subtropical forests of the eastern districts. It is related to Sichuan pepper but has its own distinct aromatic profile — more citrusy, more floral, and intensely aromatic when fresh.</p><p>Hand-picked and sun-dried, then lightly toasted in our kitchen before packing. A small pinch transforms any dish. Essential in Bhutanese ezay (chili sauce), shakam datshi, and meat preparations. Also extraordinary with scrambled eggs, pasta, or grilled fish.</p>',NULL,120,0,1,29000,'BTN','2026-05-19 16:24:04','2026-05-19 16:24:04');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES
(81,17),
(82,17),
(83,17),
(84,17),
(85,17),
(86,17),
(87,17),
(88,17),
(89,17),
(90,17),
(81,18),
(82,18),
(83,18),
(84,18);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(17,'administrator','web','2026-05-19 16:24:03','2026-05-19 16:24:03'),
(18,'staff','web','2026-05-19 16:24:03','2026-05-19 16:24:03');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('02x2I35Twxl1kwcyJyWCuQPDxk2t1FDFHg7mTCw5',7,'172.19.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiRnJFcTlCSGlUelkwbjBWc2huNXZEVFBMYThDamtnbnNFTUdhSzdHViI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjk0OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvY2hlY2tvdXQvY29uZmlybWF0aW9uLzQvQVhrYTdETWp1cUJCUnlNMTNDVE5RSDdkT0phN3ZiMlpYcVl3Y2trYXhZVjRuaFpUIjtzOjU6InJvdXRlIjtzOjIxOiJjaGVja291dC5jb25maXJtYXRpb24iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo3O3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJhMzhlN2MwMWViNWY1NTY3NjM5Y2ZkYjc5YTMzNTY3N2Y2ODRlM2VhZGY1OTY0YTVjYzAxNTQzMjc4NmZkYmY5IjtzOjY6InRhYmxlcyI7YToxOntzOjQwOiJlNzkzYTI3OWQ1NmU0NTA2MDk3NTQwMjBkNjI3YmVlY19jb2x1bW5zIjthOjg6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJudW1iZXIiO3M6NToibGFiZWwiO3M6NjoiTnVtYmVyIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNToibWV0YWRhdGEuc291cmNlIjtzOjU6ImxhYmVsIjtzOjY6IlNvdXJjZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjY6IlN0YXR1cyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6InBheW1lbnRfc3RhdHVzIjtzOjU6ImxhYmVsIjtzOjE0OiJQYXltZW50IHN0YXR1cyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTU6InNoaXBwaW5nX3N0YXR1cyI7czo1OiJsYWJlbCI7czoxNToiU2hpcHBpbmcgc3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE4OiJmdWxmaWxsbWVudF9tZXRob2QiO3M6NToibGFiZWwiO3M6MTg6IkZ1bGZpbGxtZW50IG1ldGhvZCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6InRvdGFsX21pbm9yIjtzOjU6ImxhYmVsIjtzOjU6IlRvdGFsIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czoxMDoiQ3JlYXRlZCBhdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319fX0=',1779207937);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL DEFAULT 'OTHBAR',
  `company_subtitle` varchar(255) NOT NULL DEFAULT 'Horticulture • Bhutan',
  `announcement_text` text DEFAULT NULL,
  `footer_about` text DEFAULT NULL,
  `contact_address` text DEFAULT NULL,
  `contact_phone` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `hero_badge` varchar(255) DEFAULT NULL,
  `hero_line1` varchar(255) DEFAULT NULL,
  `hero_emphasis` varchar(255) DEFAULT NULL,
  `hero_line2` varchar(255) DEFAULT NULL,
  `hero_description` text DEFAULT NULL,
  `hero_cta_primary` varchar(255) DEFAULT NULL,
  `hero_cta_secondary` varchar(255) DEFAULT NULL,
  `home_categories_label` varchar(255) DEFAULT NULL,
  `home_categories_title` varchar(255) DEFAULT NULL,
  `home_featured_label` varchar(255) DEFAULT NULL,
  `home_featured_title` varchar(255) DEFAULT NULL,
  `home_story_label` varchar(255) DEFAULT NULL,
  `home_story_title` varchar(255) DEFAULT NULL,
  `home_story_paragraph_1` text DEFAULT NULL,
  `home_story_paragraph_2` text DEFAULT NULL,
  `home_story_media_title` varchar(255) DEFAULT NULL,
  `home_story_media_subtitle` varchar(255) DEFAULT NULL,
  `home_story_stat_value` varchar(255) DEFAULT NULL,
  `home_story_stat_label` varchar(255) DEFAULT NULL,
  `home_testimonials_label` varchar(255) DEFAULT NULL,
  `home_testimonials_title` varchar(255) DEFAULT NULL,
  `newsletter_label` varchar(255) DEFAULT NULL,
  `newsletter_title` varchar(255) DEFAULT NULL,
  `newsletter_description` text DEFAULT NULL,
  `story_hero_label` varchar(255) DEFAULT NULL,
  `story_hero_title` varchar(255) DEFAULT NULL,
  `story_hero_intro` text DEFAULT NULL,
  `story_origin_label` varchar(255) DEFAULT NULL,
  `story_origin_title` varchar(255) DEFAULT NULL,
  `story_origin_paragraphs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`story_origin_paragraphs`)),
  `story_origin_media_title` varchar(255) DEFAULT NULL,
  `story_origin_media_subtitle` varchar(255) DEFAULT NULL,
  `story_principles_label` varchar(255) DEFAULT NULL,
  `story_principles_title` varchar(255) DEFAULT NULL,
  `story_team_label` varchar(255) DEFAULT NULL,
  `story_team_title` varchar(255) DEFAULT NULL,
  `story_cta_title` varchar(255) DEFAULT NULL,
  `story_cta_body` text DEFAULT NULL,
  `provenance_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`provenance_items`)),
  `stats` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`stats`)),
  `testimonials` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`testimonials`)),
  `principles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`principles`)),
  `team_members` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`team_members`)),
  `payment_channels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_channels`)),
  `payment_merchant_account` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_merchant_account`)),
  `pickup_address_label` varchar(255) DEFAULT NULL,
  `gst_percentage` decimal(5,2) NOT NULL DEFAULT 5.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES
(1,'OTHBAR','Horticulture • Bhutan','Free delivery within Thimphu • Certified Organic • Grown at 2,400m elevation','Nestled in the sacred valleys of Bhutan at 2,400 metres, we cultivate organic food with reverence for the land, guided by ancient Bhutanese agricultural wisdom.','Othbar Valley\nPunakha Dzongkhag\nBhutan','+975 02 123 456','hello@othbar.bt','Est. 2018 • Certified Organic • Punakha, Bhutan','From the','Dragon Kingdom\'s','own earth','High-altitude organic farming practiced with Gross National Happiness at its core. Every harvest carries the spirit of Bhutan\'s pristine valleys.','Explore the Harvest','Our Story','What we grow','Categories of the harvest','Latest harvest','Featured products','The Othbar way','Farming guided by Gross National Happiness','In the verdant valleys of Punakha, our farmers cultivate with a philosophy rooted in Bhutan\'s unique vision — that happiness and ecological balance are inseparable. No chemical inputs. No shortcuts.','We grow heirloom varieties that have fed Bhutanese families for centuries — red rice from Paro, buckwheat from Bumthang, wild cliff honey collected by traditional hunters in Trongsa.','Punakha Valley','2,400 metres above sea level','6+','Years of\ncultivation','What people say','From our customers','Stay connected','Seasonal harvest updates','Be the first to know when new products arrive, learn about our farming practices, and receive exclusive offers.','Who we are','Rooted in the earth of the Last Shangri-La','Founded in 2018 by a collective of 47 farming families in Punakha, Othbar exists to share Bhutan\'s extraordinary organic heritage with the world — without compromising the land that makes it possible.','The beginning','How Othbar came to be','[{\"body\":\"The name Othbar comes from an ancient Dzongkha word for the high-altitude terraced fields where our founders\' grandparents first cultivated red rice. When the youngest generation began returning to these valleys after studying modern agriculture, they brought with them a question: How do we honour what our ancestors knew while building something that can sustain our community\'s future?\"},{\"body\":\"The answer was a cooperative. Forty-seven families pooling their land, knowledge, and labour \\u2014 certified organic from day one, committed to zero synthetic inputs, and guided by Bhutan\'s own framework of Gross National Happiness.\"},{\"body\":\"Today we cultivate 120 acres across Punakha and Paro, growing 28 varieties of heritage crops. We sell directly to homes across Bhutan and to a small number of international partners who share our values.\"}]','Punakha Valley','Est. 2018','What drives us','Our principles','The people','Our farming families','Taste the difference','Every purchase supports our farming families directly and funds the regeneration of traditional Bhutanese agriculture.','[{\"icon\":\"\\ud83c\\udfd4\",\"text\":\"Grown at 2,400m\"},{\"icon\":\"\\ud83c\\udf31\",\"text\":\"Zero Pesticides\"},{\"icon\":\"\\ud83c\\udf3f\",\"text\":\"Heirloom Varieties\"},{\"icon\":\"\\u267b\",\"text\":\"Carbon Neutral\"},{\"icon\":\"\\ud83e\\udd1d\",\"text\":\"Community Owned\"},{\"icon\":\"\\ud83e\\udde1\",\"text\":\"GNH Certified\"}]','[{\"value\":\"47\",\"unit\":\"Farmer families\",\"description\":\"community owners\"},{\"value\":\"120\",\"unit\":\"Acres\",\"description\":\"of certified organic land\"},{\"value\":\"28\",\"unit\":\"Varieties\",\"description\":\"of heirloom crops\"},{\"value\":\"100%\",\"unit\":\"Organic\",\"description\":\"zero synthetic inputs\"}]','[{\"quote\":\"The red rice from Othbar has completely transformed our family meals. You can taste the difference \\u2014 nutty, complex, and deeply satisfying. Nothing like what you find in supermarkets.\",\"name\":\"Karma Wangchuk\",\"location\":\"Thimphu\",\"rating\":5},{\"quote\":\"Their wild honey is extraordinary. I have tried honey from across Asia, but the depth of flavour from the Trongsa cliff honey is unlike anything I have experienced. A true treasure of Bhutan.\",\"name\":\"Dr. Tshering Pem\",\"location\":\"Paro\",\"rating\":5},{\"quote\":\"Ordering from Othbar feels like a direct connection to the land. The packaging is beautiful, the produce is impeccable, and knowing the farmers are part of the cooperative makes it meaningful.\",\"name\":\"Sonam Dorji\",\"location\":\"Punakha\",\"rating\":5}]','[{\"number\":\"01\",\"title\":\"Earth before profit\",\"body\":\"Every farming decision is evaluated first by its impact on the soil, water, and biodiversity of the Punakha and Paro valleys. Profitability follows ecological health, never leads it.\"},{\"number\":\"02\",\"title\":\"Ancient knowledge, modern rigour\",\"body\":\"We combine the intergenerational farming wisdom of our cooperative members with contemporary organic certification standards and sustainable agriculture research.\"},{\"number\":\"03\",\"title\":\"Community ownership\",\"body\":\"Othbar is collectively owned by all 47 member families. Decisions are made by consensus. Profits are distributed equally. No investor holds a stake in our land.\"}]','[{\"name\":\"Tshering Lhamo\",\"role\":\"Lead farmer, red rice\",\"valley\":\"Paro Valley\"},{\"name\":\"Karma Wangdi\",\"role\":\"Honey cooperative head\",\"valley\":\"Trongsa\"},{\"name\":\"Sonam Choki\",\"role\":\"Herb cultivation\",\"valley\":\"Haa Valley\"},{\"name\":\"Jigme Dorji\",\"role\":\"Cooperative director\",\"valley\":\"Punakha\"}]','[]','{\"bank_label\":\"Bank of Bhutan (BoB)\",\"account_name\":\"Othbar Horticulture Project\",\"account_number\":\"10101-00123456-78\",\"qr_path\":null}','In-store pickup at Othbar',5.00,'2026-05-19 16:24:04','2026-05-19 16:24:04');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(7,'Admin Othbar','admin@othbar.local','2026-05-19 16:25:55','$2y$12$TTUmBR8Gh.JGrUX5j84IZOMKMvF1wU3lss3hf/QPd5tJ7mIRhGSDa',NULL,'2026-05-19 16:24:03','2026-05-19 16:25:55'),
(8,'Staff Othbar','staff@othbar.local','2026-05-19 16:25:56','$2y$12$eCfhHf8p7w1/0gUgitYN/.NwgWcG2dtgguuVcAcHFPgfH4H6N6j6i',NULL,'2026-05-19 16:24:04','2026-05-19 16:25:56');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Dumping routines for database 'othbar'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-05-19 16:28:29
