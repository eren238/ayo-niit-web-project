-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2026 at 05:20 PM
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
-- Table structure for table `complaints_tab`
--

CREATE TABLE `complaints_tab` (
  `complaint_id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` varchar(50) NOT NULL,
  `complaint_date` date NOT NULL,
  `status_id` varchar(50) NOT NULL DEFAULT 'PN',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  `sn` int(11) NOT NULL,
  `hostel_id` varchar(50) NOT NULL,
  `hostel_name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `gender` enum('Male','Female','Mixed') NOT NULL,
  `hostel_capacity` int(11) NOT NULL DEFAULT 0,
  `status_id` varchar(5) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_tab`
--

CREATE TABLE `maintenance_tab` (
  `maintenance_id` varchar(50) NOT NULL,
  `room_id` varchar(50) NOT NULL,
  `issue` varchar(255) NOT NULL,
  `priority` varchar(20) NOT NULL,
  `reported_date` date NOT NULL,
  `status_id` varchar(50) NOT NULL DEFAULT 'PN',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `room_bookings_tab`
--

CREATE TABLE `room_bookings_tab` (
  `sn` int(11) NOT NULL,
  `booking_id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `hostel_name` varchar(150) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `booking_date` date NOT NULL,
  `status_id` varchar(50) NOT NULL DEFAULT 'BK',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_bookings_tab`
--

INSERT INTO `room_bookings_tab` (`sn`, `booking_id`, `student_id`, `hostel_name`, `room_number`, `booking_date`, `status_id`, `created_at`, `updated_at`) VALUES
(1, 'BK20261007014211', 'STUDENT20261007124455', 'bells', '2', '2026-10-07', 'BK', '2026-10-07 12:42:11', '2026-10-07 12:42:11');

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
  `password` varchar(255) DEFAULT NULL,
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
(1, 'STAFF20261012022850', 'dayo', 'posi', 'ayo22@gmail.com', '5e0bdcbddccca4d66d74ba8c1cee1a68', '1', NULL, NULL, '2026-10-12 13:28:50', '2026-10-12 12:36:29', '1'),
(4, 'STAFF20261012053152', 'john', 'sam', 'sam2@gmail.com', '', '1', NULL, NULL, '2026-10-12 16:31:52', '2026-10-12 15:31:52', '1'),
(5, 'STAFF20261014040253', 'oola', 'hi', 'hi@gmail.com', NULL, '56', NULL, NULL, '2026-10-14 15:02:53', '2026-10-14 14:02:53', '1');

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
(9, 'N', 'NOT PAID'),
(10, 'IP', 'In Progress'),
(11, 'AS', 'Assigned'),
(12, 'RS', 'Resolved'),
(13, 'PN', 'Pending'),
(14, 'IN', 'Inside'),
(15, 'OUT', 'Out'),
(16, 'BK', 'Booked'),
(17, 'R', 'Reserved');

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
  `password` varchar(100) DEFAULT NULL,
  `reset_otp` varchar(6) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `status_id` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_tab`
--

INSERT INTO `student_tab` (`sn`, `student_id`, `first_name`, `last_name`, `gender`, `department`, `level`, `phone_number`, `email_address`, `password`, `reset_otp`, `last_login`, `status_id`, `created_at`, `updated_at`) VALUES
(2, 'STUDENT20261014011022', 'joseph', 'joseph', 'Female', 'computer science', 'hnd 2', '09050121142', 'joseph2@gmail.com', '0e2c100bf8a9cdea652040786eb7ac10', NULL, '2026-10-14 12:32:29', 'U', '2026-10-14 11:10:22', '2026-10-14 11:32:29'),
(3, 'STUDENT20261014013412', 'ayo', 'ayo', 'Male', 'science', '100', '09050121142', 'ayo1@gmail.com', '19351f0df296e6e73c827a2fff8dbed1', NULL, NULL, '1', '2026-10-14 11:34:12', '2026-10-14 11:34:12');

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

-- --------------------------------------------------------

--
-- Table structure for table `visitors_tab`
--

CREATE TABLE `visitors_tab` (
  `sn` int(11) NOT NULL,
  `visitor_id` varchar(50) NOT NULL,
  `visitor_name` varchar(150) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `visit_date` date DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `status_id` varchar(50) NOT NULL DEFAULT 'IN',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitors_tab`
--

INSERT INTO `visitors_tab` (`sn`, `visitor_id`, `visitor_name`, `student_id`, `purpose`, `visit_date`, `arrival_time`, `departure_time`, `status_id`, `created_at`, `updated_at`) VALUES
(9, 'VIS20261015150154', 'ayo', 'STUDENT20261014013412', 'visitation', '2026-10-22', '22:00:00', '00:20:26', 'OUT', '2026-10-15 14:01:54', '2026-10-15 14:08:03'),
(10, 'VIS20261015150300', 'ayo', 'STUDENT20261014013412', 'visitation', '2026-10-22', '22:00:00', NULL, 'IN', '2026-10-15 14:03:00', '2026-10-15 14:03:00');

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
-- Indexes for table `complaints_tab`
--
ALTER TABLE `complaints_tab`
  ADD PRIMARY KEY (`complaint_id`);

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
  ADD PRIMARY KEY (`sn`),
  ADD UNIQUE KEY `hostel_id` (`hostel_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `maintenance_tab`
--
ALTER TABLE `maintenance_tab`
  ADD PRIMARY KEY (`maintenance_id`);

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
-- Indexes for table `room_bookings_tab`
--
ALTER TABLE `room_bookings_tab`
  ADD PRIMARY KEY (`sn`),
  ADD UNIQUE KEY `booking_id` (`booking_id`);

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
-- Indexes for table `visitors_tab`
--
ALTER TABLE `visitors_tab`
  ADD PRIMARY KEY (`sn`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `department_tab`
--
ALTER TABLE `department_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hostels_tab`
--
ALTER TABLE `hostels_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `role_tab`
--
ALTER TABLE `role_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `room_bookings_tab`
--
ALTER TABLE `room_bookings_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `staff_tab`
--
ALTER TABLE `staff_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `status_tab`
--
ALTER TABLE `status_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `student_tab`
--
ALTER TABLE `student_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `visitors_tab`
--
ALTER TABLE `visitors_tab`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
