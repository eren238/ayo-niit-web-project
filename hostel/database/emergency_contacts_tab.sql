-- --------------------------------------------------------
-- Table structure for table `emergency_contacts_tab`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `emergency_contacts_tab` (
  `contact_id` varchar(50) NOT NULL,
  `student_id` varchar(100) NOT NULL,
  `nok_name` varchar(150) NOT NULL,
  `relationship` varchar(100) NOT NULL,
  `phone_number` varchar(50) NOT NULL,
  `residential_address` text NOT NULL,
  `status_id` varchar(50) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`contact_id`),
  KEY `idx_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample record (Optional)
INSERT INTO `emergency_contacts_tab` (`contact_id`, `student_id`, `nok_name`, `relationship`, `phone_number`, `residential_address`, `status_id`, `created_at`, `updated_at`) VALUES
('EMG20260924000001', 'STUDENT20260924025518', 'Mr. Michael Posikay', 'Father', '08031234567', '14, University Road, Lagos, Nigeria', '1', NOW(), NOW())
ON DUPLICATE KEY UPDATE `nok_name` = VALUES(`nok_name`);
