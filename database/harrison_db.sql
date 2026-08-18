-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2025 at 11:55 AM
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
-- Database: `harrison_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `borrow_history`
--

CREATE TABLE `borrow_history` (
  `b_history_id` int(11) NOT NULL,
  `tenant_id` int(255) NOT NULL,
  `borrow_id` int(11) NOT NULL,
  `h_item_name` varchar(255) NOT NULL,
  `h_quantity` int(255) NOT NULL,
  `h_on_hand` int(255) NOT NULL,
  `h_date_borrow` date DEFAULT NULL,
  `h_date_return` date DEFAULT NULL,
  `h_date_update` date DEFAULT NULL,
  `status_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_history`
--

INSERT INTO `borrow_history` (`b_history_id`, `tenant_id`, `borrow_id`, `h_item_name`, `h_quantity`, `h_on_hand`, `h_date_borrow`, `h_date_return`, `h_date_update`, `status_id`) VALUES
(20, 62, 22, 'Kraber', 1, 1, '2025-03-27', NULL, '2025-03-27', 17),
(21, 62, 22, 'Kraber', 1, 0, '2025-03-27', '2025-03-27', '2025-03-27', 18),
(22, 62, 22, 'Kraber', 1, 0, '2025-03-27', '2025-03-27', '2025-03-27', 19);

-- --------------------------------------------------------

--
-- Table structure for table `borrow_tbl`
--

CREATE TABLE `borrow_tbl` (
  `borrow_id` int(255) NOT NULL,
  `tenant_id` int(255) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(255) NOT NULL,
  `on_hand` int(255) NOT NULL,
  `date_borrow` date NOT NULL DEFAULT current_timestamp(),
  `date_return` date DEFAULT NULL,
  `date_updated` date DEFAULT NULL,
  `status_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_tbl`
--

CREATE TABLE `contact_tbl` (
  `con_id` int(255) NOT NULL,
  `full_name` text NOT NULL,
  `con_email` text NOT NULL,
  `con_number` varchar(13) NOT NULL,
  `con_sub` text NOT NULL,
  `con_mes` text NOT NULL,
  `con_datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `status_id` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_tbl`
--

INSERT INTO `contact_tbl` (`con_id`, `full_name`, `con_email`, `con_number`, `con_sub`, `con_mes`, `con_datetime`, `status_id`) VALUES
(23, 'bendono san', 'yahallo@gmail.com', '09999999999', 'hot weather', 'feels 38C in july month', '2024-07-02 14:43:42', 4),
(25, 'Kevin Laguitan', 'hahaha@gmail.com', '09999999999', 'pinas', 'mainit', '2025-03-27 20:22:00', 4),
(27, 'adwdawd', 'dasdas@gmail.com', '09999999999', 'reserve', 'reserve', '2025-03-29 16:43:47', 4);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_tbl`
--

CREATE TABLE `inventory_tbl` (
  `item_id` int(255) NOT NULL,
  `item_name` varchar(50) NOT NULL,
  `quantity` int(255) NOT NULL,
  `on_hand` int(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_id` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_tbl`
--

INSERT INTO `inventory_tbl` (`item_id`, `item_name`, `quantity`, `on_hand`, `owner`, `date_added`, `last_updated`, `status_id`) VALUES
(5, 'Dilis', 4, 0, 'Ben', '2025-03-21 13:33:01', '2025-03-22 11:25:31', 15),
(6, 'Walis', 4, 0, 'Ben', '2025-03-22 02:30:20', '2025-03-22 03:14:07', 15),
(7, 'Kraber', 1, 0, 'Zyrus', '2025-03-27 12:41:17', '2025-03-27 12:44:22', 15);

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `log_type_id` int(11) NOT NULL,
  `log_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log`
--

INSERT INTO `log` (`log_type_id`, `log_type`) VALUES
(1, 'LOGGED-IN'),
(2, 'LOGGED-OUT'),
(3, 'ACCEPT'),
(4, 'CANCEL'),
(5, 'MODIFIED'),
(6, 'ADDED'),
(7, 'DELETED'),
(8, 'REQUEST'),
(9, 'ACCESS DENY'),
(10, 'SESSION EXPIRED'),
(11, 'SENT'),
(12, 'BORROWED'),
(13, 'RETURNED');

-- --------------------------------------------------------

--
-- Table structure for table `log_tbl`
--

CREATE TABLE `log_tbl` (
  `tenant_log_id` int(50) NOT NULL COMMENT '1000 = tenant logs',
  `log_type_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `l_first_name` text NOT NULL,
  `date_entry` datetime NOT NULL,
  `user_ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_tbl`
--

INSERT INTO `log_tbl` (`tenant_log_id`, `log_type_id`, `tenant_id`, `l_first_name`, `date_entry`, `user_ip`) VALUES
(1125, 2, 1, '', '2025-03-17 05:22:38', '::1'),
(1126, 1, 61, '', '2025-03-17 12:22:46', '::1'),
(1127, 2, 61, '', '2025-03-17 05:22:49', '::1'),
(1128, 1, 1, '', '2025-03-17 12:22:56', '::1'),
(1129, 2, 1, '', '2025-03-17 05:26:24', '::1'),
(1130, 1, 61, '', '2025-03-17 12:26:30', '::1'),
(1131, 2, 61, '', '2025-03-17 05:46:56', '::1'),
(1132, 1, 1, '', '2025-03-17 12:47:08', '::1'),
(1133, 2, 1, '', '2025-03-17 07:46:23', '::1'),
(1134, 1, 1, '', '2025-03-17 21:13:26', '::1'),
(1135, 2, 1, '', '2025-03-17 14:18:05', '::1'),
(1136, 1, 2, '', '2025-03-19 11:06:57', '::1'),
(1137, 10, 2, '', '2025-03-19 05:03:20', '-'),
(1138, 1, 2, '', '2025-03-19 12:03:30', '::1'),
(1139, 10, 2, '', '2025-03-19 08:45:14', '-'),
(1140, 1, 2, '', '2025-03-19 16:21:48', '::1'),
(1141, 10, 2, '', '2025-03-19 10:05:55', '-'),
(1142, 1, 2, '', '2025-03-19 17:06:13', '::1'),
(1143, 10, 2, '', '2025-03-19 11:16:42', '-'),
(1144, 1, 2, '', '2025-03-19 18:16:48', '::1'),
(1145, 1, 2, '', '2025-03-19 19:03:52', '::1'),
(1146, 1, 2, '', '2025-03-20 18:50:35', '::1'),
(1147, 10, 2, '', '2025-03-20 13:08:59', '-'),
(1148, 1, 2, '', '2025-03-20 20:09:06', '::1'),
(1149, 1, 2, '', '2025-03-21 14:35:20', '::1'),
(1150, 2, 2, '', '2025-03-21 07:35:43', '::1'),
(1151, 1, 2, '', '2025-03-21 15:22:01', '::1'),
(1152, 1, 2, '', '2025-03-21 15:22:29', '::1'),
(1153, 1, 2, '', '2025-03-21 15:24:50', '::1'),
(1154, 2, 2, '', '2025-03-21 08:25:09', '::1'),
(1155, 1, 61, '', '2025-03-21 15:25:15', '::1'),
(1156, 1, 2, '', '2025-03-21 15:30:46', '::1'),
(1157, 2, 2, '', '2025-03-21 08:30:50', '::1'),
(1158, 1, 61, '', '2025-03-21 15:30:59', '::1'),
(1159, 2, 61, '', '2025-03-21 10:30:27', '::1'),
(1160, 1, 2, '', '2025-03-21 17:30:33', '::1'),
(1161, 2, 2, '', '2025-03-21 10:40:54', '::1'),
(1162, 1, 2, '', '2025-03-21 17:41:00', '::1'),
(1163, 10, 2, '', '2025-03-21 11:46:27', '-'),
(1164, 1, 2, '', '2025-03-21 18:46:33', '::1'),
(1165, 1, 2, '', '2025-03-22 10:28:29', '::1'),
(1166, 1, 2, '', '2025-03-22 10:54:05', '::1'),
(1167, 1, 2, '', '2025-03-22 11:05:43', '::1'),
(1168, 10, 2, '', '2025-03-22 04:52:25', '-'),
(1169, 1, 2, '', '2025-03-22 11:52:32', '::1'),
(1170, 10, 2, '', '2025-03-22 05:58:36', '-'),
(1171, 1, 2, '', '2025-03-22 12:58:40', '::1'),
(1172, 1, 2, '', '2025-03-22 13:49:29', '::1'),
(1173, 10, 2, '', '2025-03-22 11:14:02', '-'),
(1174, 1, 2, '', '2025-03-22 18:14:11', '::1'),
(1175, 2, 2, '', '2025-03-22 11:16:01', '::1'),
(1176, 1, 2, '', '2025-03-22 18:16:15', '::1'),
(1177, 1, 2, '', '2025-03-22 19:32:58', '::1'),
(1178, 5, 2, '', '2025-03-22 20:05:36', '-'),
(1179, 2, 2, '', '2025-03-22 13:12:18', '::1'),
(1180, 1, 2, '', '2025-03-22 20:12:35', '::1'),
(1181, 5, 63, '', '2025-03-22 20:12:55', '-'),
(1182, 2, 2, '', '2025-03-22 13:12:59', '::1'),
(1183, 1, 63, '', '2025-03-22 20:13:07', '::1'),
(1184, 7, 63, '', '2025-03-22 20:13:15', '-'),
(1185, 1, 2, '', '2025-03-22 20:14:26', '::1'),
(1186, 1, 2, '', '2025-03-22 20:55:18', '::1'),
(1187, 1, 2, '', '2025-03-23 15:35:19', '::1'),
(1188, 1, 2, '', '2025-03-23 16:21:53', '::1'),
(1189, 1, 2, '', '2025-03-23 17:20:27', '::1'),
(1190, 10, 2, '', '2025-03-23 11:47:29', '-'),
(1191, 1, 2, '', '2025-03-23 18:47:36', '::1'),
(1192, 2, 2, '', '2025-03-23 12:55:50', '::1'),
(1193, 1, 2, '', '2025-03-23 20:38:34', '::1'),
(1194, 1, 61, '', '2025-03-24 12:01:45', '::1'),
(1195, 1, 61, '', '2025-03-24 12:06:56', '::1'),
(1196, 10, 61, '', '2025-03-24 06:50:14', '-'),
(1197, 1, 61, '', '2025-03-24 13:50:28', '::1'),
(1198, 1, 61, '', '2025-03-24 14:00:36', '::1'),
(1199, 1, 61, '', '2025-03-24 15:37:36', '::1'),
(1200, 1, 61, '', '2025-03-24 16:25:43', '::1'),
(1201, 2, 61, '', '2025-03-24 09:25:58', '::1'),
(1202, 1, 61, '', '2025-03-24 16:26:08', '::1'),
(1203, 1, 2, '', '2025-03-24 16:38:18', '::1'),
(1204, 2, 2, '', '2025-03-24 09:38:24', '::1'),
(1205, 1, 61, '', '2025-03-24 16:38:38', '::1'),
(1206, 1, 61, '', '2025-03-24 17:40:42', '::1'),
(1207, 1, 61, '', '2025-03-24 17:53:01', '::1'),
(1208, 2, 61, '', '2025-03-24 11:03:16', '::1'),
(1209, 1, 2, '', '2025-03-24 18:03:21', '::1'),
(1210, 2, 2, '', '2025-03-24 11:06:46', '::1'),
(1211, 1, 61, '', '2025-03-24 18:06:54', '::1'),
(1212, 10, 61, '', '2025-03-24 12:16:09', '-'),
(1213, 1, 61, '', '2025-03-24 19:16:16', '::1'),
(1214, 1, 61, '', '2025-03-24 19:16:37', '::1'),
(1215, 1, 61, '', '2025-03-24 19:25:13', '::1'),
(1216, 2, 61, '', '2025-03-24 14:18:35', '::1'),
(1217, 1, 2, '', '2025-03-24 21:18:40', '::1'),
(1218, 2, 2, '', '2025-03-24 14:18:52', '::1'),
(1219, 1, 61, '', '2025-03-24 21:19:01', '::1'),
(1220, 1, 61, '', '2025-03-24 21:34:38', '::1'),
(1221, 2, 61, '', '2025-03-24 14:55:52', '::1'),
(1222, 1, 2, '', '2025-03-24 21:55:59', '::1'),
(1223, 1, 2, '', '2025-03-27 20:06:27', '::1'),
(1224, 2, 2, '', '2025-03-27 13:12:32', '::1'),
(1225, 1, 2, '', '2025-03-27 20:24:21', '::1'),
(1226, 2, 2, '', '2025-03-27 14:26:10', '::1'),
(1227, 1, 62, '', '2025-03-27 21:26:25', '::1'),
(1228, 5, 62, '', '2025-03-27 21:28:11', '-'),
(1229, 2, 62, '', '2025-03-27 14:33:21', '::1'),
(1230, 1, 2, '', '2025-03-28 07:52:29', '::1'),
(1231, 2, 2, '', '2025-03-28 01:30:15', '::1'),
(1232, 1, 62, '', '2025-03-28 08:30:23', '::1'),
(1233, 2, 62, '', '2025-03-28 01:30:36', '::1'),
(1234, 1, 61, '', '2025-03-28 08:30:44', '::1'),
(1235, 2, 61, '', '2025-03-28 01:30:47', '::1'),
(1236, 1, 2, '', '2025-03-28 08:33:04', '::1'),
(1237, 1, 2, '', '2025-03-28 09:21:34', '::1'),
(1238, 2, 2, '', '2025-03-28 02:45:09', '::1'),
(1239, 1, 2, '', '2025-03-29 08:46:17', '::1'),
(1240, 2, 2, '', '2025-03-29 01:55:28', '::1'),
(1241, 1, 2, '', '2025-03-29 08:59:25', '::1'),
(1242, 2, 2, '', '2025-03-29 02:12:02', '::1'),
(1243, 1, 2, '', '2025-03-29 09:13:01', '::1'),
(1244, 1, 2, '', '2025-03-29 09:16:23', '::1'),
(1245, 1, 2, '', '2025-03-29 09:26:06', '::1'),
(1246, 1, 2, '', '2025-03-29 09:30:42', '::1'),
(1247, 1, 2, '', '2025-03-29 09:34:05', '::1'),
(1248, 10, 2, '', '2025-03-29 07:21:52', '-'),
(1249, 1, 2, '', '2025-03-29 14:21:59', '::1'),
(1257, 2, 2, '', '2025-03-29 10:17:01', '::1'),
(1258, 1, 2, '', '2025-03-29 17:17:34', '::1'),
(1259, 2, 2, '', '2025-03-29 10:23:52', '::1'),
(1260, 1, 2, '', '2025-03-29 17:24:03', '::1'),
(1265, 1, 2, '', '2025-03-29 18:32:35', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `log_tbl_b`
--

CREATE TABLE `log_tbl_b` (
  `borrow_log_id` int(255) NOT NULL,
  `log_type_id` int(255) NOT NULL,
  `borrow_id` int(255) NOT NULL,
  `date_entry` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_tbl_b`
--

INSERT INTO `log_tbl_b` (`borrow_log_id`, `log_type_id`, `borrow_id`, `date_entry`) VALUES
(35, 12, 17, '2025-03-22 15:03:18'),
(36, 5, 17, '2025-03-22 15:03:23'),
(37, 7, 17, '2025-03-22 15:03:35'),
(38, 12, 18, '2025-03-22 15:33:37'),
(39, 12, 19, '2025-03-22 15:33:52'),
(40, 12, 20, '2025-03-22 16:05:39'),
(41, 5, 20, '2025-03-22 16:13:42'),
(42, 5, 20, '2025-03-22 16:13:53'),
(43, 12, 21, '2025-03-27 20:41:35'),
(44, 12, 22, '2025-03-27 20:42:58'),
(45, 5, 22, '2025-03-27 20:44:13'),
(46, 7, 22, '2025-03-27 20:44:28');

-- --------------------------------------------------------

--
-- Table structure for table `log_tbl_c`
--

CREATE TABLE `log_tbl_c` (
  `contact_log_id` int(50) NOT NULL COMMENT '30000 = contact log',
  `log_type_id` int(11) NOT NULL,
  `con_id` int(11) NOT NULL,
  `date_entry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_tbl_c`
--

INSERT INTO `log_tbl_c` (`contact_log_id`, `log_type_id`, `con_id`, `date_entry`) VALUES
(30001, 7, 5, '2024-06-21 23:05:55'),
(30015, 7, 15, '2024-07-02 14:44:52'),
(30016, 7, 24, '2024-07-09 21:18:33'),
(30017, 7, 26, '2025-03-27 20:48:17');

-- --------------------------------------------------------

--
-- Table structure for table `log_tbl_i`
--

CREATE TABLE `log_tbl_i` (
  `inventory_log_id` int(255) NOT NULL,
  `log_type_id` int(255) NOT NULL,
  `item_id` int(255) NOT NULL,
  `date_entry` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_tbl_i`
--

INSERT INTO `log_tbl_i` (`inventory_log_id`, `log_type_id`, `item_id`, `date_entry`) VALUES
(70001, 6, 1, '2025-03-17 06:09:19'),
(70002, 5, 1, '2025-03-17 06:28:55'),
(70003, 7, 1, '2025-03-17 06:38:29'),
(70004, 6, 2, '2025-03-17 13:13:40'),
(70005, 5, 2, '2025-03-17 13:13:46'),
(70006, 5, 2, '2025-03-19 04:04:56'),
(70007, 5, 2, '2025-03-19 04:09:46'),
(70008, 6, 3, '2025-03-19 04:15:48'),
(70009, 5, 3, '2025-03-19 04:18:27'),
(70010, 6, 4, '2025-03-19 04:20:38'),
(70011, 5, 3, '2025-03-19 04:23:47'),
(70012, 7, 4, '2025-03-21 13:31:55'),
(70013, 7, 3, '2025-03-21 13:31:57'),
(70014, 7, 2, '2025-03-21 13:32:00'),
(70015, 6, 5, '2025-03-21 13:33:01'),
(70016, 5, 5, '2025-03-21 13:33:57'),
(70017, 6, 6, '2025-03-22 02:30:20'),
(70018, 5, 6, '2025-03-22 03:13:48'),
(70019, 5, 6, '2025-03-22 03:14:07'),
(70020, 5, 5, '2025-03-22 03:14:11'),
(70021, 5, 5, '2025-03-22 03:14:16'),
(70022, 7, 11, '2025-03-22 04:58:46'),
(70023, 5, 5, '2025-03-22 11:25:31'),
(70024, 6, 7, '2025-03-27 12:41:17'),
(70025, 5, 7, '2025-03-27 12:41:25'),
(70026, 5, 7, '2025-03-27 12:44:22');

-- --------------------------------------------------------

--
-- Table structure for table `log_tbl_m`
--

CREATE TABLE `log_tbl_m` (
  `log_maint_id` int(255) NOT NULL,
  `log_type_id` int(255) NOT NULL,
  `request_id` int(255) NOT NULL,
  `date_entry` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_tbl_m`
--

INSERT INTO `log_tbl_m` (`log_maint_id`, `log_type_id`, `request_id`, `date_entry`) VALUES
(1, 7, 1, '2025-03-22 20:40:07'),
(2, 7, 2, '2025-03-23 16:10:21'),
(3, 7, 3, '2025-03-23 16:10:48'),
(4, 7, 4, '2025-03-23 16:11:29'),
(5, 7, 5, '2025-03-23 16:15:26'),
(6, 7, 6, '2025-03-23 16:44:47'),
(7, 5, 6, '2025-03-23 19:14:41'),
(8, 5, 1, '2025-03-23 19:16:53'),
(9, 5, 5, '2025-03-23 19:16:59'),
(10, 5, 5, '2025-03-23 19:18:57'),
(11, 6, 7, '2025-03-23 19:19:18'),
(12, 5, 7, '2025-03-23 19:19:22'),
(13, 5, 1, '2025-03-23 19:23:49'),
(14, 5, 1, '2025-03-23 19:24:31'),
(15, 5, 1, '2025-03-23 19:24:46'),
(16, 5, 1, '2025-03-23 19:25:03'),
(17, 5, 1, '2025-03-23 19:27:16'),
(18, 7, 0, '2025-03-23 19:51:52'),
(19, 7, 1, '2025-03-23 19:53:49'),
(20, 6, 8, '2025-03-24 16:09:25'),
(21, 6, 9, '2025-03-24 16:14:08'),
(22, 6, 10, '2025-03-27 20:45:16'),
(23, 5, 10, '2025-03-27 20:48:05'),
(24, 7, 10, '2025-03-27 20:48:10'),
(25, 6, 11, '2025-03-27 21:32:00'),
(26, 6, 12, '2025-03-28 08:41:01'),
(27, 5, 12, '2025-03-28 08:41:08'),
(28, 5, 12, '2025-03-28 08:42:08'),
(29, 5, 11, '2025-03-29 10:41:14'),
(30, 5, 11, '2025-03-29 10:41:17'),
(31, 5, 11, '2025-03-29 10:41:20'),
(32, 5, 11, '2025-03-29 10:41:23'),
(33, 5, 11, '2025-03-29 10:41:26'),
(34, 5, 11, '2025-03-29 10:41:29'),
(35, 5, 11, '2025-03-29 10:41:32'),
(36, 5, 11, '2025-03-29 10:41:36'),
(37, 5, 11, '2025-03-29 10:41:39'),
(38, 5, 9, '2025-03-29 10:43:38'),
(39, 6, 13, '2025-03-29 16:43:11'),
(40, 7, 12, '2025-03-29 18:42:23');

-- --------------------------------------------------------

--
-- Table structure for table `log_tbl_pay`
--

CREATE TABLE `log_tbl_pay` (
  `payment_log_id` int(50) NOT NULL COMMENT '40000 = pay log',
  `log_type_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `date_entry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_tbl_pay`
--

INSERT INTO `log_tbl_pay` (`payment_log_id`, `log_type_id`, `payment_id`, `date_entry`) VALUES
(40000, 6, 10000, '2024-07-09 21:56:02'),
(40001, 6, 10001, '2024-07-10 14:06:51'),
(40002, 5, 10001, '2024-07-10 14:07:37'),
(40003, 5, 10001, '2024-07-10 14:07:58'),
(40004, 6, 10002, '2024-07-10 14:08:17'),
(40005, 6, 10003, '2025-02-23 21:14:08'),
(40006, 6, 10004, '2025-02-23 21:14:56'),
(40007, 6, 10005, '2025-02-23 21:16:18'),
(40008, 5, 10057, '2025-03-14 19:40:17'),
(40009, 6, 10059, '2025-03-15 16:30:17'),
(40010, 6, 10060, '2025-03-19 18:17:56'),
(40011, 5, 10060, '2025-03-19 20:28:32'),
(40012, 5, 10057, '2025-03-19 20:46:42'),
(40013, 5, 10057, '2025-03-19 21:03:48'),
(40014, 5, 10057, '2025-03-19 21:08:47'),
(40015, 5, 10057, '2025-03-19 21:37:51'),
(40016, 5, 10057, '2025-03-19 21:41:29'),
(40017, 5, 10057, '2025-03-19 21:45:16'),
(40018, 6, 10061, '2025-03-19 21:47:41'),
(40019, 5, 10061, '2025-03-19 21:47:56'),
(40020, 5, 10061, '2025-03-19 21:48:48'),
(40021, 5, 10061, '2025-03-19 21:48:58'),
(40022, 5, 10061, '2025-03-19 21:50:07'),
(40023, 5, 10061, '2025-03-19 21:51:19'),
(40024, 5, 10057, '2025-03-21 18:10:29'),
(40025, 5, 10024, '2025-03-21 18:10:33'),
(40026, 6, 10062, '2025-03-21 20:36:16'),
(40027, 5, 10002, '2025-03-24 16:11:04'),
(40028, 6, 10065, '2025-03-24 21:06:48'),
(40029, 6, 10066, '2025-03-24 21:17:11'),
(40030, 6, 10067, '2025-03-24 21:17:35'),
(40031, 6, 10068, '2025-03-27 20:49:29'),
(40032, 6, 10069, '2025-03-27 20:49:29'),
(40033, 5, 10069, '2025-03-27 20:49:39'),
(40034, 6, 10070, '2025-03-27 20:54:11'),
(40035, 6, 10071, '2025-03-27 20:54:11'),
(40036, 6, 10072, '2025-03-27 20:56:55'),
(40037, 6, 10073, '2025-03-27 20:56:55'),
(40038, 6, 10074, '2025-03-27 20:59:31'),
(40039, 6, 10075, '2025-03-27 20:59:31'),
(40040, 6, 10076, '2025-03-27 21:08:21'),
(40041, 6, 10077, '2025-03-27 21:08:21'),
(40042, 6, 10078, '2025-03-27 21:09:01'),
(40043, 6, 10079, '2025-03-27 21:09:01'),
(40044, 6, 10080, '2025-03-27 21:13:54'),
(40045, 6, 10081, '2025-03-27 21:13:54'),
(40046, 6, 10082, '2025-03-27 21:14:34'),
(40047, 6, 10083, '2025-03-27 21:14:34'),
(40048, 6, 10084, '2025-03-27 21:18:03'),
(40049, 6, 10085, '2025-03-27 21:23:43'),
(40050, 6, 10086, '2025-03-27 21:25:53'),
(40051, 6, 10087, '2025-03-27 21:32:49'),
(40052, 6, 10089, '2025-03-28 09:40:50'),
(40053, 6, 10090, '2025-03-28 09:42:20'),
(40054, 5, 10090, '2025-03-28 09:44:02'),
(40055, 5, 10090, '2025-03-29 09:02:36'),
(40056, 5, 10090, '2025-03-29 09:17:33'),
(40057, 5, 10090, '2025-03-29 09:17:37'),
(40058, 5, 10090, '2025-03-29 09:17:40'),
(40059, 5, 10090, '2025-03-29 09:17:43'),
(40060, 5, 10090, '2025-03-29 09:17:46'),
(40061, 5, 10090, '2025-03-29 09:17:49'),
(40062, 5, 10090, '2025-03-29 09:17:53'),
(40063, 5, 10090, '2025-03-29 09:17:57'),
(40064, 5, 10088, '2025-03-29 09:18:08'),
(40065, 5, 10090, '2025-03-29 10:07:06'),
(40066, 5, 10067, '2025-03-29 10:44:05'),
(40067, 5, 10066, '2025-03-29 10:44:13'),
(40068, 6, 10094, '2025-03-29 13:38:10'),
(40069, 5, 10094, '2025-03-29 15:55:39'),
(40070, 5, 10084, '2025-03-29 16:34:26'),
(40071, 5, 10084, '2025-03-29 16:36:01'),
(40072, 5, 10070, '2025-03-29 16:36:07'),
(40073, 5, 10068, '2025-03-29 16:36:12'),
(40074, 5, 10063, '2025-03-29 16:36:18'),
(40075, 5, 10061, '2025-03-29 16:36:22');

-- --------------------------------------------------------

--
-- Table structure for table `log_tbl_r`
--

CREATE TABLE `log_tbl_r` (
  `room_log_id` int(11) NOT NULL COMMENT '50000 = room log',
  `log_type_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `date_entry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_tbl_r`
--

INSERT INTO `log_tbl_r` (`room_log_id`, `log_type_id`, `room_id`, `date_entry`) VALUES
(50001, 5, 28, '2024-07-09 21:23:17'),
(50002, 5, 28, '2024-07-09 21:29:22'),
(50003, 6, 32, '2024-07-09 21:57:00'),
(50004, 5, 32, '2024-07-09 21:57:06'),
(50005, 5, 32, '2024-07-09 21:57:10'),
(50006, 5, 32, '2024-07-10 14:07:05'),
(50007, 5, 32, '2025-03-15 16:34:19'),
(50008, 5, 32, '2025-03-17 12:18:51'),
(50009, 5, 32, '2025-03-17 12:21:19'),
(50010, 6, 33, '2025-03-17 12:21:43'),
(50011, 5, 33, '2025-03-17 12:22:29'),
(50012, 6, 34, '2025-03-21 20:54:33'),
(50013, 7, 32, '2025-03-21 20:54:37'),
(50014, 7, 33, '2025-03-22 20:40:07'),
(50015, 7, 34, '2025-03-22 20:41:41'),
(50016, 6, 35, '2025-03-22 20:42:12'),
(50017, 5, 35, '2025-03-22 20:42:17'),
(50018, 7, 0, '2025-03-23 16:10:21'),
(50019, 7, 0, '2025-03-23 16:10:48'),
(50020, 7, 0, '2025-03-23 16:11:29'),
(50021, 7, 0, '2025-03-23 16:15:26'),
(50022, 6, 36, '2025-03-27 20:31:12'),
(50023, 5, 36, '2025-03-27 20:31:17');

-- --------------------------------------------------------

--
-- Table structure for table `log_tbl_rs`
--

CREATE TABLE `log_tbl_rs` (
  `reserve_log_id` int(50) NOT NULL COMMENT '60000 = reserve log',
  `log_type_id` int(11) NOT NULL,
  `reserve_id` int(11) NOT NULL,
  `date_entry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_tbl_rs`
--

INSERT INTO `log_tbl_rs` (`reserve_log_id`, `log_type_id`, `reserve_id`, `date_entry`) VALUES
(60001, 7, 52, '2024-07-02 14:44:05'),
(60002, 4, 56, '2024-07-09 21:23:08'),
(60003, 7, 56, '2025-03-15 14:11:28'),
(60004, 3, 34, '2025-03-15 14:11:38'),
(60005, 3, 34, '2025-03-20 19:11:51'),
(60006, 4, 34, '2025-03-22 19:33:09'),
(60007, 3, 34, '2025-03-22 19:33:16'),
(60008, 4, 48, '2025-03-27 20:26:33'),
(60009, 3, 34, '2025-03-27 20:26:42'),
(60010, 7, 57, '2025-03-27 20:29:30'),
(60011, 4, 55, '2025-03-29 10:24:43'),
(60012, 4, 55, '2025-03-29 10:24:46'),
(60013, 4, 51, '2025-03-29 10:24:49');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_tbl`
--

CREATE TABLE `maintenance_tbl` (
  `request_id` int(255) NOT NULL,
  `tenant_id` int(255) NOT NULL,
  `room_id` int(255) NOT NULL,
  `issue` varchar(255) NOT NULL,
  `budget` int(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_completed` date DEFAULT NULL,
  `status_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_tbl`
--

INSERT INTO `maintenance_tbl` (`request_id`, `tenant_id`, `room_id`, `issue`, `budget`, `description`, `date_added`, `date_completed`, `status_id`) VALUES
(8, 61, 35, 'Broken Tiles', 3000, 'Dumbbells fell and broke multiple tiles', '2025-03-24 08:09:25', NULL, 13),
(9, 61, 35, 'Rat infestation', 200, 'Need mouse traps', '2025-03-24 08:14:08', NULL, 2),
(11, 62, 36, 'roach infestation', 0, 'need roach killing', '2025-03-27 13:32:00', '2025-03-29', 13),
(13, 61, 35, 'poopoo', 0, 'poopoo in the walls', '2025-03-29 08:43:11', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `message_tbl`
--

CREATE TABLE `message_tbl` (
  `message_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `send_by` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `date_sent` datetime NOT NULL,
  `status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message_tbl`
--

INSERT INTO `message_tbl` (`message_id`, `tenant_id`, `send_by`, `subject`, `message`, `date_sent`, `status_id`) VALUES
(41, 61, 'Admin', 'Welcome to Ben Harrison!', 'A heartfelt welcome to Ben Harrison Residence! We hope you have a fantastic stay with us. Our team is here to ensure your comfort and satisfaction. Please reach out us on Google Mail, BH.benharrisonofficial@gmail.com or go to the front desk if you need any assistance.', '2024-07-09 21:55:18', 4),
(45, 61, 'Admin', 'test', 'test', '2024-07-10 16:23:52', 4),
(46, 61, 'Admin', 'test', 'testttttttttt', '2024-07-10 16:25:53', 4),
(47, 61, 'Admin', 'mainit sa pinas', '35C', '2024-07-11 14:44:31', 4),
(48, 62, 'Admin', 'Welcome to Ben Harrison!', 'A heartfelt welcome to Ben Harrison Residence! We hope you have a fantastic stay with us. Our team is here to ensure your comfort and satisfaction. Please reach out us on Google Mail, BH.benharrisonofficial@gmail.com or go to the front desk if you need any assistance.', '2025-03-15 15:32:15', 4),
(49, 62, 'Admin', 'Hello', 'sup', '2025-03-28 08:30:12', 4),
(50, 1, 'Admin', 'sup', 'oyoyo', '2025-03-28 08:33:45', 4),
(51, 1, 'Admin', 'sup', 'oyoyo', '2025-03-28 08:33:48', 4),
(54, 64, 'Admin', 'Welcome to Ben Harrison!', 'A heartfelt welcome to Ben Harrison Residence! We hope you have a fantastic stay with us. Our team is here to ensure your comfort and satisfaction. There will be a intial payment upon staying the apartment, please reach out us on Google Mail, BH.benharrisonofficial@gmail.com or go to the front desk if you need any assistance.', '2025-03-29 17:51:45', 3),
(55, 65, 'Admin', 'Welcome to Ben Harrison!', 'A heartfelt welcome to Ben Harrison Residence! We hope you have a fantastic stay with us. Our team is here to ensure your comfort and satisfaction. There will be a intial payment upon staying the apartment, please reach out us on Google Mail, BH.benharrisonofficial@gmail.com or go to the front desk if you need any assistance.', '2025-03-29 18:15:43', 3);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_type_id` varchar(50) NOT NULL,
  `payment_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_type_id`, `payment_type`) VALUES
('1', 'CASH IN'),
('2', 'GCASH (PAYMONGO)'),
('3', 'GCASH (QR)');

-- --------------------------------------------------------

--
-- Table structure for table `payment_tbl`
--

CREATE TABLE `payment_tbl` (
  `payment_id` int(50) NOT NULL,
  `tenant_id` varchar(50) NOT NULL,
  `contact_number` varchar(13) NOT NULL,
  `email_address` varchar(50) NOT NULL,
  `purpose_id` int(11) NOT NULL,
  `amount` varchar(50) NOT NULL,
  `payment_intent_id` varchar(50) DEFAULT NULL,
  `date_entry` datetime NOT NULL DEFAULT current_timestamp(),
  `date_paid` date DEFAULT current_timestamp(),
  `payment_type_id` int(50) NOT NULL,
  `status_id` int(50) NOT NULL COMMENT '5 = PAID 6 = NOT PAID 7 = REFUNDED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_tbl`
--

INSERT INTO `payment_tbl` (`payment_id`, `tenant_id`, `contact_number`, `email_address`, `purpose_id`, `amount`, `payment_intent_id`, `date_entry`, `date_paid`, `payment_type_id`, `status_id`) VALUES
(10061, '62', '09999999999', 'maplesyrup@gmail.com', 4, '111', '', '2025-03-19 21:47:41', NULL, 1, 2),
(10063, '61', '09999999999', 'quackers@gmail.com', 5, '123', 'pi_e65Yo44g6aeAJkgfboytxBQD', '2025-03-24 13:05:33', NULL, 2, 2),
(10066, '61', '09999999999', 'quackers@gmail.com', 4, '12', NULL, '2025-03-24 21:17:11', NULL, 1, 7),
(10068, '61', '09999999999', 'quackers@gmail.com', 5, '420', '', '2025-03-27 20:49:29', NULL, 1, 2),
(10070, '62', '09999999999', 'maplesyrup@gmail.com', 4, '20', '', '2025-03-27 20:54:11', NULL, 1, 2),
(10084, '62', '09999999999', 'maplesyrup@gmail.com', 4, '2222', NULL, '2025-03-27 21:18:03', NULL, 1, 2),
(10094, '1', '', 'brazzblood.m@gmail.com', 3, '123.2', NULL, '2025-03-29 13:38:10', NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `purpose`
--

CREATE TABLE `purpose` (
  `purpose_id` int(10) NOT NULL,
  `purpose_type` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purpose`
--

INSERT INTO `purpose` (`purpose_id`, `purpose_type`) VALUES
(1, 'Rent Bill'),
(2, 'Electricity Bill'),
(3, 'Water Bill'),
(4, 'Miscellaneous'),
(5, 'Maintenance');

-- --------------------------------------------------------

--
-- Table structure for table `reserve_tbl`
--

CREATE TABLE `reserve_tbl` (
  `reserve_id` int(255) NOT NULL,
  `res_fname` text NOT NULL,
  `res_lname` text NOT NULL,
  `res_email` text NOT NULL,
  `res_contact` varchar(13) NOT NULL,
  `date_requested` datetime NOT NULL DEFAULT current_timestamp(),
  `status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reserve_tbl`
--

INSERT INTO `reserve_tbl` (`reserve_id`, `res_fname`, `res_lname`, `res_email`, `res_contact`, `date_requested`, `status_id`) VALUES
(25, 'Kevin', 'Godking', 'kevinlaguitan2121@gmail.com', '09999999999', '2024-05-05 13:11:50', 2),
(34, 'deez', 'nuts', 'brazzblood.m@gmail.com', '09999999999', '2024-05-15 12:14:12', 1),
(45, 'Zyrus', 'Dela cruz', 'christianzyrusdelacruz@gmail.com', '09999999999', '2024-05-25 21:56:45', 1),
(46, 'Rimuru', 'Tempest', 'iamnotabadslime@jura.com', '09999999999', '2024-05-28 13:06:23', 2),
(47, 'peepee', 'poopoo', 'peepoo@gmail.com', '09999999999', '2024-05-28 14:19:40', 2),
(48, 'Zyrus', 'De la Cruz', 'quackquack@gmail.com', '09999999999', '2024-06-07 20:37:42', 2),
(51, 'deedee', 'doodoo', 'deedoo@gmail.com', '09999999999', '2024-06-17 22:41:17', 2),
(55, 'Harold', 'Tuturu', 'bunnyrabbit@gmail.com', '09999999999', '2024-07-02 14:42:49', 2),
(58, 'adad', 'adada', 'dudu@gmail.com', '09999999999', '2025-03-29 16:41:57', 0);

-- --------------------------------------------------------

--
-- Table structure for table `room_tbl`
--

CREATE TABLE `room_tbl` (
  `room_id` int(255) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `room_floor` varchar(50) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `date_in` datetime NOT NULL DEFAULT current_timestamp(),
  `date_out` datetime NOT NULL,
  `status_id` varchar(11) NOT NULL COMMENT '10 = offline 11 = online'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_tbl`
--

INSERT INTO `room_tbl` (`room_id`, `room_number`, `room_floor`, `tenant_id`, `date_in`, `date_out`, `status_id`) VALUES
(35, 'Unit 1', '1', 61, '2025-03-22 20:42:00', '2029-12-16 20:42:00', '11'),
(36, 'Unit 2', '2', 62, '2025-04-05 20:30:00', '2026-04-05 20:30:00', '11');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `status_id` int(11) NOT NULL,
  `status_type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`status_id`, `status_type`) VALUES
(0, 'PENDING'),
(1, 'ACCEPT'),
(2, 'CANCEL'),
(3, 'UNREAD'),
(4, 'READ'),
(5, 'PAID'),
(6, 'NOT PAID'),
(7, 'REFUNDED'),
(8, 'PARTIAL'),
(10, 'INACTIVE'),
(11, 'ACTIVE'),
(12, 'DISABLED'),
(13, 'COMPLETED'),
(14, 'INCOMPLETE'),
(15, 'AVAILABLE'),
(16, 'UNAVAILABLE'),
(17, 'BORROWED'),
(18, 'RETURNED'),
(19, 'DELETED');

-- --------------------------------------------------------

--
-- Table structure for table `tenant_mes_tbl`
--

CREATE TABLE `tenant_mes_tbl` (
  `t_message_id` int(255) NOT NULL,
  `tenant_id` int(255) NOT NULL,
  `sent_to` text NOT NULL,
  `subject` varchar(255) NOT NULL,
  `tenant_message` varchar(255) NOT NULL,
  `date_sent` datetime NOT NULL DEFAULT current_timestamp(),
  `status_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenant_mes_tbl`
--

INSERT INTO `tenant_mes_tbl` (`t_message_id`, `tenant_id`, `sent_to`, `subject`, `tenant_message`, `date_sent`, `status_id`) VALUES
(6, 62, 'Admin', 'Tengen', 'Hexachromatic', '2025-03-27 21:29:27', 4),
(7, 61, 'Admin', 'dada', 'dada', '2025-03-29 16:44:19', 4);

-- --------------------------------------------------------

--
-- Table structure for table `tenant_tbl`
--

CREATE TABLE `tenant_tbl` (
  `tenant_id` int(50) NOT NULL,
  `user_type_id` int(11) NOT NULL COMMENT '1 = admin 2 = tenant',
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `password` text NOT NULL,
  `contacts` varchar(13) NOT NULL,
  `email` text NOT NULL,
  `joined_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status_id` int(50) NOT NULL COMMENT 'status 11 = active 10 = inactive',
  `token` int(50) NOT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenant_tbl`
--

INSERT INTO `tenant_tbl` (`tenant_id`, `user_type_id`, `first_name`, `last_name`, `password`, `contacts`, `email`, `joined_date`, `status_id`, `token`, `token_expiry`) VALUES
(1, 1, 'Ben', 'Mag', '$2y$10$zyUKfls3NEz2N6MUQ.oJ..mS/gsIPAI0wq5tKvsPTi3J4XiyR6QiC', '', 'brazzblood.m@gmail.com', '2024-05-27 21:33:50', 11, 0, NULL),
(2, 1, 'godfrey', 'the first elden lord', '$2y$10$Oecu0P7U/dSJjFdWhU3qxecUhHGCCCWNRHKAelk30bPf7Qi3jfKTa', '09999999999', 'mama@gmail.com', '2024-05-28 16:28:56', 11, 0, NULL),
(61, 2, 'Zyrus', 'De la Cruz', '$2y$10$ZqDp.E.gmrHPKfX3Yu717ODTkF/Tif1bjta/vAMFHbAg30M1txKii', '09999999999', 'quackers@gmail.com', '2024-07-09 21:55:17', 11, 0, NULL),
(62, 2, 'Kevin', 'La', '$2y$10$zATvK/8RWjQKx482dX6ahO8W3Sr9YPDGMoyWT00.PgQ9qeJm67Vjy', '09999996666', 'maplesyrup@gmail.com', '2025-03-15 15:32:15', 11, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_tbl`
--

CREATE TABLE `user_tbl` (
  `id` int(255) NOT NULL,
  `user_type` varchar(50) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `email_add` text NOT NULL,
  `account_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_tbl`
--

INSERT INTO `user_tbl` (`id`, `user_type`, `username`, `password`, `email_add`, `account_created`) VALUES
(1, 'admin', 'gege', '4fd8ed3f6d0d460e38fde11a12f45240', 'gege@gmail.com', '2024-04-22 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_type`
--

CREATE TABLE `user_type` (
  `user_type_id` int(50) NOT NULL,
  `user_type` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_type`
--

INSERT INTO `user_type` (`user_type_id`, `user_type`) VALUES
(1, 'Admin'),
(2, 'Tenant');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrow_history`
--
ALTER TABLE `borrow_history`
  ADD PRIMARY KEY (`b_history_id`),
  ADD KEY `b_history_id` (`b_history_id`,`borrow_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `borrow_tbl`
--
ALTER TABLE `borrow_tbl`
  ADD PRIMARY KEY (`borrow_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `contact_tbl`
--
ALTER TABLE `contact_tbl`
  ADD PRIMARY KEY (`con_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `inventory_tbl`
--
ALTER TABLE `inventory_tbl`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`log_type_id`);

--
-- Indexes for table `log_tbl`
--
ALTER TABLE `log_tbl`
  ADD PRIMARY KEY (`tenant_log_id`),
  ADD KEY `log_type_id` (`log_type_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `log_tbl_b`
--
ALTER TABLE `log_tbl_b`
  ADD PRIMARY KEY (`borrow_log_id`),
  ADD KEY `log_type_id` (`log_type_id`),
  ADD KEY `borrow_id` (`borrow_id`);

--
-- Indexes for table `log_tbl_c`
--
ALTER TABLE `log_tbl_c`
  ADD PRIMARY KEY (`contact_log_id`) USING BTREE,
  ADD KEY `log_type_id` (`log_type_id`),
  ADD KEY `con_id` (`con_id`);

--
-- Indexes for table `log_tbl_i`
--
ALTER TABLE `log_tbl_i`
  ADD PRIMARY KEY (`inventory_log_id`);

--
-- Indexes for table `log_tbl_m`
--
ALTER TABLE `log_tbl_m`
  ADD PRIMARY KEY (`log_maint_id`),
  ADD KEY `log_type_id` (`log_type_id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `log_tbl_pay`
--
ALTER TABLE `log_tbl_pay`
  ADD PRIMARY KEY (`payment_log_id`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `log_type_id` (`log_type_id`);

--
-- Indexes for table `log_tbl_r`
--
ALTER TABLE `log_tbl_r`
  ADD PRIMARY KEY (`room_log_id`),
  ADD KEY `log_type_id` (`log_type_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `log_tbl_rs`
--
ALTER TABLE `log_tbl_rs`
  ADD PRIMARY KEY (`reserve_log_id`),
  ADD KEY `log_type_id` (`log_type_id`),
  ADD KEY `reserve_id` (`reserve_id`);

--
-- Indexes for table `maintenance_tbl`
--
ALTER TABLE `maintenance_tbl`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `message_tbl`
--
ALTER TABLE `message_tbl`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `tenant_id` (`tenant_id`) USING BTREE;

--
-- Indexes for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `payment_type_id` (`payment_type_id`),
  ADD KEY `miscellaneous_id` (`purpose_id`);

--
-- Indexes for table `reserve_tbl`
--
ALTER TABLE `reserve_tbl`
  ADD PRIMARY KEY (`reserve_id`),
  ADD KEY `status_id` (`status_id`) USING BTREE;

--
-- Indexes for table `room_tbl`
--
ALTER TABLE `room_tbl`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `tenant_id` (`tenant_id`) USING BTREE;

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `tenant_mes_tbl`
--
ALTER TABLE `tenant_mes_tbl`
  ADD PRIMARY KEY (`t_message_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `tenant_tbl`
--
ALTER TABLE `tenant_tbl`
  ADD PRIMARY KEY (`tenant_id`),
  ADD KEY `user_type_id` (`user_type_id`) USING BTREE,
  ADD KEY `active` (`status_id`);

--
-- Indexes for table `user_tbl`
--
ALTER TABLE `user_tbl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_type_id` (`user_type`);

--
-- Indexes for table `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`user_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrow_history`
--
ALTER TABLE `borrow_history`
  MODIFY `b_history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `borrow_tbl`
--
ALTER TABLE `borrow_tbl`
  MODIFY `borrow_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `contact_tbl`
--
ALTER TABLE `contact_tbl`
  MODIFY `con_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `inventory_tbl`
--
ALTER TABLE `inventory_tbl`
  MODIFY `item_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `log_tbl`
--
ALTER TABLE `log_tbl`
  MODIFY `tenant_log_id` int(50) NOT NULL AUTO_INCREMENT COMMENT '1000 = tenant logs', AUTO_INCREMENT=1266;

--
-- AUTO_INCREMENT for table `log_tbl_b`
--
ALTER TABLE `log_tbl_b`
  MODIFY `borrow_log_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `log_tbl_c`
--
ALTER TABLE `log_tbl_c`
  MODIFY `contact_log_id` int(50) NOT NULL AUTO_INCREMENT COMMENT '30000 = contact log', AUTO_INCREMENT=30018;

--
-- AUTO_INCREMENT for table `log_tbl_i`
--
ALTER TABLE `log_tbl_i`
  MODIFY `inventory_log_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70027;

--
-- AUTO_INCREMENT for table `log_tbl_m`
--
ALTER TABLE `log_tbl_m`
  MODIFY `log_maint_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `log_tbl_pay`
--
ALTER TABLE `log_tbl_pay`
  MODIFY `payment_log_id` int(50) NOT NULL AUTO_INCREMENT COMMENT '40000 = pay log', AUTO_INCREMENT=40076;

--
-- AUTO_INCREMENT for table `log_tbl_r`
--
ALTER TABLE `log_tbl_r`
  MODIFY `room_log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '50000 = room log', AUTO_INCREMENT=50024;

--
-- AUTO_INCREMENT for table `log_tbl_rs`
--
ALTER TABLE `log_tbl_rs`
  MODIFY `reserve_log_id` int(50) NOT NULL AUTO_INCREMENT COMMENT '60000 = reserve log', AUTO_INCREMENT=60014;

--
-- AUTO_INCREMENT for table `maintenance_tbl`
--
ALTER TABLE `maintenance_tbl`
  MODIFY `request_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `message_tbl`
--
ALTER TABLE `message_tbl`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  MODIFY `payment_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10095;

--
-- AUTO_INCREMENT for table `reserve_tbl`
--
ALTER TABLE `reserve_tbl`
  MODIFY `reserve_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `room_tbl`
--
ALTER TABLE `room_tbl`
  MODIFY `room_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tenant_mes_tbl`
--
ALTER TABLE `tenant_mes_tbl`
  MODIFY `t_message_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tenant_tbl`
--
ALTER TABLE `tenant_tbl`
  MODIFY `tenant_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `user_tbl`
--
ALTER TABLE `user_tbl`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `user_type`
--
ALTER TABLE `user_type`
  MODIFY `user_type_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
