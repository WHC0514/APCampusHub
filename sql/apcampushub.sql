-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 15, 2026 at 12:25 PM
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
  `user_id` int NOT NULL,
  PRIMARY KEY (`admin_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(9, 4, 1, '2026-05-15', '11:11:00', '22:00:00', 'Completed', '2026-05-15 03:11:52');

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `room_checkin`
--

INSERT INTO `room_checkin` (`checkin_id`, `booking_id`, `actual_checkin`, `actual_checkout`, `occupancy_status`) VALUES
(1, 2, '2026-05-13 08:00:00', '2026-05-13 11:30:00', 'Check Out'),
(2, 4, '2026-05-14 20:57:19', '2026-05-14 20:58:00', 'Check Out'),
(6, 7, '2026-05-14 21:42:23', '2026-05-14 22:00:01', 'Check Out'),
(9, 8, '2026-05-15 11:04:00', '2026-05-15 11:04:03', 'Check Out'),
(10, 9, '2026-05-15 11:12:14', '2026-05-15 20:18:51', 'Check Out');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(123, 4, '557', '2026-05-15 13:09:46');

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
  `resource_type` enum('HDMI Cable','Extension Plug','Marker Pen','') DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('Pending','In Progress','Done','') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`),
  KEY `booking_id` (`booking_id`),
  KEY `room_id` (`room_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `user_id` int NOT NULL,
  PRIMARY KEY (`staff_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `name`, `identical_number`, `email`, `phone_number`, `gender`, `profile_photo`, `user_id`) VALUES
(1, 'Chong', '010203-10-1234', 'Tp123456@mail.apu.edu.my', '012-345 6789', 'male', 'profile1.png', 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `role`) VALUES
(1, 'TP123456', '$2y$10$BSN4.jiKmislUYGpTx2aYumeSFKTi7ZvqqAfSgpI0T/bxthSV7kDO', 'student'),
(2, 'TP000001', '$2y$10$BSN4.jiKmislUYGpTx2aYumeSFKTi7ZvqqAfSgpI0T/bxthSV7kDO', 'lecturer');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lecturer`
--
ALTER TABLE `lecturer`
  ADD CONSTRAINT `lecturer_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
