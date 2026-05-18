-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 18, 2026 at 06:08 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apcampushub`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `identical_number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `gender` enum('male','female','','') NOT NULL,
  `profile_photo` varchar(255) NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`admin_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `identical_number`, `email`, `phone_number`, `gender`, `profile_photo`, `user_id`) VALUES
(1, 'Admin 1', '010203-10-1234', 'Tp000002@mail.apu.edu.my', '012-345 6789', 'male', 'profile3.png', 3);

-- --------------------------------------------------------

--
-- Table structure for table `event_resource_request`
--

DROP TABLE IF EXISTS `event_resource_request`;
CREATE TABLE IF NOT EXISTS `event_resource_request` (
  `request_id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `user_id` int NOT NULL,
  `venue_id` int NOT NULL,
  `resource_type` enum('Cables','Extension Plug','Stationary','Projector','Microphone','Laptop','Tables Chairs') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` text NOT NULL,
  `status` enum('Pending','In Progress','Done','') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`),
  KEY `booking_id` (`booking_id`),
  KEY `user_id` (`user_id`),
  KEY `venue_id` (`venue_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `event_resource_request`
--

INSERT INTO `event_resource_request` (`request_id`, `booking_id`, `user_id`, `venue_id`, `resource_type`, `description`, `status`, `created_at`) VALUES
(1, 2, 1, 1, 'Tables Chairs', '30 Tables & 60 Chairs', 'Pending', '2026-05-17 08:26:54'),
(2, 2, 1, 1, 'Extension Plug', '30 Extension Plug', 'In Progress', '2026-05-17 08:26:54'),
(3, 2, 1, 1, 'Microphone', '2 Microphones', 'Done', '2026-05-17 08:26:54');

-- --------------------------------------------------------

--
-- Table structure for table `event_venue`
--

DROP TABLE IF EXISTS `event_venue`;
CREATE TABLE IF NOT EXISTS `event_venue` (
  `venue_id` int NOT NULL AUTO_INCREMENT,
  `venue_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `cover_image` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','','') NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`venue_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `event_venue`
--

INSERT INTO `event_venue` (`venue_id`, `venue_name`, `description`, `cover_image`, `status`) VALUES
(1, 'Main Atrium', 'Large main atrium located in Level 3 with a big stage, suitable for any big events.', 'venue1.jpg', 'Active'),
(2, 'Basketball Court', 'A basketball court which is suitable to held a basketball training or competition.', 'venue4.jpg', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer`
--

DROP TABLE IF EXISTS `lecturer`;
CREATE TABLE IF NOT EXISTS `lecturer` (
  `lecturer_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `identical_number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `gender` enum('male','female','','') NOT NULL,
  `profile_photo` varchar(255) NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`lecturer_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lecturer`
--

INSERT INTO `lecturer` (`lecturer_id`, `name`, `identical_number`, `email`, `phone_number`, `gender`, `profile_photo`, `user_id`) VALUES
(1, 'Lecturer 1', '010203-10-1234', 'Tp000001@mail.apu.edu.my', '012-345 6789', 'male', 'profile2.png', 2);

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `notification_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`notification_id`, `user_id`, `title`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'Welcome', 'Welcome to APCampusHub', 1, '2026-05-17 13:05:59'),
(2, 1, 'Booking Room Successful', 'Your booking for B-06-05 is successful, please check in to the room in 30 minutes or else your booking will be auto canceled by the system.', 1, '2026-05-17 13:38:14'),
(3, 1, 'Room Booking Auto Canceled', 'Your booking for Auditorium 1 was automatically canceled because you did not check in within 30 minutes after the booking start time.', 1, '2026-05-18 04:55:57'),
(4, 1, 'Booking Room Successful', 'Your booking for Auditorium 1 is successful, please check in to the room in 30 minutes or else your booking will be auto canceled by the system.', 1, '2026-05-18 04:56:31');

-- --------------------------------------------------------

--
-- Table structure for table `resource`
--

DROP TABLE IF EXISTS `resource`;
CREATE TABLE IF NOT EXISTS `resource` (
  `resource_id` int NOT NULL AUTO_INCREMENT,
  `resource_name` varchar(255) NOT NULL,
  `resource_type` enum('Cables','Extension Plug','Stationary','') NOT NULL,
  `quantity` int NOT NULL,
  `description` text NOT NULL,
  `status` enum('Available','Disabled','','') NOT NULL DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`resource_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resource`
--

INSERT INTO `resource` (`resource_id`, `resource_name`, `resource_type`, `quantity`, `description`, `status`, `created_at`) VALUES
(1, 'HDMI Cables', 'Cables', 350, 'HDMI cable allows you to connect your laptop to our projectors.', 'Available', '2026-05-16 12:56:51'),
(2, '3-Port Extension Plug', 'Extension Plug', 200, '3 port extension plug provide three ports which support three devices to use in the same time.', 'Available', '2026-05-16 12:58:16');

-- --------------------------------------------------------

--
-- Table structure for table `resource_borrow`
--

DROP TABLE IF EXISTS `resource_borrow`;
CREATE TABLE IF NOT EXISTS `resource_borrow` (
  `borrow_id` int NOT NULL AUTO_INCREMENT,
  `resource_id` int NOT NULL,
  `user_id` int NOT NULL,
  `quantity` int NOT NULL,
  `borrow_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `return_time` timestamp NULL DEFAULT NULL,
  `status` enum('Borrowed','Returned','','') NOT NULL DEFAULT 'Borrowed',
  PRIMARY KEY (`borrow_id`),
  KEY `resource_id` (`resource_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resource_borrow`
--

INSERT INTO `resource_borrow` (`borrow_id`, `resource_id`, `user_id`, `quantity`, `borrow_time`, `return_time`, `status`) VALUES
(1, 1, 1, 1, '2026-05-16 13:23:11', '2026-05-16 13:42:31', 'Returned'),
(2, 1, 1, 1, '2026-05-16 14:03:22', '2026-05-17 05:10:12', 'Returned');

-- --------------------------------------------------------

--
-- Table structure for table `resource_usage_log`
--

DROP TABLE IF EXISTS `resource_usage_log`;
CREATE TABLE IF NOT EXISTS `resource_usage_log` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `borrow_id` int NOT NULL,
  `action` enum('Borrow','Return','','') NOT NULL,
  `action_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `borrow_id` (`borrow_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resource_usage_log`
--

INSERT INTO `resource_usage_log` (`log_id`, `borrow_id`, `action`, `action_time`) VALUES
(1, 1, 'Borrow', '2026-05-16 13:23:11'),
(2, 1, 'Return', '2026-05-16 13:42:31'),
(3, 2, 'Borrow', '2026-05-16 14:03:22'),
(4, 2, 'Return', '2026-05-17 05:10:12');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

DROP TABLE IF EXISTS `room`;
CREATE TABLE IF NOT EXISTS `room` (
  `room_id` int NOT NULL AUTO_INCREMENT,
  `room_name` varchar(255) NOT NULL,
  `room_type` enum('Classroom','Discussion Room','Auditorium','Presentation Room','Lab') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `block` varchar(10) NOT NULL,
  `level` varchar(10) NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `capacity` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `cover_image` varchar(255) NOT NULL,
  `status` enum('Active','Maintenance','Inactive','') NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`room_id`, `room_name`, `room_type`, `block`, `level`, `room_number`, `capacity`, `description`, `cover_image`, `status`) VALUES
(1, 'Auditorium 1', 'Auditorium', 'Spine', '7', 'Auditorium 1', '200 - 350', 'The largest auditorium in APU.', 'room1.jpg', 'Active'),
(2, 'B-06-05', 'Classroom', 'B', '6', '05', '40 - 60', 'Medium classroom which is basically used for tutorial class.', 'room2.png', 'Active'),
(3, 'Tech Lab 6-09', 'Lab', 'Spine', '6', '09', '30 - 50', 'Room equip with computers for student to use during lab session.', 'room3.jpg', 'Active'),
(4, 'Discussion Room 3', 'Discussion Room', 'Library', '4', '3', '4 - 5', 'A place that allows students to make discussion or assignments together.', 'room4.jpg', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `room_booking`
--

DROP TABLE IF EXISTS `room_booking`;
CREATE TABLE IF NOT EXISTS `room_booking` (
  `booking_id` int NOT NULL AUTO_INCREMENT,
  `room_id` int NOT NULL,
  `user_id` int NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `booking_status` enum('Approved','Canceled','Completed','') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Approved',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`booking_id`),
  KEY `room_id` (`room_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `room_booking`
--

INSERT INTO `room_booking` (`booking_id`, `room_id`, `user_id`, `booking_date`, `start_time`, `end_time`, `booking_status`, `created_at`) VALUES
(1, 1, 1, '2026-05-13', '20:00:00', '21:00:00', 'Approved', '2026-05-12 13:49:17'),
(2, 1, 1, '2026-05-13', '08:00:00', '11:30:00', 'Completed', '2026-05-12 13:49:58'),
(3, 1, 1, '2026-05-13', '12:50:00', '15:30:00', 'Canceled', '2026-05-12 13:50:30'),
(4, 4, 1, '2026-05-14', '20:56:00', '20:58:00', 'Completed', '2026-05-13 10:59:40'),
(5, 2, 1, '2026-05-14', '15:30:00', '17:00:00', 'Canceled', '2026-05-13 11:06:47'),
(6, 4, 1, '2026-05-14', '15:50:00', '16:50:00', 'Approved', '2026-05-14 05:48:47'),
(7, 4, 1, '2026-05-14', '21:39:00', '22:00:00', 'Completed', '2026-05-14 12:59:40'),
(8, 4, 1, '2026-05-15', '11:00:00', '11:30:00', 'Completed', '2026-05-15 02:17:12'),
(9, 4, 1, '2026-05-15', '11:11:00', '22:00:00', 'Completed', '2026-05-15 03:11:52'),
(10, 1, 1, '2026-05-16', '15:48:00', '22:00:00', 'Completed', '2026-05-16 07:48:12'),
(11, 1, 1, '2026-05-18', '10:38:00', '11:00:00', 'Canceled', '2026-05-17 13:38:14'),
(12, 1, 1, '2026-05-18', '12:56:00', '13:00:00', 'Approved', '2026-05-18 04:56:31');

-- --------------------------------------------------------

--
-- Table structure for table `room_checkin`
--

DROP TABLE IF EXISTS `room_checkin`;
CREATE TABLE IF NOT EXISTS `room_checkin` (
  `checkin_id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `actual_checkin` datetime NOT NULL,
  `actual_checkout` datetime DEFAULT NULL,
  `occupancy_status` enum('In Use','Check Out','','') NOT NULL DEFAULT 'In Use',
  PRIMARY KEY (`checkin_id`),
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `room_checkin`
--

INSERT INTO `room_checkin` (`checkin_id`, `booking_id`, `actual_checkin`, `actual_checkout`, `occupancy_status`) VALUES
(1, 2, '2026-05-13 08:00:00', '2026-05-13 11:30:00', 'Check Out'),
(2, 4, '2026-05-14 20:57:19', '2026-05-14 20:58:00', 'Check Out'),
(6, 7, '2026-05-14 21:42:23', '2026-05-14 22:00:01', 'Check Out'),
(9, 8, '2026-05-15 11:04:00', '2026-05-15 11:04:03', 'Check Out'),
(10, 9, '2026-05-15 11:12:14', '2026-05-15 20:18:51', 'Check Out'),
(11, 10, '2026-05-16 15:50:10', '2026-05-17 15:45:54', 'Check Out');

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

DROP TABLE IF EXISTS `room_images`;
CREATE TABLE IF NOT EXISTS `room_images` (
  `image_id` int NOT NULL AUTO_INCREMENT,
  `room_id` int NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`image_id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `room_images`
--

INSERT INTO `room_images` (`image_id`, `room_id`, `image_name`, `created_at`) VALUES
(1, 1, 'room5.jpg', '2026-05-12 13:35:40'),
(2, 1, 'room6.jpg', '2026-05-12 13:47:50');

-- --------------------------------------------------------

--
-- Table structure for table `room_iot_state`
--

DROP TABLE IF EXISTS `room_iot_state`;
CREATE TABLE IF NOT EXISTS `room_iot_state` (
  `room_iot_id` int NOT NULL AUTO_INCREMENT,
  `projector` enum('ON','OFF','','') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'ON',
  `lights_brightness` int NOT NULL DEFAULT '100',
  `ac_temperature` int NOT NULL DEFAULT '24',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `room_id` int NOT NULL,
  PRIMARY KEY (`room_iot_id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `room_iot_state`
--

INSERT INTO `room_iot_state` (`room_iot_id`, `projector`, `lights_brightness`, `ac_temperature`, `updated_at`, `room_id`) VALUES
(1, 'ON', 100, 24, '2026-05-14 07:49:35', 1),
(2, 'ON', 100, 24, '2026-05-14 07:49:35', 2),
(3, 'ON', 100, 24, '2026-05-14 07:49:35', 3),
(4, 'ON', 100, 24, '2026-05-14 07:49:35', 4);

-- --------------------------------------------------------

--
-- Table structure for table `room_issue_report`
--

DROP TABLE IF EXISTS `room_issue_report`;
CREATE TABLE IF NOT EXISTS `room_issue_report` (
  `report_id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `user_id` int NOT NULL,
  `room_id` int NOT NULL,
  `issue_type` enum('Projector Issue','Lighting Problem','Air Conditioner Issue','Power/Electricity Issue','Furniture Damage','Cleanliness Issue','Internet/WiFi Issue') NOT NULL,
  `description` text NOT NULL,
  `severity` enum('Low','Medium','High','') NOT NULL DEFAULT 'Low',
  `status` enum('Pending','In Progress','Resolved','') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`report_id`),
  KEY `booking_id` (`booking_id`),
  KEY `room_id` (`room_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `room_issue_report`
--

INSERT INTO `room_issue_report` (`report_id`, `booking_id`, `user_id`, `room_id`, `issue_type`, `description`, `severity`, `status`, `created_at`) VALUES
(4, 10, 1, 1, 'Power/Electricity Issue', 'Out of electricity!', 'High', 'Resolved', '2026-05-16 11:02:57'),
(5, 10, 1, 1, 'Cleanliness Issue', 'Someone vomit here.', 'Low', 'Resolved', '2026-05-16 11:03:25');

-- --------------------------------------------------------

--
-- Table structure for table `room_otp`
--

DROP TABLE IF EXISTS `room_otp`;
CREATE TABLE IF NOT EXISTS `room_otp` (
  `otp_id` int NOT NULL AUTO_INCREMENT,
  `room_id` int NOT NULL,
  `otp_code` varchar(3) NOT NULL,
  `generated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`otp_id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=343 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `room_otp`
--

INSERT INTO `room_otp` (`otp_id`, `room_id`, `otp_code`, `generated_at`) VALUES
(1, 4, '844', '2026-05-14 14:42:32'),
(2, 4, '988', '2026-05-14 14:43:02'),
(3, 4, '621', '2026-05-14 14:43:32'),
(4, 4, '550', '2026-05-14 14:44:17'),
(5, 4, '883', '2026-05-14 14:44:47'),
(6, 4, '168', '2026-05-14 14:45:17'),
(7, 4, '480', '2026-05-14 14:46:17'),
(8, 4, '540', '2026-05-14 15:50:38'),
(9, 4, '996', '2026-05-14 15:59:29'),
(10, 4, '544', '2026-05-14 16:01:28'),
(11, 4, '123', '2026-05-14 16:10:32'),
(12, 4, '994', '2026-05-14 19:04:12'),
(13, 4, '335', '2026-05-14 19:53:54'),
(14, 4, '837', '2026-05-14 19:54:24'),
(15, 4, '108', '2026-05-14 19:54:55'),
(16, 4, '063', '2026-05-14 19:55:52'),
(17, 4, '976', '2026-05-14 19:56:52'),
(18, 4, '964', '2026-05-14 19:57:47'),
(19, 4, '334', '2026-05-14 19:58:19'),
(20, 4, '085', '2026-05-14 19:59:17'),
(21, 4, '340', '2026-05-14 20:00:04'),
(22, 4, '125', '2026-05-14 20:00:55'),
(23, 4, '897', '2026-05-14 20:01:26'),
(24, 4, '046', '2026-05-14 20:02:26'),
(25, 4, '203', '2026-05-14 20:02:58'),
(26, 4, '953', '2026-05-14 20:03:55'),
(27, 4, '709', '2026-05-14 20:04:55'),
(28, 4, '167', '2026-05-14 20:05:54'),
(29, 4, '229', '2026-05-14 20:06:50'),
(30, 4, '137', '2026-05-14 20:07:50'),
(31, 4, '179', '2026-05-14 20:08:29'),
(32, 4, '700', '2026-05-14 20:09:29'),
(33, 4, '084', '2026-05-14 20:10:29'),
(34, 4, '556', '2026-05-14 20:11:28'),
(35, 4, '058', '2026-05-14 20:12:18'),
(36, 4, '509', '2026-05-14 20:13:18'),
(37, 4, '209', '2026-05-14 20:14:18'),
(38, 4, '389', '2026-05-14 20:15:18'),
(39, 4, '371', '2026-05-14 20:16:18'),
(40, 4, '612', '2026-05-14 20:17:18'),
(41, 4, '750', '2026-05-14 20:18:18'),
(42, 4, '921', '2026-05-14 20:19:13'),
(43, 4, '396', '2026-05-14 20:20:13'),
(44, 4, '574', '2026-05-14 20:21:11'),
(45, 4, '240', '2026-05-14 20:22:11'),
(46, 4, '270', '2026-05-14 20:23:11'),
(47, 4, '391', '2026-05-14 20:23:55'),
(48, 4, '390', '2026-05-14 20:24:54'),
(49, 4, '735', '2026-05-14 20:25:54'),
(50, 4, '657', '2026-05-14 20:26:54'),
(51, 4, '340', '2026-05-14 20:27:41'),
(52, 4, '389', '2026-05-14 20:28:41'),
(53, 4, '344', '2026-05-14 20:29:41'),
(54, 4, '076', '2026-05-14 20:38:50'),
(55, 4, '507', '2026-05-14 20:44:29'),
(56, 4, '957', '2026-05-14 20:57:15'),
(57, 4, '117', '2026-05-14 21:04:09'),
(58, 4, '821', '2026-05-14 21:18:00'),
(59, 4, '865', '2026-05-14 21:18:31'),
(60, 4, '624', '2026-05-14 21:19:31'),
(61, 4, '463', '2026-05-14 21:20:31'),
(62, 4, '258', '2026-05-14 21:21:31'),
(63, 4, '007', '2026-05-14 21:22:22'),
(64, 4, '618', '2026-05-14 21:23:22'),
(65, 4, '356', '2026-05-14 21:23:53'),
(66, 4, '326', '2026-05-14 21:24:53'),
(67, 4, '965', '2026-05-14 21:25:53'),
(68, 4, '748', '2026-05-14 21:26:53'),
(69, 4, '337', '2026-05-14 21:27:46'),
(70, 4, '756', '2026-05-14 21:28:46'),
(71, 4, '154', '2026-05-14 21:29:46'),
(72, 4, '241', '2026-05-14 21:30:46'),
(73, 4, '571', '2026-05-14 21:31:46'),
(74, 4, '834', '2026-05-14 21:32:46'),
(75, 4, '898', '2026-05-14 21:33:45'),
(76, 4, '187', '2026-05-14 21:34:23'),
(77, 4, '164', '2026-05-14 21:34:54'),
(78, 4, '453', '2026-05-14 21:35:25'),
(79, 4, '166', '2026-05-14 21:36:09'),
(80, 4, '132', '2026-05-14 21:37:09'),
(81, 4, '081', '2026-05-14 21:38:09'),
(82, 4, '294', '2026-05-14 21:39:09'),
(83, 4, '030', '2026-05-14 21:39:39'),
(84, 4, '169', '2026-05-14 21:42:19'),
(85, 4, '351', '2026-05-15 10:12:17'),
(86, 4, '197', '2026-05-15 10:12:48'),
(87, 4, '117', '2026-05-15 10:13:39'),
(88, 4, '644', '2026-05-15 10:14:10'),
(89, 4, '981', '2026-05-15 10:15:09'),
(90, 4, '651', '2026-05-15 10:15:45'),
(91, 4, '159', '2026-05-15 10:16:28'),
(92, 4, '656', '2026-05-15 10:17:07'),
(93, 4, '377', '2026-05-15 10:36:43'),
(94, 4, '524', '2026-05-15 11:03:55'),
(95, 4, '730', '2026-05-15 11:04:25'),
(96, 4, '749', '2026-05-15 11:05:25'),
(97, 4, '917', '2026-05-15 11:06:25'),
(98, 4, '504', '2026-05-15 11:07:25'),
(99, 4, '510', '2026-05-15 11:08:25'),
(100, 4, '823', '2026-05-15 11:09:25'),
(101, 4, '753', '2026-05-15 11:10:18'),
(102, 4, '795', '2026-05-15 11:11:04'),
(103, 4, '682', '2026-05-15 11:12:01'),
(104, 4, '759', '2026-05-15 11:12:59'),
(105, 4, '195', '2026-05-15 11:13:30'),
(106, 4, '443', '2026-05-15 11:14:30'),
(107, 4, '263', '2026-05-15 11:15:30'),
(108, 4, '417', '2026-05-15 11:16:30'),
(109, 4, '936', '2026-05-15 11:17:30'),
(110, 4, '443', '2026-05-15 11:18:30'),
(111, 4, '325', '2026-05-15 11:19:30'),
(112, 4, '091', '2026-05-15 11:20:30'),
(113, 4, '331', '2026-05-15 11:21:26'),
(114, 4, '902', '2026-05-15 11:22:22'),
(115, 4, '370', '2026-05-15 11:23:22'),
(116, 4, '950', '2026-05-15 11:24:17'),
(117, 4, '952', '2026-05-15 11:25:17'),
(118, 4, '565', '2026-05-15 11:25:48'),
(119, 4, '126', '2026-05-15 11:26:48'),
(120, 4, '124', '2026-05-15 11:27:44'),
(121, 4, '157', '2026-05-15 11:28:44'),
(122, 4, '377', '2026-05-15 11:29:15'),
(123, 4, '557', '2026-05-15 13:09:46'),
(124, 1, '124', '2026-05-16 15:50:04'),
(125, 1, '934', '2026-05-16 18:56:32'),
(126, 1, '417', '2026-05-16 18:57:03'),
(127, 1, '334', '2026-05-16 18:57:49'),
(128, 1, '631', '2026-05-16 18:58:49'),
(129, 1, '318', '2026-05-16 18:59:40'),
(130, 1, '475', '2026-05-16 19:00:29'),
(131, 1, '150', '2026-05-16 19:01:29'),
(132, 1, '397', '2026-05-17 13:07:01'),
(133, 1, '005', '2026-05-17 13:07:32'),
(134, 1, '304', '2026-05-17 13:08:32'),
(135, 1, '082', '2026-05-17 13:09:32'),
(136, 1, '079', '2026-05-17 13:10:17'),
(137, 1, '152', '2026-05-17 13:11:17'),
(138, 1, '427', '2026-05-17 13:12:17'),
(139, 1, '260', '2026-05-17 13:13:17'),
(140, 1, '611', '2026-05-17 13:14:17'),
(141, 1, '003', '2026-05-17 13:14:55'),
(142, 1, '575', '2026-05-17 13:15:53'),
(143, 1, '110', '2026-05-17 13:16:53'),
(144, 1, '624', '2026-05-17 13:17:40'),
(145, 1, '162', '2026-05-17 13:18:11'),
(146, 1, '996', '2026-05-17 13:19:11'),
(147, 1, '826', '2026-05-17 13:20:11'),
(148, 1, '724', '2026-05-17 13:21:11'),
(149, 1, '790', '2026-05-17 13:22:11'),
(150, 1, '742', '2026-05-17 13:23:11'),
(151, 1, '256', '2026-05-17 13:24:11'),
(152, 1, '268', '2026-05-17 13:25:11'),
(153, 1, '846', '2026-05-17 13:26:04'),
(154, 1, '060', '2026-05-17 13:26:58'),
(155, 1, '692', '2026-05-17 13:27:30'),
(156, 1, '712', '2026-05-17 13:28:30'),
(157, 1, '361', '2026-05-17 13:29:30'),
(158, 1, '597', '2026-05-17 13:30:11'),
(159, 1, '907', '2026-05-17 13:31:11'),
(160, 1, '035', '2026-05-17 13:32:11'),
(161, 1, '419', '2026-05-17 13:33:06'),
(162, 1, '841', '2026-05-17 13:34:06'),
(163, 1, '569', '2026-05-17 13:35:06'),
(164, 1, '874', '2026-05-17 13:35:47'),
(165, 1, '678', '2026-05-17 13:36:47'),
(166, 1, '151', '2026-05-17 13:37:45'),
(167, 1, '995', '2026-05-17 13:38:43'),
(168, 1, '493', '2026-05-17 13:39:43'),
(169, 1, '330', '2026-05-17 13:40:43'),
(170, 1, '851', '2026-05-17 13:41:43'),
(171, 1, '484', '2026-05-17 13:42:43'),
(172, 1, '393', '2026-05-17 13:43:43'),
(173, 1, '365', '2026-05-17 13:44:38'),
(174, 1, '015', '2026-05-17 13:45:24'),
(175, 1, '951', '2026-05-17 13:46:24'),
(176, 1, '262', '2026-05-17 13:47:24'),
(177, 1, '180', '2026-05-17 13:48:24'),
(178, 1, '329', '2026-05-17 13:49:24'),
(179, 1, '016', '2026-05-17 13:50:24'),
(180, 1, '946', '2026-05-17 13:51:24'),
(181, 1, '365', '2026-05-17 13:52:14'),
(182, 1, '817', '2026-05-17 13:53:14'),
(183, 1, '541', '2026-05-17 13:54:14'),
(184, 1, '704', '2026-05-17 13:55:14'),
(185, 1, '270', '2026-05-17 13:56:14'),
(186, 1, '792', '2026-05-17 13:57:02'),
(187, 1, '417', '2026-05-17 13:58:02'),
(188, 1, '063', '2026-05-17 13:59:02'),
(189, 1, '722', '2026-05-17 14:00:02'),
(190, 1, '794', '2026-05-17 14:01:02'),
(191, 1, '170', '2026-05-17 14:01:54'),
(192, 1, '336', '2026-05-17 14:02:54'),
(193, 1, '805', '2026-05-17 14:03:54'),
(194, 1, '975', '2026-05-17 14:04:54'),
(195, 1, '249', '2026-05-17 14:05:54'),
(196, 1, '767', '2026-05-17 14:06:50'),
(197, 1, '444', '2026-05-17 14:07:50'),
(198, 1, '322', '2026-05-17 14:08:50'),
(199, 1, '263', '2026-05-17 14:09:50'),
(200, 1, '500', '2026-05-17 14:10:50'),
(201, 1, '531', '2026-05-17 14:11:39'),
(202, 1, '672', '2026-05-17 14:12:39'),
(203, 1, '685', '2026-05-17 14:13:39'),
(204, 1, '173', '2026-05-17 14:14:38'),
(205, 1, '240', '2026-05-17 14:15:38'),
(206, 1, '694', '2026-05-17 14:16:39'),
(207, 1, '435', '2026-05-17 14:17:39'),
(208, 1, '172', '2026-05-17 14:18:39'),
(209, 1, '509', '2026-05-17 14:19:37'),
(210, 1, '763', '2026-05-17 14:20:31'),
(211, 1, '004', '2026-05-17 14:21:31'),
(212, 1, '964', '2026-05-17 14:22:32'),
(213, 1, '404', '2026-05-17 14:23:32'),
(214, 1, '205', '2026-05-17 14:24:29'),
(215, 1, '358', '2026-05-17 14:25:30'),
(216, 1, '359', '2026-05-17 14:26:29'),
(217, 1, '341', '2026-05-17 14:27:29'),
(218, 1, '910', '2026-05-17 14:28:29'),
(219, 1, '047', '2026-05-17 14:29:29'),
(220, 1, '579', '2026-05-17 14:30:29'),
(221, 1, '788', '2026-05-17 14:31:29'),
(222, 1, '490', '2026-05-17 14:32:29'),
(223, 1, '837', '2026-05-17 14:33:29'),
(224, 1, '631', '2026-05-17 14:34:29'),
(225, 1, '337', '2026-05-17 14:35:28'),
(226, 1, '512', '2026-05-17 14:36:28'),
(227, 1, '994', '2026-05-17 14:37:15'),
(228, 1, '254', '2026-05-17 14:38:15'),
(229, 1, '585', '2026-05-17 14:39:15'),
(230, 1, '556', '2026-05-17 14:40:15'),
(231, 1, '492', '2026-05-17 14:41:15'),
(232, 1, '315', '2026-05-17 14:42:15'),
(233, 1, '982', '2026-05-17 14:43:15'),
(234, 1, '728', '2026-05-17 14:44:15'),
(235, 1, '420', '2026-05-17 14:45:15'),
(236, 1, '212', '2026-05-17 14:46:15'),
(237, 1, '128', '2026-05-17 14:47:15'),
(238, 1, '997', '2026-05-17 14:48:15'),
(239, 1, '176', '2026-05-17 14:49:15'),
(240, 1, '291', '2026-05-17 14:50:15'),
(241, 1, '453', '2026-05-17 14:51:15'),
(242, 1, '709', '2026-05-17 14:52:15'),
(243, 1, '126', '2026-05-17 14:53:15'),
(244, 1, '044', '2026-05-17 14:54:15'),
(245, 1, '420', '2026-05-17 14:55:15'),
(246, 1, '747', '2026-05-17 14:56:15'),
(247, 1, '117', '2026-05-17 14:57:15'),
(248, 1, '253', '2026-05-17 14:58:15'),
(249, 1, '246', '2026-05-17 14:59:02'),
(250, 1, '337', '2026-05-17 15:00:02'),
(251, 1, '963', '2026-05-17 15:01:02'),
(252, 1, '997', '2026-05-17 15:02:02'),
(253, 1, '468', '2026-05-17 15:02:52'),
(254, 1, '772', '2026-05-17 15:03:52'),
(255, 1, '064', '2026-05-17 15:04:52'),
(256, 1, '548', '2026-05-17 15:05:52'),
(257, 1, '150', '2026-05-17 15:06:52'),
(258, 1, '044', '2026-05-17 15:07:52'),
(259, 1, '279', '2026-05-17 15:08:47'),
(260, 1, '366', '2026-05-17 15:09:47'),
(261, 1, '401', '2026-05-17 15:10:41'),
(262, 1, '578', '2026-05-17 15:11:41'),
(263, 1, '292', '2026-05-17 15:12:41'),
(264, 1, '735', '2026-05-17 15:13:41'),
(265, 1, '703', '2026-05-17 15:14:41'),
(266, 1, '363', '2026-05-17 15:15:41'),
(267, 1, '261', '2026-05-17 15:16:41'),
(268, 1, '596', '2026-05-17 15:17:41'),
(269, 1, '517', '2026-05-17 15:18:41'),
(270, 1, '117', '2026-05-17 15:19:41'),
(271, 1, '692', '2026-05-17 15:20:41'),
(272, 1, '518', '2026-05-17 15:21:40'),
(273, 1, '040', '2026-05-17 15:22:28'),
(274, 1, '776', '2026-05-17 15:23:28'),
(275, 1, '381', '2026-05-17 15:24:28'),
(276, 1, '469', '2026-05-17 15:25:28'),
(277, 1, '168', '2026-05-17 15:26:28'),
(278, 1, '984', '2026-05-17 15:27:28'),
(279, 1, '825', '2026-05-17 15:28:28'),
(280, 1, '305', '2026-05-17 15:29:28'),
(281, 1, '649', '2026-05-17 15:30:28'),
(282, 1, '456', '2026-05-17 15:31:27'),
(283, 1, '845', '2026-05-17 15:32:27'),
(284, 1, '928', '2026-05-17 15:33:27'),
(285, 1, '861', '2026-05-17 15:34:06'),
(286, 1, '436', '2026-05-17 15:35:06'),
(287, 1, '979', '2026-05-17 15:36:06'),
(288, 1, '304', '2026-05-17 15:37:06'),
(289, 1, '447', '2026-05-17 15:38:06'),
(290, 1, '328', '2026-05-17 15:39:06'),
(291, 1, '556', '2026-05-17 15:40:06'),
(292, 1, '020', '2026-05-17 15:41:06'),
(293, 1, '810', '2026-05-17 15:42:06'),
(294, 1, '250', '2026-05-17 15:43:06'),
(295, 1, '097', '2026-05-17 15:44:06'),
(296, 1, '785', '2026-05-17 15:44:47'),
(297, 1, '059', '2026-05-17 15:45:47'),
(298, 1, '428', '2026-05-17 15:46:47'),
(299, 1, '012', '2026-05-17 15:47:47'),
(300, 1, '332', '2026-05-17 15:48:47'),
(301, 1, '931', '2026-05-17 15:49:47'),
(302, 1, '906', '2026-05-17 15:50:47'),
(303, 1, '579', '2026-05-17 15:51:47'),
(304, 1, '458', '2026-05-17 15:52:47'),
(305, 1, '409', '2026-05-17 15:53:47'),
(306, 1, '393', '2026-05-17 15:54:47'),
(307, 1, '648', '2026-05-17 15:55:47'),
(308, 1, '861', '2026-05-17 15:56:47'),
(309, 1, '281', '2026-05-17 15:57:47'),
(310, 1, '285', '2026-05-17 15:58:47'),
(311, 1, '807', '2026-05-17 15:59:46'),
(312, 1, '016', '2026-05-17 16:00:31'),
(313, 1, '182', '2026-05-17 16:01:18'),
(314, 1, '469', '2026-05-17 16:02:11'),
(315, 1, '849', '2026-05-17 16:03:02'),
(316, 1, '054', '2026-05-17 16:04:02'),
(317, 1, '218', '2026-05-17 16:04:53'),
(318, 1, '861', '2026-05-17 16:05:47'),
(319, 1, '163', '2026-05-17 16:06:47'),
(320, 1, '284', '2026-05-17 16:07:47'),
(321, 1, '202', '2026-05-17 16:08:47'),
(322, 1, '452', '2026-05-17 16:09:47'),
(323, 1, '277', '2026-05-17 16:10:28'),
(324, 1, '975', '2026-05-17 16:11:28'),
(325, 1, '565', '2026-05-17 16:12:28'),
(326, 1, '942', '2026-05-17 16:13:28'),
(327, 1, '971', '2026-05-17 16:14:28'),
(328, 1, '651', '2026-05-17 16:15:28'),
(329, 1, '543', '2026-05-17 16:16:28'),
(330, 1, '085', '2026-05-17 16:17:28'),
(331, 1, '293', '2026-05-17 16:18:28'),
(332, 1, '851', '2026-05-17 16:19:28'),
(333, 1, '717', '2026-05-17 16:20:28'),
(334, 1, '464', '2026-05-17 16:21:28'),
(335, 1, '247', '2026-05-17 16:22:28'),
(336, 1, '872', '2026-05-17 16:23:28'),
(337, 1, '011', '2026-05-17 16:24:28'),
(338, 1, '559', '2026-05-17 16:25:28'),
(339, 1, '640', '2026-05-17 16:26:28'),
(340, 1, '484', '2026-05-17 16:27:28'),
(341, 1, '814', '2026-05-17 16:28:28'),
(342, 1, '354', '2026-05-17 18:43:38');

-- --------------------------------------------------------

--
-- Table structure for table `room_service_request`
--

DROP TABLE IF EXISTS `room_service_request`;
CREATE TABLE IF NOT EXISTS `room_service_request` (
  `request_id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `user_id` int NOT NULL,
  `room_id` int NOT NULL,
  `request_type` enum('Borrow Resource','Technical Issue','Staff Assistance','General Request') NOT NULL,
  `resource_type` enum('Cables','Extension Plug','Stationary','') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('Pending','In Progress','Done','') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`),
  KEY `booking_id` (`booking_id`),
  KEY `room_id` (`room_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `room_service_request`
--

INSERT INTO `room_service_request` (`request_id`, `booking_id`, `user_id`, `room_id`, `request_type`, `resource_type`, `description`, `status`, `created_at`) VALUES
(1, 10, 1, 1, 'Borrow Resource', '', 'Need HDMI Cable for presentation.', 'Done', '2026-05-16 11:02:39'),
(2, 10, 1, 1, 'Technical Issue', NULL, 'Need help in setting up the screen.', 'Pending', '2026-05-16 11:05:15'),
(3, 10, 1, 1, 'Borrow Resource', 'Extension Plug', 'Need a 3-Plug Extension Plug', 'Done', '2026-05-16 13:18:29'),
(4, 10, 1, 1, 'Borrow Resource', 'Extension Plug', 'Need a extension', 'Pending', '2026-05-16 13:58:52');

-- --------------------------------------------------------

--
-- Table structure for table `room_status`
--

DROP TABLE IF EXISTS `room_status`;
CREATE TABLE IF NOT EXISTS `room_status` (
  `room_status_id` int NOT NULL AUTO_INCREMENT,
  `room_id` int NOT NULL,
  `status` enum('Available','Occupied','Closed','') NOT NULL DEFAULT 'Available',
  PRIMARY KEY (`room_status_id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `room_status`
--

INSERT INTO `room_status` (`room_status_id`, `room_id`, `status`) VALUES
(1, 1, 'Available'),
(2, 2, 'Available'),
(3, 3, 'Available'),
(4, 4, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
CREATE TABLE IF NOT EXISTS `staff` (
  `staff_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `identical_number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `gender` enum('male','female','','') NOT NULL,
  `profile_photo` varchar(255) NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`staff_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `name`, `identical_number`, `email`, `phone_number`, `gender`, `profile_photo`, `user_id`) VALUES
(1, 'Staff 1', '010203-10-1234', 'Tp000003@mail.apu.edu.my', '012-345-6789', 'male', 'user_6a0728cd06d4f1.57431873.png', 4);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `student_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `identical_number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `gender` enum('male','female','','') NOT NULL,
  `profile_photo` varchar(255) NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`student_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `name`, `identical_number`, `email`, `phone_number`, `gender`, `profile_photo`, `user_id`) VALUES
(1, 'Student 1', '010203-10-1234', 'Tp123456@mail.apu.edu.my', '012-345 6789', 'male', 'user_6a080452ea7220.43883248.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','lecturer','admin','staff') NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `role`) VALUES
(1, 'TP1', '$2y$10$duyaji09S/9sXAHjFi5queomeLGJIUw4RK88SHSEQC7cyrS2BqLKG', 'student'),
(2, 'TP2', '$2y$10$BSN4.jiKmislUYGpTx2aYumeSFKTi7ZvqqAfSgpI0T/bxthSV7kDO', 'lecturer'),
(3, 'TP3', '$2y$10$BSN4.jiKmislUYGpTx2aYumeSFKTi7ZvqqAfSgpI0T/bxthSV7kDO', 'admin'),
(4, 'TP4', '$2y$10$DIc31IlC/RBsjgpiDtg0YuSVh4shM0QRNJfRKYAg.pEdwZ9SqvxT.', 'staff');

-- --------------------------------------------------------

--
-- Table structure for table `venue_booking`
--

DROP TABLE IF EXISTS `venue_booking`;
CREATE TABLE IF NOT EXISTS `venue_booking` (
  `booking_id` int NOT NULL AUTO_INCREMENT,
  `venue_id` int NOT NULL,
  `user_id` int NOT NULL,
  `description` text NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `booking_status` enum('Pending','Approved','Rejected','Canceled') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`booking_id`),
  KEY `venue_id` (`venue_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `venue_booking`
--

INSERT INTO `venue_booking` (`booking_id`, `venue_id`, `user_id`, `description`, `start_time`, `end_time`, `booking_status`, `created_at`) VALUES
(1, 1, 1, 'Organizing a Career Fair.', '2026-05-17 10:30:00', '2026-05-17 16:00:00', 'Approved', '2026-05-17 07:39:05'),
(2, 1, 1, 'Electronic Fair', '2026-05-17 17:00:00', '2026-05-19 17:00:00', 'Pending', '2026-05-17 08:26:54');

-- --------------------------------------------------------

--
-- Table structure for table `venue_images`
--

DROP TABLE IF EXISTS `venue_images`;
CREATE TABLE IF NOT EXISTS `venue_images` (
  `image_id` int NOT NULL AUTO_INCREMENT,
  `venue_id` int NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`image_id`),
  KEY `venue_id` (`venue_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `venue_images`
--

INSERT INTO `venue_images` (`image_id`, `venue_id`, `image_name`, `created_at`) VALUES
(1, 1, 'venue2.jpg', '2026-05-17 06:45:52'),
(2, 1, 'venue3.jpg', '2026-05-17 06:45:52');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_resource_request`
--
ALTER TABLE `event_resource_request`
  ADD CONSTRAINT `event_resource_request_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `venue_booking` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `event_resource_request_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `event_resource_request_ibfk_3` FOREIGN KEY (`venue_id`) REFERENCES `event_venue` (`venue_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lecturer`
--
ALTER TABLE `lecturer`
  ADD CONSTRAINT `lecturer_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `resource_borrow`
--
ALTER TABLE `resource_borrow`
  ADD CONSTRAINT `resource_borrow_ibfk_1` FOREIGN KEY (`resource_id`) REFERENCES `resource` (`resource_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `resource_borrow_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `resource_usage_log`
--
ALTER TABLE `resource_usage_log`
  ADD CONSTRAINT `resource_usage_log_ibfk_1` FOREIGN KEY (`borrow_id`) REFERENCES `resource_borrow` (`borrow_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_booking`
--
ALTER TABLE `room_booking`
  ADD CONSTRAINT `room_booking_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `room_booking_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_checkin`
--
ALTER TABLE `room_checkin`
  ADD CONSTRAINT `room_checkin_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `room_booking` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_iot_state`
--
ALTER TABLE `room_iot_state`
  ADD CONSTRAINT `room_iot_state_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_issue_report`
--
ALTER TABLE `room_issue_report`
  ADD CONSTRAINT `room_issue_report_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `room_booking` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `room_issue_report_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `room_issue_report_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_otp`
--
ALTER TABLE `room_otp`
  ADD CONSTRAINT `room_otp_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_service_request`
--
ALTER TABLE `room_service_request`
  ADD CONSTRAINT `room_service_request_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `room_booking` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `room_service_request_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `room_service_request_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_status`
--
ALTER TABLE `room_status`
  ADD CONSTRAINT `room_status_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `venue_booking`
--
ALTER TABLE `venue_booking`
  ADD CONSTRAINT `venue_booking_ibfk_1` FOREIGN KEY (`venue_id`) REFERENCES `event_venue` (`venue_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `venue_booking_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `venue_images`
--
ALTER TABLE `venue_images`
  ADD CONSTRAINT `venue_images_ibfk_1` FOREIGN KEY (`venue_id`) REFERENCES `event_venue` (`venue_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
