ALTER TABLE `acx_purchases`
ADD COLUMN `print_count` int(11) NOT NULL DEFAULT 0 AFTER `status`;