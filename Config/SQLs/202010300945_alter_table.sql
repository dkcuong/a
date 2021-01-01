ALTER TABLE `acx_orders` ADD `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `qrcode_path`;
CREATE INDEX `token_idx` ON `acx_orders`(`token`);


ALTER TABLE `acx_orders` ADD `payment_log_id` int(11) NULL AFTER `token`;
CREATE INDEX `payment_log_id_idx` ON `acx_orders`(`payment_log_id`);