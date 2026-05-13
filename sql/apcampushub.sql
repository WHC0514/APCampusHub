-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 13, 2026 at 06:56 AM
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
  `user_id` int NOT NULL,
  PRIMARY KEY (`lecturer_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `room_booking`
--

INSERT INTO `room_booking` (`booking_id`, `room_id`, `user_id`, `booking_date`, `start_time`, `end_time`, `booking_status`, `created_at`) VALUES
(1, 1, 1, '2026-05-13', '20:00:00', '22:00:00', 'Approved', '2026-05-12 13:49:17'),
(2, 1, 1, '2026-05-13', '08:00:00', '11:30:00', 'Approved', '2026-05-12 13:49:58'),
(3, 1, 1, '2026-05-13', '12:50:00', '15:30:00', 'Approved', '2026-05-12 13:50:30');

-- --------------------------------------------------------

--
-- Table structure for table `room_checkin`
--

DROP TABLE IF EXISTS `room_checkin`;
CREATE TABLE IF NOT EXISTS `room_checkin` (
  `checkin_id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `actual_checkin` datetime NOT NULL,
  `actual_checkout` datetime NOT NULL,
  `occupancy_status` enum('In Use','Check Out','','') NOT NULL DEFAULT 'In Use',
  PRIMARY KEY (`checkin_id`),
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `role`) VALUES
(1, 'TP123456', '$2y$10$BSN4.jiKmislUYGpTx2aYumeSFKTi7ZvqqAfSgpI0T/bxthSV7kDO', 'student');

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
