CREATE TABLE `acx_movie_trailers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
	`movie_id` int(11) NOT NULL,
	`video_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `enabled_idx` (`enabled`),
  KEY `updated_by_idx` (`updated_by`),
  KEY `created_by_idx` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;