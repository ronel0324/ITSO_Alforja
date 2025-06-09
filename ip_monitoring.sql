-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 04:31 PM
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
-- Database: `ip_monitoring`
--

-- --------------------------------------------------------

--
-- Table structure for table `intellectual_properties`
--

CREATE TABLE `intellectual_properties` (
  `ip_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `authors` varchar(255) DEFAULT NULL,
  `classification` enum('Copyright','Trademark','Patent','Utility Model','Industrial Design') NOT NULL,
  `endorsement_letter` varchar(255) DEFAULT NULL,
  `status` enum('Ongoing','Pending','Completed') NOT NULL,
  `application_form` varchar(255) DEFAULT NULL,
  `submitted` tinyint(1) DEFAULT 0,
  `application_fee` varchar(255) DEFAULT NULL,
  `issued_certificate` varchar(255) DEFAULT NULL,
  `project_file` varchar(255) DEFAULT NULL,
  `authors_file` varchar(255) NOT NULL,
  `date_submitted_to_ipophil` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `department` enum('CCS','CTE','CFND','CIT','COA','CAS','CBAA','COE','CCJE','COF','CHMT','CNAH') NOT NULL,
  `applicant_name` varchar(255) NOT NULL,
  `date_submitted_to_itso` date DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `intellectual_properties`
--

INSERT INTO `intellectual_properties` (`ip_id`, `title`, `authors`, `classification`, `endorsement_letter`, `status`, `application_form`, `submitted`, `application_fee`, `issued_certificate`, `project_file`, `authors_file`, `date_submitted_to_ipophil`, `expiration_date`, `department`, `applicant_name`, `date_submitted_to_itso`, `email`) VALUES
(1, 'ITSO TRACKING SYSTEM', 'Alforja, Ronel Joshua, Decena, Mark Raniel, Espinosa, Vincent, Eusebio, Josefa Paula', 'Copyright', 'ENDORSEMENT LETTER.docx', 'Pending', NULL, 0, NULL, NULL, NULL, '', '0000-00-00', NULL, 'CCS', 'ronel Alforja', '2025-06-09', 'ronelalforja@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Coordinator') NOT NULL,
  `department_id` int(11) NOT NULL,
  `department` varchar(50) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `department_id`, `department`, `status`) VALUES
(3, 'admin', '$2y$10$UtdMm9A7b9l2Ebr4ReBbI.KNT/PcCRs9Fy86GIrnYWeewbtWbWt/i', 'Admin', 0, ' ', 'approved'),
(12, 'CCS', '$2y$10$EBYRI2ufgqySkHIikDRh5OBdrq47nQPpwgg3as7S7x.nCrNEstP4O', 'Coordinator', 0, 'CCS', 'approved'),
(13, 'CTE', '$2y$10$.j5D8UzEpXBD7tmmnQvsLemubFooTT6IhnKbOZZnYJdGeAyq3O2L.', 'Coordinator', 0, 'CTE', 'approved'),
(14, 'CFND', '$2y$10$p.9ZOivL..i0tJE6y.MvT.A38YcO8wuvWbNlvl7tlA3XGV0AEZLCO', 'Coordinator', 0, 'CFND', 'approved'),
(15, 'CIT', '$2y$10$ynI9vxEOpdYilr2XXPMrkOAOWeYb5ZrfUaalfJ2IWCoQioCDir9oC', 'Coordinator', 0, 'CIT', 'approved'),
(16, 'COA', '$2y$10$2sdibrjifuJqNeUCa2YmfOlKy.GKW8vQVmjj2gjecmbLGDh1wv0Di', 'Coordinator', 0, 'COA', 'approved'),
(17, 'CAS', '$2y$10$gUvn.37xCJ2wQ/rTOH3lR.U/3v5ecVeuF6SXGvkMURJTkGO7J7s/q', 'Coordinator', 0, 'CAS', 'approved'),
(18, 'CBAA', '$2y$10$ywSHUgD5ehHU/SGA6n6PC.BLbjJSO/KbKIYLxcpHoS3fvSpG/68ha', 'Coordinator', 0, 'CBAA', 'approved'),
(19, 'COE', '$2y$10$qoPi27013TF8kGFmUhTCp.morDpH3kk00Ciq.4FTCXpunuqZgx.ua', 'Coordinator', 0, 'COE', 'approved'),
(20, 'CCJE', '$2y$10$Y1rd9Ihbj6ziHKIfmiJfvOHQKC/4ofH0Ys9mWEr685WsjiOAuhDU2', 'Coordinator', 0, 'CCJE', 'approved'),
(21, 'COF', '$2y$10$X4RPycaayUgUKbY3CaBiF.jWFprT.LRYJS4.bAW2XWcAAcq8TXjXO', 'Coordinator', 0, 'COF', 'approved'),
(22, 'CHMT', '$2y$10$bd6NOrgyEaXk9mRXNvWd6emfdQV3szhzCRikiWX51SxrQFxelT6i6', 'Coordinator', 0, 'CHMT', 'approved'),
(23, 'CNAH', '$2y$10$eyjVEoxIBHB0897K4ZkX.eosIxjPIlg.AohVCKonz7CcthuR5ZkEC', 'Coordinator', 0, 'CNAH', 'approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `intellectual_properties`
--
ALTER TABLE `intellectual_properties`
  ADD PRIMARY KEY (`ip_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `intellectual_properties`
--
ALTER TABLE `intellectual_properties`
  MODIFY `ip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
