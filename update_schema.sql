-- Create a central users table if it doesn't exist (or migrate from admins)
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin', 'admin', 'faculty') NOT NULL DEFAULT 'faculty',
  `status` enum('active', 'inactive') NOT NULL DEFAULT 'active',
  `reference_id` int(11) DEFAULT NULL COMMENT 'ID from faculty table if role is faculty',
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Migrate existing admins to users table
-- Assuming 'sonu' (id 4) is a superadmin or admin.
INSERT INTO `users` (id, name, email, password, role, created_at, reset_token, reset_expires)
SELECT id, name, email, password, 'superadmin', created_at, reset_token, reset_expires FROM `admins`
ON DUPLICATE KEY UPDATE role='superadmin';

-- Add status column to content tables
ALTER TABLE `notes` ADD COLUMN `status` enum('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved';
ALTER TABLE `syllabus` ADD COLUMN `status` enum('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved';
ALTER TABLE `announcements` ADD COLUMN `status` enum('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved';

-- New table for unified faculty content (as requested in prompt requirement 8)
CREATE TABLE IF NOT EXISTS `faculty_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `type` enum('notes', 'syllabus', 'timetable') NOT NULL,
  `status` enum('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`faculty_id`) REFERENCES `faculty`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Activity Logs table
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text,
  `ip_address` varchar(45),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
