-- --------------------------------------------------------

--
-- Table structure for table `acx_booking_enquiries`
--
DROP TABLE IF EXISTS `acx_booking_enquiries`;
CREATE TABLE IF NOT EXISTS `acx_booking_enquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` enum('mr','mrs','ms','miss') DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `country_code` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `time_from` time DEFAULT NULL,
  `time_to` time DEFAULT NULL,
  `event_purpose` varchar(255) DEFAULT NULL,
  `movie_name` varchar(255) DEFAULT NULL,
  `no_of_attendee` int(11) DEFAULT NULL,
  `hall_id` int(11) DEFAULT NULL,
  `special_request` varchar(255) DEFAULT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



