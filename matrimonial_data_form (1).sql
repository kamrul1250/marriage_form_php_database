-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2025 at 04:04 PM
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
-- Database: `matrimonial_data_form`
--

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `user_id`, `name`, `file_path`, `file_type`, `file_size`, `created_at`) VALUES
(1, 1, '2.jpg', 'uploads/file_f3597c8542bab477.jpg', 'image/jpeg', 1788183, '2025-08-11 19:02:09');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partner_preferences`
--

CREATE TABLE `partner_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `age_from` int(11) DEFAULT NULL,
  `age_to` int(11) DEFAULT NULL,
  `height_from` varchar(10) DEFAULT NULL,
  `height_to` varchar(10) DEFAULT NULL,
  `marital_status` varchar(100) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `caste` varchar(100) DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `annual_income` varchar(50) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `diet` varchar(100) DEFAULT NULL,
  `smoke` varchar(100) DEFAULT NULL,
  `drink` varchar(100) DEFAULT NULL,
  `body_type` varchar(100) DEFAULT NULL,
  `complexion` varchar(100) DEFAULT NULL,
  `other_preferences` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partner_preferences`
--

INSERT INTO `partner_preferences` (`id`, `user_id`, `age_from`, `age_to`, `height_from`, `height_to`, `marital_status`, `religion`, `caste`, `education`, `occupation`, `annual_income`, `country`, `state`, `city`, `diet`, `smoke`, `drink`, `body_type`, `complexion`, `other_preferences`, `created_at`, `updated_at`) VALUES
(1, 1, 30, 30, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2025-08-11 19:36:04', '2025-08-11 19:55:11');

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE `photos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `is_profile_photo` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `dob` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `marital_status` enum('Never Married','Divorced','Widowed','Separated') DEFAULT NULL,
  `height` varchar(10) DEFAULT NULL,
  `weight` varchar(10) DEFAULT NULL,
  `body_type` enum('Slim','Average','Athletic','Heavy') DEFAULT NULL,
  `complexion` enum('Very Fair','Fair','Wheatish','Dark') DEFAULT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `disability` varchar(100) DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `caste` varchar(50) DEFAULT NULL,
  `sub_caste` varchar(50) DEFAULT NULL,
  `mother_tongue` varchar(50) DEFAULT NULL,
  `languages_known` varchar(255) DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL,
  `education_detail` varchar(255) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `occupation_detail` varchar(255) DEFAULT NULL,
  `annual_income` varchar(50) DEFAULT NULL,
  `employed_in` enum('Government','Private','Business','Self Employed','Not Working') DEFAULT NULL,
  `job_location` varchar(100) DEFAULT NULL,
  `family_status` enum('Middle Class','Upper Middle Class','Rich','Affluent') DEFAULT NULL,
  `family_type` enum('Joint','Nuclear') DEFAULT NULL,
  `family_values` enum('Traditional','Moderate','Liberal') DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `father_occupation` varchar(100) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `mother_occupation` varchar(100) DEFAULT NULL,
  `siblings` text DEFAULT NULL,
  `native_place` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `pincode` varchar(20) DEFAULT NULL,
  `about_me` text DEFAULT NULL,
  `hobbies` text DEFAULT NULL,
  `diet` enum('Vegetarian','Non-Vegetarian','Eggetarian','Vegan') DEFAULT NULL,
  `smoke` enum('No','Occasionally','Yes') DEFAULT NULL,
  `drink` enum('No','Occasionally','Yes') DEFAULT NULL,
  `partner_expectations` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `is_profile_complete` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `photo`, `full_name`, `gender`, `dob`, `age`, `marital_status`, `height`, `weight`, `body_type`, `complexion`, `blood_group`, `disability`, `religion`, `caste`, `sub_caste`, `mother_tongue`, `languages_known`, `education`, `education_detail`, `occupation`, `occupation_detail`, `annual_income`, `employed_in`, `job_location`, `family_status`, `family_type`, `family_values`, `father_name`, `father_occupation`, `mother_name`, `mother_occupation`, `siblings`, `native_place`, `phone`, `address`, `city`, `state`, `country`, `pincode`, `about_me`, `hobbies`, `diet`, `smoke`, `drink`, `partner_expectations`, `created_at`, `updated_at`, `is_profile_complete`) VALUES
(1, 1, 'uploads/profile_42423f4dd425d723.jpg', 'kamrul', 'Male', '1995-01-30', 30, 'Never Married', '', '', '', '', '', '', 'Islam', '', '', 'bd', '', 'Diploma', '', 'Other', '', '', '', '', '', '', '', '', '', '', '', 'dsdf', '', '01990207710', 'sdfsd', '', '', '', '', '', '', '', '', '', '', '2025-08-11 19:36:04', '2025-08-11 19:55:11', 1);

-- --------------------------------------------------------

--
-- Table structure for table `shortlists`
--

CREATE TABLE `shortlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `created_at`, `is_admin`, `is_active`) VALUES
(1, 'kamrul', 'kamrul@gmail.com', '$2y$10$NOi.tB3xF1APAO3bm4JBcuI/Vmb2NDm2vCNPl7okec9hWccaXevtq', '2025-08-11 19:02:00', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `user_id` int(11) NOT NULL,
  `theme_preference` varchar(10) NOT NULL DEFAULT 'light',
  `language_preference` varchar(5) NOT NULL DEFAULT 'en',
  `notification_preferences` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_preferences`
--

INSERT INTO `user_preferences` (`user_id`, `theme_preference`, `language_preference`, `notification_preferences`) VALUES
(1, 'light', 'de', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `partner_preferences`
--
ALTER TABLE `partner_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `shortlists`
--
ALTER TABLE `shortlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_profile` (`user_id`,`profile_id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `partner_preferences`
--
ALTER TABLE `partner_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shortlists`
--
ALTER TABLE `shortlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `partner_preferences`
--
ALTER TABLE `partner_preferences`
  ADD CONSTRAINT `partner_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shortlists`
--
ALTER TABLE `shortlists`
  ADD CONSTRAINT `shortlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shortlists_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
