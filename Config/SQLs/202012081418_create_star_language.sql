DROP TABLE IF EXISTS `acx_star_languages`;
CREATE TABLE `acx_star_languages`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `star_id` int(11) DEFAULT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `star_first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `star_surname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `star_idx`(`star_id`),
  INDEX `language_idx`(`language`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;