-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Feb 2026 pada 13.31
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `koperasi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bills`
--

CREATE TABLE `bills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `bill_type` varchar(191) NOT NULL DEFAULT 'kas',
  `amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `due_date` date NOT NULL,
  `paid_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bills`
--

INSERT INTO `bills` (`id`, `customer_id`, `bill_type`, `amount`, `paid_amount`, `due_date`, `paid_date`, `notes`, `created_at`, `updated_at`) VALUES
(2, 4, 'Kas Bulanan', 50000.00, 50000.00, '2026-02-06', '2026-02-04 20:12:46', 'dibayar ya kasssnyaaa', '2026-02-04 10:35:23', '2026-02-04 13:12:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `collaterals`
--

CREATE TABLE `collaterals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `value` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `description` varchar(191) DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nik` varchar(16) NOT NULL,
  `name` varchar(191) NOT NULL,
  `number` varchar(191) NOT NULL,
  `gender` enum('L','P') NOT NULL DEFAULT 'L',
  `birth` date DEFAULT NULL,
  `address` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `last_education` varchar(191) DEFAULT NULL,
  `profession` varchar(191) DEFAULT NULL,
  `status` enum('active','blacklist') NOT NULL DEFAULT 'active',
  `photo` varchar(191) DEFAULT NULL,
  `joined_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `deposits`
--

CREATE TABLE `deposits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('wajib','sukarela','pokok','penarikan') NOT NULL DEFAULT 'sukarela',
  `amount` int(10) UNSIGNED NOT NULL,
  `previous_balance` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `current_balance` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `loan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expense_category_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(191) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `expense_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `expenses`
--

INSERT INTO `expenses` (`id`, `expense_category_id`, `created_by`, `description`, `amount`, `expense_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'fotokopi pensi', 50000.00, '2026-02-04', 'sSQSASA', '2026-02-04 10:50:01', '2026-02-04 10:50:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Fotokopi', 'Biaya fotokopi untuk keperluan sekolah', '2026-02-04 09:43:30', '2026-02-04 09:43:30'),
(2, 'Kebersihan', 'Biaya kebersihan ruang dan area', '2026-02-04 09:43:30', '2026-02-04 09:43:30'),
(3, 'Dana Sosial', 'Dana untuk kegiatan sosial anggota', '2026-02-04 09:43:30', '2026-02-04 09:43:30'),
(4, 'Perawatan Peralatan', 'Pemeliharaan dan perbaikan peralatan', '2026-02-04 09:43:30', '2026-02-04 09:43:30'),
(5, 'Administrasi', 'Biaya administrasi dan operasional', '2026-02-04 09:43:30', '2026-02-04 09:43:30'),
(6, 'Konsumsi & Catering', 'Biaya makan minum untuk acara', '2026-02-04 09:43:30', '2026-02-04 09:43:30'),
(7, 'Dekorasi & Perlengkapan Acara', 'Biaya dekorasi untuk kegiatan', '2026-02-04 09:43:30', '2026-02-04 09:43:30'),
(8, 'Transportasi', 'Biaya transportasi anggota', '2026-02-04 09:43:30', '2026-02-04 09:43:30'),
(9, 'Lainnya', 'Kategori pengeluaran lainnya', '2026-02-04 09:43:30', '2026-02-04 09:43:30'),
(10, 'Pentas Seni', 'Pentas Seni pt23', '2026-02-04 10:54:00', '2026-02-04 10:54:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `expense_receipts`
--

CREATE TABLE `expense_receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expense_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(191) NOT NULL,
  `file_name` varchar(191) NOT NULL,
  `mime_type` varchar(191) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `expense_receipts`
--

INSERT INTO `expense_receipts` (`id`, `expense_id`, `file_path`, `file_name`, `mime_type`, `file_size`, `created_at`, `updated_at`) VALUES
(1, 1, 'expenses/OWCTuFADmDGrRh1doxHXYWUpNqjytXc96ow0MNro.png', 'Screenshot 2026-02-02 084504.png', 'image/png', 39486, '2026-02-04 10:50:03', '2026-02-04 10:50:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `foreclosures`
--

CREATE TABLE `foreclosures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `collateral_amount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `remaining_amount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `return_amount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `loan_id` bigint(20) UNSIGNED NOT NULL,
  `collateral_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `loans`
--

CREATE TABLE `loans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `period` smallint(5) UNSIGNED NOT NULL DEFAULT 12 COMMENT 'Jangka waktu cicilan dengan satuan bulan. Default 12 bulan.',
  `amount` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'jumlah pinjaman',
  `installment` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'cicilan',
  `return_amount` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'pengembalian',
  `paid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'jumlah pembayaran',
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `collateral_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2022_06_15_114258_add_gender_columns_to_users_table', 1),
(6, '2022_06_18_000725_create_customers_table', 1),
(7, '2022_06_18_160457_create_collaterals_table', 1),
(8, '2022_06_18_164213_create_loans_table', 1),
(9, '2022_06_19_094908_create_deposits_table', 1),
(10, '2022_06_25_062808_add_paid_column_to_loans_table', 1),
(11, '2022_06_25_112144_create_visits_table', 1),
(12, '2022_06_25_140614_create_foreclosures_table', 1),
(13, '2024_02_04_100001_create_expense_categories_table', 1),
(14, '2024_02_04_100002_create_expenses_table', 1),
(15, '2024_02_04_100003_create_expense_receipts_table', 1),
(16, '2024_02_04_100004_create_bills_table', 1),
(17, '2024_02_04_100005_create_telegram_users_table', 1),
(18, '2024_02_04_100006_create_transparency_reports_table', 1),
(19, '2026_02_04_173416_change_bills_customer_id_to_user_id', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `telegram_users`
--

CREATE TABLE `telegram_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `telegram_id` varchar(191) NOT NULL,
  `first_name` varchar(191) DEFAULT NULL,
  `last_name` varchar(191) DEFAULT NULL,
  `username` varchar(191) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transparency_reports`
--

CREATE TABLE `transparency_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `access_token` varchar(191) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `username` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `gender` enum('L','P') NOT NULL DEFAULT 'L',
  `birth` date DEFAULT NULL,
  `last_education` varchar(191) DEFAULT NULL,
  `address` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `joined_at` datetime DEFAULT NULL,
  `role` enum('manager','teller','collector') NOT NULL DEFAULT 'collector',
  `photo` varchar(191) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `gender`, `birth`, `last_education`, `address`, `phone`, `joined_at`, `role`, `photo`) VALUES
(1, 'Manajer', 'manajer', NULL, '$2y$10$aDlVZQnqArRkSgSsaaU4ZuGNW7GzLjyeACRwmqU/PXleNxWlWbEXy', NULL, '2026-02-04 09:43:26', '2026-02-04 09:43:26', 'L', NULL, NULL, NULL, '0821', '2026-02-03 16:43:25', 'manager', NULL),
(2, 'Luklu Miranda', 'luklu', NULL, '$2y$10$dB0ac7bYuwoI/jZp2qhD1OxRZl/Ri4JEpWX8RU3bcbzgsyYCFhFIO', NULL, '2026-02-04 09:43:26', '2026-02-04 10:19:39', 'P', '2004-06-08', 'SMA', 'Palembang', '082284818491', '2026-02-04 16:43:25', 'teller', NULL),
(4, 'Jonathan Kevin Binsar Pangaribuan', 'Joke', NULL, '$2y$10$5ev9DeQEBMfFeIZ9W2Q56e9Zr6vuCOYuPX9AZqmd6OzV.XXsVgvva', NULL, '2026-02-04 10:08:52', '2026-02-04 11:30:09', 'L', '2005-04-30', 'SMA', 'Perumahan pesona permata hijau', '081212645538', '2026-02-04 00:00:00', 'collector', '1770201009.jpg'),
(5, 'Zhafran Riko Santoso', 'Zhafran', NULL, '$2y$10$tC0fxaAFXKqsDj0PyI.d6.9Zo6xGjI6rZWh19rweb1enRvMbcSN5K', NULL, '2026-02-04 10:15:58', '2026-02-04 10:15:58', 'L', '2006-02-21', 'Sma', 'bantar wetan, bangunciptosutoro, kulon progo, jogja', '0882006773577', '2026-02-04 00:00:00', 'collector', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `visits`
--

CREATE TABLE `visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `remaining_amount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `description` text NOT NULL,
  `loan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bills_customer_id_foreign` (`customer_id`);

--
-- Indeks untuk tabel `collaterals`
--
ALTER TABLE `collaterals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collaterals_customer_id_foreign` (`customer_id`);

--
-- Indeks untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_nik_unique` (`nik`),
  ADD UNIQUE KEY `customers_number_unique` (`number`),
  ADD UNIQUE KEY `customers_phone_unique` (`phone`);

--
-- Indeks untuk tabel `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deposits_customer_id_foreign` (`customer_id`),
  ADD KEY `deposits_loan_id_foreign` (`loan_id`);

--
-- Indeks untuk tabel `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
  ADD KEY `expenses_created_by_foreign` (`created_by`);

--
-- Indeks untuk tabel `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `expense_receipts`
--
ALTER TABLE `expense_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_receipts_expense_id_foreign` (`expense_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `foreclosures`
--
ALTER TABLE `foreclosures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `foreclosures_customer_id_foreign` (`customer_id`),
  ADD KEY `foreclosures_loan_id_foreign` (`loan_id`),
  ADD KEY `foreclosures_collateral_id_foreign` (`collateral_id`);

--
-- Indeks untuk tabel `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loans_customer_id_foreign` (`customer_id`),
  ADD KEY `loans_collateral_id_foreign` (`collateral_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `telegram_users`
--
ALTER TABLE `telegram_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `telegram_users_telegram_id_unique` (`telegram_id`),
  ADD KEY `telegram_users_customer_id_foreign` (`customer_id`);

--
-- Indeks untuk tabel `transparency_reports`
--
ALTER TABLE `transparency_reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transparency_reports_access_token_unique` (`access_token`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`);

--
-- Indeks untuk tabel `visits`
--
ALTER TABLE `visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visits_loan_id_foreign` (`loan_id`),
  ADD KEY `visits_customer_id_foreign` (`customer_id`),
  ADD KEY `visits_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bills`
--
ALTER TABLE `bills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `collaterals`
--
ALTER TABLE `collaterals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `expense_receipts`
--
ALTER TABLE `expense_receipts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `foreclosures`
--
ALTER TABLE `foreclosures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `loans`
--
ALTER TABLE `loans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `telegram_users`
--
ALTER TABLE `telegram_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `transparency_reports`
--
ALTER TABLE `transparency_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `visits`
--
ALTER TABLE `visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `collaterals`
--
ALTER TABLE `collaterals`
  ADD CONSTRAINT `collaterals_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `deposits`
--
ALTER TABLE `deposits`
  ADD CONSTRAINT `deposits_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `deposits_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `expense_receipts`
--
ALTER TABLE `expense_receipts`
  ADD CONSTRAINT `expense_receipts_expense_id_foreign` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `foreclosures`
--
ALTER TABLE `foreclosures`
  ADD CONSTRAINT `foreclosures_collateral_id_foreign` FOREIGN KEY (`collateral_id`) REFERENCES `collaterals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `foreclosures_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `foreclosures_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_collateral_id_foreign` FOREIGN KEY (`collateral_id`) REFERENCES `collaterals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `loans_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `telegram_users`
--
ALTER TABLE `telegram_users`
  ADD CONSTRAINT `telegram_users_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `visits`
--
ALTER TABLE `visits`
  ADD CONSTRAINT `visits_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `visits_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `visits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
