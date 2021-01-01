ALTER TABLE `acx_movie_languages`
ADD COLUMN `lang_movie` varchar(255) DEFAULT NULL AFTER storyline,
ADD COLUMN `lang_info` varchar(255) DEFAULT NULL AFTER storyline,
ADD COLUMN `director` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER storyline,
ADD COLUMN `genre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER storyline,
ADD COLUMN `subtitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER storyline;