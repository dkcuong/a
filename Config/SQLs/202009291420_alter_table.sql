ALTER TABLE `acx_hall_details` ADD `label` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 0 AFTER `row_number`;
ALTER TABLE `acx_orders` ADD `status` tinyint(1) NOT NULL DEFAULT 0 AFTER `void`;
ALTER TABLE `acx_orders` MODIFY `status` tinyint(10) NOT NULL DEFAULT 0 AFTER `void`;
ALTER TABLE `acx_schedule_details` MODIFY `time` TIME NOT NULL;



ALTER TABLE `acx_schedules` ADD `movie_id` int(11) NOT NULL AFTER `id`;
ALTER TABLE `acx_schedules` ADD `movie_type_id` int(11) NOT NULL AFTER `movie_id`;
CREATE INDEX `movie_id_idx` ON `acx_schedules`(`movie_id`);
CREATE INDEX `movie_type_id_idx` ON `acx_schedules`(`movie_type_id`);
ALTER TABLE `acx_schedules` DROP `movies_movie_type_id`;

ALTER TABLE `acx_members` ADD `enabled` tinyint(1) NOT NULL DEFAULT 1 AFTER `email_verified`;
CREATE INDEX `enabled_idx` ON `acx_members`(`enabled`);

ALTER TABLE `acx_members` ADD `age_group_id` int(11) DEFAULT NULL AFTER `name`;
ALTER TABLE `acx_members` ADD `gender` enum('male','female') COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `name`;
ALTER TABLE `acx_members` ADD `birth_month` int(11) DEFAULT NULL AFTER `name`;
ALTER TABLE `acx_members` ADD `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `name`;
ALTER TABLE `acx_members` ADD `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `name`;

ALTER TABLE `acx_schedule_detail_layouts` ADD `label` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 0 AFTER `row_number`;
ALTER TABLE `acx_schedule_detail_layouts` ADD `enabled` tinyint(1) NOT NULL DEFAULT 1 AFTER `order_time`;
ALTER TABLE `acx_schedule_detail_layouts` MODIFY `status` tinyint(10) NOT NULL DEFAULT 1;
