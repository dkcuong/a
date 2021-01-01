ALTER TABLE `acx_items` ADD `material` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `price`;
ALTER TABLE `acx_items` ADD `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `price`;

ALTER TABLE `acx_movies` ADD `is_feature` tinyint(1) NOT NULL DEFAULT '0' AFTER `rating`;
ALTER TABLE `acx_movies` ADD `genre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `slug`;
ALTER TABLE `acx_movies` ADD `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `rating`;