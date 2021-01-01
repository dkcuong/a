-- phpMyAdmin SQL Dump
-- version 4.0.10deb1ubuntu0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 05, 2020 at 04:10 PM
-- Server version: 5.5.62-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `happyfansclub`
--

-- --------------------------------------------------------

--
-- Table structure for table `acx_administrators`
--

CREATE TABLE IF NOT EXISTS `acx_administrators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_logged_in` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `code_forgot` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_code_forgot` timestamp NULL DEFAULT NULL,
  `time_input_code` int(2) DEFAULT NULL,
  `time_input_pass` int(2) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT '1',
  `updated` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=49 ;

--
-- Dumping data for table `acx_administrators`
--

INSERT INTO `acx_administrators` (`id`, `token`, `name`, `email`, `phone`, `password`, `last_logged_in`, `code_forgot`, `created_code_forgot`, `time_input_code`, `time_input_pass`, `enabled`, `updated`, `updated_by`, `created`, `created_by`) VALUES
(1, NULL, 'VTL', 'admin@vtl-vtl.com', '56228402', '14e1b600b1fd579f47433b88e8d85291', '2019-07-24 09:26:21', NULL, NULL, NULL, NULL, 1, '2019-07-24 09:26:21', 1, '2019-03-22 04:43:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acx_administrators_roles`
--

CREATE TABLE IF NOT EXISTS `acx_administrators_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `administrator_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=325 ;

--
-- Dumping data for table `acx_administrators_roles`
--

INSERT INTO `acx_administrators_roles` (`id`, `administrator_id`, `role_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `acx_facts`
--

CREATE TABLE IF NOT EXISTS `acx_facts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `fact_type_id` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `updated` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `acx_fact_languages`
--

CREATE TABLE IF NOT EXISTS `acx_fact_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fact_id` int(11) NOT NULL,
  `alias` varchar(191) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `acx_logs`
--

CREATE TABLE IF NOT EXISTS `acx_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin` varchar(50) NOT NULL,
  `controller` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `new_data` longtext CHARACTER SET utf8,
  `old_data` longtext CHARACTER SET utf8,
  `success` longtext,
  `error` longtext,
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `remote_ip` varchar(191) CHARACTER SET utf8 DEFAULT NULL,
  `agent` varchar(191) CHARACTER SET utf8 DEFAULT NULL,
  `version` varchar(191) DEFAULT NULL,
  `platform` varchar(191) DEFAULT NULL,
  `browser` varchar(191) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `acx_log_apis`
--

CREATE TABLE IF NOT EXISTS `acx_log_apis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin` varchar(50) NOT NULL,
  `controller` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `received_params` varchar(4000) CHARACTER SET utf8 NOT NULL,
  `new_data` text CHARACTER SET utf8,
  `old_data` text CHARACTER SET utf8,
  `success` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `member_id` int(11) DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `acx_log_errors`
--

CREATE TABLE IF NOT EXISTS `acx_log_errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_id` int(11) NOT NULL,
  `content` text CHARACTER SET utf8,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `acx_log_successes`
--

CREATE TABLE IF NOT EXISTS `acx_log_successes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_id` int(11) NOT NULL,
  `content` text CHARACTER SET utf8,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `acx_permissions`
--

CREATE TABLE IF NOT EXISTS `acx_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `p_plugin` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `p_controller` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `p_model` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=165 ;

--
-- Dumping data for table `acx_permissions`
--

INSERT INTO `acx_permissions` (`id`, `slug`, `name`, `p_plugin`, `p_controller`, `p_model`, `action`, `updated`, `updated_by`, `created`, `created_by`) VALUES
(1, 'perm-admin-administartor-view', '管理員列表', 'administration', 'administrators', 'Administrator', 'view', '2017-03-20 12:49:49', NULL, '2017-03-20 12:49:00', NULL),
(2, 'perm-admin-administartor-add', '新增管理員', 'administration', 'administrators', 'Administrator', 'add', '2017-03-20 12:49:49', NULL, '2017-03-20 12:49:00', NULL),
(3, 'perm-admin-administartor-edit', '修改管理員', 'administration', 'administrators', 'Administrator', 'edit', '2017-03-20 12:49:49', NULL, '2017-03-20 12:49:00', NULL),
(4, 'perm-admin-administartor-delete', '刪除管理員', 'administration', 'administrators', 'Administrator', 'delete', '2017-03-20 12:49:49', NULL, '2017-03-20 12:49:00', NULL),
(5, 'perm-admin-permission-view', '權限列表', 'administration', 'permissions', 'Permission', 'view', '2017-03-20 12:49:49', NULL, '2017-03-20 12:49:00', NULL),
(6, 'perm-admin-permission-add', '新增權限', 'administration', 'permissions', 'Permission', 'add', '2017-03-20 12:49:49', NULL, '2017-03-20 12:49:00', NULL),
(7, 'perm-admin-permission-edit', '修改權限', 'administration', 'permissions', 'Permission', 'edit', '2017-03-20 12:49:49', NULL, '2017-03-20 12:49:00', NULL),
(8, 'perm-admin-permission-delete', '刪除權限', 'administration', 'permissions', 'Permission', 'delete', '2017-03-20 12:49:49', NULL, '2017-03-20 12:49:00', NULL),
(9, 'perm-admin-role-view', '角色列表', 'administration', 'roles', 'Role', 'view', '2017-03-20 12:49:49', NULL, '2017-03-20 12:49:00', NULL),
(10, 'perm-admin-role-add', '新增角色', 'administration', 'roles', 'Role', 'add', '2017-03-20 12:49:49', NULL, '2017-03-20 12:49:00', NULL),
(11, 'perm-admin-role-edit', '修改角色', 'administration', 'roles', 'Role', 'edit', '2017-03-20 12:49:49', NULL, '2017-03-20 12:49:00', NULL),
(12, 'perm-admin-role-delete', '刪除角色', 'administration', 'roles', 'Role', 'delete', '2017-03-20 12:49:49', NULL, '2017-03-20 12:49:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acx_roles`
--

CREATE TABLE IF NOT EXISTS `acx_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `manage_role_id` int(11) NOT NULL DEFAULT '0',
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `acx_roles`
--

INSERT INTO `acx_roles` (`id`, `manage_role_id`, `slug`, `name`, `updated`, `updated_by`, `created`, `created_by`) VALUES
(1, 0, 'role-vtl-admin', 'VTL 管理員', '2020-08-11 09:07:36', 1, '2017-02-15 13:25:30', 1);

-- --------------------------------------------------------

--
-- Table structure for table `acx_roles_permissions`
--

CREATE TABLE IF NOT EXISTS `acx_roles_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `permission_id` int(11) DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=10476 ;

--
-- Dumping data for table `acx_roles_permissions`
--

INSERT INTO `acx_roles_permissions` (`id`, `role_id`, `permission_id`, `updated`, `updated_by`, `created`, `created_by`) VALUES
(10320, 1, 1, '2020-08-11 09:07:36', 1, '2020-08-11 09:07:36', 1),
(10321, 1, 2, '2020-08-11 09:07:36', 1, '2020-08-11 09:07:36', 1),
(10322, 1, 3, '2020-08-11 09:07:36', 1, '2020-08-11 09:07:36', 1),
(10323, 1, 4, '2020-08-11 09:07:36', 1, '2020-08-11 09:07:36', 1),
(10324, 1, 5, '2020-08-11 09:07:36', 1, '2020-08-11 09:07:36', 1),
(10325, 1, 6, '2020-08-11 09:07:36', 1, '2020-08-11 09:07:36', 1),
(10326, 1, 7, '2020-08-11 09:07:36', 1, '2020-08-11 09:07:36', 1),
(10327, 1, 8, '2020-08-11 09:07:36', 1, '2020-08-11 09:07:36', 1),
(10328, 1, 9, '2020-08-11 09:07:36', 1, '2020-08-11 09:07:36', 1),
(10329, 1, 10, '2020-08-11 09:07:36', 1, '2020-08-11 09:07:36', 1),
(10330, 1, 11, '2020-08-11 09:07:36', 1, '2020-08-11 09:07:36', 1),
(10331, 1, 12, '2020-08-11 09:07:36', 1, '2020-08-11 09:07:36', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
