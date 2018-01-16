-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 16, 2018 at 04:19 AM
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
-- Table structure for table `operator`
--

CREATE TABLE `operator` (
  `EmployeeNumber` int(11) NOT NULL,
  `LastName` tinytext NOT NULL,
  `FirstName` tinytext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `operator`
--

INSERT INTO `operator` (`EmployeeNumber`, `LastName`, `FirstName`) VALUES
(170, 'Bolton', 'Rich'),
(200, 'Fowler', 'Jim'),
(268, 'Przybysz', 'Tom'),
(288, 'Williams', 'Lloyd'),
(344, 'Stenger', 'Jeremy'),
(346, 'Watt', 'Kevin'),
(320, 'Humphrey', 'Ron'),
(369, 'Fowler', 'Justin'),
(372, 'Burtch', 'Tony'),
(374, 'Dietert', 'Keith'),
(390, 'Friedlich', 'Bill'),
(392, 'Lucas', 'Maurice'),
(402, 'Bugnacki', 'Adam');

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
  `weight` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `panticket`
--

INSERT INTO `panticket` (`panTicketId`, `date`, `timeCardId`, `partNumber`, `materialNumber`, `weight`) VALUES
(1, '2018-01-12 12:41:27', 43, 101, 5002, 0),
(2, '2018-01-12 15:37:18', 42, 667, 14134, NULL),
(3, '2018-01-15 22:44:27', 41, 787, 9876, 350),
(4, '2018-01-15 22:54:16', 37, 354, 2345, 798);

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
(1, 'sensor1', 813, '2018-01-11 14:33:58', '2018-01-11 14:33:58', 50, NULL),
(2, 'sensor2', 814, '2018-01-11 14:34:05', '2018-01-11 14:34:05', 14, '2017-12-13 15:48:05'),
(3, 'sensor3', 0, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `timecard`
--

CREATE TABLE `timecard` (
  `TimeCard_ID` int(11) NOT NULL,
  `EmployeeNumber` int(11) NOT NULL,
  `Date` date NOT NULL,
  `JobNumber` int(11) NOT NULL,
  `WCNumber` int(11) NOT NULL,
  `OPPNumber` int(11) DEFAULT NULL,
  `SetupTime` int(11) NOT NULL,
  `RunTime` int(11) NOT NULL,
  `PanCount` int(11) NOT NULL,
  `PartsCount` int(11) NOT NULL,
  `ScrapCount` int(11) NOT NULL,
  `Comments` varchar(1024) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `timecard`
--

INSERT INTO `timecard` (`TimeCard_ID`, `EmployeeNumber`, `Date`, `JobNumber`, `WCNumber`, `OPPNumber`, `SetupTime`, `RunTime`, `PanCount`, `PartsCount`, `ScrapCount`, `Comments`) VALUES
(34, 402, '2017-12-05', 12, 821, NULL, 90, 75, 4, 78, 14, 'All done for the day!'),
(36, 372, '2017-12-11', 456, 616, NULL, 75, 15, 4, 1254, 11111, 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'),
(3, 200, '2017-02-08', 1234, 1234, 1234, 6, 1, 4, 1111, 1111, 'Fuck dat shit!'),
(5, 346, '2017-03-09', 12345, 987654321, NULL, 75, 150, 12, 1234, 23, 'Boo-ya!'),
(27, 390, '2017-11-10', 123, 608, NULL, 105, 165, 12, 12, 12, ''),
(7, 268, '2017-03-11', 789456, 987654321, NULL, 0, 0, 5, 789, 74, 'Fudgetastic!'),
(8, 268, '2017-03-11', 89632147, 987654321, NULL, 0, 0, 4, 789, 123, ''),
(25, 390, '2017-11-09', 789, 619, NULL, 60, 60, 4, 12, 45, ''),
(31, 402, '2017-12-05', 312, 616, NULL, 75, 15, 6, 7, 8, ''),
(24, 369, '2017-11-09', 123, 627, NULL, 60, 60, 4, 45, 56, ''),
(13, 288, '2017-10-10', 456, 987654321, NULL, 30, 105, 2, 4999, 784, 'Good luck mates!'),
(28, 288, '2017-11-10', 123, 820, NULL, 60, 60, 4, 123, 1, ''),
(22, 200, '2017-10-13', 123, 987654321, NULL, 60, 15, 8, 7, 1, ''),
(23, 346, '2017-11-08', 123, 1234567890, NULL, 60, 60, 4, 45, 12, ''),
(29, 268, '2017-11-13', 123, 614, NULL, 75, 15, 4, 12, 1, ''),
(32, 372, '2017-12-05', 122, 615, NULL, 75, 135, 4, 5, 6, 'Test this!'),
(33, 374, '2017-12-05', 12, 804, NULL, 60, 60, 4, 12, 4, 'Newest!'),
(35, 268, '2017-12-11', 123, 616, NULL, 15, 15, 3, 0, 1, ''),
(37, 170, '2017-12-11', 125, 617, NULL, 143, 180, 2, 125, 78, 'Test'),
(41, 402, '2017-12-12', 456, 617, NULL, 75, 150, 3, 777, 45, ''),
(42, 369, '2017-12-12', 1254, 800, NULL, 15, 135, 2, 47851, 14, ''),
(43, 344, '2017-12-15', 125, 625, NULL, 75, 225, 2, 45871, 41, 'Good job Jeremy!');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `Username` varchar(16) NOT NULL,
  `Password` varchar(16) NOT NULL,
  `permissions` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`Username`, `Password`, `permissions`) VALUES
('jnt', 'jnt', 2),
('mfg', 'mfg', 0),
('cmn', 'cmn', 1);

-- --------------------------------------------------------

--
-- Table structure for table `workcenter`
--

CREATE TABLE `workcenter` (
  `WCNumber` int(11) NOT NULL,
  `Name` varchar(30) NOT NULL,
  `Image` blob
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `workcenter`
--

INSERT INTO `workcenter` (`WCNumber`, `Name`, `Image`) VALUES
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
-- Indexes for table `operator`
--
ALTER TABLE `operator`
  ADD PRIMARY KEY (`EmployeeNumber`);

--
-- Indexes for table `panticket`
--
ALTER TABLE `panticket`
  ADD PRIMARY KEY (`panTicketId`);

--
-- Indexes for table `sensor`
--
ALTER TABLE `sensor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timecard`
--
ALTER TABLE `timecard`
  ADD PRIMARY KEY (`TimeCard_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Username`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `workcenter`
--
ALTER TABLE `workcenter`
  ADD PRIMARY KEY (`WCNumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `panticket`
--
ALTER TABLE `panticket`
  MODIFY `panTicketId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `sensor`
--
ALTER TABLE `sensor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `timecard`
--
ALTER TABLE `timecard`
  MODIFY `TimeCard_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
