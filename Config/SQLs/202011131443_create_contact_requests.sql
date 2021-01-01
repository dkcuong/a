-- --------------------------------------------------------

--
-- Table structure for table `acx_contact_requests`
--
DROP TABLE IF EXISTS `acx_contact_requests`;
CREATE TABLE IF NOT EXISTS `acx_contact_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` enum('mr','mrs','ms','miss') DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `country_code` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



