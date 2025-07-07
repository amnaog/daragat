-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2025 at 04:19 PM
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
-- Database: `darajat`
--

-- --------------------------------------------------------

--
-- Table structure for table `halaqat`
--

CREATE TABLE `halaqat` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `schedule` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `time` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `halaqat`
--

INSERT INTO `halaqat` (`id`, `name`, `teacher_id`, `schedule`, `created_at`, `time`) VALUES
(1, 'AL-Fajr Halqa', 1, 'Daily, 5:30 AM - 7:00 AM', '2025-06-27 09:23:41', 'العصر'),
(2, 'Halaqat Morning', 1, 'Sun - Wed, 9:00 AM - 10:00 AM', '2025-06-27 11:38:05', 'الفجر'),
(3, 'Halaqat Evening', 1, 'Mon - Thu, 6:00 PM - 7:00 PM', '2025-06-27 11:38:05', 'المغرب');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `student_id`, `teacher_id`, `content`, `sent_at`, `is_read`) VALUES
(1, 1, 1, 'You have an exam tomorrow', '2025-07-03 15:03:16', 1),
(2, 1, 1, 'Tomorrow\'s class has been cancelled due to health reasons.', '2025-07-03 15:14:59', 1);

-- --------------------------------------------------------

--
-- Table structure for table `quran_surahs`
--

CREATE TABLE `quran_surahs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `ayah_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quran_surahs`
--

INSERT INTO `quran_surahs` (`id`, `name`, `ayah_count`) VALUES
(1, 'Al-Fatiha', 7),
(2, 'Al-Baqarah', 286),
(3, 'Al-Imran', 200),
(4, 'An-Nisa', 176),
(5, 'Al-Ma\'idah', 120),
(6, 'Al-An\'am', 165),
(7, 'Al-A\'raf', 206),
(8, 'Al-Anfal', 75),
(9, 'At-Tawbah', 129),
(10, 'Yunus', 109),
(11, 'Hud', 123),
(12, 'Yusuf', 111),
(13, 'Ar-Ra\'d', 43),
(14, 'Ibrahim', 52),
(15, 'Al-Hijr', 99),
(16, 'An-Nahl', 128),
(17, 'Al-Isra', 111),
(18, 'Al-Kahf', 110),
(19, 'Maryam', 98),
(20, 'Ta-Ha', 135),
(21, 'Al-Anbiya', 112),
(22, 'Al-Hajj', 78),
(23, 'Al-Mu\'minun', 118),
(24, 'An-Nur', 64),
(25, 'Al-Furqan', 77),
(26, 'Ash-Shu\'ara', 227),
(27, 'An-Naml', 93),
(28, 'Al-Qasas', 88),
(29, 'Al-Ankabut', 69),
(30, 'Ar-Rum', 60),
(31, 'Luqman', 34),
(32, 'As-Sajda', 30),
(33, 'Al-Ahzab', 73),
(34, 'Saba', 54),
(35, 'Fatir', 45),
(36, 'Ya-Sin', 83),
(37, 'As-Saffat', 182),
(38, 'Sad', 88),
(39, 'Az-Zumar', 75),
(40, 'Ghafir', 85),
(41, 'Fussilat', 54),
(42, 'Ash-Shura', 53),
(43, 'Az-Zukhruf', 89),
(44, 'Ad-Dukhan', 59),
(45, 'Al-Jathiya', 37),
(46, 'Al-Ahqaf', 35),
(47, 'Muhammad', 38),
(48, 'Al-Fath', 29),
(49, 'Al-Hujurat', 18),
(50, 'Qaf', 45),
(51, 'Adh-Dhariyat', 60),
(52, 'At-Tur', 49),
(53, 'An-Najm', 62),
(54, 'Al-Qamar', 55),
(55, 'Ar-Rahman', 78),
(56, 'Al-Waqi\'ah', 96),
(57, 'Al-Hadid', 29),
(58, 'Al-Mujadila', 22),
(59, 'Al-Hashr', 24),
(60, 'Al-Mumtahanah', 13),
(61, 'As-Saff', 14),
(62, 'Al-Jumu\'ah', 11),
(63, 'Al-Munafiqun', 11),
(64, 'At-Taghabun', 18),
(65, 'At-Talaq', 12),
(66, 'At-Tahrim', 12),
(67, 'Al-Mulk', 30),
(68, 'Al-Qalam', 52),
(69, 'Al-Haqqah', 52),
(70, 'Al-Ma\'arij', 44),
(71, 'Nuh', 28),
(72, 'Al-Jinn', 28),
(73, 'Al-Muzzammil', 20),
(74, 'Al-Muddathir', 56),
(75, 'Al-Qiyamah', 40),
(76, 'Al-Insan', 31),
(77, 'Al-Mursalat', 50),
(78, 'An-Naba', 40),
(79, 'An-Nazi\'at', 46),
(80, 'Abasa', 42),
(81, 'At-Takwir', 29),
(82, 'Al-Infitar', 19),
(83, 'Al-Mutaffifin', 36),
(84, 'Al-Inshiqaq', 25),
(85, 'Al-Buruj', 22),
(86, 'At-Tariq', 17),
(87, 'Al-A\'la', 19),
(88, 'Al-Ghashiyah', 26),
(89, 'Al-Fajr', 30),
(90, 'Al-Balad', 20),
(91, 'Ash-Shams', 15),
(92, 'Al-Layl', 21),
(93, 'Ad-Duhaa', 11),
(94, 'Ash-Sharh', 8),
(95, 'At-Tin', 8),
(96, 'Al-`Alaq', 19),
(97, 'Al-Qadr', 5),
(98, 'Al-Bayyinah', 8),
(99, 'Az-Zalzalah', 8),
(100, 'Al-`Adiyat', 11),
(101, 'Al-Qari`ah', 11),
(102, 'At-Takathur', 8),
(103, 'Al-Asr', 3),
(104, 'Al-Humazah', 9),
(105, 'Al-Fil', 5),
(106, 'Quraysh', 4),
(107, 'Al-Ma`un', 7),
(108, 'Al-Kawthar', 3),
(109, 'Al-Kafirun', 6),
(110, 'An-Nasr', 3),
(111, 'Al-Masad', 5),
(112, 'Al-Ikhlas', 4),
(113, 'Al-Falaq', 5),
(114, 'An-Nas', 6);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `halaqa_id` int(11) DEFAULT NULL,
  `performance` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `progress_percent` float NOT NULL DEFAULT 0,
  `verses_memorized` int(11) DEFAULT NULL,
  `surah_id` int(11) DEFAULT NULL,
  `from_ayah` int(11) DEFAULT NULL,
  `to_ayah` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `student_id`, `halaqa_id`, `performance`, `created_at`, `progress_percent`, `verses_memorized`, `surah_id`, `from_ayah`, `to_ayah`) VALUES
(124, 1, NULL, NULL, '2025-07-01 23:00:00', 0, NULL, 1, 1, 7),
(125, 1, NULL, NULL, '2025-07-01 23:00:00', 0, NULL, 2, 1, 40),
(126, 2, NULL, NULL, '2025-07-01 23:00:00', 0, NULL, 1, 1, 7),
(127, 3, NULL, NULL, '2025-07-01 23:00:00', 0, NULL, 1, 1, 7),
(128, 3, NULL, NULL, '2025-07-01 23:00:00', 0, NULL, 2, 1, 122),
(129, 4, NULL, NULL, '2025-07-01 23:00:00', 0, NULL, 1, 1, 7),
(130, 4, NULL, NULL, '2025-07-01 23:00:00', 0, NULL, 2, 1, 18),
(131, 1, NULL, NULL, '2025-07-02 23:00:00', 0, NULL, 2, 41, 80),
(132, 1, NULL, NULL, '2025-07-02 23:00:00', 0, NULL, 2, 81, 286),
(133, 1, NULL, NULL, '2025-07-02 23:00:00', 0, NULL, 3, 1, 15);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `permissions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `permissions`) VALUES
(1, 'admin', NULL),
(2, 'teacher', NULL),
(3, 'student', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `halaqa_id` int(11) DEFAULT NULL,
  `attendance_status` enum('present','absent') DEFAULT 'present',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `level` varchar(100) DEFAULT NULL,
  `progress` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `full_name`, `email`, `phone`, `halaqa_id`, `attendance_status`, `created_at`, `level`, `progress`) VALUES
(1, 'Yusuf Ahmed', 'yusuf.ahmed@example.com', '0921234567', 1, 'present', '2025-06-26 17:26:17', 'Juz Amma', 5),
(2, 'Fatima Al-Fihri', 'fatima.fihri@example.com', '0927654321', 1, 'present', '2025-06-26 17:26:17', 'Juz Tabarak', 0),
(3, 'Ali ibn Abi Talib', 'ali.talib@example.com', '0911239876', 2, 'absent', '2025-06-26 17:26:17', 'Full Quran Review', 2),
(4, 'Aisha bint Abu Bakr', 'aisha.bakr@example.com', '0917894561', 2, 'present', '2025-06-26 17:26:17', '5 Juz', 0),
(5, 'Zayd ibn Thabit', 'zayd.thabit@example.com', '0932581473', 3, 'present', '2025-06-26 17:26:17', 'Juz Amma', 2),
(6, 'Khadija Al-Kubra', 'khadija.kubra@example.com', '0941597532', 3, 'absent', '2025-06-26 17:26:17', '10 Juz', 40);

-- --------------------------------------------------------

--
-- Table structure for table `student_goals`
--

CREATE TABLE `student_goals` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `monthly_goal` int(11) DEFAULT NULL,
  `monthly_achieved` int(11) DEFAULT NULL,
  `daily_goal` int(11) DEFAULT NULL,
  `daily_achieved` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_goals`
--

INSERT INTO `student_goals` (`id`, `student_id`, `monthly_goal`, `monthly_achieved`, `daily_goal`, `daily_achieved`) VALUES
(1, 1, 450, 145, 15, 5);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `halaqa_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `full_name`, `email`, `phone`, `halaqa_id`, `created_at`) VALUES
(1, 'Sheikh Abdullah', 'sheikh.abdullah@quran.com', '0912345678', 1, '2025-06-27 10:27:26'),
(2, 'Sheikh Omar', 'omar@example.com', '0911111111', 2, '2025-06-27 11:40:19'),
(3, 'Sheikh Ayman', 'ayman@example.com', '0922222222', 3, '2025-06-27 11:40:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role_id`) VALUES
(1, 'admin', 'admin@example.com', 'admin', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `halaqat`
--
ALTER TABLE `halaqat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `quran_surahs`
--
ALTER TABLE `quran_surahs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `halaqa_id` (`halaqa_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student_goals`
--
ALTER TABLE `student_goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `halaqat`
--
ALTER TABLE `halaqat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `quran_surahs`
--
ALTER TABLE `quran_surahs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `student_goals`
--
ALTER TABLE `student_goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `halaqat`
--
ALTER TABLE `halaqat`
  ADD CONSTRAINT `halaqat_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`halaqa_id`) REFERENCES `halaqat` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_goals`
--
ALTER TABLE `student_goals`
  ADD CONSTRAINT `student_goals_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
