ALTER TABLE `acx_members` ADD `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `id`;
CREATE INDEX `code_idx` ON `acx_members`(`code`);

ALTER TABLE `acx_members` ADD `qrcode_path` varchar(255) COLLATE utf8mb4_unicode_ci NULL AFTER `email_verified`;
ALTER TABLE `acx_orders` ADD `qrcode_path` varchar(255) COLLATE utf8mb4_unicode_ci NULL AFTER `status`;

ALTER TABLE `acx_movies` MODIFY `rating` varchar(10) COLLATE utf8mb4_unicode_ci NULL;