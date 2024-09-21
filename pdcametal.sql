-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2024 at 05:53 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pdcametal`
--

-- --------------------------------------------------------

--
-- Table structure for table `divisi`
--

CREATE TABLE `divisi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_divisi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `divisi`
--

INSERT INTO `divisi` (`id`, `nama_divisi`, `created_at`, `updated_at`) VALUES
(1, 'PERUSAHAAN', NULL, NULL),
(2, 'PERUSAHAAN SEKITAR', NULL, NULL),
(3, 'PRODUCTION', NULL, NULL),
(4, 'PRODUCTIONS SDG', NULL, NULL),
(5, 'MTC MECHANICAL', NULL, NULL),
(6, 'MTC MECHANICAL SDG', NULL, NULL),
(7, 'MTC ELECTRICS', NULL, NULL),
(8, 'MTC ELECTRICS SDG', NULL, NULL),
(9, 'MTC UTILITY', NULL, NULL),
(10, 'HR & GA', NULL, NULL),
(11, 'HR & GA SDG', NULL, NULL),
(12, 'SAFETY', NULL, NULL),
(13, 'QA', NULL, NULL),
(14, 'QA SDG', NULL, NULL),
(15, 'WAREHOUSE', NULL, NULL),
(16, 'WAREHOUSE SDG', NULL, NULL),
(17, 'SALES', NULL, NULL),
(18, 'IT', NULL, NULL),
(19, 'ACCOUNTING', NULL, NULL),
(20, 'PROCUREMENT', NULL, NULL),
(21, 'PPIC', NULL, NULL),
(22, 'DELIVERY', NULL, NULL),
(23, 'EKSPOR - IMPOR', NULL, NULL),
(24, 'FINANCE', NULL, NULL),
(25, 'INVOICING', NULL, NULL),
(26, 'ENGINEER', NULL, NULL),
(27, 'ENGINEER SDG', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `listform`
--

CREATE TABLE `listform` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_divisi` bigint(20) UNSIGNED NOT NULL,
  `issue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `peluang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tingkatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `risk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `before` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `after` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listform`
--

INSERT INTO `listform` (`id`, `id_divisi`, `issue`, `peluang`, `tingkatan`, `status`, `risk`, `created_at`, `updated_at`, `before`, `after`) VALUES
(1, 3, 'Kapasitas mesin R/O terbatas (3m3/jam) sedangkan total pemakaian di cleaning section, fume scruber, SPM & TL, Water Quenching  > 3 m3', 'Penghematan R/O, \r\nbagi produksi  tidak adda ketidak kekwatiran kekurangan RO saat tension leveller, spm dan cleaning section dijalankan secara bersamaan.', 'HIGH', 'OPEN', 'LOW', '2024-09-20 19:44:34', '2024-09-20 19:44:34', 'Test Before', 'Test After'),
(2, 3, 'cc', 'cc', 'MEDIUM', 'ON PROGRESS', 'HIGH', '2024-09-20 19:46:05', '2024-09-20 19:46:05', 'cc', 'cc'),
(3, 3, 'dd', 'dd', 'MEDIUM', 'ON PROGRESS', 'MEDIUM', '2024-09-20 19:46:40', '2024-09-20 19:46:40', 'dd', 'dd');

-- --------------------------------------------------------

--
-- Table structure for table `listkecil`
--

CREATE TABLE `listkecil` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_tindakan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realisasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsible` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accountable` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `consulted` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `informed` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anumgoal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anumbudget` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listkecil`
--

INSERT INTO `listkecil` (`id`, `id_tindakan`, `realisasi`, `responsible`, `accountable`, `consulted`, `informed`, `anumgoal`, `anumbudget`, `desc`, `date`, `created_at`, `updated_at`) VALUES
(1, '1', 'Progres awal 20/09/2024', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-09-30', '2024-09-20 19:44:34', '2024-09-20 19:44:57'),
(2, '2', 'progres awal 22/9/2024', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-09-27', '2024-09-20 19:44:34', '2024-09-20 19:45:19'),
(3, '3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-09-20 19:46:05', '2024-09-20 19:46:05'),
(4, '4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-09-20 19:46:40', '2024-09-20 19:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(18, '2014_10_12_100000_create_password_resets_table', 1),
(19, '2019_08_19_000000_create_failed_jobs_table', 1),
(20, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(21, '2024_09_02_035143_create_divisi_table', 1),
(22, '2024_09_02_045659_create_listform_table', 1),
(23, '2024_09_03_075947_create_user_table', 2),
(24, '2024_09_04_142005_add_pihak_to_listform_table', 3),
(25, '2024_09_06_025435_create_listkecil_table', 4),
(26, '2024_09_06_031422_create_listkecil_table', 5),
(27, '2024_09_11_025808_create_listkecil_table', 6),
(28, '2024_09_11_030625_create_listkecil_table', 7),
(29, '2024_09_11_162118_create_tindakan_table', 8),
(30, '2024_09_11_163207_create_tindakan_table', 9);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tindakan`
--

CREATE TABLE `tindakan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_tindakan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_listform` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resiko` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pihak` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tindakan`
--

INSERT INTO `tindakan` (`id`, `nama_tindakan`, `id_listform`, `pic`, `resiko`, `pihak`, `created_at`, `updated_at`) VALUES
(1, 'Menggunakan air industri utk yg tidak berhubungan dgn kualitas produk.', '1', 'Yudhy K', 'Line stop, produk cacat', '3', '2024-09-20 19:44:34', '2024-09-20 19:44:34'),
(2, 'Monitoring ketat penggunaan R/O', '1', 'Ferdi', 'Pencemaran lingkungan', '9', '2024-09-20 19:44:34', '2024-09-20 19:44:34'),
(3, 'cc', '2', 'cc', 'cc', '3', '2024-09-20 19:46:05', '2024-09-20 19:46:05'),
(4, 'dd', '3', 'dd', 'dd', '15', '2024-09-20 19:46:40', '2024-09-20 19:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `nama_user`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Taswono', 'taswono@tatalogam.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(2, 'Group Moxo PPK MFG', '5fb5bc550559143ed97b76d562659a3ac@tatalogam.moxo.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(3, 'Ossa', 'ossa.adi@tatalogam.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(4, 'Sugiyono', 'sugiyono@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(5, 'Maskula', 'ahmad.maskula@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(6, 'Rama Hasan H', 'rama.hasan@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(7, 'Tugiyanto', 'tugiyanto@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(8, 'Roby Risanda', 'roby@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(9, 'Ilham Jamaludin', 'ilham.jamaludin@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(10, 'Dian Persada', 'dian.persada@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(11, 'Ferdinandus Paulus Tirtadinata', 'ferdinandus@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(12, 'Lili Yusuf', 'lili.yusuf@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(13, 'Krismanto Susilo', 'krismanto.susilo@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(14, 'Agus Wicaksono', 'agus.wicaksono@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(15, 'Ahmed Nugroho', 'ahmed.nugroho@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(16, 'Ari Octaviyan', 'ari.octaviyan@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(17, 'Arsy Kusumagraha', 'arsy.kusumagraha@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(18, 'Diska Bustanul Hadi', 'diska@tatalogam.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(19, 'Mohamad Ramdhani', 'mohamad.ramdhani@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'admin', NULL, NULL),
(20, 'Satmoko', 'satmoko@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(21, 'Izan', 'andika.bachtiar@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(22, 'Ditta Pratama', 'ditta.pratama@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(23, 'Imam Qoirudin', 'imam.qoirudin@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(24, 'Ho alex marjoko', 'alex.ho@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(25, 'Zulkifli', 'zulkifli@tatalogam.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'admin', NULL, NULL),
(26, 'Shift Leader Produksi', 'leader.prd@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(27, 'Rendra Fernanda', 'rendra.fernanda@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(28, 'Ali Mahfut', 'ali.mahfut@tatalogam.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(29, 'Tony Widy Utomo', 'toni.widi@tatalogam.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(30, 'Ahmad Faozan', 'ahmad.faozan@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(31, 'Dami Arta', 'dami.arta@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(32, 'Fauzan Dini Fadhillah', 'fauzan.fadhillah@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(33, 'Guruh Sindu', 'guruh.putra@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(34, 'Benny Saputro', 'benny.saputro@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(35, 'Muhammad Nanang', 'muhammad.nanang@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(36, 'Wahyu Bagas', 'wahyu.laksana@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(37, 'Sigit Sejati', 'sigit.sejati@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(38, 'Hizbul', 'hizbul.sabiilafurqon@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(39, 'Ade Kurniawan', 'ade.kurniawan@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(40, 'Liana Waty Rusli', 'nana@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(41, 'Brigitta Maria Suharwati', 'brigitta.suharwati@tatalogam.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(42, 'Freddy', 'freddy.tampubolon@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(43, 'Riyan Hidayat', 'riyan.hidayat@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(44, 'Panggah Sahistyo', 'panggah@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(45, 'Antonius Danu Kurniawan', 'antonius.danu@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(46, 'Andi Setiawan', 'andi.setiawan@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(47, 'Asep Hilman', 'asep.hilman@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(48, 'Bakhtarudin', 'bakhtarudin@tatametal.com', '$2y$10$J9cw9kbCXawm/Y083r09X.3sAWwxGhCuBB4lp/4THUGOneXng5Qb6', 'user', NULL, NULL),
(49, 'ADMIN', 'admin@admin.com', '$2y$10$XxHmb/R4/Tvzjo38Sn9RoeCgBA.Hal0u23c13NBjvUl7E4gJXFSMC', 'admin', '2024-09-04 01:55:23', '2024-09-05 00:23:56'),
(50, 'USER', 'user@user.com', '$2y$10$J/9Tv0w49k4KEsQi/23aDueK16SsSWUgWTuzkZST9bHKUH23NO4uC', 'user', '2024-09-04 01:55:53', '2024-09-06 21:11:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `divisi`
--
ALTER TABLE `divisi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `listform`
--
ALTER TABLE `listform`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listform_id_divisi_foreign` (`id_divisi`);

--
-- Indexes for table `listkecil`
--
ALTER TABLE `listkecil`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `tindakan`
--
ALTER TABLE `tindakan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `divisi`
--
ALTER TABLE `divisi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `listform`
--
ALTER TABLE `listform`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `listkecil`
--
ALTER TABLE `listkecil`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tindakan`
--
ALTER TABLE `tindakan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `listform`
--
ALTER TABLE `listform`
  ADD CONSTRAINT `listform_id_divisi_foreign` FOREIGN KEY (`id_divisi`) REFERENCES `divisi` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
