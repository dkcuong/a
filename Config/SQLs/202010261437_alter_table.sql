ALTER TABLE `acx_movies`
ADD COLUMN `lang_info` varchar(255) DEFAULT NULL AFTER `language`;

ALTER TABLE `acx_movie_trailers`
ADD COLUMN `poster_path` varchar(255) NOT NULL AFTER `video_path`;

ALTER TABLE `acx_orders`
ADD COLUMN `print_count` int(11) NOT NULL DEFAULT 0 AFTER `status`;