ALTER TABLE `acx_hall_details` ADD `is_disability_seat` tinyint(1) NOT NULL DEFAULT 0 AFTER `label`;
ALTER TABLE `acx_schedule_detail_layouts` ADD `is_disability_seat` tinyint(1) NOT NULL DEFAULT 0 AFTER `label`;

ALTER TABLE `acx_staffs` ADD `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `name`;
ALTER TABLE `acx_staffs` ADD `password` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `name`;
ALTER TABLE `acx_staffs` ADD `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `name`;

ALTER TABLE `acx_member_verifications` ADD `email` varchar(255) DEFAULT NULL AFTER `phone`