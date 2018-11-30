-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2018 at 06:39 PM
-- Server version: 5.7.14
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pptp`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `code` int(11) NOT NULL,
  `description` tinytext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`code`, `description`) VALUES
(1, 'All good'),
(2, 'Fixing another machine'),
(3, 'Meeting'),
(4, 'Moved to another machine'),
(5, 'No lube oil'),
(6, 'No tooling'),
(7, 'QC shut down');

-- --------------------------------------------------------

--
-- Table structure for table `job`
--

CREATE TABLE `job` (
  `jobNumber` varchar(16) NOT NULL,
  `creator` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `partNumber` varchar(16) NOT NULL,
  `wcNumber` int(11) NOT NULL,
  `cycleTime` double NOT NULL,
  `netPercentage` double NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `job`
--

INSERT INTO `job` (`jobNumber`, `creator`, `dateTime`, `partNumber`, `wcNumber`, `cycleTime`, `netPercentage`, `status`) VALUES
('M1975-1234', 999, '2018-02-20 06:00:00', 'M1975', 813, 2, 95, 1),
('M5432-6543', 1975, '2018-03-02 03:23:13', '', 630, 45, 80, 3),
('M4323-3214', 1975, '2018-03-02 03:32:08', '', 804, 12, 80, 3),
('M6564-24232', 1975, '2018-03-02 03:33:15', '', 804, 10, 57, 3),
('M5454-4443', 1975, '2018-03-08 23:56:58', 'M5454', 803, 3, 100, 1),
('M654-321', 1975, '2018-03-02 03:57:23', 'M654', 801, 15, 100, 3),
('M1234-5678', 1975, '2018-03-02 03:59:26', 'M1234', 608, 3, 80, 1),
('M888-777', 1975, '2018-03-05 02:55:43', 'M888', 812, 3, 100, 0),
('M67676-5432', 1975, '2018-04-06 15:54:48', 'M67676', 607, 2, 66.6, 0),
('M666-666', 1975, '2018-04-06 20:19:28', 'M666', 616, 3, 65, 0),
('M999-333', 1975, '2018-04-09 17:23:30', 'M999', 802, 13, 99, 1),
('M1111-1234', 1975, '2018-04-11 18:06:02', 'M1111', 800, 6, 85.5, 0),
('M6565-4444', 1975, '2018-05-03 18:58:21', 'M6565', 617, 2, 69.9, 1),
('M3333-3333', 1975, '2018-05-03 19:02:27', 'M3333', 615, 2.65, 95.5, 1),
('M3232-12d', 1975, '2018-06-27 18:07:38', 'M3232', 607, 2, 75, 3),
('M7676-12t', 1975, '2018-06-27 18:09:45', 'M7676', 619, 2, 100, 3),
('M3564-56R', 1975, '2018-06-27 18:13:37', 'M3564', 619, 1, 90, 0);

-- --------------------------------------------------------

--
-- Table structure for table `lineinspection`
--

CREATE TABLE `lineinspection` (
  `entryId` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `inspector` int(11) NOT NULL,
  `operator` int(11) NOT NULL,
  `jobNumber` varchar(16) NOT NULL,
  `wcNumber` int(11) NOT NULL,
  `inspection1` tinyint(1) NOT NULL,
  `inspection2` tinyint(1) NOT NULL,
  `inspection3` tinyint(1) NOT NULL,
  `inspection4` tinyint(1) NOT NULL,
  `comments` varchar(1024) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lineinspection`
--

INSERT INTO `lineinspection` (`entryId`, `dateTime`, `inspector`, `operator`, `jobNumber`, `wcNumber`, `inspection1`, `inspection2`, `inspection3`, `inspection4`, `comments`) VALUES
(1, '2018-11-12 00:00:00', 999, 170, 'M3333-3333', 615, 1, 1, 2, 1, 'Give this kid a promotion!'),
(6, '2018-11-14 14:22:56', 1975, 372, 'M999-333', 802, 1, 1, 1, 2, 'Looks like shite!');

-- --------------------------------------------------------

--
-- Table structure for table `panticket`
--

CREATE TABLE `panticket` (
  `panTicketId` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timeCardId` int(11) NOT NULL,
  `partNumber` int(11) NOT NULL,
  `materialNumber` int(11) NOT NULL,
  `weight` decimal(11,0) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `panticket`
--

INSERT INTO `panticket` (`panTicketId`, `date`, `timeCardId`, `partNumber`, `materialNumber`, `weight`) VALUES
(5, '2018-01-22 01:30:00', 27, 148, 2567, NULL),
(2, '2018-01-12 15:37:18', 42, 667, 14134, NULL),
(3, '2018-01-15 22:44:27', 41, 787, 9876, '350'),
(4, '2018-01-15 22:54:16', 37, 354, 2345, '798'),
(6, '2018-01-22 17:20:05', 25, 1112, 2222, '123'),
(7, '2018-01-21 12:00:00', 34, 1231, 3210, '542'),
(9, '2018-01-23 11:12:24', 53, 145, 2541, '1254'),
(10, '2018-01-24 02:33:27', 54, 111, 111, NULL),
(11, '2018-01-24 15:50:28', 43, 222, 222, '567'),
(12, '2018-01-24 16:44:40', 55, 444, 444, '1457'),
(13, '2018-01-24 08:28:38', 5, 145, 1254, '2365'),
(14, '2018-01-24 09:12:40', 56, 1256, 1254, '1258'),
(15, '2018-01-24 09:17:56', 7, 125, 4523, '126'),
(16, '2018-01-24 09:25:45', 8, 145, 254, '1'),
(17, '2018-01-24 09:31:43', 29, 452, 145, '1258'),
(18, '2018-01-24 09:56:20', 50, 1265, 1253, '1524'),
(19, '2018-01-25 15:41:50', 57, 587, 1458, '1547'),
(20, '2018-01-25 10:05:21', 33, 120, 4558, '1452'),
(21, '2018-01-26 13:28:40', 58, 125, 4587, '1547'),
(22, '2018-01-26 14:05:12', 49, 658, 4587, '214'),
(24, '2018-01-26 17:36:20', 60, 125, 4789, '1458'),
(25, '2018-01-26 08:00:16', 61, 5698, 4568, '125'),
(26, '2018-01-29 15:44:38', 63, 8542, 258, '500'),
(27, '2018-02-09 08:27:25', 65, 7894, 7894, '205'),
(28, '2018-02-12 17:54:23', 36, 147, 1256, '650'),
(29, '2018-02-12 20:33:38', 32, 165, 651, NULL),
(30, '2018-02-19 14:57:33', 69, 7458, 1569, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `partcount`
--

CREATE TABLE `partcount` (
  `sensorId` int(11) NOT NULL,
  `countType` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `count` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `partinspection`
--

CREATE TABLE `partinspection` (
  `partInspectionId` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `employeeNumber` int(11) NOT NULL DEFAULT '0',
  `wcNumber` int(11) NOT NULL DEFAULT '0',
  `partNumber` varchar(10) NOT NULL DEFAULT '',
  `partCount` int(11) NOT NULL DEFAULT '0',
  `failures` int(11) NOT NULL DEFAULT '0',
  `efficiency` decimal(10,0) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `partinspection`
--

INSERT INTO `partinspection` (`partInspectionId`, `dateTime`, `employeeNumber`, `wcNumber`, `partNumber`, `partCount`, `failures`, `efficiency`) VALUES
(32, '2018-02-14 00:23:00', 372, 624, 'M6539', 0, 0, '0'),
(33, '2018-02-14 00:29:00', 402, 828, 'M6731', 0, 2, '0'),
(20, '2018-02-13 10:56:00', 390, 626, 'M8276', 12, 0, '0'),
(34, '2018-02-14 00:29:00', 402, 828, 'M6731', 0, 2, '0'),
(18, '2018-02-13 10:55:00', 390, 615, 'M8263', 12, 0, '0'),
(17, '2018-02-13 10:53:00', 390, 627, 'M8261', 12, 0, '0'),
(9, '2018-02-12 10:45:00', 390, 627, 'M8261', 12, 1, '0'),
(10, '2018-02-12 17:47:00', 390, 627, 'M8261', 0, 0, '0'),
(11, '2018-02-12 10:47:00', 390, 615, 'M8262', 12, 0, '0'),
(12, '2018-02-12 16:22:00', 390, 615, 'M8263', 687, 0, '0'),
(13, '2018-02-12 10:49:00', 390, 626, 'M8276', 12, 1, '0'),
(14, '2018-02-12 17:49:00', 204, 626, 'M8276', 0, 0, '0'),
(15, '2018-02-09 16:06:00', 406, 624, 'M6539', 0, 0, '0'),
(35, '2018-02-13 18:02:00', 402, 802, 'M6459A', 0, 1, '0'),
(23, '2018-02-13 17:04:00', 390, 627, 'M8261', 3308, 1, '0'),
(24, '2018-02-13 17:10:00', 390, 626, 'M8276', 2809, 0, '0'),
(36, '2018-02-13 18:02:00', 402, 802, 'M6459A', 0, 1, '0'),
(27, '2018-02-13 17:23:00', 390, 615, 'M8263', 3360, 0, '0'),
(31, '2018-02-14 00:23:00', 372, 624, 'M6539', 0, 0, '0'),
(29, '2018-02-08 18:25:00', 344, 800, 'M8277', 0, 0, '0'),
(30, '2018-02-10 00:32:00', 402, 829, 'M6498A', 0, 5, '0'),
(37, '2018-02-13 18:05:00', 402, 802, 'M6459B', 0, 0, '0'),
(38, '2018-02-13 18:05:00', 402, 802, 'M6459B', 0, 0, '0'),
(39, '2018-02-14 00:29:00', 402, 828, 'M6731', 0, 4, '0'),
(40, '2018-02-14 00:29:00', 402, 828, 'M6731', 0, 4, '0'),
(41, '2018-02-13 15:10:00', 402, 829, 'M6498A', 0, 2, '0'),
(42, '2018-02-13 15:10:00', 402, 829, 'M6498A', 0, 2, '0'),
(43, '2018-02-13 15:13:00', 402, 829, 'M6498B', 0, 0, '0'),
(44, '2018-02-13 15:13:00', 402, 829, 'M6498B', 0, 0, '0'),
(45, '2018-02-13 18:02:00', 402, 802, 'M6459A', 0, 1, '0'),
(46, '2018-02-13 18:02:00', 402, 802, 'M6459A', 0, 1, '0'),
(47, '2018-02-13 18:05:00', 402, 802, 'M6459B', 0, 0, '0'),
(48, '2018-02-13 18:05:00', 402, 802, 'M6459B', 0, 0, '0'),
(49, '2018-02-13 17:10:00', 372, 626, 'M8276', 0, 0, '0'),
(50, '2018-02-13 17:10:00', 372, 626, 'M8276', 0, 0, '0'),
(51, '2018-02-13 17:04:00', 372, 627, 'M8261', 0, 2, '0'),
(52, '2018-02-13 17:04:00', 372, 627, 'M8261', 0, 2, '0'),
(53, '2018-02-13 17:23:00', 372, 615, 'M8263', 0, 0, '0'),
(54, '2018-02-13 17:23:00', 372, 615, 'M8263', 0, 0, '0'),
(55, '2018-02-14 00:29:00', 402, 829, 'M6731', 0, 5, '0'),
(56, '2018-02-14 00:29:00', 402, 829, 'M6731', 0, 5, '0'),
(57, '2018-02-13 15:10:00', 402, 829, 'M6498A', 0, 4, '0'),
(58, '2018-02-13 15:10:00', 402, 829, 'M6498A', 0, 4, '0'),
(59, '2018-02-13 15:13:00', 402, 829, 'M6498B', 0, 0, '0'),
(60, '2018-02-13 15:13:00', 402, 829, 'M6498B', 0, 0, '0'),
(61, '2018-02-13 18:02:00', 402, 802, 'M6459A', 0, 1, '0'),
(62, '2018-02-13 18:02:00', 402, 802, 'M6459A', 0, 1, '0'),
(63, '2018-02-13 18:05:00', 402, 802, 'M6459B', 0, 0, '0'),
(64, '2018-02-13 18:05:00', 402, 802, 'M6459B', 0, 0, '0'),
(65, '2018-02-14 21:22:00', 344, 806, 'M6459B', 0, 0, '0'),
(66, '2018-02-14 21:40:00', 344, 800, 'M8278', 0, 0, '0'),
(67, '2018-02-14 21:20:00', 344, 806, 'M6459A', 0, 0, '0'),
(68, '2018-02-15 00:00:00', 372, 625, 'M8265', 0, 1, '0'),
(69, '2018-02-05 15:43:00', 372, 630, 'M8044', 0, 1, '0'),
(70, '2018-02-15 01:23:00', 402, 802, 'M6459B', 0, 0, '0'),
(71, '2018-02-14 22:36:00', 402, 802, 'M6459A', 0, 2, '0'),
(72, '2018-02-14 22:32:00', 402, 828, 'M6731', 0, 1, '0'),
(73, '2018-02-14 21:22:00', 402, 802, 'M6459B', 0, 0, '0'),
(74, '2018-02-14 22:36:00', 402, 802, 'M6459A', 0, 2, '0'),
(75, '2018-02-14 12:09:00', 290, 615, 'M8263', 12, 0, '0'),
(76, '2018-02-14 13:23:00', 390, 627, 'M8261', 0, 5, '0'),
(77, '2018-02-14 13:26:00', 390, 626, 'M8276', 12, 0, '0'),
(78, '2018-02-14 17:26:00', 390, 615, 'M8263', 2700, 0, '0'),
(79, '2018-02-14 17:13:00', 390, 626, 'M8276', 2150, 0, '0'),
(80, '2018-02-14 12:32:00', 406, 625, 'M8265', 0, 0, '0'),
(81, '2018-02-15 00:00:00', 372, 625, 'M8265', 0, 11, '0'),
(82, '2018-02-15 11:00:00', 390, 627, 'M8261', 12, 1, '0'),
(83, '2018-02-15 11:03:00', 390, 626, 'M8276', 12, 0, '0'),
(84, '2018-02-15 11:05:00', 390, 615, 'M8263', 12, 0, '0'),
(85, '2018-02-15 14:10:00', 406, 630, 'M8044', 0, 0, '0'),
(86, '2018-02-15 17:55:00', 406, 630, 'M8044', 0, 0, '0'),
(87, '2018-02-15 18:58:00', 390, 626, 'M8276', 0, 0, '0'),
(88, '2018-02-15 21:32:00', 372, 625, 'M8265', 0, 0, '0'),
(89, '2018-02-15 22:21:00', 402, 802, 'M6459A', 0, 2, '0'),
(90, '2018-02-15 16:21:00', 402, 802, 'M6459B', 0, 4, '0'),
(91, '2018-02-15 22:20:00', 402, 828, 'M6731', 0, 4, '0'),
(92, '2018-02-15 11:44:00', 402, 829, 'M6585', 0, 2, '0'),
(93, '2018-02-15 14:04:00', 344, 800, 'M8278', 0, 0, '0'),
(94, '2018-02-15 22:21:00', 344, 805, 'M6459A', 0, 2, '0'),
(95, '2018-02-15 16:21:00', 344, 805, 'M6459B', 0, 4, '0'),
(96, '2018-02-15 17:27:00', 372, 615, 'M8263', 0, 1, '0'),
(97, '2018-02-15 22:20:00', 402, 828, 'M6731', 0, 5, '0'),
(98, '2018-02-15 11:44:00', 402, 829, 'M6585', 0, 3, '0'),
(99, '2018-02-15 18:58:00', 372, 626, 'M8276', 0, 0, '0'),
(100, '2018-02-15 21:32:00', 372, 625, 'M8265', 0, 1, '0'),
(101, '2018-02-16 10:42:00', 390, 615, 'M8263', 12, 0, '0'),
(102, '2018-02-16 10:43:00', 390, 626, 'M8276', 12, 0, '0'),
(103, '2018-02-16 12:28:00', 406, 630, 'M8044', 0, 0, '0'),
(104, '2018-02-16 15:44:00', 406, 630, 'M8044', 0, 0, '0'),
(105, '2018-02-16 15:46:00', 406, 625, 'M8265', 0, 0, '0'),
(106, '2018-02-16 17:36:00', 390, 626, 'M8276', 3203, 0, '0'),
(107, '2018-02-16 17:49:00', 390, 615, 'M8263', 0, 0, '0'),
(108, '2018-02-16 19:05:00', 406, 625, 'M8265', 0, 0, '0'),
(109, '2018-02-17 01:04:00', 372, 626, 'M8276', 0, 0, '0'),
(110, '2018-02-17 01:11:00', 372, 615, 'M8263', 0, 1, '0'),
(111, '2018-02-17 01:20:00', 372, 624, 'M8265', 0, 0, '0'),
(112, '2018-02-19 10:43:00', 390, 627, 'M8261', 12, 2, '0');

-- --------------------------------------------------------

--
-- Table structure for table `partwasher`
--

CREATE TABLE `partwasher` (
  `partWasherEntryId` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `employeeNumber` int(11) NOT NULL,
  `timeCardId` int(11) NOT NULL,
  `panCount` int(11) NOT NULL,
  `partCount` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `partwasher`
--

INSERT INTO `partwasher` (`partWasherEntryId`, `dateTime`, `employeeNumber`, `timeCardId`, `panCount`, `partCount`) VALUES
(8, '2018-02-16 05:34:42', 320, 78, 15, 1458),
(6, '2018-02-15 20:21:38', 392, 76, 21, 1254),
(29, '2018-05-03 20:41:46', 1975, 75, 12, 1254),
(27, '2018-05-03 20:40:43', 1975, 74, 12, 4563),
(20, '2018-04-09 15:51:56', 1975, 79, 12, 1458),
(24, '2018-05-03 20:35:51', 1975, 81, 25, 145),
(28, '2018-05-03 20:41:18', 1975, 82, 14, 142);

-- --------------------------------------------------------

--
-- Table structure for table `partweight`
--

CREATE TABLE `partweight` (
  `partWeightEntryId` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `employeeNumber` int(11) NOT NULL,
  `timeCardId` int(11) NOT NULL,
  `weight` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `partweight`
--

INSERT INTO `partweight` (`partWeightEntryId`, `dateTime`, `employeeNumber`, `timeCardId`, `weight`) VALUES
(7, '2018-04-09 17:29:42', 1975, 81, 145.3),
(3, '2018-04-05 21:31:14', 1975, 75, 1245.25),
(4, '2018-04-05 21:32:02', 416, 74, 145.24),
(5, '2018-04-06 20:23:58', 1975, 70, 58.2),
(6, '2018-04-06 20:49:22', 1975, 71, 125.3),
(8, '2018-04-20 19:45:18', 1975, 82, 125.6),
(17, '2018-06-15 14:58:30', 1975, 84, 125),
(16, '2018-05-03 20:33:43', 1975, 83, 147.2);

-- --------------------------------------------------------

--
-- Table structure for table `sensor`
--

CREATE TABLE `sensor` (
  `id` int(11) NOT NULL,
  `sensorId` varchar(32) NOT NULL,
  `wcNumber` int(11) DEFAULT NULL,
  `lastContact` datetime DEFAULT NULL,
  `lastCount` datetime DEFAULT NULL,
  `partCount` int(11) NOT NULL DEFAULT '0',
  `resetTime` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sensor`
--

INSERT INTO `sensor` (`id`, `sensorId`, `wcNumber`, `lastContact`, `lastCount`, `partCount`, `resetTime`) VALUES
(1, 'sensor1', 813, '2018-01-26 18:09:21', '2018-01-26 17:49:02', 72, NULL),
(2, 'sensor2', 814, '2018-01-26 17:49:43', '2018-01-25 10:37:14', 22, '2017-12-13 15:48:05'),
(3, 'sensor3', 0, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `timecard`
--

CREATE TABLE `timecard` (
  `timeCardId` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `employeeNumber` int(11) NOT NULL,
  `jobNumber` varchar(16) CHARACTER SET latin1 NOT NULL,
  `materialNumber` int(11) NOT NULL,
  `setupTime` int(11) NOT NULL,
  `runTime` int(11) NOT NULL,
  `panCount` int(11) NOT NULL,
  `partCount` int(11) NOT NULL,
  `scrapCount` int(11) NOT NULL,
  `commentCodes` int(11) NOT NULL DEFAULT '0',
  `comments` varchar(1024) NOT NULL,
  `approvedBy` int(11) NOT NULL DEFAULT '0',
  `approvedDateTime` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `timecard`
--

INSERT INTO `timecard` (`timeCardId`, `dateTime`, `employeeNumber`, `jobNumber`, `materialNumber`, `setupTime`, `runTime`, `panCount`, `partCount`, `scrapCount`, `commentCodes`, `comments`, `approvedBy`, `approvedDateTime`) VALUES
(70, '2018-03-04 00:00:00', 170, 'M1975-1234', 123, 60, 135, 25, 1234, 123, 1, 'Very first time card!', 0, NULL),
(71, '2018-03-08 23:51:34', 1975, 'M1234-5678', 456, 74, 134, 3, 7851, 14, 3, 'Better in stereo too!', 0, NULL),
(72, '2018-03-08 23:55:48', 1975, 'M888-777', 789, 270, 75, 25, 7854, 12, 2, 'You, you, the better part of me, me.', 0, NULL),
(73, '2018-03-08 23:59:55', 1975, 'M5454-4443', 123, 75, 210, 24, 4444, 142, 4, '', 0, NULL),
(74, '2018-03-09 00:16:04', 170, 'M1975-1234', 456, 60, 120, 2, 7894, 41, 7, '', 0, NULL),
(75, '2018-03-10 04:27:54', 1975, 'M888-777', 1253, 75, 60, 5, 1562, 14, 8, 'Boom, ba ba, boom!', 0, NULL),
(76, '2018-03-10 04:40:02', 1975, 'M1234-5678', 1452, 75, 255, 24, 1452, 14, 15, 'Testes!', 0, NULL),
(77, '2018-03-15 21:46:05', 1975, 'M5454-4443', 1258, 60, 60, 5, 458, 4, 0, 'Here we go!', 0, NULL),
(78, '2018-03-15 21:49:53', 1975, 'M888-777', 6587, 75, 330, 4, 7854, 5, 9, 'Here we go!', 0, NULL),
(79, '2018-04-06 16:08:44', 1975, 'M1975-1234', 1254, 15, 135, 25, 1254, 125, 9, 'Didn\'t have to use my AK.', 0, NULL),
(80, '2018-04-09 15:40:31', 1975, 'M1234-5678', 1258, 15, 75, 4, 145, 14, 1, '', 0, NULL),
(81, '2018-04-09 17:29:18', 1975, 'M999-333', 1254, 60, 135, 14, 4785, 12, 5, 'A fine day.', 0, NULL),
(82, '2018-04-13 21:31:51', 1975, 'M999-333', 1478, 0, 120, 5, 452, 12, 8, '', 0, NULL),
(83, '2018-04-20 20:41:22', 170, 'M1234-5678', 1256, 0, 75, 12, 7000, 12, 15, 'Oil me, bro!', 0, NULL),
(84, '2018-06-15 14:29:36', 1975, 'M1234-5678', 5556, 60, 135, 14, 1452, 12, 1, '', 1975, NULL),
(85, '2018-06-27 15:07:19', 1975, 'M1234-5678', 5566, 15, 135, 14, 1452, 123, 15, 'Can I have a day off?', 1975, NULL),
(86, '2018-06-27 20:06:19', 1975, 'M1975-1234', 5555, 0, 60, 12, 1458, 111, 39, '', 0, NULL),
(87, '2018-06-27 21:07:58', 1975, 'M6565-4444', 1452, 15, 330, 15, 144, 12, 71, '', 1975, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `employeeNumber` int(11) NOT NULL,
  `username` varchar(16) NOT NULL,
  `password` varchar(16) NOT NULL,
  `roles` int(11) NOT NULL,
  `permissions` int(11) NOT NULL DEFAULT '0',
  `firstName` varchar(16) NOT NULL,
  `lastName` varchar(16) NOT NULL,
  `email` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`employeeNumber`, `username`, `password`, `roles`, `permissions`, `firstName`, `lastName`, `email`) VALUES
(170, 'rbolton', 'rbolton', 3, 268, 'Rich', 'Bolton', ''),
(1975, 'jtost', 'jtost', 1, 65535, 'Jason', 'Tost', 'jasontost@gmail.com'),
(999, 'cneal', 'cneal', 2, 65535, 'Craig', 'Neal', 'cnealjr@pittsburghprecision.com'),
(274, 'jcrusan', 'jcrusan', 5, 8396, 'Jesse', 'Crusan', 'jcrusan@pittsburghprecision.com'),
(200, 'jfowler', 'jfowler', 3, 268, 'Jim', 'Fowler', ''),
(268, 'tprzybysz', 'tprzybysz', 3, 268, 'Tom', 'Przybysz', ''),
(288, 'lwilliams', 'lwilliams', 3, 268, 'Lloyd', 'Williams', ''),
(344, 'jstenger', 'jstenger', 3, 268, 'Jeremy', 'Stenger', ''),
(346, 'kwatt', 'kwatt', 3, 268, 'Kevin', 'Watt', ''),
(320, 'rhumphrey', 'rhumphrey', 3, 460, 'Ron', 'Humphrey', ''),
(369, 'jfowler2', 'jfowler2', 3, 268, 'Justin', 'Fowler', ''),
(372, 'tburtch', 'tburtch', 3, 268, 'Tony', 'Burtch', ''),
(374, 'kdietert', 'kdietert', 3, 460, 'Keith', 'Dietert', ''),
(390, 'bfriedlich', 'bfriedlich', 3, 8460, 'Bill', 'Friedlich', ''),
(392, 'mlucas', 'mlucas', 3, 268, 'Maurice', 'Lucas', ''),
(402, 'abugnacki', 'abugnacki', 3, 268, 'Adam', 'Bugnacki', ''),
(414, 'jholmes', 'jholmes', 5, 8396, 'Jim', 'Holmes', ''),
(366, 'cmccambridge', 'cmccambridge', 5, 8396, 'Chris', 'McCambridge', ''),
(410, 'bgentile', 'bgentile', 5, 8396, 'Ben', 'Gentile', ''),
(416, 'mneal', 'mneal', 5, 8396, 'Morris', 'Neal', ''),
(998, 'jorbin', 'jorbin', 2, 65535, 'Jason', 'Orbin', 'jorbin@pittsburghprecision.com');

-- --------------------------------------------------------

--
-- Table structure for table `workcenter`
--

CREATE TABLE `workcenter` (
  `wcNumber` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `image` blob
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `workcenter`
--

INSERT INTO `workcenter` (`wcNumber`, `name`, `image`) VALUES
(813, 'Machine 1', NULL),
(812, 'Machine 2', NULL),
(811, '', NULL),
(801, '', NULL),
(820, '', NULL),
(821, '', NULL),
(829, '', NULL),
(828, '', NULL),
(802, '', NULL),
(806, '', NULL),
(805, '', NULL),
(804, '', NULL),
(803, '', NULL),
(800, '', NULL),
(607, '', NULL),
(608, '', NULL),
(616, '', NULL),
(617, '', NULL),
(619, '', NULL),
(622, '', NULL),
(630, '', NULL),
(624, '', NULL),
(625, '', NULL),
(614, '', NULL),
(615, '', NULL),
(626, '', NULL),
(627, '', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`code`);

--
-- Indexes for table `job`
--
ALTER TABLE `job`
  ADD PRIMARY KEY (`jobNumber`);

--
-- Indexes for table `lineinspection`
--
ALTER TABLE `lineinspection`
  ADD PRIMARY KEY (`entryId`);

--
-- Indexes for table `panticket`
--
ALTER TABLE `panticket`
  ADD PRIMARY KEY (`panTicketId`);

--
-- Indexes for table `partinspection`
--
ALTER TABLE `partinspection`
  ADD PRIMARY KEY (`partInspectionId`);

--
-- Indexes for table `partwasher`
--
ALTER TABLE `partwasher`
  ADD PRIMARY KEY (`partWasherEntryId`);

--
-- Indexes for table `partweight`
--
ALTER TABLE `partweight`
  ADD PRIMARY KEY (`partWeightEntryId`);

--
-- Indexes for table `sensor`
--
ALTER TABLE `sensor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timecard`
--
ALTER TABLE `timecard`
  ADD PRIMARY KEY (`timeCardId`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`employeeNumber`);

--
-- Indexes for table `workcenter`
--
ALTER TABLE `workcenter`
  ADD PRIMARY KEY (`wcNumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lineinspection`
--
ALTER TABLE `lineinspection`
  MODIFY `entryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `panticket`
--
ALTER TABLE `panticket`
  MODIFY `panTicketId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT for table `partinspection`
--
ALTER TABLE `partinspection`
  MODIFY `partInspectionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;
--
-- AUTO_INCREMENT for table `partwasher`
--
ALTER TABLE `partwasher`
  MODIFY `partWasherEntryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `partweight`
--
ALTER TABLE `partweight`
  MODIFY `partWeightEntryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `sensor`
--
ALTER TABLE `sensor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `timecard`
--
ALTER TABLE `timecard`
  MODIFY `timeCardId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
