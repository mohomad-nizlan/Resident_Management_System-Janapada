-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Apr 11, 2025 at 12:14 PM
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
-- Database: `resident_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `nic` varchar(12) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `occupation` varchar(50) DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `registered_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`id`, `full_name`, `dob`, `nic`, `address`, `phone`, `email`, `occupation`, `gender`, `registered_date`) VALUES
(1, 'Jayasena Munasinghe', '1985-07-23', '857236789V', '12/3 Temple Road, Kandy', '0712345678', 'jayamuna@gmail.com', 'Teacher', 'Male', '2025-04-11 10:09:05'),
(2, 'Fatima Zuhara Niyas', '1991-10-31', '913016789012', '45 Beach Road, Kalmunai', '0773456789', 'fatima.z91@gmail.com', 'Housewife', 'Female', '2025-04-11 10:09:05'),
(3, 'Abdul Hameed Mohamed', '1975-09-05', '755096789V', '78 Main Street, Akurana', '0724567890', 'ahameed75@hotmail.com', 'Imam', 'Male', '2025-04-11 10:09:05'),
(4, 'Sunethra Perera', '1992-11-15', '921145678912', '45 Galle Road, Colombo 03', '0773456789', 'sunethra.p@yahoo.com', 'Nurse', 'Female', '2025-04-11 10:09:05'),
(5, 'Ravindra Bandara', '1978-03-30', '783045678V', '78/2 Flower Lane, Gampaha', '0724567890', 'ravib78@hotmail.com', 'Farmer', 'Male', '2025-04-11 10:09:05'),
(6, 'Kanchana Vickramasinghe', '1989-09-12', '891236789V', '23 Lake Drive, Anuradhapura', '0765678901', 'kanchana.w@outlook.com', 'Accountant', 'Female', '2025-04-11 10:09:05'),
(7, 'Premadasa Silva', '1995-05-05', '955056789123', '34 Temple Street, Matara', '0786789012', 'premasilva95@gmail.com', 'Software Engineer', 'Male', '2025-04-11 10:09:05'),
(8, 'Sivananthan Murugeshan', '1983-04-17', '834176789V', '12/4 Kovil Lane, Jaffna', '0717890123', 'sivan.m@yahoo.com', 'Fisherman', 'Male', '2025-04-11 10:09:05'),
(9, 'Anbuselvi Rajendran', '1990-12-08', '901286789012', '45 Hospital Road, Batticaloa', '0778901234', 'anbu.r90@gmail.com', 'Doctor', 'Female', '2025-04-11 10:09:05'),
(10, 'Karthikeyan Shanmugam', '1987-08-22', '872286789V', '78 Church Street, Trincomalee', '0729012345', 'karthi.s87@hotmail.com', 'Shop Owner', 'Male', '2025-04-11 10:09:05'),
(11, 'Maheswari Arunachalam', '1993-01-25', '932556789123', '23 School Lane, Vavuniya', '0760123456', 'mahesh.a93@outlook.com', 'Teacher', 'Female', '2025-04-11 10:09:05'),
(12, 'Prakash Chandrasekaran', '1980-06-19', '806196789V', '34 Market Road, Kilinochchi', '0781234567', 'prakash.c80@gmail.com', 'Driver', 'Male', '2025-04-11 10:09:05'),
(13, 'Mohamed Rizwan Farook', '1986-02-14', '862146789V', '12 Mosque Road, Beruwala', '0712345678', 'rizwan.f@yahoo.com', 'Businessman', 'Male', '2025-04-11 10:09:05'),
(14, 'Fathima Rifka Ismail', '1988-07-18', '881786789V', '23 Hill Street, Kattankudy', '0765678901', 'rifka.i88@outlook.com', 'Banker', 'Female', '2025-04-11 10:09:05'),
(15, 'Ibrahim Niyas Mohamed', '1994-04-22', '944226789123', '34 Garden Lane, Galle', '0786789012', 'ibrahim.nm@gmail.com', 'Engineer', 'Male', '2025-04-11 10:09:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nic` (`nic`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
