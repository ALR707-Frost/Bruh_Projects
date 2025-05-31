-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 07, 2025 at 05:59 PM
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
-- Database: `bloodbankmanagement`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Contact_Info` varchar(255) DEFAULT NULL,
  `Office_ID` int(11) DEFAULT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Last_Login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_ID`, `Name`, `Email`, `Contact_Info`, `Office_ID`, `Username`, `Password`, `Last_Login`) VALUES
(1, 'Admin User', 'admin@bloodbank.com', '555-0000', NULL, 'admin', 'admin123', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `blood_requests`
--

CREATE TABLE `blood_requests` (
  `Request_ID` int(11) NOT NULL,
  `Doctor_ID` int(11) NOT NULL,
  `Blood_Type` varchar(5) NOT NULL,
  `Amount_ML` int(11) NOT NULL,
  `Reason` text NOT NULL,
  `Request_Date` datetime DEFAULT current_timestamp(),
  `Status` varchar(20) DEFAULT 'Pending',
  `Hospital_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blood_requests`
--

INSERT INTO `blood_requests` (`Request_ID`, `Doctor_ID`, `Blood_Type`, `Amount_ML`, `Reason`, `Request_Date`, `Status`, `Hospital_ID`) VALUES
(1, 5, 'A+', 150, 'road accident', '2025-01-07 22:32:00', 'Pending', 1);

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `Doctor_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Specialty` varchar(100) DEFAULT NULL,
  `Contact_Info` varchar(255) DEFAULT NULL,
  `Hospital_ID` int(11) DEFAULT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`Doctor_ID`, `Name`, `Specialty`, `Contact_Info`, `Hospital_ID`, `Username`, `Password`) VALUES
(5, 'Dr. Robert Chen', 'Hematology', '555-3001', 1, 'rchen', 'doc123'),
(6, 'Dr. Maria Garcia', 'Internal Medicine', '555-3002', 2, 'mgarcia', 'doc124'),
(7, 'Dr. David Kim', 'Surgery', '555-3003', 3, 'dkim', 'doc125');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `Donation_ID` int(11) NOT NULL,
  `Donor_ID` int(11) DEFAULT NULL,
  `Hospital_ID` int(11) DEFAULT NULL,
  `Donation_Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`Donation_ID`, `Donor_ID`, `Hospital_ID`, `Donation_Date`) VALUES
(1, 1, 1, '2024-01-15'),
(2, 2, 2, '2024-01-20'),
(3, 3, 3, '2024-01-10');

-- --------------------------------------------------------

--
-- Table structure for table `donor`
--

CREATE TABLE `donor` (
  `Donor_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Blood_Type` varchar(5) NOT NULL,
  `Phone_Number` varchar(15) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Age` int(11) DEFAULT NULL,
  `Medical_History` text DEFAULT NULL,
  `Last_Donation_Date` date DEFAULT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donor`
--

INSERT INTO `donor` (`Donor_ID`, `Name`, `Blood_Type`, `Phone_Number`, `Address`, `Age`, `Medical_History`, `Last_Donation_Date`, `Username`, `Password`) VALUES
(1, 'Emma Davis', 'A+', '555-1001', '100 Pine St, Boston', 28, 'No major issues', '2024-01-15', 'edavis', 'donor123'),
(2, 'James Brown', 'O-', '555-1002', '200 Maple Ave, NYC', 35, 'Allergies', '2024-01-20', 'jbrown', 'donor124'),
(3, 'Lisa Anderson', 'B+', '555-1003', '300 Cedar Ln, LA', 42, 'High BP history', '2024-01-10', 'landerson', 'donor125');

-- --------------------------------------------------------

--
-- Table structure for table `donor_notifications`
--

CREATE TABLE `donor_notifications` (
  `Notification_ID` int(11) NOT NULL,
  `Request_ID` int(11) NOT NULL,
  `Donor_ID` int(11) NOT NULL,
  `Status` varchar(20) DEFAULT 'Pending',
  `Notification_Date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital`
--

CREATE TABLE `hospital` (
  `Hospital_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `Contact_Info` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospital`
--

INSERT INTO `hospital` (`Hospital_ID`, `Name`, `Location`, `Contact_Info`) VALUES
(1, 'City General Hospital', '789 Hospital Dr, Boston', '617-555-2001'),
(2, 'Metropolitan Medical', '321 Health Ave, NYC', '212-555-2002'),
(3, 'West Coast Medical Center', '654 Care Blvd, LA', '213-555-2003');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `Patient_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Contact_Info` varchar(255) DEFAULT NULL,
  `Blood_Type` varchar(5) DEFAULT NULL,
  `Current_Disease` varchar(255) DEFAULT NULL,
  `Hospital_ID` int(11) DEFAULT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`Patient_ID`, `Name`, `Location`, `Contact_Info`, `Blood_Type`, `Current_Disease`, `Hospital_ID`, `Username`, `Password`) VALUES
(1, 'Tom Wilson', 'Boston', '555-4001', 'A-', 'Anemia', 1, 'twilson', 'pat123'),
(2, 'Carol White', 'NYC', '555-4002', 'O+', 'Surgery Recovery', 2, 'cwhite', 'pat124'),
(3, 'Kevin Lee', 'LA', '555-4003', 'B-', 'Leukemia', 3, 'klee', 'pat125');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_ID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `blood_requests`
--
ALTER TABLE `blood_requests`
  ADD PRIMARY KEY (`Request_ID`),
  ADD KEY `Doctor_ID` (`Doctor_ID`),
  ADD KEY `Hospital_ID` (`Hospital_ID`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`Doctor_ID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD KEY `Hospital_ID` (`Hospital_ID`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`Donation_ID`),
  ADD KEY `Donor_ID` (`Donor_ID`),
  ADD KEY `Hospital_ID` (`Hospital_ID`);

--
-- Indexes for table `donor`
--
ALTER TABLE `donor`
  ADD PRIMARY KEY (`Donor_ID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `donor_notifications`
--
ALTER TABLE `donor_notifications`
  ADD PRIMARY KEY (`Notification_ID`),
  ADD KEY `Request_ID` (`Request_ID`),
  ADD KEY `Donor_ID` (`Donor_ID`);

--
-- Indexes for table `hospital`
--
ALTER TABLE `hospital`
  ADD PRIMARY KEY (`Hospital_ID`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`Patient_ID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD KEY `Hospital_ID` (`Hospital_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `Admin_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blood_requests`
--
ALTER TABLE `blood_requests`
  MODIFY `Request_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `Doctor_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `Donation_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `donor`
--
ALTER TABLE `donor`
  MODIFY `Donor_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `donor_notifications`
--
ALTER TABLE `donor_notifications`
  MODIFY `Notification_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital`
--
ALTER TABLE `hospital`
  MODIFY `Hospital_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `Patient_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blood_requests`
--
ALTER TABLE `blood_requests`
  ADD CONSTRAINT `blood_requests_ibfk_1` FOREIGN KEY (`Doctor_ID`) REFERENCES `doctor` (`Doctor_ID`),
  ADD CONSTRAINT `blood_requests_ibfk_2` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospital` (`Hospital_ID`);

--
-- Constraints for table `doctor`
--
ALTER TABLE `doctor`
  ADD CONSTRAINT `doctor_ibfk_1` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospital` (`Hospital_ID`);

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`Donor_ID`) REFERENCES `donor` (`Donor_ID`),
  ADD CONSTRAINT `donations_ibfk_2` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospital` (`Hospital_ID`);

--
-- Constraints for table `donor_notifications`
--
ALTER TABLE `donor_notifications`
  ADD CONSTRAINT `donor_notifications_ibfk_1` FOREIGN KEY (`Request_ID`) REFERENCES `blood_requests` (`Request_ID`),
  ADD CONSTRAINT `donor_notifications_ibfk_2` FOREIGN KEY (`Donor_ID`) REFERENCES `donor` (`Donor_ID`);

--
-- Constraints for table `patient`
--
ALTER TABLE `patient`
  ADD CONSTRAINT `patient_ibfk_1` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospital` (`Hospital_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
