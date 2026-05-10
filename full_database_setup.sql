-- ==========================================
-- FULL DATABASE SETUP FOR MIT COLLEGE
-- Includes original tables + Multi-role Auth System
-- ==========================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 1. Original Table: admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Original Table: announcements
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `badge` varchar(50) DEFAULT 'info',
  `pdf` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `status` enum('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Original Table: faculty
CREATE TABLE IF NOT EXISTS `faculty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `education` text NOT NULL,
  `experience` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `faculty_type` enum('teaching','non-teaching') NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Original Table: notes
CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(100) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `status` enum('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Original Table: syllabus
CREATE TABLE IF NOT EXISTS `syllabus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(255) NOT NULL,
  `uploaded_by` varchar(255) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `syllabus_file` varchar(255) NOT NULL,
  `file_size` varchar(50) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0,
  `status` enum('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Original Table: events
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `event_images` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- NEW ROLE-BASED AUTH SYSTEM TABLES
-- --------------------------------------------------------

-- 7. Central Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin', 'admin', 'faculty') NOT NULL DEFAULT 'faculty',
  `status` enum('active', 'inactive', 'pending', 'rejected') NOT NULL DEFAULT 'pending',
  `reference_id` int(11) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Faculty Content Table (for uploads)
CREATE TABLE IF NOT EXISTS `faculty_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `type` enum('notes', 'syllabus', 'timetable', 'circulars') NOT NULL,
  `status` enum('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Activity Logs
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text,
  `ip_address` varchar(45),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- INITIAL DATA
-- --------------------------------------------------------


-- 10. NAAC Documents Table
CREATE TABLE IF NOT EXISTS `naac_docs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category` enum('AQAR', 'SSR', 'Minutes', 'Policy', 'Disclosure', 'Certificate') NOT NULL,
  `year` varchar(20) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` enum('Active', 'Archived') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Committees Table
CREATE TABLE IF NOT EXISTS `committees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Committee Members
CREATE TABLE IF NOT EXISTS `committee_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `committee_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `committee_id` (`committee_id`),
  CONSTRAINT `committee_members_ibfk_1` FOREIGN KEY (`committee_id`) REFERENCES `committees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. Feedback Table
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` enum('Student', 'Parent', 'Faculty', 'Employer', 'Alumni') DEFAULT NULL,
  `category` enum('Curriculum', 'Infrastructure', 'Support', 'Overall') DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 14. Placements Table
CREATE TABLE IF NOT EXISTS `placements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_name` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `package` decimal(10,2) DEFAULT NULL,
  `year` varchar(20) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Initial Committees
INSERT IGNORE INTO `committees` (`name`, `description`, `icon`, `color`) VALUES 
('Anti-Ragging Committee', 'Ensuring a zero-tolerance campus environment.', 'fa-user-shield', 'amber'),
('Internal Complaint Committee', 'Cell for prevention of sexual harassment.', 'fa-venus', 'rose'),
('Grievance Redressal Cell', 'Platform for students and staff to raise concerns.', 'fa-hands-helping', 'blue'),
('Women Development Cell', 'Promoting gender equality and empowerment.', 'fa-female', 'purple'),
('SC/ST Cell', 'Protecting the interests of reserved category students.', 'fa-users', 'emerald'),
('IQAC Cell', 'Internal Quality Assurance Cell for systematic institutional evaluation.', 'fa-microchip', 'blue');

COMMIT;
