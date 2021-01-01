ALTER TABLE `acx_movies`
ADD COLUMN `film_master_id` varchar(50) COLLATE utf8mb4_unicode_ci NULL AFTER `code`;

ALTER TABLE `acx_movies_movie_types`
ADD COLUMN `film_id` varchar(50) COLLATE utf8mb4_unicode_ci NULL AFTER `movie_type_id`;