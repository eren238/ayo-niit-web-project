-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2026 at 02:31 PM
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
-- Database: `hostel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `allocation_tab`
--

CREATE TABLE `allocation_tab` (
  `allocation_id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `hostel_id` varchar(20) NOT NULL,
  `room_id` varchar(50) NOT NULL,
  `bed_id` varchar(50) NOT NULL,
  `status_id` varchar(50) NOT NULL DEFAULT 'A',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `beds_tab`
--

CREATE TABLE `beds_tab` (
  `bed_id` varchar(50) NOT NULL,
  `hostel_id` varchar(20) NOT NULL,
  `room_id` varchar(50) NOT NULL,
  `bed_number` varchar(10) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `status_id` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beds_tab`
--

INSERT INTO `beds_tab` (`bed_id`, `hostel_id`, `room_id`, `bed_number`, `student_id`, `status_id`, `created_at`, `updated_at`) VALUES
('AYO-2-1', 'AYO', 'ROOM20260923112821', '1', NULL, 'D', '2026-09-23 10:48:08', '2026-09-23 10:48:08');

-- --------------------------------------------------------

--
-- Table structure for table `checkouts_tab`
--

CREATE TABLE `checkouts_tab` (
  `checkout_id` varchar(50) NOT NULL,
  `allocation_id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `hostel_id` varchar(20) NOT NULL,
  `room_id` varchar(50) NOT NULL,
  `bed_id` varchar(50) NOT NULL,
  `checkout_date` date NOT NULL,
  `reason` varchar(255) NOT NULL,
  `room_condition` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department_tab`
--

CREATE TABLE `department_tab` (
  `sn` int(11) NOT NULL,
  `department_id` varchar(100) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_tab`
--

CREATE TABLE `fees_tab` (
  `fee_id` varchar(50) NOT NULL,
  `fee_name` varchar(150) NOT NULL,
  `hostel_id` varchar(50) NOT NULL,
  `session` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `due_date` date NOT NULL,
  `status_id` varchar(50) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hostels_tab`
--

CREATE TABLE `hostels_tab` (
  `hostel_id` varchar(20) NOT NULL,
  `hostel_name` varchar(255) NOT NULL,
  `code` varchar(15) NOT NULL,
  `gender` enum('Male','Female','Mixed') NOT NULL,
  `hostel_capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hostels_tab`
--

INSERT INTO `hostels_tab` (`hostel_id`, `hostel_name`, `code`, `gender`, `hostel_capacity`) VALUES
('ayo', 'bells', 'ayo', 'Male', 100),
('bayo', 'bells', 'bayo', 'Male', 100);

-- --------------------------------------------------------

--
-- Table structure for table `payments_tab`
--

CREATE TABLE `payments_tab` (
  `payment_id` varchar(50) NOT NULL,
  `receipt_no` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `fee_id` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `status_id` varchar(50) NOT NULL DEFAULT 'P',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_tab`
--

CREATE TABLE `role_tab` (
  `sn` int(11) NOT NULL,
  `role_id` int(255) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_tab`
--

INSERT INTO `role_tab` (`sn`, `role_id`, `role_name`) VALUES
(1, 1, 'Super Admin'),
(2, 2, 'Hostel Admin'),
(3, 3, 'Warden');

-- --------------------------------------------------------

--
-- Table structure for table `rooms_tab`
--

CREATE TABLE `rooms_tab` (
  `room_id` varchar(50) NOT NULL,
  `hostel_id` varchar(20) NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `block` varchar(20) NOT NULL,
  `floor` varchar(20) NOT NULL,
  `room_capacity` int(11) NOT NULL,
  `status_id` varchar(50) NOT NULL DEFAULT 'Open',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms_tab`
--

INSERT INTO `rooms_tab` (`room_id`, `hostel_id`, `room_number`, `block`, `floor`, `room_capacity`, `status_id`, `created_at`, `updated_at`) VALUES
('ROOM20260924152140', 'AYO', '2', '2', '1', 4, '1', '2026-09-24 14:21:40', '2026-09-24 14:21:40'),
('ROOM20260924153633', 'AYO', '3', '2', '1', 40, '1', '2026-09-24 14:36:33', '2026-09-24 14:36:33');

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
  `password` varchar(255) NOT NULL,
  `role_id` varchar(100) DEFAULT NULL,
  `reset_otp` varchar(6) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_tab`
--

INSERT INTO `staff_tab` (`sn`, `staff_id`, `first_name`, `last_name`, `email_address`, `password`, `role_id`, `reset_otp`, `last_login`, `created_at`, `updated_at`, `status_id`) VALUES
(1, 'STAFF20260917025259', 'ayomide', 'ayo', 'ayo2@gmail.com', '19351f0df296e6e73c827a2fff8dbed1', '1', '783910', NULL, '2026-09-17 13:52:59', '2026-09-23 13:07:27', '1'),
(2, 'STAFF20260917025336', 'dayo', 'posi', 'ayo22@gmail.com', '833f311c198f54ab6bcddaa97e929493', '2', NULL, NULL, '2026-09-17 13:53:36', '2026-09-17 12:53:36', '1');

-- --------------------------------------------------------

--
-- Table structure for table `status_tab`
--

CREATE TABLE `status_tab` (
  `sn` int(11) NOT NULL,
  `status_id` varchar(255) NOT NULL,
  `status_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status_tab`
--

INSERT INTO `status_tab` (`sn`, `status_id`, `status_name`) VALUES
(1, '1', 'Active'),
(2, '2', 'Inactive'),
(3, 'A', 'Allocated'),
(4, 'U', 'Unallocated'),
(5, 'D', 'Available'),
(6, 'O', 'Occupied'),
(7, 'P', 'Paid'),
(8, 'PR', 'Partial'),
(9, 'N', 'NOT PAID');

-- --------------------------------------------------------

--
-- Table structure for table `student_tab`
--

CREATE TABLE `student_tab` (
  `sn` int(11) NOT NULL,
  `student_id` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `level` varchar(100) NOT NULL,
  `phone_number` varchar(100) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `status_id` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_tab`
--

INSERT INTO `student_tab` (`sn`, `student_id`, `first_name`, `last_name`, `gender`, `department`, `level`, `phone_number`, `email_address`, `password`, `status_id`, `created_at`, `updated_at`) VALUES
(1, 'STUDENT20260924025518', 'ayo', 'ayo', 'Male', 'computer science', 'hnd 1', '09050121142', 'ayo22@gmail.com', '52007e9771105b2ceb09208b6f66690e', 'u', '2026-09-24 12:55:18', '2026-09-24 13:09:47'),
(2, 'STUDENT20260924025831', 'posi', 'posi', 'Male', 'computer science', 'hnd 1', '09050121142', 'ayo2@gmail.com', '52007e9771105b2ceb09208b6f66690e', 'U', '2026-09-24 12:58:31', '2026-09-24 12:58:31');

-- --------------------------------------------------------

--
-- Table structure for table `transfers_tab`
--

CREATE TABLE `transfers_tab` (
  `transfer_id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `old_bed_id` varchar(50) NOT NULL,
  `old_room_id` varchar(50) NOT NULL,
  `old_hostel_id` varchar(20) NOT NULL,
  `new_bed_id` varchar(50) NOT NULL,
  `new_room_id` varchar(50) NOT NULL,
  `new_hostel_id` varchar(20) NOT NULL,
  `reason` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `allocation_tab`
--
ALTER TABLE `allocation_tab`
  ADD PRIMARY KEY (`allocation_id`);

--
-- Indexes for table `beds_tab`
--
ALTER TABLE `beds_tab`
  ADD PRIMARY KEY (`bed_id`);

--
-- Indexes for table `checkouts_tab`
--
ALTER TABLE `checkouts_tab`
  ADD PRIMARY KEY (`checkout_id`);

--
-- Indexes for table `department_tab`
--
ALTER TABLE `department_tab`
  ADD PRIMARY KEY (`sn`);

--
-- Indexes for table `fees_tab`
--
ALTER TABLE `fees_tab`
  ADD PRIMARY KEY (`fee_id`);

--
-- Indexes for table `hostels_tab`
--
ALTER TABLE `hostels_tab`
  ADD PRIMARY KEY (`hostel_id`);

--
-- Indexes for table `payments_tab`
--
ALTER TABLE `payments_tab`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `receipt_no` (`receipt_no`);

--
-- Indexes for table `role_tab`
--
ALTER TABLE `role_tab`
  ADD PRIMARY KEY (`sn`);

--
-- Indexes for table `rooms_tab`
--
ALTER TABLE `rooms_tab`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `fk_rooms_hostel` (`hostel_id`);

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
-- Indexes for table `student_tab`
--
ALTER TABLE `student_tab`
  ADD PRIMARY KEY (`sn`);

--
-- Indexes for table `transfers_tab`
--
ALTER TABLE `transfers_tab`
  ADD PRIMARY KEY (`transfer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `department_tab`
--
ALTER TABLE `department_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_tab`
--
ALTER TABLE `role_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `staff_tab`
--
ALTER TABLE `staff_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `status_tab`
--
ALTER TABLE `status_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `student_tab`
--
ALTER TABLE `student_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rooms_tab`
--
ALTER TABLE `rooms_tab`
  ADD CONSTRAINT `fk_rooms_hostel` FOREIGN KEY (`hostel_id`) REFERENCES `hostels_tab` (`hostel_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
