ALTER TABLE `acx_members`
MODIFY COLUMN `phone_verified` datetime DEFAULT NULL AFTER `phone_verification`,
MODIFY COLUMN `email_verified` datetime DEFAULT NULL AFTER `email_verification`;


ALTER TABLE `acx_staffs` ADD `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `name`;
ALTER TABLE `acx_staffs` ADD `country_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `name`;