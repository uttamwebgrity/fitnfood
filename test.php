-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 04, 2015 at 07:20 AM
-- Server version: 5.5.20
-- PHP Version: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tabjoy_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_utb_student_group_archived`
--

CREATE TABLE IF NOT EXISTS `tbl_utb_student_group_archived` (
  `id` int(10) unsigned NOT NULL,
  `student_group_name` varchar(150) DEFAULT NULL,
  `assigned_lession` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '4' COMMENT '1- completed/ 0 - Pending /2- Rejected/3- Accepted/4 - not started yet',
  `replacement_teacher_needed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_added` date DEFAULT NULL,
  `teacher_id` int(10) unsigned DEFAULT NULL,
  `trainee_teacher_id` int(10) unsigned DEFAULT NULL,
  `trainee_teacher_status` tinyint(1) unsigned DEFAULT NULL,
  `co_teacher_id` int(10) unsigned NOT NULL DEFAULT '0',
  `connector_teacher_id` int(10) unsigned NOT NULL DEFAULT '0',
  `supervisor_teacher_id` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
