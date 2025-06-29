-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2025 at 09:35 PM
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `halaqat`
--

INSERT INTO `halaqat` (`id`, `name`, `teacher_id`, `schedule`, `created_at`) VALUES
(1, 'AL-Fajr Halqaaaa', 1, 'Daily, 5:30 AM - 7:00 AM', '2025-06-27 09:23:41'),
(2, 'Halaqat Omar Morning', NULL, 'Sun - Wed, 9:00 AM - 10:00 AM', '2025-06-27 11:38:05'),
(3, 'Halaqat Ayman Evening', NULL, 'Mon - Thu, 6:00 PM - 7:00 PM', '2025-06-27 11:38:05');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `message`, `created_at`, `student_id`) VALUES
(1, 'vjhh', '2025-06-27 10:31:31', 1),
(2, 'vjhh', '2025-06-27 10:33:42', 1),
(3, 'hi ftoma altoma', '2025-06-27 11:36:32', 2),
(4, 'hi ftoma altoma', '2025-06-27 11:45:21', 2),
(5, 'ASVB\r\n', '2025-06-27 15:14:21', 2),
(6, 'aaaaaaaa', '2025-06-27 16:24:41', 1);

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
  `progress_percent` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `student_id`, `halaqa_id`, `performance`, `created_at`, `progress_percent`) VALUES
(5, 1, 1, 'Memorized Surah Al-Baqarah', '2025-05-31 23:00:00', 80),
(6, 2, 1, 'Memorized Surah Yasin', '2025-06-07 23:00:00', 75),
(7, 3, 1, 'Memorized Surah Al-Asr', '2025-06-14 23:00:00', 60),
(8, 4, 1, 'Memorized Surah Al-Falaq', '2025-06-21 23:00:00', 90),
(9, 5, 3, 'Memorized Surah Al-Qadr', '2025-06-24 23:00:00', 70),
(10, 6, 3, 'Memorized Surah Al-Ma\'un', '2025-06-23 23:00:00', 40),
(11, 7, 3, 'Memorized Surah Al-Takathur', '2025-06-25 23:00:00', 80);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `permissions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Yusuf Ahmed', 'yusuf.ahmed@example.com', '0921234567', 1, 'present', '2025-06-26 17:26:17', 'Juz Amma', 85),
(2, 'Fatima Al-Fihri', 'fatima.fihri@example.com', '0927654321', 1, 'present', '2025-06-26 17:26:17', 'Juz Tabarak', 45),
(3, 'Ali ibn Abi Talib', 'ali.talib@example.com', '0911239876', 2, 'absent', '2025-06-26 17:26:17', 'Full Quran Review', 95),
(4, 'Aisha bint Abu Bakr', 'aisha.bakr@example.com', '0917894561', 2, 'present', '2025-06-26 17:26:17', '5 Juz', 60),
(5, 'Zayd ibn Thabit', 'zayd.thabit@example.com', '0932581473', 3, 'present', '2025-06-26 17:26:17', 'Juz Amma', 70),
(6, 'Khadija Al-Kubra', 'khadija.kubra@example.com', '0941597532', 3, 'absent', '2025-06-26 17:26:17', '10 Juz', 40),
(7, 'Uthman ibn Affan', 'uthman.affan@example.com', '0957539512', 3, 'present', '2025-06-26 17:26:17', 'Juz Tabarak', 80);

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
-- Indexes for dumped tables
--

--
-- Indexes for table `halaqat`
--
ALTER TABLE `halaqat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifications_student` (`student_id`);

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
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `halaqat`
--
ALTER TABLE `halaqat`
  ADD CONSTRAINT `halaqat_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`halaqa_id`) REFERENCES `halaqat` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
