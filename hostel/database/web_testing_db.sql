-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 23, 2026 at 03:37 PM
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
-- Database: `web_testing_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `role_tab`
--

CREATE TABLE `role_tab` (
  `sn` int(11) NOT NULL,
  `role_id` int(255) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_tab`
--

CREATE TABLE `staff_tab` (
  `sn` int(11) NOT NULL,
  `staff_id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `status_id` varchar(255) NOT NULL,
  `department_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` varchar(255) NOT NULL,
  `reset_otp` varchar(6) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `status_tab`
--

CREATE TABLE `status_tab` (
  `sn` int(11) NOT NULL,
  `status_id` varchar(255) NOT NULL,
  `status_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_tab`
--

CREATE TABLE `user_tab` (
  `sn` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `status_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_otp` varchar(6) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_tab`
--

INSERT INTO `user_tab` (`sn`, `user_id`, `first_name`, `last_name`, `email_address`, `phone_number`, `status_id`, `password`, `reset_otp`, `created_at`, `updated_at`) VALUES
(1, 'USER20260916031231', 'ayo', 'ayo', 'ayo@gmail.com', '09050121142', 'A', '2d02e669731cbade6a64b58d602cf2a4', '0', '2026-09-16 14:12:31', '2026-09-16 13:12:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `role_tab`
--
ALTER TABLE `role_tab`
  ADD PRIMARY KEY (`sn`);

--
-- Indexes for table `staff_tab`
--
ALTER TABLE `staff_tab`
  ADD PRIMARY KEY (`sn`);

--
-- Indexes for table `status_tab`
--
ALTER TABLE `status_tab`
  ADD PRIMARY KEY (`sn`);

--
-- Indexes for table `user_tab`
--
ALTER TABLE `user_tab`
  ADD PRIMARY KEY (`sn`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `role_tab`
--
ALTER TABLE `role_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_tab`
--
ALTER TABLE `staff_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `status_tab`
--
ALTER TABLE `status_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_tab`
--
ALTER TABLE `user_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
