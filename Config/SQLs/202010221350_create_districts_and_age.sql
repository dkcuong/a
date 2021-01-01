-- --------------------------------------------------------

--
-- Table structure for table `acx_districts`
--
drop table if exists `acx_districts`;
CREATE TABLE IF NOT EXISTS `acx_districts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=19 ;

--
-- Dumping data for table `acx_districts`
--

INSERT INTO `acx_districts` (`id`, `enabled`, `created`, `created_by`, `updated`, `updated_by`) VALUES
(1, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(2, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(3, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(4, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(5, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(6, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(7, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(8, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(9, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(10, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(11, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(12, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(13, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(14, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(15, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(16, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(17, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1),
(18, 1, '2020-09-04 04:35:58', 1, '2020-09-04 04:35:58', 1);

-- --------------------------------------------------------

--
-- Table structure for table `acx_district_languages`
--
drop table if exists `acx_district_languages`;
CREATE TABLE IF NOT EXISTS `acx_district_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_id` int(11) NOT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=55 ;

--
-- Dumping data for table `acx_district_languages`
--

INSERT INTO `acx_district_languages` (`id`, `district_id`, `language`, `name`) VALUES
(1, 1, 'zho', '中西區'),
(2, 2, 'zho', '灣仔區'),
(3, 3, 'zho', '東區'),
(4, 4, 'zho', '南區'),
(5, 5, 'zho', '深水埗區'),
(6, 6, 'zho', '油尖旺區'),
(7, 7, 'zho', '九龍城區'),
(8, 8, 'zho', '黃大仙區'),
(9, 9, 'zho', '觀塘區'),
(10, 10, 'zho', '北區'),
(11, 11, 'zho', '大埔區'),
(12, 12, 'zho', '沙田區'),
(13, 13, 'zho', '西貢區'),
(14, 14, 'zho', '元朗區'),
(15, 15, 'zho', '屯門區'),
(16, 16, 'zho', '荃灣區'),
(17, 17, 'zho', '葵青區'),
(18, 18, 'zho', '離島區'),
(19, 1, 'eng', 'Central and Western District'),
(20, 2, 'eng', 'Wan Chai District'),
(21, 3, 'eng', 'Eastern District'),
(22, 4, 'eng', 'Southern District'),
(23, 5, 'eng', 'Sham Shui Po District'),
(24, 6, 'eng', 'Yau Tsim Mong District'),
(25, 7, 'eng', 'Kowloon City District'),
(26, 8, 'eng', 'Wong Tai Sin District'),
(27, 9, 'eng', 'Kwun Tong District'),
(28, 10, 'eng', 'North District'),
(29, 11, 'eng', 'Tai Po District'),
(30, 12, 'eng', 'Sha Tin District'),
(31, 13, 'eng', 'Sai Kung District'),
(32, 14, 'eng', 'Yuen Long District'),
(33, 15, 'eng', 'Tuen Mun District'),
(34, 16, 'eng', 'Tsuen Wan District'),
(35, 17, 'eng', 'Kwai Tsing District'),
(36, 18, 'eng', 'Islands District');





-- --------------------------------------------------------

--
-- Table structure for table `acx_age_groups`
--
drop table if exists `acx_age_groups`;
CREATE TABLE IF NOT EXISTS `acx_age_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from` int(11) DEFAULT NULL,
  `to` int(11) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `acx_age_groups`
--

INSERT INTO `acx_age_groups` (`id`, `slug`, `from`, `to`, `enabled`, `created`, `created_by`, `updated`, `updated_by`) VALUES
(1, 'age-group-a', NULL, 17, 1, '2017-10-11 04:40:47', 6, '2019-03-22 09:32:24', 1),
(2, 'age-group-b', 18, 25, 1, '2017-10-11 04:41:22', 6, '2017-10-11 04:41:22', 1),
(3, 'age-group-c', 26, 30, 1, '2017-10-11 04:42:10', 6, '2017-10-11 04:42:10', 1),
(4, 'age-group-d', 31, 40, 1, '2017-10-11 04:42:38', 6, '2017-10-11 04:42:38', 1),
(5, 'age-group-e', 41, 50, 1, '2017-10-11 05:41:12', 6, '2017-10-11 05:41:12', 1),
(6, 'age-group-f', 51, NULL, 1, '2017-10-11 05:41:49', 6, '2017-10-11 05:41:49', 1);

-- --------------------------------------------------------

--
-- Table structure for table `acx_age_group_languages`
--

drop table if exists `acx_age_group_languages`;
CREATE TABLE IF NOT EXISTS `acx_age_group_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `age_id` int(11) NOT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=25 ;

--
-- Dumping data for table `acx_age_group_languages`
--

INSERT INTO `acx_age_group_languages` (`id`, `age_id`, `language`, `name`, `description`) VALUES
(1, 1, 'zho', '18歲以下', ''),
(3, 1, 'eng', 'Under 18', ''),
(4, 2, 'zho', '18-25 歲', ''),
(6, 2, 'eng', '18-25 years old', ''),
(7, 3, 'zho', '26-30 歲', ''),
(9, 3, 'eng', '26-30 years old', ''),
(10, 4, 'zho', '31-40 歲', ''),
(12, 4, 'eng', '31-40 years old', ''),
(13, 5, 'zho', '41-50 歲', ''),
(15, 5, 'eng', '41-50 years old', ''),
(16, 6, 'zho', '50 歲以上', ''),
(18, 6, 'eng', '50 or Above', '');