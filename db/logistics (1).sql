-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 21, 2025 at 08:45 PM
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
-- Database: `logistics`
--

-- --------------------------------------------------------

--
-- Table structure for table `fuel`
--

CREATE TABLE `fuel` (
  `id` int(11) NOT NULL,
  `type` varchar(140) NOT NULL,
  `uplt` varchar(89) NOT NULL,
  `discount` int(11) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'current',
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `fuel`
--

INSERT INTO `fuel` (`id`, `type`, `uplt`, `discount`, `status`, `date`) VALUES
(11, 'Diesel', '1980', 20, 'active', '2025-09-17 22:43:57'),
(12, 'Petrol', '1760', 40, 'active', '2025-09-21 17:21:33');

-- --------------------------------------------------------

--
-- Table structure for table `fuel_request`
--

CREATE TABLE `fuel_request` (
  `req_id` int(11) NOT NULL,
  `stf_code` varchar(50) NOT NULL,
  `requested_date` date NOT NULL,
  `head_mission` varchar(255) NOT NULL,
  `driver_name` varchar(100) NOT NULL,
  `vehicle_type` varchar(50) NOT NULL,
  `plate_number` varchar(20) NOT NULL,
  `location_from` varchar(255) NOT NULL,
  `location_to` varchar(255) NOT NULL,
  `kilometer` int(11) DEFAULT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `fuel_type` varchar(50) NOT NULL,
  `requested_qty` decimal(10,2) NOT NULL,
  `received_qty` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `verified_by` varchar(50) DEFAULT '-',
  `approved_by` varchar(50) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fuel_request`
--

INSERT INTO `fuel_request` (`req_id`, `stf_code`, `requested_date`, `head_mission`, `driver_name`, `vehicle_type`, `plate_number`, `location_from`, `location_to`, `kilometer`, `date_from`, `date_to`, `fuel_type`, `requested_qty`, `received_qty`, `price`, `status`, `verified_by`, `approved_by`, `signature`, `created_at`, `updated_at`) VALUES
(1, '95', '2025-09-18', 'Iragena Fabrice', 'Louise', 'TOYOTA HILUX', 'RAE449P', 'Kicukiro', 'Nyanza', 106, '2025-09-18', '2025-09-18', 'Diesel', 27.00, 0.00, 53460.00, 'pending', '-', '-', 'uploads/signature_68cb3bda4e6568.31045919.jpg', '2025-09-17 22:53:14', '2025-09-17 22:53:14'),
(2, '114', '2025-09-18', 'MAHORO PEACE', 'Peace', 'TOYOTA HILUX', 'RAE450P', 'Gasabo', 'Nyamagabe', 134, '2025-09-17', '2025-09-19', 'Diesel', 34.00, 50.00, 99000.00, 'approved', 'MUYENZI SIMPUNGA', 'Maj. A. KARASIRA', NULL, '2025-09-17 23:10:46', '2025-09-17 23:15:18'),
(3, '114', '2025-09-18', 'MAHORO PEACE', 'Louise', 'TOYOTA HILUX', 'RAE450P', 'Rutsiro', 'Rubavu', 56, '2025-09-17', '2025-09-19', 'Diesel', 14.00, 0.00, 27720.00, 'pending', '-', '-', 'uploads/signature_68cb4023436069.45223033.jpg', '2025-09-17 23:11:31', '2025-09-17 23:11:31'),
(4, '115', '2025-09-18', 'UMWIZA JANCY', 'Louise', 'TOYOTA HILUX', 'RAE450P', 'Huye', 'Huye', 0, '2025-09-18', '2025-09-19', 'Diesel', 0.00, 5.00, 9900.00, 'pending', 'MUYENZI SIMPUNGA', '-', NULL, '2025-09-18 13:11:47', '2025-09-18 13:18:36'),
(5, '110', '2025-09-21', 'KANYANA LOUISE', 'Louise', 'TOYOTA HILUX', 'RAF553E', 'Muhanga', 'Huye', 80, '2025-09-21', '2025-09-23', 'Diesel', 20.00, 10.00, 19800.00, 'approved', 'MUYENZI SIMPUNGA', 'Maj. A. KARASIRA', 'uploads/signature_68d0243ceb81a3.89936002.jpg', '2025-09-21 16:13:48', '2025-09-21 16:53:17'),
(6, '110', '2025-09-21', 'KANYANA LOUISE', 'Elisa', 'TOYOTA HILUX', 'RDF198P', 'Gasabo', 'Ruhango', 92, '2025-09-22', '2025-09-30', 'Petrol', 26.00, 30.00, 0.00, 'pending', 'MUYENZI SIMPUNGA', '-', NULL, '2025-09-21 16:34:24', '2025-09-21 16:41:04');

-- --------------------------------------------------------

--
-- Table structure for table `operation`
--

CREATE TABLE `operation` (
  `id` int(11) NOT NULL,
  `fuel` varchar(50) NOT NULL,
  `litter` double NOT NULL,
  `required_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `prepared_by` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `operation`
--

INSERT INTO `operation` (`id`, `fuel`, `litter`, `required_date`, `description`, `prepared_by`, `created_at`, `status`) VALUES
(1, 'Petrol', 30, '2025-09-18', 'Test Operational administrative tasks', 'Iragena Fabrice', '2025-09-18 01:15:55', 'approved'),
(2, 'Diesel', 60, '2025-09-18', 'Test Operational administrative tasks', 'Iragena Fabrice', '2025-09-18 00:44:37', 'pending'),
(3, 'Petrol', 100, '2025-09-21', 'Requesting fuel for operation', 'MUYENZI SIMPUNGA', '2025-09-21 19:29:54', 'approved'),
(4, 'Diesel', 150, '2025-09-21', 'Requesting fuel for operation', 'MUYENZI SIMPUNGA', '2025-09-21 19:27:26', 'rejected');

-- --------------------------------------------------------

--
-- Table structure for table `operation_report`
--

CREATE TABLE `operation_report` (
  `id` int(11) NOT NULL,
  `driver` varchar(255) NOT NULL,
  `op_car` varchar(50) NOT NULL,
  `op_from` varchar(255) NOT NULL,
  `op_to` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `operation_report`
--

INSERT INTO `operation_report` (`id`, `driver`, `op_car`, `op_from`, `op_to`, `date`, `description`) VALUES
(1, 'Elisa', 'RAG154X', 'Nyagatare', 'Ngahanga', '2025-09-18 00:00:00', 'Here we Test'),
(2, 'Louise', 'RAB145K', 'Gasabo', 'Kicukiro', '2025-09-21 00:00:00', 'Kugura Yogurt');

-- --------------------------------------------------------

--
-- Table structure for table `parts`
--

CREATE TABLE `parts` (
  `part_id` int(10) UNSIGNED NOT NULL,
  `record_id` int(10) UNSIGNED NOT NULL,
  `part_name` varchar(100) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parts`
--

INSERT INTO `parts` (`part_id`, `record_id`, `part_name`, `quantity`, `unit_price`) VALUES
(1, 1, 'Glace', 4, 150000.00),
(2, 1, 'Tyer', 4, 100000.00),
(3, 2, 'wheels', 2, 50000.00),
(4, 2, 'Glasses', 4, 150000.00);

-- --------------------------------------------------------

--
-- Table structure for table `quick_action`
--

CREATE TABLE `quick_action` (
  `action_id` int(11) NOT NULL,
  `head_mission` varchar(255) NOT NULL,
  `driver` varchar(255) NOT NULL,
  `plate_no` varchar(50) NOT NULL,
  `fuel` double NOT NULL,
  `price` double NOT NULL,
  `origin` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `prepared_by` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quick_action`
--

INSERT INTO `quick_action` (`action_id`, `head_mission`, `driver`, `plate_no`, `fuel`, `price`, `origin`, `destination`, `description`, `prepared_by`, `created_at`) VALUES
(1, 'Elisa', 'Louise', 'RAG154X', 20, 0, 'Kicukiro', 'Nyarungenge', 'Testing the working', 'Iragena Fabrice', '2025-09-18 00:39:45'),
(2, 'Louise', 'Elisa', 'RAG154X', 5, 9900, 'Kicukiro', 'Nyarungenge', 'testing description', 'MUYENZI SIMPUNGA', '2025-09-21 19:23:51');

-- --------------------------------------------------------

--
-- Table structure for table `request_repair`
--

CREATE TABLE `request_repair` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service` varchar(255) NOT NULL,
  `createdAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_repair`
--

INSERT INTO `request_repair` (`id`, `user_id`, `service`, `createdAt`) VALUES
(1, 112, 'Repair service breake', '2025-09-18 16:25:38'),
(2, 112, 'Cractch repaire', '2025-09-18 16:25:38'),
(3, 96, 'Service Break', '2025-09-21 19:40:07'),
(4, 96, 'Replace Glace', '2025-09-21 19:40:07'),
(5, 96, 'Repair of Wheels', '2025-09-21 19:40:07');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(10) UNSIGNED NOT NULL,
  `record_id` int(10) UNSIGNED NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `record_id`, `service_type`, `description`) VALUES
(1, 1, 'Repair', 'Glaces Replacement'),
(2, 2, 'repair', 'Repair Wheels and add new'),
(3, 2, 'Service Breake', 'repair Service break'),
(4, 2, 'Replacement of Glass', 'Replacement of Glass description');

-- --------------------------------------------------------

--
-- Table structure for table `service_records`
--

CREATE TABLE `service_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `license_plate` varchar(20) NOT NULL,
  `make` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `total_parts` decimal(10,2) DEFAULT NULL,
  `grand_total` decimal(10,2) DEFAULT NULL,
  `service_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_records`
--

INSERT INTO `service_records` (`id`, `license_plate`, `make`, `model`, `notes`, `total_parts`, `grand_total`, `service_date`) VALUES
(1, 'RAG154X', 'USA', 'KIA', 'Test the record', 1000000.00, 1000000.00, '2025-09-18'),
(2, 'RAB145K', 'USA', 'KIA', 'All services are done', 700000.00, 700000.00, '2025-09-21');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `stf_code` int(11) NOT NULL,
  `stf_names` varchar(50) NOT NULL,
  `stf_phoneno` varchar(12) NOT NULL,
  `stf_pwd` varchar(40) NOT NULL,
  `stf_pwd_vis` varchar(100) NOT NULL,
  `stf_gender` varchar(1) NOT NULL,
  `stf_fpno` int(2) NOT NULL,
  `stf_email` varchar(100) NOT NULL,
  `stf_position` varchar(50) NOT NULL,
  `locatio` varchar(50) NOT NULL,
  `stf_degree` varchar(100) NOT NULL,
  `stf_field` varchar(100) NOT NULL,
  `stf_univ` varchar(100) NOT NULL,
  `stf_byear` varchar(15) NOT NULL,
  `stf_prov` varchar(50) NOT NULL,
  `stf_dist` varchar(50) NOT NULL,
  `stf_sect` varchar(50) NOT NULL,
  `stf_photo` varchar(50) NOT NULL,
  `stf_dorecruit` varchar(20) NOT NULL,
  `stf_status` varchar(15) NOT NULL,
  `Token` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`stf_code`, `stf_names`, `stf_phoneno`, `stf_pwd`, `stf_pwd_vis`, `stf_gender`, `stf_fpno`, `stf_email`, `stf_position`, `locatio`, `stf_degree`, `stf_field`, `stf_univ`, `stf_byear`, `stf_prov`, `stf_dist`, `stf_sect`, `stf_photo`, `stf_dorecruit`, `stf_status`, `Token`) VALUES
(78, 'fab', '250788643043', 'cad06f3c4901bbcd4a396dd83c4544a146d6e3e8', '228', 'M', 0, '', 'HR', '', '', '', '', '', '', '', '', 'dc1c83c35305cba33149b77bdbe73a7c.jpg', '', 'Active', 'dfOmJEPKQ4CdUkvmmNZJ4E:APA91bHG9QIVYDeCISZp_tW3T4_JuK8Jb-UDlXQkkPSr_WfCtaC9ZxWxer53l_LrpEYJOU1vJ6o8XCG8ESj6DuZoOEgLXQXuJQBY7t8r0w1NekngIsDrdOO7IiZg9PqeaBVwulHhaVW5'),
(95, 'Iragena Fabrice', '250785149563', 'f04b1d726c615672552fa5116aa5b958d8d41676', '448', 'M', 0, '', 'Logistics', 'Nyakabanda', '', '', '', '', '', '', '', 'nopic.png', '', 'Active', 'dfOmJEPKQ4CdUkvmmNZJ4E:APA91bHG9QIVYDeCISZp_tW3T4_JuK8Jb-UDlXQkkPSr_WfCtaC9ZxWxer53l_LrpEYJOU1vJ6o8XCG8ESj6DuZoOEgLXQXuJQBY7t8r0w1NekngIsDrdOO7IiZg9PqeaBVwulHhaVW5'),
(96, 'Test Testing', '250788692437', 'b9b7037f4e5f49eabdfff6726aef77588898db42', 'essi2019', 'M', 0, '', 'Driver', '', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'KUMULINDI', 'nopic.png', '', 'Active', ''),
(97, 'James Kalimba', '250788678310', '717b2f3d8816830549097908c134e1729c516542', '170', 'M', 0, '', 'HR', '', 'A0', '', '', '', 'KIGALI', 'Kicukiro', 'KABEZA', 'nopic.png', '', 'Active', ''),
(106, 'MAJ. ROGERS', '250788309714', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Ag MD, RMP', 'RUGARI', 'A0', '', '', '', 'KIGALI', 'Kicukiro', 'NYARUGUNGA', 'nopic.png', '', 'Active', ''),
(107, 'CEO', '250788306481', '682a03f4cd9e0c79b8a1f0e34266b9651ad9821c', '264', 'M', 0, '', 'CEO', 'APTC', 'A0', '', '', '', 'KIGALI', 'Nyarugenge', 'NYARUGENGE', 'nopic.png', '', 'Active', ''),
(108, 'CEO D/CEO', '250788638510', 'fc1200c7a7aa52109d762a9f005b149abef01479', '789', 'M', 0, '', 'D/CEO', 'APTC', 'A0', '', '', '', 'KIGALI', 'Nyarugenge', 'NYARUGENGE', 'nopic.png', '', 'Active', ''),
(109, 'MILINDI J DE DIEU', '250788306305', '6cc71d91778fcdda0eb8709e9348240b251afe5e', '486', 'M', 0, '', 'H/Department', 'APTC', 'Masters', '', '', '', 'KIGALI', 'Nyarugenge', 'NYARUGENGE', 'nopic.png', '', 'Active', ''),
(110, 'KANYANA LOUISE', '250788588227', '5c6b6ba5b4021329119e336efda03dd739e3a85c', '19762', 'M', 0, '', 'H/Department', 'APTC', 'A0', '', '', '', 'KIGALI', 'Nyarugenge', 'NYARUGENGE', 'nopic.png', '', 'Active', ''),
(111, 'IRAMBONA AIMABLE', '250788468611', '5f573b82f1da8677c86d695538c530d136b6c489', '259', 'M', 0, '', 'Head of Accounts', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'NYARUGENGE', 'nopic.png', '', 'Active', ''),
(112, 'Logistics Manager', '250788647932', '683e725c03a87baaad2623231644e944e537acab', '116', 'M', 0, '', 'Logistics', 'APTC', 'A1', '', '', '', 'KIGALI', 'Nyarugenge', 'NYRUGENGE', 'nopic.png', '', 'Active', ''),
(113, 'MUKAYIRANGA ROSE', '250788304692', '9d8974baddfc0e53300829f37e5fc88b0f5ce61b', '155', 'F', 0, '', 'H/Department', 'APTC', 'A0', '', '', '', 'KIGALI', 'Nyarugenge', 'NYARUGENGE', 'nopic.png', '', 'Active', ''),
(114, 'MAHORO PEACE', '250787398960', '89d79a520700d1cce8a6d6c0873ae93de21ffcc0', '681', 'F', 0, '', 'IT', 'APTC', 'A0', '', '', '', 'KIGALI', 'Nyarugenge', 'NYARUGENGE', 'nopic.png', '', 'Active', ''),
(115, 'UMWIZA JANCY', '250738674153', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'H/Department', 'NMI', 'A0', '', '', '', 'KIGALI', 'Kicukiro', 'NYARUGUNGA', 'nopic.png', '', 'Active', ''),
(116, 'Twahirwa Emmy', '250788490124', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'H/Department', 'NMI', 'A0', '', '', '', 'SOUTHERN', 'Nyanza', 'BUSASAMANA', 'nopic.png', '', 'Active', ''),
(117, 'UWIMANA JANE', '250788795508', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'H/Department', 'API', 'A0', '', '', '', 'EASTERN', 'Nyagatare', 'KARANGAZI', 'nopic.png', '', 'Active', ''),
(118, 'Maj. Canisius', '250788484525', '54524bfc1be987c6b091ecb009f398b53b4454d8', 'kapeca14571', 'M', 0, '', 'Director of Operations', 'APTC', 'Masters', '', '', '', 'KIGALI', 'Gasabo', 'NYAMIRAMBO', 'nopic.png', '', 'Active', ''),
(119, 'Lt Col. Gakuba', '250788353663', 'b66cd90e3946dd63b5a914d5eb2c7eddb46177ec', '663', 'M', 0, '', 'Other', 'APTC', 'Masters', '', '', '', 'KIGALI', 'Nyarugenge', 'RUYENZI', 'nopic.png', '', 'Active', ''),
(120, 'Arinda Christine', '250780593091', 'da4b9237bacccdf19c0760cab7aec4a8359010b0', '2', 'M', 0, '', 'Secretary', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'KANOMBE', 'nopic.png', '', 'Active', ''),
(121, 'Alex Kamanda Bugingo', '250784800041', '298f93b1b0efeaf41f0ce468d29abfd252985869', '785', 'M', 0, '', 'Legal', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'RUSORORO', 'nopic.png', '0', 'Active', ''),
(122, 'Tuyizere Laurent', '250783521548', '4396c2d023b9d985eed0ba30fe1c672637c01718', '414', 'M', 0, '', 'Secretary', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'MUHIMA', 'nopic.png', '', 'Active', ''),
(123, 'Mark AGABA', '250789921555', 'c5f2486dc8a9ff70c8047c5d500cf9530ba8c1d3', '885', 'M', 0, '', 'Legal', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'KABEZA', 'nopic.png', '', 'Active', ''),
(124, 'Teddy', '250785026511', '1e7b95c5614637fdcde70eb7f2d109134c95c6bf', '202', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Kicukiro', 'MASAKA', 'nopic.png', '', 'Active', ''),
(127, 'Capt HABUMUGISHA ERIC', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(128, 'LT BENON MUGUME', '250787545227', 'efa6e44dfa0145249be273ecd84a97f534b04920', '115', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(129, '(Rtd) Capt Eugene MULISA', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(130, 'KANGABO MUNEZA Moses', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(131, 'SGT  HARELIMANA Ernest', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(132, 'MAHORO PEACE', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(133, 'HABIMANA Jean Paul', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(134, 'NDAYAMBAJE Jean Claude', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(135, 'DUKUZABO Asanti Beata', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(136, 'NSENGIYUMVA Vincent', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(137, 'UMURERWA M.Grace', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(138, 'NSENGIYUMVA Vincent', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(139, 'YANKURIJE Agnes', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(140, 'DUSABE Constantine', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(141, 'UMWERE Jeannette', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(142, 'UWIMPUHWE Genevieve', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(143, 'MWISENEZA Jean Pierre', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(144, 'AYINKAMIYE Rose', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(145, 'ISANO MIKAMO Donatha', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(146, 'NSENGIYUMVA Moses', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(147, 'MUKAMUTARA KAYITARE Jane', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(148, 'KARAYENZI LINDA', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(149, 'MUKANDAYISENGA Leatitia', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(150, 'KARASIRA Boniface', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(151, 'NYIRANTAHOBARI Louise', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(152, 'TUMUSIME David', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(153, 'TWAGIRAYEZU ALEX', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(154, 'BIZIMANA ENOCK', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(155, 'BISHYOZA THOMAS', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(156, 'GATETE JEAN BOSCO', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(157, 'IYAMUREMYE MWISENEZA', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(158, 'UWAMWEZI VANESSA', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(159, 'RWAHAMA JAMES', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(160, 'UWAMAHORO ALICE DIANE', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(161, 'MUHIRWA J.De Dieu', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(162, 'MUKAYITESI Alice', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(163, 'NYIRAMAJORO Josiane', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(164, 'MUTETERI EMMA', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(165, 'KAYIRANGA PAMELA', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(166, 'KAGABO KARAKE', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(167, 'KAYIGANWA EMERANCE', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(168, 'MIHIGO LAMBERT', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(169, '(Rtd) Capt NTIRUSHWA JEAN de DIEU', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(170, 'S/SGT NSENGIYUMVA Vedaste', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(171, 'UWANTEGE Jacqueline', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(172, 'ZABIBU Rosine', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(173, 'GAKWAYA Caroline', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(174, 'ZABIBU Rosine', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(175, 'ZABIBU Rosine', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(176, '(Rtd) SSGT TUYIZERE Laurent', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(177, 'SHYAKA JACKSON NOEL', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(178, 'TUYISHIME DENISE', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(179, 'BAYISINGIZE FIDENS', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(180, 'NTIRENGANYA XAVIER', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(181, 'MUCYO BIGIN', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(182, 'MUCYO BIGIN', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(183, 'BOUDHA VITAL BONAVANTURE', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(184, 'KALINDA VIATEUR', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(185, 'UMULISA Joseline', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(186, 'KATABARWA Simon', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(187, 'RANGIRA STANISLAS', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(188, 'MUHOZA Victoire', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'F', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(189, 'SHYIRAMBERE Damascene', '-', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, '', 'Other', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'A', 'nopic.png', '', 'Active', ''),
(190, 'Etienne', '250788575426', '4869161b9385d8f9b7fba924fa3102011a48454c', 'aptc2021', 'M', 0, 'secretaryaptc@gmail.com', 'Secretary', 'APTC', 'A1', 'jhjh', '', '', 'KIGALI', 'Gasabo', 'KANOMBE', 'nopic.png', '', 'Active', ''),
(191, 'Shyaka Hamza', '250783963599', '1047b56881438260286a8e7a57e07c53445ceb19', '982', 'M', 0, 'shyakahamza@gmail.com', 'H/IT', 'APTC', 'A0', '', '', '', 'KIGALI', 'Gasabo', 'NYARUGUNGA', 'nopic.png', '', 'Active', ''),
(192, 'Nasla Mukamurenzi', '250788360207', 'bc33ea4e26e5e1af1408321416956113a4658763', '27', 'F', 0, 'logistics@aptc.rw', 'Logistics', 'APTC', 'A0', '', '', '', 'KIGALI', 'Nyarugenge', 'NYAMIRAMBO', 'nopic.png', '', 'Active', 'dfOmJEPKQ4CdUkvmmNZJ4E:APA91bHG9QIVYDeCISZp_tW3T4_JuK8Jb-UDlXQkkPSr_WfCtaC9ZxWxer53l_LrpEYJOU1vJ6o8XCG8ESj6DuZoOEgLXQXuJQBY7t8r0w1NekngIsDrdOO7IiZg9PqeaBVwulHhaVW5'),
(193, 'max', '250789728513', 'c08d9955148bc0199789922ca232a77b69003980', '875', 'M', 0, '', 'HR', '', '', '', '', '', '', '', '', 'dc1c83c35305cba33149b77bdbe73a7c.jpg', '', 'Active', 'cA1I3DFKS16UgQ8DRPkwup:APA91bGmlYIm6Pd-tq6kOHsMPyxDcR_h3ny3q_oRTiLXlR4_WMed5DQ-TDoTQojyGbPpuJEj_T6EsBXuqHJYT6lBZ8T9c9lteRciCS-H7U_0pqpnn6A2bXU9X7AvdcMSMoMOVe4nL7D3'),
(194, 'Utabazi Protais', '250784687597', '2473f01571bf0dcb7d2b16d67da6dd031769947d', '559', 'M', 0, '', 'HR', '', '', '', '', '', '', '', '', 'dc1c83c35305cba33149b77bdbe73a7c.jpg', '', 'Active', 'dfOmJEPKQ4CdUkvmmNZJ4E:APA91bHG9QIVYDeCISZp_tW3T4_JuK8Jb-UDlXQkkPSr_WfCtaC9ZxWxer53l_LrpEYJOU1vJ6o8XCG8ESj6DuZoOEgLXQXuJQBY7t8r0w1NekngIsDrdOO7IiZg9PqeaBVwulHhaVW5');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `vname` varchar(162) NOT NULL,
  `plateno` varchar(47) NOT NULL,
  `Locatio` varchar(240) NOT NULL,
  `payment` varchar(20) NOT NULL,
  `sold_date` varchar(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vname`, `plateno`, `Locatio`, `payment`, `sold_date`) VALUES
(16, 'RAV4', 'RAG154X', 'APTC', '23000000', '2024-03-18'),
(17, 'RAV4', 'RAG023Y', 'APTC', '24,000,000', '2023-11-11'),
(18, 'TOYOTA HILUX', 'RAE449P', 'APTC', '50000000', '2018-02-20'),
(19, 'TOYOTA HILUX', 'RAE450P', 'APTC', '50000000', '2020-09-12'),
(20, 'TOYOTA HILUX', 'RAD032P', 'APTC', '50000000', '2021-08-10'),
(21, 'TOYOTA HILUX', 'RAF553E', 'APTC', '50000000', '2020-08-10'),
(22, 'TOYOTA HILUX', 'RAE409W', 'APTC', '50000000', '2020-06-09'),
(23, 'TOYOTA HILUX', 'RAC359N', 'APTC', '30000000', '2018-10-07'),
(24, 'TOYOTA HILUX', 'RDF198P', 'APTC', '50000000', '2010-09-12'),
(25, 'HIACE', 'RDF697S', 'APTC', '50000000', '2019-10-07'),
(26, 'FUSO MITSUBISHI', 'RAF855R', 'APTC', '50000000', '2022-09-04'),
(27, 'TOYOTA COROLLA', 'RAF047D', 'APTC', '24000000', '2021-07-26'),
(28, 'TOYOTA COROLLA', 'RAE900Z', 'APTC', '24,000,000', '2022-06-04'),
(29, 'TOYOTA LANDCRUISER', 'RAB675W', 'APTC', '30000000', '2010-09-09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fuel`
--
ALTER TABLE `fuel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fuel_request`
--
ALTER TABLE `fuel_request`
  ADD PRIMARY KEY (`req_id`);

--
-- Indexes for table `operation`
--
ALTER TABLE `operation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `operation_report`
--
ALTER TABLE `operation_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parts`
--
ALTER TABLE `parts`
  ADD PRIMARY KEY (`part_id`),
  ADD KEY `record_id` (`record_id`);

--
-- Indexes for table `quick_action`
--
ALTER TABLE `quick_action`
  ADD PRIMARY KEY (`action_id`);

--
-- Indexes for table `request_repair`
--
ALTER TABLE `request_repair`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_request_staff` (`user_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `record_id` (`record_id`);

--
-- Indexes for table `service_records`
--
ALTER TABLE `service_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`stf_code`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fuel`
--
ALTER TABLE `fuel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `fuel_request`
--
ALTER TABLE `fuel_request`
  MODIFY `req_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `operation`
--
ALTER TABLE `operation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `operation_report`
--
ALTER TABLE `operation_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `parts`
--
ALTER TABLE `parts`
  MODIFY `part_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `quick_action`
--
ALTER TABLE `quick_action`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `request_repair`
--
ALTER TABLE `request_repair`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `service_records`
--
ALTER TABLE `service_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `stf_code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `parts`
--
ALTER TABLE `parts`
  ADD CONSTRAINT `parts_ibfk_1` FOREIGN KEY (`record_id`) REFERENCES `service_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `request_repair`
--
ALTER TABLE `request_repair`
  ADD CONSTRAINT `fk_request_staff` FOREIGN KEY (`user_id`) REFERENCES `staff` (`stf_code`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`record_id`) REFERENCES `service_records` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
