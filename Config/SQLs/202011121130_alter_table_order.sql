ALTER TABLE `acx_orders`
ADD COLUMN `country_code` varchar(10) COLLATE utf8mb4_unicode_ci NULL AFTER `paid_amount`;