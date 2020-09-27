-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 24, 2020 at 11:37 AM
-- Server version: 5.7.31-0ubuntu0.18.04.1
-- PHP Version: 7.2.33-1+ubuntu18.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nourish_erp`
--

-- --------------------------------------------------------

--
-- Table structure for table `core_agent`
--

CREATE TABLE `core_agent` (
  `id` int(11) NOT NULL,
  `agent_group_id` int(11) DEFAULT NULL,
  `upozila_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agentId` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` longtext COLLATE utf8mb4_unicode_ci,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `binNo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terminal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `core_agent`
--

INSERT INTO `core_agent` (`id`, `agent_group_id`, `upozila_id`, `district_id`, `name`, `agentId`, `mobile`, `phone`, `email`, `address`, `path`, `nid`, `binNo`, `terminal`) VALUES
(1, NULL, NULL, NULL, 'rterter', NULL, '23423423423', NULL, NULL, NULL, NULL, NULL, NULL, 1),
(2, NULL, NULL, NULL, 'asdasd', NULL, '3423423423', NULL, NULL, NULL, NULL, NULL, NULL, 1),
(3, NULL, NULL, NULL, 'asdasd', NULL, '3423423423', NULL, NULL, NULL, NULL, NULL, NULL, 1),
(4, NULL, NULL, NULL, 'asdasd', NULL, '3423423423', NULL, NULL, NULL, NULL, NULL, NULL, 1),
(5, NULL, NULL, NULL, 'asdasd', NULL, '3423423423', NULL, NULL, NULL, NULL, NULL, NULL, 1),
(6, 10, NULL, NULL, 'xcasdfa', NULL, '54353453453', NULL, NULL, NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `core_bank`
--

CREATE TABLE `core_bank` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `core_bank`
--

INSERT INTO `core_bank` (`id`, `name`, `slug`, `status`) VALUES
(1, 'AB BANK LTD.\n', '', NULL),
(2, 'AGRANI BANK LTD.\n', '', NULL),
(3, 'AL-ARAFAH ISLAMI BANK LTD.', '', NULL),
(4, 'BANGLADESH  COMMERCE BANK LTD.\n', '', NULL),
(5, 'BANGLADESH SHILPA BANK LTD.', '', NULL),
(6, 'BANGLADESH DEVELOPMENT BANK LTD.', '', NULL),
(7, 'BANGLADESH KRISHI BANK LTD.', '', NULL),
(8, 'BANK ALFALAH LTD.', '', NULL),
(9, 'BANK ASIA LTD.\r\n', '', NULL),
(10, 'BASIC BANK LTD.\r\n', '', NULL),
(11, 'THE CITY BANK LTD.\r\n', '', NULL),
(12, 'DHAKA BANK LTD.\r\n', '', NULL),
(13, 'DUTCH BANGLA BANK LTD.\r\n', '', NULL),
(14, 'EASTERN BANK LTD.\r\n', '', NULL),
(15, 'EXIM BANK LTD.\r\n', '', NULL),
(16, 'FIRST SECURITY ISLAMI BANK LTD.\r\n', '', NULL),
(17, 'HSBC BANK LTD.\r\n', '', NULL),
(18, 'IFIC BANK LTD.\r\nn', '', NULL),
(19, 'ICB ISLAMIC BANK\r\n', '', NULL),
(20, 'ISLAMI BANK BANGLADESH LTD.\r\n', '', NULL),
(21, 'JAMUNA BANK LTD.\r\n', '', NULL),
(22, 'JANATA BANK\r\n', '', NULL),
(23, 'MERCANTILE BANK LTD.\r\n', '', NULL),
(24, 'MUTUAL TRUST BANK LTD.\r\n', '', NULL),
(25, 'MODHUMOTI BANK LTD.\r\n', '', NULL),
(26, 'NATIONAL BANK LTD.\r\n', '', NULL),
(27, 'NCC BANK LTD.\r\n', '', NULL),
(28, 'ONE BANK LTD.\r\n', '', NULL),
(29, 'PREMIER BANK LTD.\r\n', '', NULL),
(30, 'PRIME BANK LTD.\r\n', '', NULL),
(31, 'PUBALI BANK\r\n', '', NULL),
(32, 'RUPALI  BANK LTD.\r\n', '', NULL),
(33, 'SHAHJALAL ISLAMI BANK LTD.\r\n', '', NULL),
(34, 'SOCIAL INVESTMENT BANK LTD.\r\n', '', NULL),
(35, 'SOCIAL ISLAMI BANK LTD.\r\n', '', NULL),
(36, 'SONALI BANK LTD.\r\n', '', NULL),
(37, 'SOUTHEAST BANK LTD\r\n', '', NULL),
(38, 'STANDARD BANK LTD.\r\n', '', NULL),
(39, 'THE CITY BANK LTD.\r\n', '', NULL),
(40, 'TRUST BANK LTD.\r\n', '', NULL),
(41, 'UNITED COMMERCIAL BANK LTD.\r\n', '', NULL),
(42, 'BRAC BANK LTD.', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `core_company`
--

CREATE TABLE `core_company` (
  `id` int(11) NOT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `holding_entity` tinyint(1) DEFAULT NULL,
  `trade_license` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trade_license_issue_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incorporation_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incorporation_issue_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_tin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_tin_name_of_company` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `different_etin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trading_brand_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equity_information` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equity_local_share` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bida_registration_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bida_issue_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_operation_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `police_station` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `land_phone_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `web_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `head_quarter_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `head_quarter_address_outside` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `irc_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `erc_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_other_specify` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `irc_issue_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `erc_issue_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taxable_turnover` double DEFAULT NULL,
  `projected_turnover` double DEFAULT NULL,
  `no_of_employee` double DEFAULT NULL,
  `zero_rated_supply` tinyint(1) DEFAULT NULL,
  `vat_exempted_supply` tinyint(1) DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `is_manufacturing` tinyint(1) DEFAULT NULL,
  `is_servicing` tinyint(1) DEFAULT NULL,
  `is_export` tinyint(1) DEFAULT NULL,
  `is_import` tinyint(1) DEFAULT NULL,
  `is_economic_activity_other` tinyint(1) DEFAULT NULL,
  `economic_activity_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uniqueKey` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `core_country`
--

CREATE TABLE `core_country` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nickname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iso` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iso3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `core_key_value`
--

CREATE TABLE `core_key_value` (
  `id` int(11) NOT NULL,
  `setting_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `metaKey` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metaValue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sorting` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `core_key_value`
--

INSERT INTO `core_key_value` (`id`, `setting_id`, `user_id`, `metaKey`, `metaValue`, `sorting`) VALUES
(1, 14, NULL, 'a', 'aaa', NULL),
(2, 15, NULL, 'aa', 'QQQ', NULL),
(3, 15, NULL, 'AA', 'aaaa', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `core_location`
--

CREATE TABLE `core_location` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `level` int(11) DEFAULT NULL,
  `path` varchar(3000) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `core_location`
--

INSERT INTO `core_location` (`id`, `parent_id`, `name`, `level`, `path`) VALUES
(1, NULL, 'Gazipur', 1, '/1'),
(2, NULL, 'Chattogram', 1, '/2'),
(3, NULL, 'Rajshahi', 1, '/3'),
(4, NULL, 'Rangpur', 1, '/4'),
(5, NULL, 'Khulna', 1, '/5'),
(6, NULL, 'Barishal', 1, '/6'),
(7, NULL, 'Sylhet', 1, '/7'),
(8, NULL, 'Narsingdi', 1, '/8'),
(9, NULL, 'Corporate', 1, '/9'),
(10, NULL, 'Sister Concern', 1, '/10'),
(11, NULL, 'Mymensingh', 1, '/11'),
(12, NULL, 'Cumilla', 1, '/12'),
(13, 3, 'Sirajgong', 3, '/3/13/'),
(14, 13, 'Pabna', 4, '/3/13/14/'),
(15, 14, 'ATGHARIA', 5, '/3/13/14/15/'),
(16, 14, 'BERA', 5, '/3/13/14/16/'),
(17, 14, 'BHANGURA', 5, '/3/13/14/17/'),
(18, 12, 'Aff', 3, '/12/18/');

-- --------------------------------------------------------

--
-- Table structure for table `core_setting`
--

CREATE TABLE `core_setting` (
  `id` int(11) NOT NULL,
  `setting_type_id` int(11) DEFAULT NULL,
  `terminal` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_bn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `core_setting`
--

INSERT INTO `core_setting` (`id`, `setting_type_id`, `terminal`, `name`, `name_bn`, `slug`, `status`) VALUES
(1, 1, 1, 'Sales', NULL, NULL, 1),
(2, 3, 1, 'General Manager', NULL, NULL, 1),
(3, 3, 1, 'Manager', NULL, NULL, 1),
(4, 3, 1, 'Zonal Manager', NULL, NULL, 1),
(5, 3, 1, 'Regional Manager', NULL, NULL, 1),
(6, 3, 1, 'Sales Force', NULL, NULL, 1),
(7, 3, 1, 'Doctor', NULL, NULL, 1),
(8, 2, 1, 'Administrator', NULL, NULL, 1),
(9, 2, 1, 'General', NULL, NULL, 1),
(10, 4, 1, 'Feed', NULL, NULL, 1),
(11, 4, 1, 'Cheek', NULL, NULL, 1),
(12, 1, 1, 'Test', NULL, 'test', 1),
(13, 4, 1, 'l', NULL, 'l', 1),
(14, 4, 1, 'okay', NULL, 'okay', 1),
(15, 3, 1, 'AAA', NULL, 'aaa', 1);

-- --------------------------------------------------------

--
-- Table structure for table `core_setting_type`
--

CREATE TABLE `core_setting_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `core_setting_type`
--

INSERT INTO `core_setting_type` (`id`, `name`, `slug`, `status`) VALUES
(1, 'Department', 'department', 1),
(2, 'User Group', 'user-group', 1),
(3, 'Designation', 'designation', 1),
(4, 'Agent Group', 'agent-group', 1);

-- --------------------------------------------------------

--
-- Table structure for table `core_user`
--

CREATE TABLE `core_user` (
  `id` int(11) NOT NULL,
  `designation_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `user_group_id` int(11) DEFAULT NULL,
  `terminal_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app_roles` json DEFAULT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joining_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terms_condition` tinyint(1) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `is_delete` tinyint(1) NOT NULL,
  `roles` json NOT NULL,
  `district_id` int(11) DEFAULT NULL,
  `regional_id` int(11) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `date_of_birth` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `effective_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_of_vehicle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `speciality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `educational_qualification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `training_skill` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsibility` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assets` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leave_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_branch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `core_user`
--

INSERT INTO `core_user` (`id`, `designation_id`, `department_id`, `branch_id`, `user_group_id`, `terminal_id`, `name`, `mobile`, `phone`, `app_roles`, `path`, `username`, `email`, `password`, `otp`, `joining_date`, `address`, `terms_condition`, `enabled`, `is_delete`, `roles`, `district_id`, `regional_id`, `zone_id`, `date_of_birth`, `effective_date`, `salary`, `type_of_vehicle`, `vehicle_no`, `speciality`, `educational_qualification`, `training_skill`, `responsibility`, `assets`, `leave_status`, `account_number`, `bank_id`, `location_id`, `user_id`, `bank_branch`, `area`) VALUES
(1, NULL, NULL, NULL, NULL, 1, '', NULL, NULL, NULL, NULL, 'admin', 'admin@gmail.com', '$2y$13$XOpuydcgweVBIsjpP2ZfZeubPzu/n1E.czmeDiXp18pfl71UB3WUG', NULL, NULL, NULL, 0, 1, 0, '[\"ROLE_ADMIN\"]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL),
(2, 7, 1, NULL, 8, 1, 'Umar', '01827-164133', NULL, '[]', NULL, 'umar', 'umar@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$VmuZCqdS3ZXRIWvZDuMoeQ$vT/ZbB+dyJ7CsYFEt8OYc/6bBkyCI+EQvD/L8QAeeEk', NULL, NULL, NULL, 0, 1, 0, '[\"ROLE_USER\", \"ROLE_KPI_SALES_FORCE\", \"ROLE_KPI_CSO\", \"ROLE_KPI_REGIONAL_MANAGER\", \"ROLE_KPI_ADMIN\", \"ROLE_CRM_CSO\", \"ROLE_CRM_REGIONAL_MANAGER\", \"ROLE_CRM_ZONAL_MANAGER\", \"ROLE_CRM_ADMIN\"]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, NULL),
(3, 6, 1, NULL, 9, 1, 'Jony Amin', '01721-234454', NULL, '[]', NULL, 'jony', 'jony@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$OPI0cs/3mxMl/trg8lyknA$ThEpVv4rjFFxES93O4pBuLQyT2SmSswOcCE/P2M9n50', NULL, '01-08-2020', NULL, 0, 1, 0, '[\"ROLE_USER\", \"ROLE_CRM_CSO\", \"ROLE_CRM_REGIONAL_MANAGER\", \"ROLE_CRM_ZONAL_MANAGER\", \"ROLE_CRM_ADMIN\"]', NULL, NULL, NULL, '25-08-2020', '25-08-2020', '10000', 'car', '1221332', 'sdasdasd', 'M.A', 'IT Expert', NULL, 'Pen', 'In-active', '34324234', 16, 13, '234324', 'Dhaka', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `core_user_profile`
--

CREATE TABLE `core_user_profile` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fatherName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motherName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `userGroup` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phoneNo` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebookId` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profession` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about` longtext COLLATE utf8mb4_unicode_ci,
  `address` longtext COLLATE utf8mb4_unicode_ci,
  `permanentAddress` longtext COLLATE utf8mb4_unicode_ci,
  `postalCode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `additionalPhone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` datetime DEFAULT NULL,
  `bloodGroup` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `religionStatus` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maritalStatus` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employeeType` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interest` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joiningDate` datetime DEFAULT NULL,
  `leaveDate` datetime DEFAULT NULL,
  `accountNo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `termsConditionAccept` tinyint(1) DEFAULT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `core_user_profile`
--

INSERT INTO `core_user_profile` (`id`, `user_id`, `name`, `fatherName`, `motherName`, `userGroup`, `mobile`, `phoneNo`, `email`, `facebookId`, `profession`, `about`, `address`, `permanentAddress`, `postalCode`, `additionalPhone`, `occupation`, `nid`, `gender`, `dob`, `bloodGroup`, `religionStatus`, `maritalStatus`, `employeeType`, `interest`, `joiningDate`, `leaveDate`, `accountNo`, `branch`, `termsConditionAccept`, `path`) VALUES
(1, 1, 'Admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `core_user_role`
--

CREATE TABLE `core_user_role` (
  `id` int(11) NOT NULL,
  `domain` int(11) DEFAULT NULL,
  `branch` int(11) DEFAULT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unique_key` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app_roles` longtext COLLATE utf8mb4_unicode_ci,
  `app_password` longtext COLLATE utf8mb4_unicode_ci,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terms_condition` tinyint(1) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `is_delete` tinyint(1) NOT NULL,
  `roles` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_broiler_standard`
--

CREATE TABLE `crm_broiler_standard` (
  `id` int(11) NOT NULL,
  `age` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_body_weight` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_feed_consumption` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_broiler_standard`
--

INSERT INTO `crm_broiler_standard` (`id`, `age`, `target_body_weight`, `target_feed_consumption`) VALUES
(1, '1', '56', '13'),
(3, '2', '72', '30'),
(4, '3', '89', '51'),
(5, '4', '109', '74'),
(6, '5', '131', '101'),
(7, '6', '157', '132'),
(8, '7', '185', '167'),
(9, '8', '215', '206'),
(10, '9', '247', '250'),
(11, '10', '283', '298'),
(12, '11', '321', '352'),
(13, '12', '364', '410'),
(14, '13', '412', '474'),
(15, '14', '465', '542'),
(16, '15', '524', '617'),
(17, '16', '586', '698'),
(18, '17', '651', '785'),
(19, '18', '719', '878'),
(20, '19', '790', '976'),
(21, '20', '865', '1081'),
(22, '21', '943', '1192'),
(23, '22', '1023', '1309'),
(24, '23', '1104', '1432'),
(25, '24', '1186', '1562'),
(26, '25', '1269', '1696'),
(27, '26', '1353', '1837'),
(28, '27', '1438', '1985'),
(29, '28', '1524', '2137'),
(30, '29', '1613', '2295'),
(31, '30', '1705', '2458'),
(32, '31', '1799', '2627'),
(33, '32', '1895', '2801'),
(34, '33', '1993', '2981'),
(35, '34', '2092', '3163'),
(36, '35', '2191', '3352'),
(37, '36', '2289', '3545'),
(38, '37', '2386', '3742'),
(39, '38', '2482', '3943'),
(40, '39', '2577', '4148'),
(41, '40', '2671', '4357'),
(42, '41', '2764', '4570'),
(43, '42', '2857', '4786');

-- --------------------------------------------------------

--
-- Table structure for table `crm_chick_life_cycle`
--

CREATE TABLE `crm_chick_life_cycle` (
  `id` int(11) NOT NULL,
  `officer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hatching_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visitingweek` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `totalbirds` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age_days` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mortality_pes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weightStandard` longtext COLLATE utf8mb4_unicode_ci,
  `weightAchieved` longtext COLLATE utf8mb4_unicode_ci,
  `feedTotalkg` longtext COLLATE utf8mb4_unicode_ci,
  `feedStandard` longtext COLLATE utf8mb4_unicode_ci,
  `hatchery` longtext COLLATE utf8mb4_unicode_ci,
  `breed` longtext COLLATE utf8mb4_unicode_ci,
  `feed` longtext COLLATE utf8mb4_unicode_ci,
  `feedType` longtext COLLATE utf8mb4_unicode_ci,
  `proDate` longtext COLLATE utf8mb4_unicode_ci,
  `batchNo` longtext COLLATE utf8mb4_unicode_ci,
  `bird_mode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` longtext COLLATE utf8mb4_unicode_ci,
  `reporting_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_chick_life_cycle`
--

INSERT INTO `crm_chick_life_cycle` (`id`, `officer_name`, `region`, `hatching_date`, `visitingweek`, `totalbirds`, `age_days`, `mortality_pes`, `weightStandard`, `weightAchieved`, `feedTotalkg`, `feedStandard`, `hatchery`, `breed`, `feed`, `feedType`, `proDate`, `batchNo`, `bird_mode`, `remarks`, `reporting_date`) VALUES
(1, 'mir', 'Jessore', '2020-09-01', '1st', '12', '12', '1', '12', '11', '12', '15', '12', '12', '12', 'xyz', '2020-09-22', '12', 'SONALI', 'aa', '2020-09-23'),
(2, 'mir', 'Khulna', '2020-09-01', '1st', '12', '12', '1', '12', '11', '12', '15', 'a', 'y', 'y', 'xyz', '2020-09-01', '11', 'BROILER', 'a', '2020-09-22');

-- --------------------------------------------------------

--
-- Table structure for table `crm_customers`
--

CREATE TABLE `crm_customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agentId` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subagentId` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` longtext COLLATE utf8mb4_unicode_ci,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_customers`
--

INSERT INTO `crm_customers` (`id`, `name`, `mobile`, `address`, `custom_group`, `agentId`, `subagentId`, `location`, `created`, `updated`) VALUES
(1, 'a', '1', 'a', 'a', '1', '1', 'aa', '2020-09-14 11:08:59', NULL),
(2, 'Fahim', '121', 'a', '1', '2', '2', 'a', '2020-09-14 11:37:19', NULL),
(15, 'dsa', '12', 'd', '1', '2', '3', 'd', '2020-09-14 11:40:35', NULL),
(16, 'Mir', '32', 'a', '1', '1', '2', 'dd', '2020-09-14 11:43:29', NULL),
(17, 'd', '43', 'ff', '2', '3', '23', 'ff', '2020-09-14 11:44:39', NULL),
(18, 'd', '43', 'ff', '2', '3', '23', 'ff', '2020-09-14 11:45:13', NULL),
(19, 'karim', '43', 'es', '1', '1', '1', 'ds', '2020-09-14 11:45:57', NULL),
(20, 's', '2', 'ds', '1', '3', '4', 'ssd', '2020-09-14 11:49:57', NULL),
(21, 'vx', '2', 'a', '2', '2', '2', 'ssa', '2020-09-14 12:03:00', NULL),
(34, 'as', '212', 'as', 'ss', 'asa', 'as', 'sa', '2020-09-15 04:58:43', NULL),
(35, 'sa', '32', 's', '1', '1', '1', 'dsa', '2020-09-15 05:01:47', NULL),
(37, 'ssa', '12', 'sa', '8', '2', '2', 'ds', '2020-09-15 06:40:52', NULL),
(38, 'sa', '21', 'sa', 'Group A', '3', '3', 'ds', '2020-09-15 06:41:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `crm_expense`
--

CREATE TABLE `crm_expense` (
  `id` int(11) NOT NULL,
  `setting_id` int(11) DEFAULT NULL,
  `schedule_visit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `visiting_area` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conveyance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `daily_allowance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hotel_rent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photostate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `courier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `food` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maintenace` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `toll_bill` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_charge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `others` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_expense`
--

INSERT INTO `crm_expense` (`id`, `setting_id`, `schedule_visit`, `visiting_area`, `conveyance`, `daily_allowance`, `hotel_rent`, `photostate`, `courier`, `food`, `mobile`, `maintenace`, `toll_bill`, `service_charge`, `others`) VALUES
(1, 11, '1', 'dhaka', '12', '21', '21', '21', '12', '12', '12', '12', '12', '12', '12'),
(2, 17, '2', 'Barisal', '222', '21', '5000', '100', '50', '600', '50', '600', '120', '630', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `crm_fcr`
--

CREATE TABLE `crm_fcr` (
  `id` int(11) NOT NULL,
  `cso` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fcr_of_feed` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporting_month` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hatching_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `totalbirds` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age_day` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_feed_consumption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hatchery` longtext COLLATE utf8mb4_unicode_ci,
  `breed` longtext COLLATE utf8mb4_unicode_ci,
  `feed` longtext COLLATE utf8mb4_unicode_ci,
  `remarks` longtext COLLATE utf8mb4_unicode_ci,
  `pes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_fcr`
--

INSERT INTO `crm_fcr` (`id`, `cso`, `fcr_of_feed`, `reporting_month`, `hatching_date`, `totalbirds`, `age_day`, `weight`, `total_feed_consumption`, `hatchery`, `breed`, `feed`, `remarks`, `pes`) VALUES
(1, 'mr', 'BEFORE', '09-2020', '2020-09-21', '12', '12', '12', '12', '12', '12', '12', 'm', '10'),
(2, 'mr', 'BEFORE', '09-2020', '2020-09-21', '12', '12', '12', '12', '12', '12', '12', 'a', '11'),
(3, 'mr', 'AFTER', '09-2020', '09-2020', '12', '11', '12', '12', '12', '12', '12', 'sa', '10'),
(4, 'mr', 'AFTER', '09-2020', '09-2020', '12', '12', '12', '12', '12', '12', '12', 'a', '11'),
(5, 'mr', 'AFTER', '09-2020', '2020-09-21', '12', '12', '12', '10', '12', '12', '12', 'assa', '2'),
(6, 'mr', 'BEFORE', '09-2020', '2020-09-21', '12', '12', '10', '12', 'Mymensingh', '12', '12', 'sa', '5');

-- --------------------------------------------------------

--
-- Table structure for table `crm_layer_life_cycle`
--

CREATE TABLE `crm_layer_life_cycle` (
  `id` int(11) NOT NULL,
  `total_birds` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hatchery_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hatchery` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `breed` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dead_bird` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avg_weight` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_weight` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uniformity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `feed_per_bird` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_feed_per_bird` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_eggs` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_egg_production` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `egg_weight_actual` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `egg_weight_standard` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `feed_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `production_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `feed_mill` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `medicine` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `visiting_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age_week` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_layer_life_cycle`
--

INSERT INTO `crm_layer_life_cycle` (`id`, `total_birds`, `hatchery_date`, `hatchery`, `breed`, `dead_bird`, `avg_weight`, `target_weight`, `uniformity`, `feed_per_bird`, `target_feed_per_bird`, `total_eggs`, `target_egg_production`, `egg_weight_actual`, `egg_weight_standard`, `feed_type`, `production_date`, `batch_no`, `feed_mill`, `medicine`, `remarks`, `created`, `updated`, `visiting_date`, `age_week`) VALUES
(1, '22', '2020-09-22', 's', 'y', '2', '22', '23', '1', '12', '13', '12', '12', '12', '12', 'v', '2020-09-22', '1', 'a', 'a', 'a', '2020-09-22 13:29:56', NULL, '2020-09-22', '1st');

-- --------------------------------------------------------

--
-- Table structure for table `crm_layer_performance`
--

CREATE TABLE `crm_layer_performance` (
  `id` int(11) NOT NULL,
  `total_birds` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age_wk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bird_weight_achieved` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bird_weight_target` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `feed_intake_per_bird` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `feed_Target` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `egg_production_achieved` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `egg_production_target` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `egg_weight_achieved` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `egg_weight_stand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `feed_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `production_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `feed_mill` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hatchery` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `breed` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `disease` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `cso` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `month` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_layer_performance`
--

INSERT INTO `crm_layer_performance` (`id`, `total_birds`, `age_wk`, `bird_weight_achieved`, `bird_weight_target`, `feed_intake_per_bird`, `feed_Target`, `egg_production_achieved`, `egg_production_target`, `egg_weight_achieved`, `egg_weight_stand`, `feed_type`, `production_date`, `batch_no`, `feed_mill`, `hatchery`, `breed`, `color`, `disease`, `remarks`, `created`, `updated`, `cso`, `month`, `region`, `designation`) VALUES
(1, '12', '2', '12', '15', '12', '12', '15', '20', '12', '12', 'xyz', '2020-09-22', '123', 'd', 'sa', 'sa', 'red', 'no', 'no', '2020-09-22 10:13:34', NULL, 'mir', '09-2020', 'Dhaka', 'Sale_force');

-- --------------------------------------------------------

--
-- Table structure for table `crm_setting`
--

CREATE TABLE `crm_setting` (
  `id` int(11) NOT NULL,
  `setting_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_setting`
--

INSERT INTO `crm_setting` (`id`, `setting_type`, `name`, `status`) VALUES
(5, 'PURPOSE', 'Performance Report', 1),
(8, 'CUSTOMER_GROUP', 'Farmer', 1),
(9, 'CUSTOMER_GROUP', 'Agent', 1),
(10, 'CUSTOMER_GROUP', 'Sub Agent', 1),
(11, 'PURPOSE', 'Agent Service', 1),
(12, 'PURPOSE', 'Survey', 1),
(13, 'PURPOSE', 'Problem Farm Visit', 1),
(14, 'PURPOSE', 'Customer Service', 1),
(15, 'PURPOSE', 'Life Cycle', 1),
(16, 'PURPOSE', 'Market Promotion', 1),
(17, 'PURPOSE', 'Area Problems', 1),
(18, 'PURPOSE', 'Others', 1),
(24, 'Visiting_Week', '1st Week', 1),
(25, 'Visiting_Week', '2nd Week', 1),
(26, 'Visiting_Week', '3rd Week', 1),
(27, 'Visiting_Week', '4th Week', 1),
(28, 'Visiting_Week', '5th Week', 1),
(29, 'Visiting_Week', '6th', 1);

-- --------------------------------------------------------

--
-- Table structure for table `crm_sonali_standard`
--

CREATE TABLE `crm_sonali_standard` (
  `id` int(11) NOT NULL,
  `age` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `feed_intake_per_day` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_body_weight` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_sonali_standard`
--

INSERT INTO `crm_sonali_standard` (`id`, `age`, `feed_intake_per_day`, `target_body_weight`) VALUES
(1, '0', '0', '20'),
(2, '1', '5', '25'),
(3, '2', '6', '30'),
(4, '3', '7', '35'),
(5, '4', '8', '40'),
(6, '5', '9', '50'),
(7, '6', '10', '65'),
(8, '7', '11', '70'),
(9, '8', '11.9', '87'),
(10, '9', '12.8', '94'),
(11, '10', '13.65', '100'),
(12, '11', '14.5', '105'),
(13, '12', '15.3', '110'),
(14, '13', '16.1', '115'),
(15, '14', '16.85', '120'),
(16, '15', '17.7', '130'),
(17, '16', '18.4', '145'),
(18, '17', '19.1', '155'),
(19, '18', '19.75', '165'),
(20, '19', '20.4', '175'),
(21, '20', '21', '185'),
(22, '21', '21.6', '200'),
(23, '22', '23.18', '225'),
(24, '23', '23.77', '245'),
(25, '24', '24.36', '255'),
(26, '25', '24.95', '265'),
(28, '26', '25.54', '275'),
(29, '27', '26.13', '285'),
(30, '28', '26.72', '300'),
(31, '29', '27.49', '325'),
(32, '30', '28.26', '345'),
(33, '31', '30.03', '355'),
(34, '32', '30.8', '365'),
(35, '33', '31.57', '375'),
(36, '34', '32.34', '385'),
(37, '35', '33.11', '400'),
(38, '36', '33.73', '425'),
(39, '37', '34.35', '445'),
(40, '38', '34.97', '455'),
(41, '39', '35.59', '465'),
(42, '40', '36.21', '475'),
(43, '41', '37.83', '485'),
(44, '42', '38.45', '500'),
(45, '43', '39.16', '525'),
(46, '44', '39.87', '535'),
(47, '45', '40.58', '550'),
(48, '46', '41.29', '565'),
(49, '47', '42', '575'),
(50, '48', '42.71', '585'),
(51, '49', '43.42', '600'),
(52, '50', '44.03', '625'),
(53, '51', '44.64', '635'),
(54, '52', '45.25', '645'),
(55, '53', '45.86', '650'),
(56, '54', '46.47', '665'),
(57, '55', '47.08', '670'),
(58, '56', '47.69', '685'),
(59, '57', '48.4', '692'),
(60, '58', '49.11', '700'),
(61, '59', '49.82', '715'),
(62, '60', '50.53', '725');

-- --------------------------------------------------------

--
-- Table structure for table `crm_visit`
--

CREATE TABLE `crm_visit` (
  `id` int(11) NOT NULL,
  `cso_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `working_duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_visit`
--

INSERT INTO `crm_visit` (`id`, `cso_id`, `created`, `updated`, `working_duration`, `area_name`) VALUES
(1, '1', '2020-09-12 07:30:23', NULL, '1', 'a'),
(23, '2', '2020-09-12 08:37:39', NULL, '3', 'DHaka'),
(24, '12', '2020-09-12 08:45:05', NULL, '12', 'khulna'),
(25, '12', '2020-09-12 09:29:10', NULL, '1', 'DHaka'),
(26, '13', '2020-09-16 04:35:43', NULL, '12', 'Sylhet'),
(27, '2', '2020-09-16 04:37:44', NULL, '2', 'Chittagong'),
(29, '2', '2020-09-16 13:02:16', NULL, '1', 'Gajipur'),
(30, '3', '2020-09-16 13:54:47', NULL, '5', 'khulna');

-- --------------------------------------------------------

--
-- Table structure for table `crm_visit_details`
--

CREATE TABLE `crm_visit_details` (
  `id` int(11) NOT NULL,
  `crm_visit_id` int(11) DEFAULT NULL,
  `farmCapacity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `comments` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purpose` longtext COLLATE utf8mb4_unicode_ci,
  `created` datetime NOT NULL,
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ext_log_entries`
--

CREATE TABLE `ext_log_entries` (
  `id` int(11) NOT NULL,
  `action` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logged_at` datetime NOT NULL,
  `object_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `object_class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` int(11) NOT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:array)',
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `ext_translations`
--

CREATE TABLE `ext_translations` (
  `id` int(11) NOT NULL,
  `locale` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `object_class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foreign_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `kpi_agent_order`
--

CREATE TABLE `kpi_agent_order` (
  `id` int(11) NOT NULL,
  `upozila_id` int(11) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` double DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kpi_agent_order_check`
--

CREATE TABLE `kpi_agent_order_check` (
  `id` int(11) NOT NULL,
  `upozila_id` int(11) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `outstanding` double DEFAULT NULL,
  `actualAmount` double DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kpi_agent_outstanding`
--

CREATE TABLE `kpi_agent_outstanding` (
  `id` int(11) NOT NULL,
  `upozila_id` int(11) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `outstanding` double DEFAULT NULL,
  `actualAmount` double DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kpi_agent_sales_growth`
--

CREATE TABLE `kpi_agent_sales_growth` (
  `id` int(11) NOT NULL,
  `upozila_id` int(11) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` double DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kpi_employee`
--

CREATE TABLE `kpi_employee` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `employee_setup_id` int(11) DEFAULT NULL,
  `parameter_id` int(11) DEFAULT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `attributes_id` int(11) DEFAULT NULL,
  `mark_distribution_id` int(11) DEFAULT NULL,
  `mark` double DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kpi_employee_board`
--

CREATE TABLE `kpi_employee_board` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `employee_setup_id` int(11) DEFAULT NULL,
  `attributes_id` int(11) DEFAULT NULL,
  `mark` double DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kpi_employee_board`
--

INSERT INTO `kpi_employee_board` (`id`, `employee_id`, `employee_setup_id`, `attributes_id`, `mark`, `status`) VALUES
(1, 3, 22, NULL, NULL, 1),
(2, 3, 22, NULL, NULL, 1),
(3, 3, 22, NULL, NULL, 1),
(4, 3, 22, NULL, NULL, 1),
(5, 3, 22, NULL, NULL, 1),
(6, 3, 22, NULL, NULL, 1),
(7, 3, 22, NULL, NULL, 1),
(8, 3, 22, NULL, NULL, 1),
(9, 3, 22, NULL, NULL, 1),
(10, 3, 22, NULL, NULL, 1),
(11, 3, 22, NULL, NULL, 1),
(12, 3, 22, NULL, NULL, 1),
(13, 3, 22, NULL, NULL, 1),
(14, 3, 22, NULL, NULL, 1),
(15, 3, 22, NULL, NULL, 1),
(16, 3, 22, NULL, NULL, 1),
(17, 3, 22, NULL, NULL, 1),
(18, 3, 22, NULL, NULL, 1),
(19, 3, 22, NULL, NULL, 1),
(20, 3, 22, NULL, NULL, 1),
(21, 3, 22, NULL, NULL, 1),
(22, 3, 22, NULL, NULL, 1),
(23, 3, 22, NULL, NULL, 1),
(24, 3, 22, NULL, NULL, 1),
(25, 3, 22, NULL, NULL, 1),
(26, 3, 22, NULL, NULL, 1),
(27, 3, 22, NULL, NULL, 1),
(28, 3, 22, NULL, NULL, 1),
(29, 3, 22, NULL, NULL, 1),
(30, 3, 22, NULL, NULL, 1),
(31, 3, 22, NULL, NULL, 1),
(32, 3, 22, NULL, NULL, 1),
(33, 3, 22, NULL, NULL, 1),
(34, 3, 22, NULL, NULL, 1),
(35, 3, 22, NULL, NULL, 1),
(36, 3, 22, NULL, NULL, 1),
(37, 3, 22, NULL, NULL, 1),
(38, 3, 22, NULL, NULL, 1),
(39, 3, 22, NULL, NULL, 1),
(40, 3, 22, NULL, NULL, 1),
(41, 3, 22, NULL, NULL, 1),
(42, 3, 22, NULL, NULL, 1),
(43, 3, 22, NULL, NULL, 1),
(44, 3, 22, NULL, NULL, 1),
(45, 3, 22, NULL, NULL, 1),
(46, 3, 22, NULL, NULL, 1),
(47, 3, 22, NULL, NULL, 1),
(48, 3, 22, NULL, NULL, 1),
(49, 3, 22, NULL, NULL, 1),
(50, 3, 22, NULL, NULL, 1),
(51, 3, 22, NULL, NULL, 1),
(52, 3, 22, NULL, NULL, 1),
(53, 3, 22, NULL, NULL, 1),
(54, 3, 22, NULL, NULL, 1),
(55, 3, 22, NULL, NULL, 1),
(56, 3, 22, NULL, NULL, 1),
(57, 3, 22, NULL, NULL, 1),
(58, 3, 22, NULL, NULL, 1),
(59, 3, 22, NULL, NULL, 1),
(60, 3, 22, NULL, NULL, 1),
(61, 3, 22, NULL, NULL, 1),
(62, 3, 22, NULL, NULL, 1),
(63, 3, 22, NULL, NULL, 1),
(64, 3, 22, NULL, NULL, 1),
(65, 3, 22, NULL, NULL, 1),
(66, 3, 22, NULL, NULL, 1),
(67, 3, 22, NULL, NULL, 1),
(68, 3, 22, NULL, NULL, 1),
(69, 3, 22, NULL, NULL, 1),
(70, 3, 22, NULL, NULL, 1),
(71, 3, 22, NULL, NULL, 1),
(72, 3, 22, NULL, NULL, 1),
(73, 3, 22, NULL, NULL, 1),
(74, 3, 22, NULL, NULL, 1),
(75, 3, 22, NULL, NULL, 1),
(76, 3, 22, NULL, NULL, 1),
(77, 3, 22, NULL, NULL, 1),
(78, 3, 22, NULL, NULL, 1),
(79, 3, 22, NULL, NULL, 1),
(80, 3, 22, NULL, NULL, 1),
(81, 3, 22, NULL, NULL, 1),
(82, 3, 22, NULL, NULL, 1),
(83, 3, 22, NULL, NULL, 1),
(84, 3, 22, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `kpi_location_sales_target`
--

CREATE TABLE `kpi_location_sales_target` (
  `id` int(11) NOT NULL,
  `mark_distribution_id` int(11) DEFAULT NULL,
  `upozila_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `regional_id` int(11) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kpi_location_sales_target`
--

INSERT INTO `kpi_location_sales_target` (`id`, `mark_distribution_id`, `upozila_id`, `district_id`, `regional_id`, `zone_id`, `amount`) VALUES
(1, 4, 15, 14, 13, 3, 200),
(2, 4, 16, 14, 13, 3, 20),
(3, 5, 15, 14, 13, 3, 30),
(4, 5, 16, 14, 13, 3, 40),
(5, 6, 15, 14, 13, 3, 50),
(6, 6, 16, 14, 13, 3, 60),
(7, 7, 15, 14, 13, 3, 70),
(8, 7, 16, 14, 13, 3, 80),
(9, 4, 17, 14, 13, 3, NULL),
(10, 5, 17, 14, 13, 3, NULL),
(11, 6, 17, 14, 13, 3, NULL),
(12, 7, 17, 14, 13, 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kpi_mark_chart`
--

CREATE TABLE `kpi_mark_chart` (
  `id` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mark` double DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `path` varchar(3000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terminal` int(11) DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `setting_group_id` int(11) DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `system_entry` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kpi_mark_chart`
--

INSERT INTO `kpi_mark_chart` (`id`, `parent`, `name`, `mark`, `level`, `path`, `terminal`, `slug`, `status`, `setting_group_id`, `description`, `system_entry`) VALUES
(1, NULL, 'Department Activities', NULL, 1, '1/', 1, 'department-activities', 1, NULL, NULL, NULL),
(2, 1, 'Sales', NULL, 2, '1/2/', 1, 'sales', 1, NULL, NULL, NULL),
(3, 2, 'Product wise  Sales Achievement', 20, 3, '1/2/3/', 1, 'product-wise-sales-achievement', 1, 3, NULL, 0),
(4, 3, 'Broiler', 5, 4, '1/2/3/4/', 1, 'broiler', 1, 4, NULL, NULL),
(5, 3, 'Layer', 5, 4, '1/2/3/5/', 1, 'layer', 1, 4, NULL, NULL),
(6, 3, 'Fish', 5, 4, '1/2/3/6/', 1, 'fish', 1, 4, NULL, NULL),
(7, 3, 'Cattle Feed', 5, 4, '1/2/3/7/', 1, 'cattle-feed', 1, 4, NULL, NULL),
(8, 4, '>=100%=5', 5, 5, '1/2/3/4/8/', 1, '100-5', 1, 5, NULL, NULL),
(9, 4, '90%-99% = 3', 3, 5, '1/2/3/4/9/', 1, '90-99-3', 1, 5, NULL, NULL),
(10, 4, '80%-99% = 2', 2, 5, '1/2/3/4/10/', 1, '80-99-2', 1, 5, NULL, NULL),
(11, 4, '<80% = 0.5', 0.5, 5, '1/2/3/4/11/', 1, '80-0-5', 1, 5, NULL, NULL),
(12, 7, '>=100%=5', 5, 5, '1/2/3/7/12/', 1, '100-5-1', 1, 5, NULL, NULL),
(13, 6, '>=100%=5', 5, 5, '1/2/3/6/13/', 1, '100-5-2', 1, 5, NULL, NULL),
(14, 5, '>=100%=5', 5, 5, '1/2/3/5/14/', 1, '100-5-3', 1, 5, NULL, NULL),
(15, 7, '90%-99% = 3', 3, 5, '1/2/3/7/15/', 1, '90-99-3-1', 1, 5, NULL, NULL),
(16, 6, '90%-99% = 3', 3, 5, '1/2/3/6/16/', 1, '90-99-3-2', 1, 5, NULL, NULL),
(17, 5, '90%-99% = 3', 3, 5, '1/2/3/5/17/', 1, '90-99-3-3', 1, 5, NULL, NULL),
(18, 7, '80%-99% = 2', 2, 5, '1/2/3/7/18/', 1, '80-99-2-1', 1, 5, NULL, NULL),
(19, 6, '80%-99% = 2', 2, 5, '1/2/3/6/19/', 1, '80-99-2-2', 1, 5, NULL, NULL),
(20, 5, '80%-99% = 2', 2, 5, '1/2/3/5/20/', 1, '80-99-2-3', 1, 5, NULL, NULL),
(21, 7, '<80% = 0.5', 0.5, 5, '1/2/3/7/21/', 1, '80-0-5-1', 1, 5, NULL, NULL),
(22, 6, '<80% = 0.5', 0.5, 5, '1/2/3/6/22/', 1, '80-0-5-2', 1, 5, NULL, NULL),
(23, 5, '<80% = 0.5', 0.5, 5, '1/2/3/5/23/', 1, '80-0-5-3', 1, 5, NULL, NULL),
(24, 2, 'All District Achievement  on Total Feed', 4, 3, '1/2/24/', 1, 'all-district-achievement-on-total-feed', 1, 2, NULL, 0),
(25, 24, 'All District achievement=4', 4, 4, '1/2/24/25/', 1, 'all-district-achievement-4', 1, 5, NULL, NULL),
(26, 24, '> 50 District achievement = 2', 2, 4, '1/2/24/26/', 1, '50-district-achievement-2', 1, 5, NULL, NULL),
(27, 24, '50%< achievement =1', 1, 4, '1/2/24/27/', 1, '50-achievement-1', 1, 5, NULL, NULL),
(28, 24, 'No District Acheivement', 0, 4, '1/2/24/28/', 1, 'no-district-acheivement', 1, 5, NULL, NULL),
(29, 2, 'Individual Team Members Achievement(%) on Total Feed', 12, 3, '1/2/29/', 1, 'individual-team-members-achievement-on-total-feed', 1, 3, NULL, NULL),
(30, 2, 'Product Sales Growth over last yeart', 12, 3, '1/2/30/', 1, 'product-sales-growth-over-last-yeart', 1, 2, NULL, NULL),
(31, 29, 'All Members ach = 4', 4, 4, '1/2/29/31/', 1, 'all-members-ach-4', 1, 5, NULL, NULL),
(32, 29, '>50% ach=2', 2, 4, '1/2/29/32/', 1, '50-ach-2', 1, 5, NULL, NULL),
(33, 29, '<50% ach=1', 1, 4, '1/2/29/33/', 1, '50-ach-1', 1, 5, NULL, 0),
(34, 29, 'No Team Members ach(100%)', 0, 4, '1/2/29/34/', 1, 'no-team-members-ach-100', 1, 5, NULL, 0),
(35, 30, 'growth 15%=3', 3, 4, '1/2/30/35/', 1, 'growth-15-3', 1, 5, NULL, 0),
(36, 30, '10-14%=2', 2, 4, '1/2/30/36/', 1, '10-14-2', 1, 5, NULL, 0),
(37, 30, '<10%=1', 1, 4, '1/2/30/37/', 1, '10-1', 1, 5, NULL, 0),
(39, 1, 'Outstanding', NULL, 2, '1/39/', 1, 'outstanding', 1, 5, NULL, 0),
(40, 39, 'Outstanding Limit vs Actual(Feed)', 12, 3, '1/39/40/', 1, 'outstanding-limit-vs-actual-feed', 1, 3, NULL, 0),
(41, 40, 'Outstanding within limit =12', 12, 4, '1/39/40/41/', 1, 'outstanding-within-limit-12', 1, 5, NULL, 0),
(42, 40, 'outstanding within limit=12', 12, 4, '1/39/40/42/', 1, 'outstanding-within-limit-12-1', 1, 5, NULL, 0),
(43, 40, 'outstanding >10 Lack=0', 0, 4, '1/39/40/43/', 1, 'outstanding-10-lack-0', 1, 5, NULL, 0),
(44, 40, 'outstanding 5-9 lacks=5', 5, 4, '1/39/40/44/', 1, 'outstanding-5-9-lacks-5', 1, 5, NULL, 0),
(45, 40, 'outstanding <3 Lack=8', 8, 4, '1/39/40/45/', 1, 'outstanding-3-lack-8', 1, 5, NULL, 0),
(46, 39, 'No of agents do cash business(More than 75% of total agents)', 5, 3, '1/39/46/', 1, 'no-of-agents-do-cash-business-more-than-75-of-total-agents', 1, 3, NULL, 0),
(48, 46, '75%=5', 5, 4, '1/39/46/48/', 1, '75-5', 1, 5, NULL, 0),
(49, 46, '60-70%=4', 4, 4, '1/39/46/49/', 1, '60-70-4', 1, 5, NULL, 0),
(50, 46, '50%-59%=3', 3, 4, '1/39/46/50/', 1, '50-59-3', 1, 5, NULL, 0),
(51, 46, '<50%=2', 2, 4, '1/39/46/51/', 1, '50-2', 1, 5, NULL, 0),
(53, 39, 'Chick Sales collection', 5, 3, '1/39/53/', 1, 'chick-sales-collection', 1, 3, NULL, 0),
(54, 53, '100%=5', 5, 4, '1/39/53/54/', 1, '100-5-4', 1, 5, NULL, 0),
(55, 53, '91%-99%=4', 4, 4, '1/39/53/55/', 1, '91-99-4', 1, 5, NULL, 0),
(56, 53, '80=90%=2', 2, 4, '1/39/53/56/', 1, '80-90-2', 1, 5, NULL, 0),
(57, 53, '<80%=0', 0, 4, '1/39/53/57/', 1, '80-0', 1, 5, NULL, 0),
(58, 1, 'Customer Development', NULL, 2, '1/58/', 1, 'customer-development', 1, 2, NULL, 0),
(59, 58, 'develop existing customer sales volume', 3, 3, '1/58/59/', 1, 'develop-existing-customer-sales-volume', 1, 3, NULL, 1),
(60, 59, '>40% agents sales develop=3', 3, 4, '1/58/59/60/', 1, '40-agents-sales-develop-3', 1, 5, NULL, 1),
(61, 59, '20%-30% agents sales develop=2', 2, 4, '1/58/59/61/', 1, '20-30-agents-sales-develop-2', 1, 5, NULL, 1),
(62, 59, '>50% agents sales decline=0 (provide supporting document)', 0, 4, '1/58/59/62/', 1, '50-agents-sales-decline-0-provide-supporting-document', 1, 5, NULL, 1),
(64, 58, 'New agents', 8, 3, '1/58/64/', 1, 'new-agents', 1, 3, NULL, 1),
(65, 64, 'Average agents monthly >2 &  Maximum \"A\" category =8', 8, 4, '1/58/64/65/', 1, 'average-agents-monthly-2-maximum-a-category-8', 1, 5, NULL, 1),
(66, 64, 'Maximum \"B\" category agents=6', 6, 4, '1/58/64/66/', 1, 'maximum-b-category-agents-6', 1, 5, NULL, 1),
(67, 64, 'Maximum \"C\" category agents=4', 4, 4, '1/58/64/67/', 1, 'maximum-c-category-agents-4', 1, 5, NULL, 1),
(68, 64, 'No new Agents=0', 0, 4, '1/58/64/68/', 1, 'no-new-agents-0', 1, 5, NULL, 1),
(69, 64, 'Average 1 agents/ monthly any category=3', 3, 4, '1/58/64/69/', 1, 'average-1-agents-monthly-any-category-3', 1, 5, NULL, 1),
(71, 58, 'New farmers development (mention number)', 3, 3, '1/58/71/', 1, 'new-farmers-development-mention-number', 1, 3, NULL, 1),
(72, 71, 'Yes=3', 3, 4, '1/58/71/72/', 1, 'yes-3', 1, 5, NULL, 1),
(73, 71, 'No=0', 0, 4, '1/58/71/73/', 1, 'no-0', 1, 5, NULL, 1),
(75, 58, 'Convert competitive farmers to nourish (Attach document)', 3, 3, '1/58/75/', 1, 'convert-competitive-farmers-to-nourish-attach-document', 1, 3, NULL, 1),
(76, 75, 'Yes=3', 3, 4, '1/58/75/76/', 1, 'yes-3-1', 1, 5, NULL, 1),
(77, 75, 'No=0', 0, 4, '1/58/75/77/', 1, 'no-0-1', 1, 5, NULL, 1),
(78, 1, 'Communication', NULL, 2, '1/78/', 1, 'communication', 1, 2, NULL, 1),
(79, 78, 'Time management: All types of activity & reporting completed by', 3, 3, '1/78/79/', 1, 'time-management-all-types-of-activity-reporting-completed-by', 1, 3, NULL, 1),
(80, 79, 'Send before deadline=3', 3, 4, '1/78/79/80/', 1, 'send-before-deadline-3', 1, 5, NULL, 1),
(81, 79, 'within deadline=2', 2, 4, '1/78/79/81/', 1, 'within-deadline-2', 1, 5, NULL, 0),
(82, 79, 'after deadline=0', 0, 4, '1/78/79/82/', 1, 'after-deadline-0', 1, 5, NULL, 1),
(94, 78, 'Effective communication with colleague', 3, 3, '1/78/94/', 1, 'effective-communication-with-colleague', 1, 3, NULL, 1),
(95, 94, '100% under coverage=3', 3, 4, '1/78/94/95/', 1, '100-under-coverage-3', 1, 5, NULL, 1),
(96, 94, '80% well coverage=2', 2, 4, '1/78/94/96/', 1, '80-well-coverage-2', 1, 5, NULL, 1),
(97, 94, 'below 80% coverage=1', 1, 4, '1/78/94/97/', 1, 'below-80-coverage-1', 1, 5, NULL, 1),
(98, NULL, 'Core Competency', NULL, 1, '98/', 1, 'core-competency', 1, 1, NULL, 1),
(99, 98, 'Skill', NULL, 2, '98/99/', 1, 'skill', 1, 2, NULL, 1),
(100, 99, 'Job knowledge: Understanding of the technical side the job and add value for customer', 2, 3, '98/99/100/', 1, 'job-knowledge-understanding-of-the-technical-side-the-job-and-add-value-for-customer', 1, 3, NULL, 1),
(101, 100, 'High technical knowledge and add value for customer=2', 2, 4, '98/99/100/101/', 1, 'high-technical-knowledge-and-add-value-for-customer-2', 1, 5, NULL, 1),
(102, 100, 'High technical knowledge but add no value for customer=0', 0, 4, '98/99/100/102/', 1, 'high-technical-knowledge-but-add-no-value-for-customer-0', 1, 5, NULL, 1),
(104, 99, 'Problem solving: Consistently proactive for problem solving', 2, 3, '98/99/104/', 1, 'problem-solving-consistently-proactive-for-problem-solving', 1, 3, NULL, 1),
(105, 104, 'Always proactive for problem solving=2', 2, 4, '98/99/104/105/', 1, 'always-proactive-for-problem-solving-2', 1, 5, NULL, 1),
(106, 104, 'Very casual for problem solving=1', 1, 4, '98/99/104/106/', 1, 'very-casual-for-problem-solving-1', 1, 5, NULL, 1),
(107, 104, 'No initiative for problem solving=0', 0, 4, '98/99/104/107/', 1, 'no-initiative-for-problem-solving-0', 1, 5, NULL, 1),
(108, 99, 'Creativity & Willingness: Implementation self creativity & strong willing power for success', 2, 3, '98/99/108/', 1, 'creativity-willingness-implementation-self-creativity-strong-willing-power-for-success', 1, 3, NULL, 1),
(109, 108, 'Show creativity & willingness for success=2', 2, 4, '98/99/108/109/', 1, 'show-creativity-willingness-for-success-2', 1, 5, NULL, 1),
(110, 108, 'No creativity & Willingness=0', 0, 4, '98/99/108/110/', 1, 'no-creativity-willingness-0', 1, 5, NULL, 1),
(111, 98, 'Values', NULL, 2, '98/111/', 1, 'values', 1, 1, NULL, 1),
(112, 111, 'Integrity: Honesty,sense of right and wrong, standard of values and moral codes', 3, 3, '98/111/112/', 1, 'integrity-honesty-sense-of-right-and-wrong-standard-of-values-and-moral-codes', 1, 3, NULL, 1),
(113, 112, 'High =3', 3, 4, '98/111/112/113/', 1, 'high-3', 1, 5, NULL, 1),
(114, 112, 'Medium=2', 2, 4, '98/111/112/114/', 1, 'medium-2', 1, 5, NULL, 1),
(115, 112, 'low=1', 1, 4, '98/111/112/115/', 1, 'low-1', 1, 5, NULL, 1),
(116, 111, 'continuous improvement: interested to take new tasks and show intentions to change ways for better solutions', 3, 3, '98/111/116/', 1, 'continuous-improvement-interested-to-take-new-tasks-and-show-intentions-to-change-ways-for-better-solutions', 1, 3, NULL, 1),
(117, 116, 'High =3', 3, 4, '98/111/116/117/', 1, 'high-3-1', 1, 5, NULL, 1),
(118, 116, 'Medium=2', 2, 4, '98/111/116/118/', 1, 'medium-2-1', 1, 5, NULL, 1),
(119, 116, 'low=1', 1, 4, '98/111/116/119/', 1, 'low-1-1', 1, 5, NULL, 1),
(120, 111, 'Humility:Shows positive attitude and respect to others', 3, 3, '98/111/120/', 1, 'humility-shows-positive-attitude-and-respect-to-others', 1, 3, NULL, 1),
(121, 120, 'High =3', 3, 4, '98/111/120/121/', 1, 'high-3-2', 1, 5, NULL, 1),
(122, 120, 'Medium=2', 2, 4, '98/111/120/122/', 1, 'medium-2-2', 1, 5, NULL, 1),
(123, 120, 'low=1', 1, 4, '98/111/120/123/', 1, 'low-1-2', 1, 5, NULL, 1),
(124, 111, 'People smartness: Shows ability to understand and interact effectively with others', 3, 3, '98/111/124/', 1, 'people-smartness-shows-ability-to-understand-and-interact-effectively-with-others', 1, 3, NULL, 1),
(125, 124, 'High =3', 3, 4, '98/111/124/125/', 1, 'high-3-3', 1, 5, NULL, 1),
(126, 124, 'Medium=2', 2, 4, '98/111/124/126/', 1, 'medium-2-3', 1, 5, NULL, 1),
(127, 124, 'low=1', 1, 4, '98/111/124/127/', 1, 'low-1-3', 1, 5, NULL, 1),
(128, 98, 'Compliance with company Goals', NULL, 2, '98/128/', 1, 'compliance-with-company-goals', 1, 1, NULL, 1),
(129, 128, 'Quality leadership: Work is always of a high standard, demonstrating precision and accuracy and a concern for quality', 3, 3, '98/128/129/', 1, 'quality-leadership-work-is-always-of-a-high-standard-demonstrating-precision-and-accuracy-and-a-concern-for-quality', 1, 3, NULL, 1),
(130, 129, 'High =3', 3, 4, '98/128/129/130/', 1, 'high-3-4', 1, 5, NULL, 1),
(131, 129, 'Medium=2', 2, 4, '98/128/129/131/', 1, 'medium-2-4', 1, 5, NULL, 1),
(132, 129, 'low=1', 1, 4, '98/128/129/132/', 1, 'low-1-4', 1, 5, NULL, 1),
(133, 128, 'Contribution Over Consumption:focus in on continuous contribution rather than reward in return', 3, 3, '98/128/133/', 1, 'contribution-over-consumption-focus-in-on-continuous-contribution-rather-than-reward-in-return', 1, 3, NULL, 1),
(134, 133, 'High =3', 3, 4, '98/128/133/134/', 1, 'high-3-5', 1, 5, NULL, 1),
(135, 133, 'Medium=2', 2, 4, '98/128/133/135/', 1, 'medium-2-5', 1, 5, NULL, 1),
(136, 133, 'low=1', 1, 4, '98/128/133/136/', 1, 'low-1-5', 1, 5, NULL, 1),
(137, 128, 'Customer First: Give best services to the internal and external customers', 3, 3, '98/128/137/', 1, 'customer-first-give-best-services-to-the-internal-and-external-customers', 1, 3, NULL, 1),
(138, 137, 'High =3', 3, 4, '98/128/137/138/', 1, 'high-3-6', 1, 5, NULL, 1),
(139, 137, 'Medium=2', 2, 4, '98/128/137/139/', 1, 'medium-2-6', 1, 5, NULL, 1),
(140, 137, 'low=1', 1, 4, '98/128/137/140/', 1, 'low-1-6', 1, 5, NULL, 1),
(141, 78, 'Effective communication with dealer', 3, 3, '1/78/141/', 1, 'effective-communication-with-dealer', 1, 3, NULL, 1),
(142, 141, '100% Dealer under coverage=3', 3, 4, '1/78/141/142/', 1, '100-dealer-under-coverage-3', 1, 5, NULL, 1),
(143, 141, '80% dealer well coverage=2', 2, 4, '1/78/141/143/', 1, '80-dealer-well-coverage-2', 1, 5, NULL, 1),
(144, 141, 'below 80% dealer coverage=1', 1, 4, '1/78/141/144/', 1, 'below-80-dealer-coverage-1', 1, 5, NULL, 1),
(145, 78, 'Effective communication with top management', 3, 3, '1/78/145/', 1, 'effective-communication-with-top-management', 1, 3, NULL, 1),
(146, 145, 'Most Frequent=3', 3, 4, '1/78/145/146/', 1, 'most-frequent-3', 1, 5, NULL, 1),
(147, 145, 'As need basis=2', 2, 4, '1/78/145/147/', 1, 'as-need-basis-2', 1, 5, NULL, 1),
(148, 145, 'Poor Communication=1', 1, 4, '1/78/145/148/', 1, 'poor-communication-1', 1, 5, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `kpi_setting`
--

CREATE TABLE `kpi_setting` (
  `id` int(11) NOT NULL,
  `setting_type_id` int(11) DEFAULT NULL,
  `terminal` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_bn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kpi_setting`
--

INSERT INTO `kpi_setting` (`id`, `setting_type_id`, `terminal`, `name`, `name_bn`, `slug`, `status`, `description`) VALUES
(1, 1, 1, 'Departments Activities', NULL, 'departments-activities', 1, NULL),
(2, 1, 1, 'Departmental Activities', NULL, 'departmental-activities', 1, NULL),
(3, 1, 1, 'Core Competency', NULL, 'core-competency', 1, NULL),
(4, 1, 1, 'Values', NULL, 'values', 1, NULL),
(5, 1, 1, 'Compliance with Company Goals', NULL, 'compliance-with-company-goals', 1, NULL),
(6, 2, 1, 'Sales', NULL, 'sales', 1, NULL),
(7, 2, 1, 'Outstanding', NULL, 'outstanding', 1, NULL),
(8, 2, 1, 'Customer Development', NULL, 'customer-development', 1, NULL),
(9, 2, 1, 'Communication', NULL, 'communication', 1, NULL),
(10, 2, 1, 'Skill', NULL, 'skill', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kpi_setting_type`
--

CREATE TABLE `kpi_setting_type` (
  `id` int(11) NOT NULL,
  `terminal` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kpi_setting_type`
--

INSERT INTO `kpi_setting_type` (`id`, `terminal`, `name`, `slug`, `status`) VALUES
(1, 1, 'Parameter', 'parameter', 1),
(2, 1, 'Activity', 'activity', 1),
(3, 1, 'Attributes', 'attributes', 1),
(4, 1, 'Marks', 'marks', 1),
(5, 1, 'Mark Distribution', 'mark-distribution', 1);

-- --------------------------------------------------------

--
-- Table structure for table `kpi_setup`
--

CREATE TABLE `kpi_setup` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kpi_setup`
--

INSERT INTO `kpi_setup` (`id`, `employee_id`, `status`) VALUES
(22, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `kpi_setup_matrix`
--

CREATE TABLE `kpi_setup_matrix` (
  `id` int(11) NOT NULL,
  `employee_setup_id` int(11) DEFAULT NULL,
  `mark_distribution_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `upozila_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `regional_id` int(11) DEFAULT NULL,
  `zonal_id` int(11) DEFAULT NULL,
  `mark_chart_id` int(11) DEFAULT NULL,
  `sales_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kpi_setup_matrix`
--

INSERT INTO `kpi_setup_matrix` (`id`, `employee_setup_id`, `mark_distribution_id`, `amount`, `status`, `upozila_id`, `district_id`, `regional_id`, `zonal_id`, `mark_chart_id`, `sales_id`) VALUES
(41, 22, NULL, NULL, 1, 16, NULL, NULL, NULL, NULL, 4),
(42, 22, NULL, NULL, 1, 15, NULL, NULL, NULL, NULL, 5),
(43, 22, NULL, NULL, 1, 16, NULL, NULL, NULL, NULL, 6),
(44, 22, NULL, NULL, 1, 15, NULL, NULL, NULL, NULL, 7),
(45, 22, NULL, NULL, 1, 16, NULL, NULL, NULL, NULL, 4),
(46, 22, NULL, NULL, 1, 15, NULL, NULL, NULL, NULL, 5),
(47, 22, NULL, NULL, 1, 16, NULL, NULL, NULL, NULL, 6),
(48, 22, NULL, NULL, 1, 15, NULL, NULL, NULL, NULL, 7);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `level` int(11) DEFAULT NULL,
  `path` varchar(3000) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `parent_id`, `name`, `level`, `path`) VALUES
(1, NULL, 'Gazipur', 1, '/1'),
(2, NULL, 'Chattogram', 1, '/2'),
(3, NULL, 'Rajshahi', 1, '/3'),
(4, NULL, 'Rangpur', 1, '/4'),
(5, NULL, 'Khulna', 1, '/5'),
(6, NULL, 'Barishal', 1, '/6'),
(7, NULL, 'Sylhet', 1, '/7'),
(8, 1, 'Dhaka', 2, '/1/8'),
(9, 1, 'Faridpur', 2, '/1/9'),
(10, 1, 'Gazipur', 2, '/1/10'),
(11, 1, 'Gopalganj', 2, '/1/11'),
(12, 1, 'Jamalpur', 2, '/1/12'),
(13, 1, 'Kishorgonj', 2, '/1/13'),
(14, 1, 'Madaripur', 2, '/1/14'),
(15, 1, 'Manikganj', 2, '/1/15'),
(16, 1, 'Munshiganj', 2, '/1/16'),
(17, 1, 'Mymensingh', 2, '/1/17'),
(18, 1, 'Narayanganj', 2, '/1/18'),
(19, 1, 'Narsingdi', 2, '/1/19'),
(20, 1, 'Netrakona', 2, '/1/20'),
(21, 1, 'Rajbari', 2, '/1/21'),
(22, 1, 'Shariatpur', 2, '/1/22'),
(23, 1, 'Sherpur', 2, '/1/23'),
(24, 1, 'Tangail', 2, '/1/24'),
(25, 2, 'Bandarban', 2, '/2/25'),
(26, 2, 'Brahmanbaria', 2, '/2/26'),
(27, 2, 'Chandpur', 2, '/2/27'),
(28, 2, 'Chittagong', 2, '/2/28'),
(29, 2, 'Comilla', 2, '/2/29'),
(30, 2, 'Cox\'s bazar', 2, '/2/30'),
(31, 2, 'Feni', 2, '/2/31'),
(32, 2, 'Khagrachhari', 2, '/2/32'),
(33, 2, 'Lakshmipur', 2, '/2/33'),
(34, 2, 'Noakhali', 2, '/2/34'),
(35, 2, 'Rangamati', 2, '/2/35'),
(36, 3, 'Bogra', 2, '/3/36'),
(37, 3, 'Chapai nawabganj', 2, '/3/37'),
(38, 3, 'Joypurhat', 2, '/3/38'),
(39, 3, 'Naogaon', 2, '/3/39'),
(40, 3, 'Natore', 2, '/3/40'),
(41, 3, 'Pabna', 2, '/3/41'),
(42, 3, 'Rajshahi', 2, '/3/42'),
(43, 3, 'Sirajganj', 2, '/3/43'),
(44, 4, 'Dinajpur', 2, '/4/44'),
(45, 4, 'Gaibandha', 2, '/4/45'),
(46, 4, 'Kurigram', 2, '/4/46'),
(47, 4, 'Lalmonirhat', 2, '/4/47'),
(48, 4, 'Nilphamari', 2, '/4/48'),
(49, 4, 'Panchagarh', 2, '/4/49'),
(50, 4, 'Rangpur', 2, '/4/50'),
(51, 4, 'Thakurgaon', 2, '/4/51'),
(52, 5, 'Bagerhat', 2, '/5/52'),
(53, 5, 'Chuadanga', 2, '/5/53'),
(54, 5, 'Jessore', 2, '/5/54'),
(55, 5, 'Jhenaidah', 2, '/5/55'),
(56, 5, 'Khulna', 2, '/5/56'),
(57, 5, 'Kushtia', 2, '/5/57'),
(58, 5, 'Magura', 2, '/5/58'),
(59, 5, 'Meherpur', 2, '/5/59'),
(60, 5, 'Narail', 2, '/5/60'),
(61, 5, 'Satkhira', 2, '/5/61'),
(62, 6, 'Barguna', 2, '/6/62'),
(63, 6, 'Barisal', 2, '/6/63'),
(64, 6, 'Bhola', 2, '/6/64'),
(65, 6, 'Jhalokati', 2, '/6/65'),
(66, 6, 'Patuakhali', 2, '/6/66'),
(67, 6, 'Pirojpur', 2, '/6/67'),
(68, 7, 'Habiganj', 2, '/7/68'),
(69, 7, 'Maulvibazar', 2, '/7/69'),
(70, 7, 'Sunamganj', 2, '/7/70'),
(71, 7, 'Sylhet', 2, '/7/71'),
(72, 25, 'Alikadam', 3, '/2/25/72'),
(73, 25, 'Bandarban sadar', 3, '/2/25/73'),
(74, 25, 'Lama', 3, '/2/25/74'),
(75, 25, 'Naikhongchhari', 3, '/2/25/75'),
(76, 25, 'Rowangchhari', 3, '/2/25/76'),
(77, 25, 'Ruma', 3, '/2/25/77'),
(78, 25, 'Thanchi', 3, '/2/25/78'),
(79, 26, 'Akhaura', 3, '/2/26/79'),
(80, 26, 'Banchharampur', 3, '/2/26/80'),
(81, 26, 'Bijoynagar', 3, '/2/26/81'),
(82, 26, 'Brahmanbaria sadar', 3, '/2/26/82'),
(83, 26, 'Ashuganj', 3, '/2/26/83'),
(84, 26, 'Kasba', 3, '/2/26/84'),
(85, 26, 'Nabinagar', 3, '/2/26/85'),
(86, 26, 'Nasirnagar', 3, '/2/26/86'),
(87, 26, 'Sarail', 3, '/2/26/87'),
(88, 27, 'Chandpur sadar', 3, '/2/27/88'),
(89, 27, 'Faridganj', 3, '/2/27/89'),
(90, 27, 'Haim char', 3, '/2/27/90'),
(91, 27, 'Hajiganj', 3, '/2/27/91'),
(92, 27, 'Kachua', 3, '/2/27/92'),
(93, 27, 'Matlab dakshin', 3, '/2/27/93'),
(94, 27, 'Matlab uttar', 3, '/2/27/94'),
(95, 27, 'Shahrasti', 3, '/2/27/95'),
(96, 28, 'Anowara', 3, '/2/28/96'),
(97, 28, 'Bayejid bostami', 3, '/2/28/97'),
(98, 28, 'Banshkhali', 3, '/2/28/98'),
(99, 28, 'Bakalia', 3, '/2/28/99'),
(100, 28, 'Boalkhali', 3, '/2/28/100'),
(101, 28, 'Chandanaish', 3, '/2/28/101'),
(102, 28, 'Chandgaon', 3, '/2/28/102'),
(103, 28, 'Chittagong port', 3, '/2/28/103'),
(104, 28, 'Double mooring', 3, '/2/28/104'),
(105, 28, 'Fatikchhari', 3, '/2/28/105'),
(106, 28, 'Halishahar', 3, '/2/28/106'),
(107, 28, 'Hathazari', 3, '/2/28/107'),
(108, 28, 'Kotwali', 3, '/2/28/108'),
(109, 28, 'Khulshi', 3, '/2/28/109'),
(110, 28, 'Lohagara', 3, '/2/28/110'),
(111, 28, 'Mirsharai', 3, '/2/28/111'),
(112, 28, 'Pahartali', 3, '/2/28/112'),
(113, 28, 'Panchlaish', 3, '/2/28/113'),
(114, 28, 'Patiya', 3, '/2/28/114'),
(115, 28, 'Patenga', 3, '/2/28/115'),
(116, 28, 'Rangunia', 3, '/2/28/116'),
(117, 28, 'Raozan', 3, '/2/28/117'),
(118, 28, 'Sandwip', 3, '/2/28/118'),
(119, 28, 'Satkania', 3, '/2/28/119'),
(120, 28, 'Sitakunda', 3, '/2/28/120'),
(121, 29, 'Barura', 3, '/2/29/121'),
(122, 29, 'Brahman para', 3, '/2/29/122'),
(123, 29, 'Burichang', 3, '/2/29/123'),
(124, 29, 'Chandina', 3, '/2/29/124'),
(125, 29, 'Chauddagram', 3, '/2/29/125'),
(126, 29, 'Comilla sadar dakshin', 3, '/2/29/126'),
(127, 29, 'Daudkandi', 3, '/2/29/127'),
(128, 29, 'Debidwar', 3, '/2/29/128'),
(129, 29, 'Homna', 3, '/2/29/129'),
(130, 29, 'Comilla adarsha sadar', 3, '/2/29/130'),
(131, 29, 'Laksam', 3, '/2/29/131'),
(132, 29, 'Manoharganj', 3, '/2/29/132'),
(133, 29, 'Meghna', 3, '/2/29/133'),
(134, 29, 'Muradnagar', 3, '/2/29/134'),
(135, 29, 'Nangalkot', 3, '/2/29/135'),
(136, 29, 'Titas', 3, '/2/29/136'),
(137, 30, 'Chakaria', 3, '/2/30/137'),
(138, 30, 'Cox\'s bazar sadar', 3, '/2/30/138'),
(139, 30, 'Kutubdia', 3, '/2/30/139'),
(140, 30, 'Maheshkhali', 3, '/2/30/140'),
(141, 30, 'Pekua', 3, '/2/30/141'),
(142, 30, 'Ramu', 3, '/2/30/142'),
(143, 30, 'Teknaf', 3, '/2/30/143'),
(144, 30, 'Ukhia', 3, '/2/30/144'),
(145, 31, 'Chhagalnaiya', 3, '/2/31/145'),
(146, 31, 'Daganbhuiyan', 3, '/2/31/146'),
(147, 31, 'Feni sadar', 3, '/2/31/147'),
(148, 31, 'Fulgazi', 3, '/2/31/148'),
(149, 31, 'Parshuram', 3, '/2/31/149'),
(150, 31, 'Sonagazi', 3, '/2/31/150'),
(151, 32, 'Dighinala', 3, '/2/32/151'),
(152, 32, 'Khagrachhari sadar', 3, '/2/32/152'),
(153, 32, 'Lakshmichhari', 3, '/2/32/153'),
(154, 32, 'Mahalchhari', 3, '/2/32/154'),
(155, 32, 'Manikchhari', 3, '/2/32/155'),
(156, 32, 'Matiranga', 3, '/2/32/156'),
(157, 32, 'Panchhari', 3, '/2/32/157'),
(158, 32, 'Ramgarh', 3, '/2/32/158'),
(159, 33, 'Kamalnagar', 3, '/2/33/159'),
(160, 62, 'Amtali', 3, '/6/62/160'),
(161, 62, 'Bamna', 3, '/6/62/161'),
(162, 62, 'Barguna sadar', 3, '/6/62/162'),
(163, 62, 'Betagi', 3, '/6/62/163'),
(164, 62, 'Patharghata', 3, '/6/62/164'),
(165, 62, 'Taltali', 3, '/6/62/165'),
(166, 63, 'Agailjhara', 3, '/6/63/166'),
(167, 63, 'Babuganj', 3, '/6/63/167'),
(168, 63, 'Bakerganj', 3, '/6/63/168'),
(169, 63, 'Banari para', 3, '/6/63/169'),
(170, 63, 'Gaurnadi', 3, '/6/63/170'),
(171, 63, 'Hizla', 3, '/6/63/171'),
(172, 63, 'Barisal sadar (kotwali)', 3, '/6/63/172'),
(173, 63, 'Mhendiganj', 3, '/6/63/173'),
(174, 63, 'Muladi', 3, '/6/63/174'),
(175, 63, 'Wazirpur', 3, '/6/63/175'),
(176, 64, 'Bhola sadar', 3, '/6/64/176'),
(177, 64, 'Burhanuddin', 3, '/6/64/177'),
(178, 64, 'Char fasson', 3, '/6/64/178'),
(179, 64, 'Daulat khan', 3, '/6/64/179'),
(180, 64, 'Lalmohan', 3, '/6/64/180'),
(181, 64, 'Manpura', 3, '/6/64/181'),
(182, 64, 'Tazumuddin', 3, '/6/64/182'),
(183, 65, 'Jhalokati sadar', 3, '/6/65/183'),
(184, 65, 'Kanthalia', 3, '/6/65/184'),
(185, 65, 'Nalchity', 3, '/6/65/185'),
(186, 65, 'Rajapur', 3, '/6/65/186'),
(187, 66, 'Bauphal', 3, '/6/66/187'),
(188, 66, 'Dashmina', 3, '/6/66/188'),
(189, 66, 'Dumki', 3, '/6/66/189'),
(190, 66, 'Galachipa', 3, '/6/66/190'),
(191, 66, 'Kalapara', 3, '/6/66/191'),
(192, 66, 'Mirzaganj', 3, '/6/66/192'),
(193, 66, 'Patuakhali sadar', 3, '/6/66/193'),
(194, 66, 'Rangabali', 3, '/6/66/194'),
(195, 67, 'Bhandaria', 3, '/6/67/195'),
(196, 67, 'Kawkhali', 3, '/6/67/196'),
(197, 67, 'Mathbaria', 3, '/6/67/197'),
(198, 67, 'Nazirpur', 3, '/6/67/198'),
(199, 67, 'Pirojpur sadar', 3, '/6/67/199'),
(200, 67, 'Nesarabad (swarupkati)', 3, '/6/67/200'),
(201, 67, 'Zianagar', 3, '/6/67/201'),
(202, 8, 'Adabor', 3, '/1/8/202'),
(203, 8, 'Badda', 3, '/1/8/203'),
(204, 8, 'Bangshal', 3, '/1/8/204'),
(205, 8, 'Biman bandar', 3, '/1/8/205'),
(206, 8, 'Banani', 3, '/1/8/206'),
(207, 8, 'Cantonment', 3, '/1/8/207'),
(208, 8, 'Chak bazar', 3, '/1/8/208'),
(209, 8, 'Dakshinkhan', 3, '/1/8/209'),
(210, 8, 'Darus salam', 3, '/1/8/210'),
(211, 8, 'Demra', 3, '/1/8/211'),
(212, 8, 'Dhamrai', 3, '/1/8/212'),
(213, 8, 'Dohar', 3, '/1/8/213'),
(214, 8, 'Bhasan tek', 3, '/1/8/214'),
(215, 8, 'Bhatara', 3, '/1/8/215'),
(216, 8, 'Gendaria', 3, '/1/8/216'),
(217, 8, 'Gulshan', 3, '/1/8/217'),
(218, 8, 'Jatrabari', 3, '/1/8/218'),
(219, 8, 'Kafrul', 3, '/1/8/219'),
(220, 8, 'Kadamtali', 3, '/1/8/220'),
(221, 8, 'Kalabagan', 3, '/1/8/221'),
(222, 8, 'Kamrangir char', 3, '/1/8/222'),
(223, 8, 'Khilgaon', 3, '/1/8/223'),
(224, 8, 'Khilkhet', 3, '/1/8/224'),
(225, 8, 'Keraniganj', 3, '/1/8/225'),
(226, 8, 'Kotwali', 3, '/1/8/226'),
(227, 8, 'Lalbagh', 3, '/1/8/227'),
(228, 8, 'Mirpur', 3, '/1/8/228'),
(229, 8, 'Motijheel', 3, '/1/8/229'),
(230, 8, 'Mugda para', 3, '/1/8/230'),
(231, 8, 'Nawabganj', 3, '/1/8/231'),
(232, 8, 'New market', 3, '/1/8/232'),
(233, 8, 'Pallabi', 3, '/1/8/233'),
(234, 8, 'Paltan', 3, '/1/8/234'),
(235, 8, 'Rampura', 3, '/1/8/235'),
(236, 8, 'Sabujbagh', 3, '/1/8/236'),
(237, 8, 'Rupnagar', 3, '/1/8/237'),
(238, 8, 'Savar', 3, '/1/8/238'),
(239, 8, 'Shahjahanpur', 3, '/1/8/239'),
(240, 8, 'Shah ali', 3, '/1/8/240'),
(241, 8, 'Shahbagh', 3, '/1/8/241'),
(242, 8, 'Shyampur', 3, '/1/8/242'),
(243, 8, 'Sher-e-bangla nagar', 3, '/1/8/243'),
(244, 8, 'Sutrapur', 3, '/1/8/244'),
(245, 8, 'Tejgaon', 3, '/1/8/245'),
(246, 8, 'Tejgaon ind. area', 3, '/1/8/246'),
(247, 8, 'Turag', 3, '/1/8/247'),
(248, 8, 'Uttara  paschim', 3, '/1/8/248'),
(249, 8, 'Uttara  purba', 3, '/1/8/249'),
(250, 8, 'Uttar khan', 3, '/1/8/250'),
(251, 8, 'Wari', 3, '/1/8/251'),
(252, 9, 'Alfadanga', 3, '/1/9/252'),
(253, 9, 'Bhanga', 3, '/1/9/253'),
(254, 9, 'Boalmari', 3, '/1/9/254'),
(255, 9, 'Char bhadrasan', 3, '/1/9/255'),
(256, 9, 'Faridpur sadar', 3, '/1/9/256'),
(257, 9, 'Madhukhali', 3, '/1/9/257'),
(258, 9, 'Nagarkanda', 3, '/1/9/258'),
(259, 9, 'Sadarpur', 3, '/1/9/259'),
(260, 9, 'Saltha', 3, '/1/9/260'),
(261, 10, 'Gazipur sadar', 3, '/1/10/261'),
(262, 10, 'Kaliakair', 3, '/1/10/262'),
(263, 10, 'Kaliganj', 3, '/1/10/263'),
(264, 10, 'Kapasia', 3, '/1/10/264'),
(265, 10, 'Sreepur', 3, '/1/10/265'),
(266, 11, 'Gopalganj sadar', 3, '/1/11/266'),
(267, 11, 'Kashiani', 3, '/1/11/267'),
(268, 11, 'Kotalipara', 3, '/1/11/268'),
(269, 11, 'Muksudpur', 3, '/1/11/269'),
(270, 11, 'Tungipara', 3, '/1/11/270'),
(271, 12, 'Bakshiganj', 3, '/1/12/271'),
(272, 12, 'Dewanganj', 3, '/1/12/272'),
(273, 12, 'Islampur', 3, '/1/12/273'),
(274, 12, 'Jamalpur sadar', 3, '/1/12/274'),
(275, 12, 'Madarganj', 3, '/1/12/275'),
(276, 12, 'Melandaha', 3, '/1/12/276'),
(277, 12, 'Sarishabari upazila', 3, '/1/12/277'),
(278, 13, 'Austagram', 3, '/1/13/278'),
(279, 13, 'Bajitpur', 3, '/1/13/279'),
(280, 13, 'Bhairab', 3, '/1/13/280'),
(281, 13, 'Hossainpur', 3, '/1/13/281'),
(282, 13, 'Itna', 3, '/1/13/282'),
(283, 13, 'Karimganj', 3, '/1/13/283'),
(284, 13, 'Katiadi', 3, '/1/13/284'),
(285, 13, 'Kishoreganj sadar', 3, '/1/13/285'),
(286, 13, 'Kuliar char', 3, '/1/13/286'),
(287, 13, 'Mithamain', 3, '/1/13/287'),
(288, 13, 'Nikli', 3, '/1/13/288'),
(289, 13, 'Pakundia', 3, '/1/13/289'),
(290, 13, 'Tarail', 3, '/1/13/290'),
(291, 14, 'Kalkini', 3, '/1/14/291'),
(292, 14, 'Madaripur sadar', 3, '/1/14/292'),
(293, 14, 'Rajoir', 3, '/1/14/293'),
(294, 14, 'Shibchar', 3, '/1/14/294'),
(295, 15, 'Daulatpur', 3, '/1/15/295'),
(296, 15, 'Ghior', 3, '/1/15/296'),
(297, 15, 'Harirampur', 3, '/1/15/297'),
(298, 15, 'Manikganj sadar', 3, '/1/15/298'),
(299, 15, 'Saturia', 3, '/1/15/299'),
(300, 15, 'Shibalaya', 3, '/1/15/300'),
(301, 15, 'Singair', 3, '/1/15/301'),
(302, 16, 'Gazaria', 3, '/1/16/302'),
(303, 16, 'Lohajang', 3, '/1/16/303'),
(304, 16, 'Munshiganj sadar', 3, '/1/16/304'),
(305, 16, 'Serajdikhan', 3, '/1/16/305'),
(306, 16, 'Sreenagar', 3, '/1/16/306'),
(307, 16, 'Tongibari', 3, '/1/16/307'),
(308, 17, 'Bhaluka', 3, '/1/17/308'),
(309, 33, 'Lakshmipur sadar', 3, '/2/33/309'),
(310, 33, 'Roypur', 3, '/2/33/310'),
(311, 33, 'Ramganj', 3, '/2/33/311'),
(312, 33, 'Ramgati', 3, '/2/33/312'),
(313, 34, 'Begumganj', 3, '/2/34/313'),
(314, 34, 'Chatkhil', 3, '/2/34/314'),
(315, 34, 'Companiganj', 3, '/2/34/315'),
(316, 34, 'Hatiya', 3, '/2/34/316'),
(317, 34, 'Kabirhat', 3, '/2/34/317'),
(318, 34, 'Senbagh', 3, '/2/34/318'),
(319, 34, 'Sonaimuri', 3, '/2/34/319'),
(320, 34, 'Subarnachar', 3, '/2/34/320'),
(321, 34, 'Noakhali sadar', 3, '/2/34/321'),
(322, 35, 'Baghaichhari', 3, '/2/35/322'),
(323, 35, 'Barkal upazila', 3, '/2/35/323'),
(324, 35, 'Kawkhali (betbunia)', 3, '/2/35/324'),
(325, 35, 'Belai chhari  upazi', 3, '/2/35/325'),
(326, 35, 'Kaptai  upazila', 3, '/2/35/326'),
(327, 35, 'Jurai chhari upazil', 3, '/2/35/327'),
(328, 35, 'Langadu  upazila', 3, '/2/35/328'),
(329, 35, 'Naniarchar  upazila', 3, '/2/35/329'),
(330, 35, 'Rajasthali  upazila', 3, '/2/35/330'),
(331, 35, 'Rangamati sadar  up', 3, '/2/35/331'),
(332, 17, 'Dhobaura', 3, '/1/17/332'),
(333, 17, 'Fulbaria', 3, '/1/17/333'),
(334, 17, 'Gaffargaon', 3, '/1/17/334'),
(335, 17, 'Gauripur', 3, '/1/17/335'),
(336, 17, 'Haluaghat', 3, '/1/17/336'),
(337, 17, 'Ishwarganj', 3, '/1/17/337'),
(338, 17, 'Mymensingh sadar', 3, '/1/17/338'),
(339, 17, 'Muktagachha', 3, '/1/17/339'),
(340, 17, 'Nandail', 3, '/1/17/340'),
(341, 17, 'Phulpur', 3, '/1/17/341'),
(342, 17, 'Tarakanda', 3, '/1/17/342'),
(343, 17, 'Trishal', 3, '/1/17/343'),
(344, 18, 'Araihazar', 3, '/1/18/344'),
(345, 18, 'Sonargaon', 3, '/1/18/345'),
(346, 18, 'Bandar', 3, '/1/18/346'),
(347, 18, 'Narayanganj sadar', 3, '/1/18/347'),
(348, 18, 'Rupganj', 3, '/1/18/348'),
(349, 19, 'Belabo', 3, '/1/19/349'),
(350, 19, 'Manohardi', 3, '/1/19/350'),
(351, 19, 'Narsingdi sadar', 3, '/1/19/351'),
(352, 19, 'Palash', 3, '/1/19/352'),
(353, 19, 'Roypura', 3, '/1/19/353'),
(354, 19, 'Shibpur', 3, '/1/19/354'),
(355, 20, 'Atpara', 3, '/1/20/355'),
(356, 20, 'Barhatta', 3, '/1/20/356'),
(357, 20, 'Durgapur', 3, '/1/20/357'),
(358, 20, 'Khaliajuri', 3, '/1/20/358'),
(359, 20, 'Kalmakanda', 3, '/1/20/359'),
(360, 20, 'Kendua', 3, '/1/20/360'),
(361, 20, 'Madan', 3, '/1/20/361'),
(362, 20, 'Mohanganj', 3, '/1/20/362'),
(363, 20, 'Netrokona sadar', 3, '/1/20/363'),
(364, 20, 'Purbadhala', 3, '/1/20/364'),
(365, 21, 'Baliakandi', 3, '/1/21/365'),
(366, 21, 'Goalanda', 3, '/1/21/366'),
(367, 21, 'Kalukhali', 3, '/1/21/367'),
(368, 21, 'Pangsha', 3, '/1/21/368'),
(369, 21, 'Rajbari sadar', 3, '/1/21/369'),
(370, 22, 'Bhedarganj', 3, '/1/22/370'),
(371, 22, 'Damudya', 3, '/1/22/371'),
(372, 22, 'Gosairhat', 3, '/1/22/372'),
(373, 22, 'Naria', 3, '/1/22/373'),
(374, 22, 'Shariatpur sadar', 3, '/1/22/374'),
(375, 22, 'Zanjira', 3, '/1/22/375'),
(376, 23, 'Jhenaigati', 3, '/1/23/376'),
(377, 23, 'Nakla', 3, '/1/23/377'),
(378, 23, 'Nalitabari', 3, '/1/23/378'),
(379, 23, 'Sherpur sadar', 3, '/1/23/379'),
(380, 23, 'Sreebardi', 3, '/1/23/380'),
(381, 24, 'Basail', 3, '/1/24/381'),
(382, 24, 'Bhuapur', 3, '/1/24/382'),
(383, 24, 'Delduar', 3, '/1/24/383'),
(384, 24, 'Dhanbari', 3, '/1/24/384'),
(385, 24, 'Ghatail', 3, '/1/24/385'),
(386, 24, 'Gopalpur', 3, '/1/24/386'),
(387, 24, 'Kalihati', 3, '/1/24/387'),
(388, 24, 'Madhupur', 3, '/1/24/388'),
(389, 24, 'Mirzapur', 3, '/1/24/389'),
(390, 24, 'Nagarpur', 3, '/1/24/390'),
(391, 24, 'Sakhipur', 3, '/1/24/391'),
(392, 24, 'Tangail sadar', 3, '/1/24/392'),
(393, 36, 'Adamdighi', 3, '/3/36/393'),
(394, 36, 'Bogra sadar', 3, '/3/36/394'),
(395, 36, 'Dhunat', 3, '/3/36/395'),
(396, 36, 'Dhupchanchia', 3, '/3/36/396'),
(397, 36, 'Gabtali', 3, '/3/36/397'),
(398, 52, 'Bagerhat sadar', 3, '/5/52/398'),
(399, 52, 'Chitalmari', 3, '/5/52/399'),
(400, 52, 'Fakirhat', 3, '/5/52/400'),
(401, 52, 'Kachua', 3, '/5/52/401'),
(402, 52, 'Mollahat', 3, '/5/52/402'),
(403, 52, 'Mongla', 3, '/5/52/403'),
(404, 52, 'Morrelganj', 3, '/5/52/404'),
(405, 52, 'Rampal', 3, '/5/52/405'),
(406, 52, 'Sarankhola', 3, '/5/52/406'),
(407, 53, 'Alamdanga', 3, '/5/53/407'),
(408, 53, 'Chuadanga sadar', 3, '/5/53/408'),
(409, 53, 'Damurhuda', 3, '/5/53/409'),
(410, 53, 'Jiban nagar', 3, '/5/53/410'),
(411, 54, 'Abhaynagar', 3, '/5/54/411'),
(412, 54, 'Bagher para', 3, '/5/54/412'),
(413, 54, 'Chaugachha', 3, '/5/54/413'),
(414, 54, 'Jhikargachha', 3, '/5/54/414'),
(415, 54, 'Keshabpur', 3, '/5/54/415'),
(416, 54, 'Jessore sadar', 3, '/5/54/416'),
(417, 54, 'Manirampur', 3, '/5/54/417'),
(418, 54, 'Sharsha', 3, '/5/54/418'),
(419, 55, 'Harinakunda', 3, '/5/55/419'),
(420, 55, 'Jhenaidah sadar', 3, '/5/55/420'),
(421, 55, 'Kaliganj', 3, '/5/55/421'),
(422, 55, 'Kotchandpur', 3, '/5/55/422'),
(423, 55, 'Maheshpur', 3, '/5/55/423'),
(424, 55, 'Shailkupa', 3, '/5/55/424'),
(425, 56, 'Batiaghata', 3, '/5/56/425'),
(426, 56, 'Dacope', 3, '/5/56/426'),
(427, 56, 'Daulatpur', 3, '/5/56/427'),
(428, 56, 'Dumuria', 3, '/5/56/428'),
(429, 56, 'Dighalia', 3, '/5/56/429'),
(430, 56, 'Khalishpur', 3, '/5/56/430'),
(431, 56, 'Khan jahan ali', 3, '/5/56/431'),
(432, 56, 'Khulna sadar', 3, '/5/56/432'),
(433, 56, 'Koyra', 3, '/5/56/433'),
(434, 56, 'Paikgachha', 3, '/5/56/434'),
(435, 56, 'Phultala', 3, '/5/56/435'),
(436, 56, 'Rupsa', 3, '/5/56/436'),
(437, 56, 'Sonadanga', 3, '/5/56/437'),
(438, 56, 'Terokhada', 3, '/5/56/438'),
(439, 57, 'Bheramara', 3, '/5/57/439'),
(440, 57, 'Daulatpur', 3, '/5/57/440'),
(441, 57, 'Khoksa', 3, '/5/57/441'),
(442, 57, 'Kumarkhali', 3, '/5/57/442'),
(443, 57, 'Kushtia sadar', 3, '/5/57/443'),
(444, 57, 'Mirpur', 3, '/5/57/444'),
(445, 58, 'Magura sadar', 3, '/5/58/445'),
(446, 58, 'Mohammadpur', 3, '/5/58/446'),
(447, 58, 'Shalikha', 3, '/5/58/447'),
(448, 58, 'Sreepur', 3, '/5/58/448'),
(449, 59, 'Gangni', 3, '/5/59/449'),
(450, 59, 'Mujib nagar', 3, '/5/59/450'),
(451, 59, 'Meherpur sadar', 3, '/5/59/451'),
(452, 60, 'Kalia', 3, '/5/60/452'),
(453, 60, 'Lohagara', 3, '/5/60/453'),
(454, 60, 'Narail sadar', 3, '/5/60/454'),
(455, 61, 'Assasuni', 3, '/5/61/455'),
(456, 61, 'Debhata', 3, '/5/61/456'),
(457, 61, 'Kalaroa', 3, '/5/61/457'),
(458, 61, 'Kaliganj', 3, '/5/61/458'),
(459, 61, 'Satkhira sadar', 3, '/5/61/459'),
(460, 61, 'Shyamnagar', 3, '/5/61/460'),
(461, 61, 'Tala', 3, '/5/61/461'),
(462, 36, 'Kahaloo', 3, '/3/36/462'),
(463, 36, 'Nandigram', 3, '/3/36/463'),
(464, 36, 'Sariakandi', 3, '/3/36/464'),
(465, 36, 'Shajahanpur', 3, '/3/36/465'),
(466, 36, 'Sherpur', 3, '/3/36/466'),
(467, 36, 'Shibganj', 3, '/3/36/467'),
(468, 36, 'Sonatola', 3, '/3/36/468'),
(469, 37, 'Bholahat', 3, '/3/37/469'),
(470, 37, 'Gomastapur', 3, '/3/37/470'),
(471, 37, 'Nachole', 3, '/3/37/471'),
(472, 37, 'Chapai nababganj sadar', 3, '/3/37/472'),
(473, 37, 'Shibganj', 3, '/3/37/473'),
(474, 44, 'Birampur', 3, '/4/44/474'),
(475, 44, 'Birganj', 3, '/4/44/475'),
(476, 44, 'Biral', 3, '/4/44/476'),
(477, 44, 'Bochaganj', 3, '/4/44/477'),
(478, 44, 'Chirirbandar', 3, '/4/44/478'),
(479, 44, 'Fulbari', 3, '/4/44/479'),
(480, 44, 'Ghoraghat', 3, '/4/44/480'),
(481, 44, 'Hakimpur', 3, '/4/44/481'),
(482, 44, 'Kaharole', 3, '/4/44/482'),
(483, 44, 'Khansama', 3, '/4/44/483'),
(484, 44, 'Dinajpur sadar', 3, '/4/44/484'),
(485, 44, 'Nawabganj', 3, '/4/44/485'),
(486, 44, 'Parbatipur', 3, '/4/44/486'),
(487, 45, 'Fulchhari', 3, '/4/45/487'),
(488, 45, 'Gaibandha sadar', 3, '/4/45/488'),
(489, 45, 'Gobindaganj', 3, '/4/45/489'),
(490, 45, 'Palashbari', 3, '/4/45/490'),
(491, 45, 'Sadullapur', 3, '/4/45/491'),
(492, 45, 'Saghata', 3, '/4/45/492'),
(493, 45, 'Sundarganj', 3, '/4/45/493'),
(494, 38, 'Akkelpur', 3, '/3/38/494'),
(495, 38, 'Joypurhat sadar', 3, '/3/38/495'),
(496, 38, 'Kalai', 3, '/3/38/496'),
(497, 38, 'Khetlal', 3, '/3/38/497'),
(498, 38, 'Panchbibi', 3, '/3/38/498'),
(499, 46, 'Bhurungamari', 3, '/4/46/499'),
(500, 46, 'Char rajibpur', 3, '/4/46/500'),
(501, 46, 'Chilmari', 3, '/4/46/501'),
(502, 46, 'Phulbari', 3, '/4/46/502'),
(503, 46, 'Kurigram sadar', 3, '/4/46/503'),
(504, 46, 'Nageshwari', 3, '/4/46/504'),
(505, 46, 'Rajarhat', 3, '/4/46/505'),
(506, 46, 'Raumari', 3, '/4/46/506'),
(507, 46, 'Ulipur', 3, '/4/46/507'),
(508, 47, 'Aditmari', 3, '/4/47/508'),
(509, 47, 'Hatibandha', 3, '/4/47/509'),
(510, 47, 'Kaliganj', 3, '/4/47/510'),
(511, 47, 'Lalmonirhat sadar', 3, '/4/47/511'),
(512, 47, 'Patgram', 3, '/4/47/512'),
(513, 39, 'Atrai', 3, '/3/39/513'),
(514, 39, 'Badalgachhi', 3, '/3/39/514'),
(515, 39, 'Dhamoirhat', 3, '/3/39/515'),
(516, 39, 'Manda', 3, '/3/39/516'),
(517, 39, 'Mahadebpur', 3, '/3/39/517'),
(518, 39, 'Naogaon sadar', 3, '/3/39/518'),
(519, 39, 'Niamatpur', 3, '/3/39/519'),
(520, 39, 'Patnitala', 3, '/3/39/520'),
(521, 39, 'Porsha', 3, '/3/39/521'),
(522, 39, 'Raninagar', 3, '/3/39/522'),
(523, 39, 'Sapahar', 3, '/3/39/523'),
(524, 40, 'Bagatipara', 3, '/3/40/524'),
(525, 40, 'Baraigram', 3, '/3/40/525'),
(526, 40, 'Gurudaspur', 3, '/3/40/526'),
(527, 40, 'Lalpur', 3, '/3/40/527'),
(528, 40, 'Naldanga', 3, '/3/40/528'),
(529, 40, 'Natore sadar', 3, '/3/40/529'),
(530, 40, 'Singra', 3, '/3/40/530'),
(531, 48, 'Dimla', 3, '/4/48/531'),
(532, 48, 'Domar upazila', 3, '/4/48/532'),
(533, 48, 'Jaldhaka', 3, '/4/48/533'),
(534, 48, 'Kishoreganj', 3, '/4/48/534'),
(535, 48, 'Nilphamari sadar', 3, '/4/48/535'),
(536, 48, 'Saidpur upazila', 3, '/4/48/536'),
(537, 41, 'Atgharia', 3, '/3/41/537'),
(538, 41, 'Bera', 3, '/3/41/538'),
(539, 41, 'Bhangura', 3, '/3/41/539'),
(540, 41, 'Chatmohar', 3, '/3/41/540'),
(541, 41, 'Faridpur', 3, '/3/41/541'),
(542, 41, 'Ishwardi', 3, '/3/41/542'),
(543, 41, 'Pabna sadar', 3, '/3/41/543'),
(544, 41, 'Santhia', 3, '/3/41/544'),
(545, 41, 'Sujanagar', 3, '/3/41/545'),
(546, 49, 'Atwari', 3, '/4/49/546'),
(547, 49, 'Boda', 3, '/4/49/547'),
(548, 49, 'Debiganj', 3, '/4/49/548'),
(549, 49, 'Panchagarh sadar', 3, '/4/49/549'),
(550, 49, 'Tentulia', 3, '/4/49/550'),
(551, 42, 'Bagha', 3, '/3/42/551'),
(552, 42, 'Baghmara', 3, '/3/42/552'),
(553, 42, 'Boalia', 3, '/3/42/553'),
(554, 42, 'Charghat', 3, '/3/42/554'),
(555, 42, 'Durgapur', 3, '/3/42/555'),
(556, 42, 'Godagari', 3, '/3/42/556'),
(557, 42, 'Matihar', 3, '/3/42/557'),
(558, 42, 'Mohanpur', 3, '/3/42/558'),
(559, 42, 'Paba', 3, '/3/42/559'),
(560, 42, 'Puthia', 3, '/3/42/560'),
(561, 42, 'Rajpara', 3, '/3/42/561'),
(562, 42, 'Shah makhdum', 3, '/3/42/562'),
(563, 42, 'Tanore', 3, '/3/42/563'),
(564, 50, 'Badarganj', 3, '/4/50/564'),
(565, 50, 'Gangachara', 3, '/4/50/565'),
(566, 50, 'Kaunia', 3, '/4/50/566'),
(567, 50, 'Rangpur sadar', 3, '/4/50/567'),
(568, 50, 'Mitha pukur', 3, '/4/50/568'),
(569, 50, 'Pirgachha', 3, '/4/50/569'),
(570, 50, 'Pirganj', 3, '/4/50/570'),
(571, 50, 'Taraganj', 3, '/4/50/571'),
(572, 43, 'Belkuchi', 3, '/3/43/572'),
(573, 43, 'Chauhali', 3, '/3/43/573'),
(574, 43, 'Kamarkhanda', 3, '/3/43/574'),
(575, 43, 'Kazipur', 3, '/3/43/575'),
(576, 43, 'Royganj', 3, '/3/43/576'),
(577, 43, 'Shahjadpur', 3, '/3/43/577'),
(578, 43, 'Sirajganj sadar', 3, '/3/43/578'),
(579, 43, 'Tarash', 3, '/3/43/579'),
(580, 43, 'Ullah para', 3, '/3/43/580'),
(581, 51, 'Baliadangi', 3, '/4/51/581'),
(582, 51, 'Haripur', 3, '/4/51/582'),
(583, 51, 'Pirganj', 3, '/4/51/583'),
(584, 51, 'Ranisankail', 3, '/4/51/584'),
(585, 51, 'Thakurgaon sadar', 3, '/4/51/585'),
(586, 68, 'Ajmiriganj', 3, '/7/68/586'),
(587, 68, 'Bahubal', 3, '/7/68/587'),
(588, 68, 'Baniachong', 3, '/7/68/588'),
(589, 68, 'Chunarughat', 3, '/7/68/589'),
(590, 68, 'Habiganj sadar', 3, '/7/68/590'),
(591, 68, 'Lakhai', 3, '/7/68/591'),
(592, 68, 'Madhabpur', 3, '/7/68/592'),
(593, 68, 'Nabiganj', 3, '/7/68/593'),
(594, 69, 'Barlekha', 3, '/7/69/594'),
(595, 69, 'Juri', 3, '/7/69/595'),
(596, 69, 'Kamalganj', 3, '/7/69/596'),
(597, 69, 'Kulaura', 3, '/7/69/597'),
(598, 69, 'Maulvibazar sadar', 3, '/7/69/598'),
(599, 69, 'Rajnagar', 3, '/7/69/599'),
(600, 69, 'Sreemangal', 3, '/7/69/600'),
(601, 70, 'Bishwambarpur', 3, '/7/70/601'),
(602, 70, 'Chhatak', 3, '/7/70/602'),
(603, 70, 'Dakshin sunamganj', 3, '/7/70/603'),
(604, 70, 'Derai', 3, '/7/70/604'),
(605, 70, 'Dharampasha', 3, '/7/70/605'),
(606, 70, 'Dowarabazar', 3, '/7/70/606'),
(607, 70, 'Jagannathpur', 3, '/7/70/607'),
(608, 70, 'Jamalganj', 3, '/7/70/608'),
(609, 70, 'Sulla', 3, '/7/70/609'),
(610, 70, 'Sunamganj sadar', 3, '/7/70/610'),
(611, 70, 'Tahirpur', 3, '/7/70/611'),
(612, 71, 'Balaganj', 3, '/7/71/612'),
(613, 71, 'Beani bazar', 3, '/7/71/613'),
(614, 71, 'Bishwanath', 3, '/7/71/614'),
(615, 71, 'Companiganj', 3, '/7/71/615'),
(616, 71, 'Dakshin surma', 3, '/7/71/616'),
(617, 71, 'Fenchuganj', 3, '/7/71/617'),
(618, 71, 'Golapganj', 3, '/7/71/618'),
(619, 71, 'Gowainghat', 3, '/7/71/619'),
(620, 71, 'Jaintiapur', 3, '/7/71/620'),
(621, 71, 'Kanaighat', 3, '/7/71/621'),
(622, 71, 'Sylhet sadar', 3, '/7/71/622'),
(623, 71, 'Zakiganj', 3, '/7/71/623'),
(1468, 8, 'Dhanmondi', 3, '/1/8/1468/'),
(1469, 8, 'Mohammadpur', 3, '/1/8/1469/'),
(1470, NULL, 'Narsingdi', 1, '/1470'),
(1471, NULL, 'Corporate', 1, '/1471'),
(1472, NULL, 'Sister Concern', 1, '/1472'),
(1473, NULL, 'Mymensingh', 1, '/1473'),
(1474, NULL, 'Cumilla', 1, '/1474');

-- --------------------------------------------------------

--
-- Table structure for table `mark_chart_setting`
--

CREATE TABLE `mark_chart_setting` (
  `mark_chart_id` int(11) NOT NULL,
  `setting_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mark_chart_setting`
--

INSERT INTO `mark_chart_setting` (`mark_chart_id`, `setting_id`) VALUES
(8, 3),
(8, 6),
(24, 5),
(29, 4),
(29, 5),
(29, 6),
(29, 7),
(30, 6),
(31, 4),
(31, 5),
(32, 4),
(32, 5),
(33, 4),
(33, 5),
(34, 4),
(34, 5),
(35, 4),
(35, 5),
(35, 6),
(36, 4),
(36, 5),
(37, 4),
(37, 5),
(39, 4),
(39, 5),
(39, 6),
(39, 7),
(40, 4),
(40, 5),
(40, 6),
(40, 7),
(41, 4),
(41, 5),
(41, 6),
(41, 7),
(42, 4),
(42, 5),
(42, 6),
(42, 7),
(43, 4),
(43, 5),
(43, 6),
(43, 7),
(44, 4),
(44, 5),
(44, 6),
(44, 7),
(45, 4),
(45, 5),
(45, 6),
(45, 7),
(46, 4),
(46, 5),
(46, 6),
(46, 7),
(48, 4),
(48, 5),
(48, 6),
(48, 7),
(49, 4),
(49, 5),
(49, 6),
(49, 7),
(50, 4),
(50, 5),
(50, 6),
(50, 7),
(51, 4),
(51, 5),
(51, 6),
(51, 7),
(53, 4),
(53, 5),
(53, 6),
(53, 7),
(54, 4),
(54, 5),
(54, 6),
(54, 7),
(55, 4),
(55, 5),
(55, 6),
(55, 7),
(56, 4),
(56, 5),
(56, 6),
(56, 7),
(57, 4),
(57, 5),
(57, 6),
(57, 7),
(58, 4),
(58, 5),
(58, 6),
(58, 7),
(59, 4),
(59, 5),
(59, 6),
(59, 7),
(60, 4),
(60, 5),
(60, 6),
(60, 7),
(61, 4),
(61, 5),
(61, 6),
(61, 7),
(62, 4),
(62, 5),
(62, 6),
(62, 7),
(64, 2),
(64, 4),
(64, 5),
(64, 6),
(64, 7),
(65, 4),
(65, 5),
(65, 6),
(66, 4),
(66, 5),
(66, 6),
(67, 4),
(67, 5),
(67, 6),
(68, 4),
(68, 5),
(68, 6),
(69, 4),
(69, 5),
(69, 6),
(69, 7),
(71, 2),
(71, 4),
(71, 5),
(71, 6),
(71, 7),
(72, 4),
(72, 5),
(72, 6),
(73, 4),
(73, 5),
(73, 6),
(73, 7),
(75, 4),
(75, 5),
(75, 6),
(75, 7),
(76, 4),
(76, 5),
(76, 6),
(76, 7),
(77, 4),
(77, 5),
(77, 6),
(77, 7),
(78, 2),
(78, 4),
(78, 5),
(78, 6),
(78, 7),
(79, 4),
(79, 5),
(79, 6),
(79, 7),
(80, 4),
(80, 5),
(80, 6),
(80, 7),
(81, 4),
(81, 5),
(81, 6),
(81, 7),
(82, 4),
(82, 5),
(82, 6),
(82, 7),
(94, 4),
(94, 5),
(94, 6),
(94, 7),
(95, 4),
(95, 5),
(95, 6),
(95, 7),
(96, 4),
(96, 5),
(96, 6),
(96, 7),
(97, 4),
(97, 5),
(97, 6),
(97, 7),
(99, 4),
(99, 5),
(99, 6),
(99, 7),
(100, 3),
(100, 4),
(100, 5),
(100, 6),
(100, 7),
(101, 4),
(101, 5),
(101, 6),
(101, 7),
(102, 4),
(102, 5),
(102, 6),
(102, 7),
(104, 3),
(104, 4),
(104, 5),
(104, 6),
(104, 7),
(105, 4),
(105, 5),
(105, 6),
(105, 7),
(106, 4),
(106, 5),
(106, 6),
(106, 7),
(107, 4),
(107, 5),
(107, 6),
(107, 7),
(108, 4),
(108, 5),
(108, 6),
(108, 7),
(109, 4),
(109, 5),
(109, 6),
(109, 7),
(110, 4),
(110, 5),
(110, 6),
(110, 7),
(111, 2),
(111, 3),
(111, 4),
(111, 5),
(111, 6),
(111, 7),
(112, 2),
(112, 4),
(112, 5),
(112, 6),
(112, 7),
(113, 4),
(113, 5),
(113, 6),
(113, 7),
(114, 4),
(114, 5),
(114, 6),
(114, 7),
(115, 4),
(115, 5),
(115, 6),
(115, 7),
(116, 4),
(116, 5),
(116, 6),
(116, 7),
(117, 4),
(117, 5),
(117, 6),
(117, 7),
(118, 4),
(118, 5),
(118, 6),
(118, 7),
(119, 4),
(119, 5),
(119, 6),
(119, 7),
(120, 2),
(120, 4),
(120, 5),
(120, 6),
(120, 7),
(121, 4),
(121, 5),
(121, 6),
(121, 7),
(122, 4),
(122, 5),
(122, 6),
(122, 7),
(123, 4),
(123, 5),
(123, 6),
(123, 7),
(124, 4),
(124, 5),
(124, 6),
(124, 7),
(125, 4),
(125, 5),
(125, 6),
(125, 7),
(126, 4),
(126, 5),
(126, 6),
(126, 7),
(127, 4),
(127, 5),
(127, 6),
(127, 7),
(128, 4),
(128, 5),
(128, 6),
(128, 7),
(129, 2),
(129, 4),
(129, 5),
(129, 6),
(129, 7),
(130, 4),
(130, 5),
(130, 6),
(130, 7),
(131, 4),
(131, 5),
(131, 6),
(131, 7),
(132, 4),
(132, 5),
(132, 6),
(132, 7),
(133, 2),
(133, 4),
(133, 5),
(133, 6),
(133, 7),
(134, 4),
(134, 5),
(134, 6),
(134, 7),
(135, 4),
(135, 5),
(135, 6),
(135, 7),
(136, 4),
(136, 5),
(136, 6),
(136, 7),
(137, 2),
(137, 4),
(137, 5),
(137, 6),
(137, 7),
(138, 4),
(138, 5),
(138, 6),
(138, 7),
(139, 4),
(139, 5),
(139, 6),
(139, 7),
(140, 4),
(140, 5),
(140, 6),
(140, 7),
(141, 4),
(141, 5),
(141, 6),
(141, 7),
(142, 4),
(142, 5),
(142, 6),
(142, 7),
(143, 4),
(143, 5),
(143, 6),
(143, 7),
(144, 4),
(144, 5),
(144, 6),
(144, 7),
(145, 4),
(145, 5),
(145, 6),
(145, 7),
(146, 4),
(146, 5),
(146, 6),
(146, 7),
(147, 4),
(147, 5),
(147, 6),
(147, 7),
(148, 4),
(148, 5),
(148, 6),
(148, 7);

-- --------------------------------------------------------

--
-- Table structure for table `tbd_app_module`
--

CREATE TABLE `tbd_app_module` (
  `id` int(11) NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `app_url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `app_background` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `application_manual` longtext COLLATE utf8mb4_unicode_ci,
  `short_content` longtext COLLATE utf8mb4_unicode_ci,
  `price` double DEFAULT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbd_app_module`
--

INSERT INTO `tbd_app_module` (`id`, `path`, `name`, `short_name`, `app_url`, `app_background`, `module_class`, `slug`, `content`, `application_manual`, `short_content`, `price`, `status`) VALUES
(1, NULL, 'KPI (Key Personnel Intigration)', '', '', NULL, NULL, 'KPI (Key Personnel Intigration)', NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbd_kpi`
--

CREATE TABLE `tbd_kpi` (
  `id` int(11) NOT NULL,
  `terminal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `terminal`
--

CREATE TABLE `terminal` (
  `id` int(11) NOT NULL,
  `main_app_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `terminal`
--

INSERT INTO `terminal` (`id`, `main_app_id`) VALUES
(1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `terminal_app_module`
--

CREATE TABLE `terminal_app_module` (
  `terminal_id` int(11) NOT NULL,
  `app_module_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `core_agent`
--
ALTER TABLE `core_agent`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_700ABD7DC885C0D3` (`agent_group_id`),
  ADD KEY `IDX_700ABD7DB08FA272` (`district_id`),
  ADD KEY `IDX_700ABD7D2B399097` (`upozila_id`);

--
-- Indexes for table `core_bank`
--
ALTER TABLE `core_bank`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_company`
--
ALTER TABLE `core_company`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_5DA8BC7CE77B6CE8` (`terminal_id`),
  ADD KEY `IDX_5DA8BC7CB03A8386` (`created_by_id`),
  ADD KEY `IDX_5DA8BC7C64D218E` (`location_id`);

--
-- Indexes for table `core_country`
--
ALTER TABLE `core_country`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_key_value`
--
ALTER TABLE `core_key_value`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B02915DFEE35BD72` (`setting_id`),
  ADD KEY `IDX_B02915DFA76ED395` (`user_id`);

--
-- Indexes for table `core_location`
--
ALTER TABLE `core_location`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E15CFF68727ACA70` (`parent_id`);

--
-- Indexes for table `core_setting`
--
ALTER TABLE `core_setting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8D630DAB9D1E3C7B` (`setting_type_id`);

--
-- Indexes for table `core_setting_type`
--
ALTER TABLE `core_setting_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `core_user`
--
ALTER TABLE `core_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_BF76157CF85E0677` (`username`),
  ADD UNIQUE KEY `UNIQ_BF76157CE7927C74` (`email`),
  ADD UNIQUE KEY `UNIQ_BF76157CA76ED395` (`user_id`),
  ADD KEY `IDX_BF76157CFAC7D83F` (`designation_id`),
  ADD KEY `IDX_BF76157CAE80F5DF` (`department_id`),
  ADD KEY `IDX_BF76157CDCD6CC49` (`branch_id`),
  ADD KEY `IDX_BF76157C1ED93D47` (`user_group_id`),
  ADD KEY `IDX_BF76157CE77B6CE8` (`terminal_id`),
  ADD KEY `IDX_BF76157CB08FA272` (`district_id`),
  ADD KEY `IDX_BF76157CE039A55` (`regional_id`),
  ADD KEY `IDX_BF76157C9F2C3FAB` (`zone_id`),
  ADD KEY `IDX_BF76157C11C8FB41` (`bank_id`),
  ADD KEY `IDX_BF76157C64D218E` (`location_id`);

--
-- Indexes for table `core_user_profile`
--
ALTER TABLE `core_user_profile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_74EA0DDDA76ED395` (`user_id`);

--
-- Indexes for table `core_user_role`
--
ALTER TABLE `core_user_role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_6288F687F85E0677` (`username`),
  ADD UNIQUE KEY `UNIQ_6288F687E7927C74` (`email`);

--
-- Indexes for table `crm_broiler_standard`
--
ALTER TABLE `crm_broiler_standard`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crm_chick_life_cycle`
--
ALTER TABLE `crm_chick_life_cycle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crm_customers`
--
ALTER TABLE `crm_customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crm_expense`
--
ALTER TABLE `crm_expense`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C171C131EE35BD72` (`setting_id`);

--
-- Indexes for table `crm_fcr`
--
ALTER TABLE `crm_fcr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crm_layer_life_cycle`
--
ALTER TABLE `crm_layer_life_cycle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crm_layer_performance`
--
ALTER TABLE `crm_layer_performance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crm_setting`
--
ALTER TABLE `crm_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crm_sonali_standard`
--
ALTER TABLE `crm_sonali_standard`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crm_visit`
--
ALTER TABLE `crm_visit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crm_visit_details`
--
ALTER TABLE `crm_visit_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_55226A061B7D4259` (`crm_visit_id`),
  ADD KEY `IDX_55226A069395C3F3` (`customer_id`);

--
-- Indexes for table `ext_log_entries`
--
ALTER TABLE `ext_log_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_class_lookup_idx` (`object_class`),
  ADD KEY `log_date_lookup_idx` (`logged_at`),
  ADD KEY `log_user_lookup_idx` (`username`),
  ADD KEY `log_version_lookup_idx` (`object_id`,`object_class`,`version`);

--
-- Indexes for table `ext_translations`
--
ALTER TABLE `ext_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lookup_unique_idx` (`locale`,`object_class`,`field`,`foreign_key`),
  ADD KEY `translations_lookup_idx` (`locale`,`object_class`,`foreign_key`);

--
-- Indexes for table `kpi_agent_order`
--
ALTER TABLE `kpi_agent_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C1FF4C782B399097` (`upozila_id`),
  ADD KEY `IDX_C1FF4C783414710B` (`agent_id`),
  ADD KEY `IDX_C1FF4C784584665A` (`product_id`);

--
-- Indexes for table `kpi_agent_order_check`
--
ALTER TABLE `kpi_agent_order_check`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_82AC80ED2B399097` (`upozila_id`),
  ADD KEY `IDX_82AC80ED3414710B` (`agent_id`);

--
-- Indexes for table `kpi_agent_outstanding`
--
ALTER TABLE `kpi_agent_outstanding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_2C8002A52B399097` (`upozila_id`),
  ADD KEY `IDX_2C8002A53414710B` (`agent_id`);

--
-- Indexes for table `kpi_agent_sales_growth`
--
ALTER TABLE `kpi_agent_sales_growth`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A6205E1C2B399097` (`upozila_id`),
  ADD KEY `IDX_A6205E1C3414710B` (`agent_id`),
  ADD KEY `IDX_A6205E1C4584665A` (`product_id`);

--
-- Indexes for table `kpi_employee`
--
ALTER TABLE `kpi_employee`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6493574F8C03F15C` (`employee_id`),
  ADD KEY `IDX_6493574F5DF2E749` (`employee_setup_id`),
  ADD KEY `IDX_6493574F7C56DBD6` (`parameter_id`),
  ADD KEY `IDX_6493574F81C06096` (`activity_id`),
  ADD KEY `IDX_6493574FBAAF4009` (`attributes_id`),
  ADD KEY `IDX_6493574FDFEBBA8A` (`mark_distribution_id`);

--
-- Indexes for table `kpi_employee_board`
--
ALTER TABLE `kpi_employee_board`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_4678410F8C03F15C` (`employee_id`),
  ADD KEY `IDX_4678410F5DF2E749` (`employee_setup_id`),
  ADD KEY `IDX_4678410FBAAF4009` (`attributes_id`);

--
-- Indexes for table `kpi_location_sales_target`
--
ALTER TABLE `kpi_location_sales_target`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_2BB5A031DFEBBA8A` (`mark_distribution_id`),
  ADD KEY `IDX_2BB5A0312B399097` (`upozila_id`),
  ADD KEY `IDX_2BB5A031B08FA272` (`district_id`),
  ADD KEY `IDX_2BB5A031E039A55` (`regional_id`),
  ADD KEY `IDX_2BB5A0319F2C3FAB` (`zone_id`);

--
-- Indexes for table `kpi_mark_chart`
--
ALTER TABLE `kpi_mark_chart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_207E3D3C3D8E604F` (`parent`),
  ADD KEY `IDX_207E3D3C50DDE1BD` (`setting_group_id`);

--
-- Indexes for table `kpi_setting`
--
ALTER TABLE `kpi_setting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_90E5947B9D1E3C7B` (`setting_type_id`);

--
-- Indexes for table `kpi_setting_type`
--
ALTER TABLE `kpi_setting_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kpi_setup`
--
ALTER TABLE `kpi_setup`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_94A6887E8C03F15C` (`employee_id`);

--
-- Indexes for table `kpi_setup_matrix`
--
ALTER TABLE `kpi_setup_matrix`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_82F1874A5DF2E749` (`employee_setup_id`),
  ADD KEY `IDX_82F1874ADFEBBA8A` (`mark_distribution_id`),
  ADD KEY `IDX_82F1874A2B399097` (`upozila_id`),
  ADD KEY `IDX_82F1874AB08FA272` (`district_id`),
  ADD KEY `IDX_82F1874AE039A55` (`regional_id`),
  ADD KEY `IDX_82F1874A5B100A73` (`zonal_id`),
  ADD KEY `IDX_82F1874AC24A7B4` (`mark_chart_id`),
  ADD KEY `IDX_82F1874AA4522A07` (`sales_id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5E9E89CB727ACA70` (`parent_id`);

--
-- Indexes for table `mark_chart_setting`
--
ALTER TABLE `mark_chart_setting`
  ADD PRIMARY KEY (`mark_chart_id`,`setting_id`),
  ADD KEY `IDX_EF7BFAD9C24A7B4` (`mark_chart_id`),
  ADD KEY `IDX_EF7BFAD9EE35BD72` (`setting_id`);

--
-- Indexes for table `tbd_app_module`
--
ALTER TABLE `tbd_app_module`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_866877C7989D9B62` (`slug`);

--
-- Indexes for table `tbd_kpi`
--
ALTER TABLE `tbd_kpi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `terminal`
--
ALTER TABLE `terminal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8F7B1541E69B993B` (`main_app_id`);

--
-- Indexes for table `terminal_app_module`
--
ALTER TABLE `terminal_app_module`
  ADD PRIMARY KEY (`terminal_id`,`app_module_id`),
  ADD KEY `IDX_A4C0FD2DE77B6CE8` (`terminal_id`),
  ADD KEY `IDX_A4C0FD2D7ADEAA4` (`app_module_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `core_agent`
--
ALTER TABLE `core_agent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `core_bank`
--
ALTER TABLE `core_bank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
--
-- AUTO_INCREMENT for table `core_company`
--
ALTER TABLE `core_company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `core_country`
--
ALTER TABLE `core_country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `core_key_value`
--
ALTER TABLE `core_key_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `core_location`
--
ALTER TABLE `core_location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `core_setting`
--
ALTER TABLE `core_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `core_setting_type`
--
ALTER TABLE `core_setting_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `core_user`
--
ALTER TABLE `core_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `core_user_profile`
--
ALTER TABLE `core_user_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `core_user_role`
--
ALTER TABLE `core_user_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `crm_broiler_standard`
--
ALTER TABLE `crm_broiler_standard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
--
-- AUTO_INCREMENT for table `crm_chick_life_cycle`
--
ALTER TABLE `crm_chick_life_cycle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `crm_customers`
--
ALTER TABLE `crm_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT for table `crm_expense`
--
ALTER TABLE `crm_expense`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `crm_fcr`
--
ALTER TABLE `crm_fcr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `crm_layer_life_cycle`
--
ALTER TABLE `crm_layer_life_cycle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `crm_layer_performance`
--
ALTER TABLE `crm_layer_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `crm_setting`
--
ALTER TABLE `crm_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `crm_sonali_standard`
--
ALTER TABLE `crm_sonali_standard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
--
-- AUTO_INCREMENT for table `crm_visit`
--
ALTER TABLE `crm_visit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT for table `crm_visit_details`
--
ALTER TABLE `crm_visit_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ext_log_entries`
--
ALTER TABLE `ext_log_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ext_translations`
--
ALTER TABLE `ext_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `kpi_agent_order`
--
ALTER TABLE `kpi_agent_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `kpi_agent_order_check`
--
ALTER TABLE `kpi_agent_order_check`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `kpi_agent_outstanding`
--
ALTER TABLE `kpi_agent_outstanding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `kpi_agent_sales_growth`
--
ALTER TABLE `kpi_agent_sales_growth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `kpi_employee`
--
ALTER TABLE `kpi_employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `kpi_employee_board`
--
ALTER TABLE `kpi_employee_board`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;
--
-- AUTO_INCREMENT for table `kpi_location_sales_target`
--
ALTER TABLE `kpi_location_sales_target`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `kpi_mark_chart`
--
ALTER TABLE `kpi_mark_chart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;
--
-- AUTO_INCREMENT for table `kpi_setting`
--
ALTER TABLE `kpi_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `kpi_setting_type`
--
ALTER TABLE `kpi_setting_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `kpi_setup`
--
ALTER TABLE `kpi_setup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `kpi_setup_matrix`
--
ALTER TABLE `kpi_setup_matrix`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1475;
--
-- AUTO_INCREMENT for table `tbd_app_module`
--
ALTER TABLE `tbd_app_module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `tbd_kpi`
--
ALTER TABLE `tbd_kpi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `terminal`
--
ALTER TABLE `terminal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `core_agent`
--
ALTER TABLE `core_agent`
  ADD CONSTRAINT `FK_700ABD7D2B399097` FOREIGN KEY (`upozila_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_700ABD7DB08FA272` FOREIGN KEY (`district_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_700ABD7DC885C0D3` FOREIGN KEY (`agent_group_id`) REFERENCES `core_setting` (`id`);

--
-- Constraints for table `core_company`
--
ALTER TABLE `core_company`
  ADD CONSTRAINT `FK_5DA8BC7C64D218E` FOREIGN KEY (`location_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_5DA8BC7CB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `core_user` (`id`),
  ADD CONSTRAINT `FK_5DA8BC7CE77B6CE8` FOREIGN KEY (`terminal_id`) REFERENCES `terminal` (`id`);

--
-- Constraints for table `core_key_value`
--
ALTER TABLE `core_key_value`
  ADD CONSTRAINT `FK_B02915DFA76ED395` FOREIGN KEY (`user_id`) REFERENCES `core_user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_B02915DFEE35BD72` FOREIGN KEY (`setting_id`) REFERENCES `core_setting` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `core_location`
--
ALTER TABLE `core_location`
  ADD CONSTRAINT `FK_E15CFF68727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `core_location` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `core_setting`
--
ALTER TABLE `core_setting`
  ADD CONSTRAINT `FK_8D630DAB9D1E3C7B` FOREIGN KEY (`setting_type_id`) REFERENCES `core_setting_type` (`id`);

--
-- Constraints for table `core_user`
--
ALTER TABLE `core_user`
  ADD CONSTRAINT `FK_BF76157C11C8FB41` FOREIGN KEY (`bank_id`) REFERENCES `core_bank` (`id`),
  ADD CONSTRAINT `FK_BF76157C1ED93D47` FOREIGN KEY (`user_group_id`) REFERENCES `core_setting` (`id`),
  ADD CONSTRAINT `FK_BF76157C64D218E` FOREIGN KEY (`location_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_BF76157C9F2C3FAB` FOREIGN KEY (`zone_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_BF76157CAE80F5DF` FOREIGN KEY (`department_id`) REFERENCES `core_setting` (`id`),
  ADD CONSTRAINT `FK_BF76157CB08FA272` FOREIGN KEY (`district_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_BF76157CDCD6CC49` FOREIGN KEY (`branch_id`) REFERENCES `core_setting` (`id`),
  ADD CONSTRAINT `FK_BF76157CE039A55` FOREIGN KEY (`regional_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_BF76157CE77B6CE8` FOREIGN KEY (`terminal_id`) REFERENCES `terminal` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_BF76157CFAC7D83F` FOREIGN KEY (`designation_id`) REFERENCES `core_setting` (`id`);

--
-- Constraints for table `core_user_profile`
--
ALTER TABLE `core_user_profile`
  ADD CONSTRAINT `FK_74EA0DDDA76ED395` FOREIGN KEY (`user_id`) REFERENCES `core_user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crm_expense`
--
ALTER TABLE `crm_expense`
  ADD CONSTRAINT `FK_C171C131EE35BD72` FOREIGN KEY (`setting_id`) REFERENCES `crm_setting` (`id`);

--
-- Constraints for table `crm_visit_details`
--
ALTER TABLE `crm_visit_details`
  ADD CONSTRAINT `FK_55226A061B7D4259` FOREIGN KEY (`crm_visit_id`) REFERENCES `crm_visit` (`id`),
  ADD CONSTRAINT `FK_55226A069395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `crm_customers` (`id`);

--
-- Constraints for table `kpi_agent_order`
--
ALTER TABLE `kpi_agent_order`
  ADD CONSTRAINT `FK_C1FF4C782B399097` FOREIGN KEY (`upozila_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_C1FF4C783414710B` FOREIGN KEY (`agent_id`) REFERENCES `core_agent` (`id`),
  ADD CONSTRAINT `FK_C1FF4C784584665A` FOREIGN KEY (`product_id`) REFERENCES `kpi_mark_chart` (`id`);

--
-- Constraints for table `kpi_agent_order_check`
--
ALTER TABLE `kpi_agent_order_check`
  ADD CONSTRAINT `FK_82AC80ED2B399097` FOREIGN KEY (`upozila_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_82AC80ED3414710B` FOREIGN KEY (`agent_id`) REFERENCES `core_agent` (`id`);

--
-- Constraints for table `kpi_agent_outstanding`
--
ALTER TABLE `kpi_agent_outstanding`
  ADD CONSTRAINT `FK_2C8002A52B399097` FOREIGN KEY (`upozila_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_2C8002A53414710B` FOREIGN KEY (`agent_id`) REFERENCES `core_agent` (`id`);

--
-- Constraints for table `kpi_agent_sales_growth`
--
ALTER TABLE `kpi_agent_sales_growth`
  ADD CONSTRAINT `FK_A6205E1C2B399097` FOREIGN KEY (`upozila_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_A6205E1C3414710B` FOREIGN KEY (`agent_id`) REFERENCES `core_agent` (`id`),
  ADD CONSTRAINT `FK_A6205E1C4584665A` FOREIGN KEY (`product_id`) REFERENCES `kpi_mark_chart` (`id`);

--
-- Constraints for table `kpi_employee`
--
ALTER TABLE `kpi_employee`
  ADD CONSTRAINT `FK_6493574F5DF2E749` FOREIGN KEY (`employee_setup_id`) REFERENCES `kpi_setup` (`id`),
  ADD CONSTRAINT `FK_6493574F7C56DBD6` FOREIGN KEY (`parameter_id`) REFERENCES `kpi_mark_chart` (`id`),
  ADD CONSTRAINT `FK_6493574F81C06096` FOREIGN KEY (`activity_id`) REFERENCES `kpi_mark_chart` (`id`),
  ADD CONSTRAINT `FK_6493574F8C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `core_user` (`id`),
  ADD CONSTRAINT `FK_6493574FBAAF4009` FOREIGN KEY (`attributes_id`) REFERENCES `kpi_mark_chart` (`id`),
  ADD CONSTRAINT `FK_6493574FDFEBBA8A` FOREIGN KEY (`mark_distribution_id`) REFERENCES `kpi_mark_chart` (`id`);

--
-- Constraints for table `kpi_employee_board`
--
ALTER TABLE `kpi_employee_board`
  ADD CONSTRAINT `FK_4678410F5DF2E749` FOREIGN KEY (`employee_setup_id`) REFERENCES `kpi_setup` (`id`),
  ADD CONSTRAINT `FK_4678410F8C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `core_user` (`id`),
  ADD CONSTRAINT `FK_4678410FBAAF4009` FOREIGN KEY (`attributes_id`) REFERENCES `kpi_mark_chart` (`id`);

--
-- Constraints for table `kpi_location_sales_target`
--
ALTER TABLE `kpi_location_sales_target`
  ADD CONSTRAINT `FK_2BB5A0312B399097` FOREIGN KEY (`upozila_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_2BB5A0319F2C3FAB` FOREIGN KEY (`zone_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_2BB5A031B08FA272` FOREIGN KEY (`district_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_2BB5A031DFEBBA8A` FOREIGN KEY (`mark_distribution_id`) REFERENCES `kpi_mark_chart` (`id`),
  ADD CONSTRAINT `FK_2BB5A031E039A55` FOREIGN KEY (`regional_id`) REFERENCES `core_location` (`id`);

--
-- Constraints for table `kpi_mark_chart`
--
ALTER TABLE `kpi_mark_chart`
  ADD CONSTRAINT `FK_207E3D3C3D8E604F` FOREIGN KEY (`parent`) REFERENCES `kpi_mark_chart` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_207E3D3C50DDE1BD` FOREIGN KEY (`setting_group_id`) REFERENCES `kpi_setting_type` (`id`);

--
-- Constraints for table `kpi_setting`
--
ALTER TABLE `kpi_setting`
  ADD CONSTRAINT `FK_90E5947B9D1E3C7B` FOREIGN KEY (`setting_type_id`) REFERENCES `kpi_setting_type` (`id`);

--
-- Constraints for table `kpi_setup`
--
ALTER TABLE `kpi_setup`
  ADD CONSTRAINT `FK_94A6887E8C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `core_user` (`id`);

--
-- Constraints for table `kpi_setup_matrix`
--
ALTER TABLE `kpi_setup_matrix`
  ADD CONSTRAINT `FK_82F1874A2B399097` FOREIGN KEY (`upozila_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_82F1874A5B100A73` FOREIGN KEY (`zonal_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_82F1874A5DF2E749` FOREIGN KEY (`employee_setup_id`) REFERENCES `kpi_setup` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_82F1874AA4522A07` FOREIGN KEY (`sales_id`) REFERENCES `kpi_location_sales_target` (`id`),
  ADD CONSTRAINT `FK_82F1874AB08FA272` FOREIGN KEY (`district_id`) REFERENCES `core_location` (`id`),
  ADD CONSTRAINT `FK_82F1874AC24A7B4` FOREIGN KEY (`mark_chart_id`) REFERENCES `kpi_mark_chart` (`id`),
  ADD CONSTRAINT `FK_82F1874ADFEBBA8A` FOREIGN KEY (`mark_distribution_id`) REFERENCES `kpi_mark_chart` (`id`),
  ADD CONSTRAINT `FK_82F1874AE039A55` FOREIGN KEY (`regional_id`) REFERENCES `core_location` (`id`);

--
-- Constraints for table `location`
--
ALTER TABLE `location`
  ADD CONSTRAINT `FK_17E64ABA727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `location` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `mark_chart_setting`
--
ALTER TABLE `mark_chart_setting`
  ADD CONSTRAINT `FK_EF7BFAD9C24A7B4` FOREIGN KEY (`mark_chart_id`) REFERENCES `kpi_mark_chart` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_EF7BFAD9EE35BD72` FOREIGN KEY (`setting_id`) REFERENCES `core_setting` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `terminal`
--
ALTER TABLE `terminal`
  ADD CONSTRAINT `FK_8F7B1541E69B993B` FOREIGN KEY (`main_app_id`) REFERENCES `tbd_app_module` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `terminal_app_module`
--
ALTER TABLE `terminal_app_module`
  ADD CONSTRAINT `FK_A4C0FD2D7ADEAA4` FOREIGN KEY (`app_module_id`) REFERENCES `tbd_app_module` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_A4C0FD2DE77B6CE8` FOREIGN KEY (`terminal_id`) REFERENCES `terminal` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
