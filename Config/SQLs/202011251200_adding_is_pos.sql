ALTER TABLE `acx_orders` ADD `is_pos` tinyint(1) NOT NULL DEFAULT 0 AFTER `status`;

ALTER TABLE `acx_orders` ADD `remark` text CHARACTER SET utf8 NULL AFTER `payment_log_id`;

ALTER TABLE `acx_staffs` ADD `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff' AFTER `name`;