-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2024 at 01:14 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `my database`
--

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dep_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dep_id`, `name`, `location`) VALUES
(1, 'ປະເພດຕຳ', 'ລາວ');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `E_ID` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Surname` varchar(255) DEFAULT NULL,
  `Gender` varchar(50) DEFAULT NULL,
  `Age` int(11) DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`E_ID`, `Name`, `Surname`, `Gender`, `Age`, `DateOfBirth`, `Address`, `PhoneNumber`, `Status`, `Email`) VALUES
(1, 'along', 'chalernsouk', 'ຊາຍ', 36, '2024-07-13', 'ຫ້ວຍເຕີ', '66489599', 'ແຕ່ງງານແລ້ວ', 'gftiff5@gmail.com'),
(2, 'along', 'chalernsouk', 'ຊາຍ', 36, '2024-07-14', 'ຫ້ວຍເຕີຍ', '66489599', 'ແຕ່ງງານແລ້ວ', 'gftiff5@gmail.com'),
(3, 'ghfuj', 'yhju', 'ຊາຍ', 55, '2024-07-18', 'ວັດໄຊ', '77945628', 'ມີເມຍແລ້ວ', 'brett37@example.orgghgfgfg');

-- --------------------------------------------------------

--
-- Table structure for table `staymember`
--

CREATE TABLE `staymember` (
  `P_ID` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Surname` varchar(255) DEFAULT NULL,
  `Gender` varchar(50) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `IDNumber` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `DayOfEntry` date DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Position` varchar(255) DEFAULT NULL,
  `Member` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staymember`
--

INSERT INTO `staymember` (`P_ID`, `Name`, `Surname`, `Gender`, `Status`, `PhoneNumber`, `IDNumber`, `Email`, `DayOfEntry`, `Address`, `Position`, `Member`) VALUES
(10, 'ArLong', 'Phouthavong', 'male', 'single', '12345678', '1234', 'cottonfleraykawaii@gmail.com', '2024-07-21', 'kkk', 'It Support', 'new_member'),
(12, 'ເອງ', 'ພົນ', 'male', 'ມີເມຍແລ້ວ', '15978625', '222', 'brett37@example.orgghgfgfg', '2024-07-22', 'ຫ້ວຍເຕີຍ', 'It Support', 'moves_in'),
(13, 'ຈັນພອນ', 'ໄຊຍະສີ', 'male', 'ແຕ່ງງານແລ້ວ', '96532787', '333', 'alongchalernsouk2020@gmail.com', '2024-07-22', 'ປາກທາງ', 'ຜູ້ຊ່ວຍ', 'moves_out'),
(14, 'ລັດ', 'ທີນະດາ', 'female', 'ໂສດ', '77997821', '4444', 'alongchalernsouk2020@gmail.com', '2024-07-22', 'ຫ້ວຍເຕີຍ', 'Cleaner', 'moves_in'),
(15, 'ດຳ', 'ໄຊທະນາ', 'male', 'ໂສດ', '99558866', '5555', 'brett37@example.orgghgfgfg', '2017-06-03', 'ສີວີໄລ່', 'HR', 'moves_in'),
(16, 'ເຈນ', 'ທະນະພົນ', 'female', 'ແຕ່ງງານແລ້ວ', '12345678', '66666', 'cottonfleraykawaii@gmail.com', '2015-07-31', 'ວັດໃຕ້', 'ຜູ້ຊ່ວຍ', 'alternate_member'),
(17, 'ກ່ຳ', 'ໄຊຍະວົງ', 'male', 'ໂສດ', '93993834', '7777', 'cottonfleraykawaii@gmail.com', '2024-03-04', 'ວັດໄຊ', 'It Support', 'complete_member');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(9, 'admin', '$2y$10$OuZopaAm3KG8F5OzLFjP1uirsb/lo82ffgbVIuEskHc/0wgynBfwO', 'admin', '2024-07-14 06:55:15'),
(10, 'qwe', '$2y$10$CltnO1QRse4gIbfBdDWsOurGD2PIkzKEuFxhQWsKUeIlF2JMcfhdC', 'admin', '2024-07-14 07:23:23'),
(12, 'wer', '$2y$10$U7uDyObNEwCqEH77.wuGAeve1U69BCbGqYRbAHCiRSp.mVYe0w3k6', 'user', '2024-07-20 06:59:52'),
(13, '345', '$2y$10$cRaRxzaBU3rA./JcMfc/SeZ79qLwVVqBjlmHb2SKUeAndXbBF1Y3G', 'user', '2024-07-24 03:48:53'),
(14, 'user', '$2y$10$ijBcgN4UMJGTlLTLhCp9wOEUZ2D4b3aCpiYn.tyv8G6vW9.IBDF0S', 'user', '2024-07-24 06:42:30'),
(16, 'Lao', '$2y$10$mcNT0g092JkzwYLpJs0wKOw/aRTF5EfQGNZCCLAgPNTBf0jsjhr5G', 'user', '2024-07-24 07:37:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dep_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`E_ID`);

--
-- Indexes for table `staymember`
--
ALTER TABLE `staymember`
  ADD PRIMARY KEY (`P_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `dep_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `E_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `staymember`
--
ALTER TABLE `staymember`
  MODIFY `P_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
