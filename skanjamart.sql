-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2026 at 07:06 AM
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
-- Database: `skanjamart`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-jane@gmail.com|127.0.0.1', 'i:1;', 1789001426),
('laravel-cache-jane@gmail.com|127.0.0.1:timer', 'i:1789001426;', 1789001426);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-08-10 23:49:11', '2026-08-10 23:49:11'),
(2, 3, '2026-08-11 17:28:37', '2026-08-11 17:28:37'),
(3, 1, '2026-08-11 18:13:49', '2026-08-11 18:13:49'),
(4, 5, '2026-09-09 17:50:58', '2026-09-09 17:50:58');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(6, 3, 6, 3, '2026-08-11 18:13:49', '2026-08-11 18:26:19'),
(8, 3, 3, 2, '2026-08-11 18:37:26', '2026-08-11 18:38:23');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'recycle(daur ulang)', 'recycledaur-ulang', '2026-08-10 23:36:08', '2026-08-10 23:36:08'),
(2, 'Kerajinan', 'kerajinan', '2026-08-10 23:36:49', '2026-08-10 23:36:49'),
(3, 'Aksesoris', 'aksesoris', '2026-08-10 23:58:55', '2026-08-10 23:58:55'),
(4, 'Friendship', 'friendship', '2026-08-11 00:04:03', '2026-08-11 00:04:03'),
(5, 'Minuman', 'minuman', '2026-09-09 21:37:27', '2026-09-09 21:37:27'),
(6, 'Makanan', 'makanan', '2026-09-09 21:37:33', '2026-09-09 21:37:33');

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
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_08_10_100001_add_role_to_users_table', 1),
(5, '2026_08_10_100002_create_categories_table', 1),
(6, '2026_08_10_100003_create_products_table', 1),
(7, '2026_08_10_100004_create_carts_table', 1),
(8, '2026_08_10_100005_create_cart_items_table', 1),
(9, '2026_08_10_100006_create_orders_table', 1),
(10, '2026_08_10_100007_create_order_items_table', 1),
(11, '2026_08_10_100008_create_payments_table', 1),
(12, '2026_09_07_100000_add_cost_price_to_products_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `total` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_status` varchar(255) NOT NULL DEFAULT 'unpaid',
  `payment_method` varchar(255) DEFAULT NULL,
  `shipping_address` text NOT NULL,
  `phone` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `total`, `status`, `payment_status`, `payment_method`, `shipping_address`, `phone`, `created_at`, `updated_at`) VALUES
(1, 'ORD-KC0BLZPIPV', 2, 15000, 'processing', 'unpaid', 'cod', 'darmayasa city', '086534287872', '2026-08-10 23:49:39', '2026-08-11 00:48:48'),
(2, 'ORD-AHJFPJJYLB', 2, 40000, 'shipped', 'unpaid', 'cod', 'gembongan', '087654432', '2026-08-11 00:13:50', '2026-08-11 00:48:41'),
(3, 'ORD-6GID2XFTYA', 2, 28000, 'completed', 'unpaid', 'midtrans', 'tlahab', '0822222222', '2026-08-11 00:15:40', '2026-08-11 00:48:30'),
(4, 'ORD-G9HDHA3B0N', 3, 12000, 'pending', 'unpaid', 'midtrans', 'hahaha', '98239832', '2026-08-11 17:29:00', '2026-08-11 17:29:00'),
(5, 'ORD-ZAJWAMVJPR', 3, 7000, 'pending', 'unpaid', 'midtrans', 'adudu', '09967676767', '2026-08-11 17:52:33', '2026-08-11 17:52:33'),
(6, 'ORD-ZKMNXYJTW1', 3, 20000, 'pending', 'unpaid', 'midtrans', 'eauyhgdaudbaw', '09238709870', '2026-08-11 18:14:13', '2026-08-11 18:14:13'),
(7, 'ORD-HU1VISSDDO', 3, 50000, 'pending', 'unpaid', 'midtrans', 'asdsda', '1234567890', '2026-08-11 18:40:19', '2026-08-11 18:40:19'),
(8, 'ORD-UFWZXGXQB8', 5, 8000, 'pending', 'unpaid', 'midtrans', 'jakarta', '-09809780987987', '2026-09-09 17:51:34', '2026-09-09 17:51:34'),
(9, 'ORD-MXAWOB72FY', 5, 10000, 'pending', 'unpaid', 'midtrans', 'jimbu', '092929927377', '2026-09-09 17:53:25', '2026-09-09 17:53:25');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'pot tanaman', 15000, 1, '2026-08-10 23:49:39', '2026-08-10 23:49:39'),
(2, 2, 6, 'photo keychain', 20000, 2, '2026-08-11 00:13:50', '2026-08-11 00:13:50'),
(3, 3, 4, 'Gantungan Kunci', 7000, 4, '2026-08-11 00:15:40', '2026-08-11 00:15:40'),
(4, 4, 5, 'Gelang Couple', 12000, 1, '2026-08-11 17:29:00', '2026-08-11 17:29:00'),
(5, 5, 4, 'Gantungan Kunci', 7000, 1, '2026-08-11 17:52:33', '2026-08-11 17:52:33'),
(6, 6, 6, 'photo keychain', 20000, 1, '2026-08-11 18:14:13', '2026-08-11 18:14:13'),
(7, 7, 3, 'tas anyam', 50000, 1, '2026-08-11 18:40:19', '2026-08-11 18:40:19'),
(8, 8, 9, 'unique pot', 8000, 1, '2026-09-09 17:51:34', '2026-09-09 17:51:34'),
(9, 9, 8, 'lamp accessories', 10000, 1, '2026-09-09 17:53:25', '2026-09-09 17:53:25');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `snap_token` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `transaction_status` varchar(255) DEFAULT NULL,
  `payment_type` varchar(255) DEFAULT NULL,
  `raw_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw_response`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `snap_token`, `transaction_id`, `transaction_status`, `payment_type`, `raw_response`, `created_at`, `updated_at`) VALUES
(1, 5, '8fe43c3d-cac1-4390-b1e3-2a8a5fcbc2ab', NULL, NULL, NULL, NULL, '2026-08-11 18:08:46', '2026-08-11 18:08:46'),
(2, 6, '91a79d05-c9ba-45b3-9fd6-e73b5eb0e7b9', NULL, NULL, NULL, NULL, '2026-08-11 18:14:14', '2026-08-11 18:14:14'),
(3, 7, 'e0f78a03-f8f8-42fe-8c88-25800108b404', NULL, NULL, NULL, NULL, '2026-08-11 18:40:20', '2026-08-11 18:40:20');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` bigint(20) UNSIGNED NOT NULL,
  `cost_price` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `cost_price`, `stock`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'pot tanaman', 'pot-tanaman-d22O2', 'Terbuat dari galon bekas lemineral', 15000, 0, 9, 'products/bTBzgVYCzTf16H1x3eKasEPtnDCJMAHSBEcOSHZP.jpg', 1, '2026-08-10 23:39:54', '2026-08-10 23:49:39'),
(3, 2, 'tas anyam', 'tas-anyam-Kz8j8', 'terbuat dari rotan impor kamboja', 50000, 0, 14, 'products/xtudZSWdbxAwB6eR7Nh0AAF0w4vBZHOXTnQGKdKV.jpg', 1, '2026-08-10 23:54:26', '2026-08-11 18:40:19'),
(4, 3, 'Gantungan Kunci', 'gantungan-kunci-kQTSe', 'imut dan menggemaskan', 7000, 0, 25, 'products/ieRgROn72JEi9OCBX2uXWHczVMYveDbj2ClZbrPD.jpg', 1, '2026-08-11 00:01:11', '2026-08-11 17:52:33'),
(5, 4, 'Gelang Couple', 'gelang-couple-QjyDJ', 'barang yang couple dengan teman', 12000, 0, 99, 'products/p8u5drfeQZC2oUtrf0Pr2IqqUrW3PSvbmRXRiq5p.jpg', 1, '2026-08-11 00:08:04', '2026-08-11 17:29:00'),
(6, 4, 'photo keychain', 'photo-keychain-AQBg4', 'gantungan kunci couple', 20000, 0, 87, 'products/nhENCToXzdhtClzakAl2wiT1os5dIdTvLs8fuwKt.webp', 1, '2026-08-11 00:12:02', '2026-08-11 18:14:13'),
(7, 1, 'wadah pensil', 'wadah-pensil-nkuLj', 'lucu dengan berbagai varian warna dan bebas request', 5000, 0, 20, 'products/7nSqo6Zn6wO6FJ58Yvxc0Azuv018WlmM0I6fXnK5.png', 1, '2026-08-11 23:30:26', '2026-08-11 23:35:05'),
(8, 1, 'lamp accessories', 'lamp-accessories-DndzM', 'bagus untuk membuat lampu agar tidak membosankan', 10000, 0, 19, 'products/Wg4tvHXs77U9mDok7b71hg0R1klVhXRYigi5zsH8.jpg', 1, '2026-08-11 23:32:57', '2026-09-09 17:53:25'),
(9, 1, 'unique pot', 'unique-pot-leU0G', 'karakter lucu dengan pot dan tanaman', 8000, 0, 14, 'products/qFnv74g7BPb2aJy7FthdBhvSQxIc83okqHgHPOMC.jpg', 1, '2026-08-11 23:33:59', '2026-09-09 17:51:34'),
(10, 2, 'Totebag', 'totebag-tL9OI', 'Totebag dengan bahan canvas dengan motif dedaunan dan awet', 10000, 5000, 35, 'products/xp4DI4pHtCzEGkxVzN0r8489zl2eJy5alRr1eS2P.jpg', 1, '2026-09-09 21:32:41', '2026-09-09 21:32:41'),
(11, 2, 'Kaos ecoprint', 'kaos-ecoprint-1gych', 'Kaos dengan bahan katun yang cepat menyerap keringat', 40000, 30000, 25, 'products/7cQvCZo0d5hFhUy2CwF7w4yBJUFbXV4kWA7ldiDk.jpg', 1, '2026-09-09 21:36:30', '2026-09-09 21:36:30'),
(12, 5, 'Es Teh Jumbo', 'es-teh-jumbo-8khsB', 'Es Teh Jumbo dengan rasa segar setiap tegukan karna dibuat dengan teh lokal dan gula pasir tanpa biang manis', 5000, 3000, 100, 'products/M8aZE1uk2q3oPlXQoSNKQS4RLhbiF50K2ynNJkwO.jpg', 1, '2026-09-09 21:40:28', '2026-09-09 22:02:19'),
(13, 5, 'Jus Buah', 'jus-buah-JWGnd', 'Jus Buah segar dengan berbagai macam rasa yang segar dan menyehatkan tubuh', 8000, 5000, 35, 'products/CVxXigy5eAcLYBleyDWunGyK18PIEdpVdCuhoQCS.jpg', 1, '2026-09-09 21:42:26', '2026-09-09 22:02:04'),
(14, 5, 'Es Ubi Ungu', 'es-ubi-ungu-iTcpE', 'Es Ubi Ungu dengan ubi premium dan keju cheedar yang gurih dan fla yang manis dalam satu suapan', 5000, 4000, 15, 'products/SUX8hfh6Lzc5NaQMGA5MaDsvgZONdOAVBQJe1o2l.jpg', 1, '2026-09-09 21:46:22', '2026-09-09 22:03:13'),
(15, 6, 'Salad Buah', 'salad-buah-2GBCQ', 'Salad Buah dengan buah yang premium dan manis asam dalam setiap gigitan dan menyatu dengan dresing salad', 5000, 4500, 20, 'products/5kGmkNt3r9geGwsjYChn7EJ65O681YlyPEPxN2gr.jpg', 1, '2026-09-09 21:48:11', '2026-09-09 22:01:35'),
(16, 6, 'Fruit Sando', 'fruit-sando-8I6Zz', 'Fruit Sando roti lembut dengan wipe creamy manis dan buah segar', 8000, 6000, 15, 'products/44leXcagRTxSfPifJb8GQYXkP5cEsVdot28Zj1QJ.jpg', 1, '2026-09-09 21:49:51', '2026-09-09 22:01:20'),
(17, 6, 'Nasi Rames', 'nasi-rames-Mcaq7', 'Nasi Rames dengan orek dan mie bihun goreng dipadukan dengan nasi hangat putih dan taburan bawang goreng', 5000, 4500, 30, 'products/zpJ0Di9Z2CtBQMtg1V2ROJ5YBQ3n957MaquRdV84.jpg', 1, '2026-09-09 21:51:43', '2026-09-09 22:00:44');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('gnNIzUQMLVc4oMkpTVf9j3UzpBLbnVSDLh3bKZHC', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoia0s2THJDdU05elJqOW9KMWFCTDdvY25aZGtLaGw2bTF0ZW9RcGJveCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wcm9kdWN0cy9jcmVhdGUiO3M6NToicm91dGUiO3M6MjE6ImFkbWluLnByb2R1Y3RzLmNyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1789016625);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `phone`, `address`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', 'admin', NULL, NULL, NULL, '$2y$12$WoNXe4.TW5EhfJilpBlR0.UwXedH43WF1Jt8WZb8ZawbnC6VqFQ46', '9Hkcs4Z9fPB5TLTFUQZn86A2jz2Qvcl24cWr5bBcNdpHAk6xmtoj0ar9JQ8o', '2026-08-10 17:34:12', '2026-08-10 17:34:12'),
(2, 'Asel', 'aselole@gmail.com', 'user', NULL, NULL, NULL, '$2y$12$TORCZ3X8oVw6YQw.kePpLOvLkkolvHU63itYYr8mymS.vCWohr.86', NULL, '2026-08-10 23:48:56', '2026-08-10 23:48:56'),
(3, 'kula', 'kuladan@gmail.com', 'user', NULL, NULL, NULL, '$2y$12$6IcQU2rMV59VvZJa5HuQGOEeK1Q74yr5oI60G24Um1VXiE9Bfl3oS', NULL, '2026-08-11 17:28:21', '2026-08-11 17:28:21'),
(4, 'janedoe', 'jande@gmail.com', 'user', NULL, NULL, NULL, '$2y$12$JUUHF1VBfKdSfIE/XZhkD.MN7bBwgp7xTXcJnaUT5Dha.cBVEhh8q', NULL, '2026-08-11 23:15:43', '2026-08-11 23:15:43'),
(5, 'jane', 'jane@gmail.com', 'user', NULL, NULL, NULL, '$2y$12$vCBoXQd3p/fOGjozO.7Ug.Jdhwm9FGYWAr2QVGcjb58D0tz/82hse', NULL, '2026-09-09 17:50:35', '2026-09-09 17:50:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carts_user_id_foreign` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cart_items_cart_id_product_id_unique` (`cart_id`,`product_id`),
  ADD KEY `cart_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_order_id_foreign` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
