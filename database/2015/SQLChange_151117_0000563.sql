-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2015 at 04:21 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `virtualpost`
--

-- --------------------------------------------------------


SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for cases_verification_settings
-- ----------------------------
DROP TABLE IF EXISTS `cases_verification_settings`;
CREATE TABLE `cases_verification_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code` varchar(11) DEFAULT NULL,
  `risk_class` tinyint(4) DEFAULT NULL,
  `invoice_address_verification` tinyint(4) DEFAULT NULL,
  `private_postbox_verification` tinyint(4) DEFAULT NULL,
  `business_postbox_verification` tinyint(4) DEFAULT NULL,
  `list_case_number` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=441 DEFAULT CHARSET=utf8;

alter table `cases_milestone_instance` (
add  `partner_id` bigint(20) NOT NULL
) ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
