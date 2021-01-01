ALTER TABLE `acx_staffs` ADD `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `password`;
ALTER TABLE `acx_staff_logs` ADD `model_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `clock_out`;


ALTER TABLE `acx_ticket_types` ADD `is_main` tinyint(1) NOT NULL DEFAULT 0 AFTER `name`;
ALTER TABLE `acx_ticket_types` ADD `is_disability` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_main`;

ALTER TABLE `acx_orders` ADD `schedule_detail_id` int(11) NOT NULL AFTER `staff_id`;
CREATE INDEX `schedule_detail_id_idx` ON `acx_orders`(`schedule_detail_id`);

ALTER TABLE `acx_order_details` ADD `qty` int(11) NOT NULL DEFAULT 1 AFTER `schedule_detail_ticket_type_id`;
ALTER TABLE `acx_order_details` ADD `service_charge_percentage` int(11) NOT NULL DEFAULT 0 AFTER `service_charge`;

ALTER TABLE `acx_order_details` ADD `schedule_detail_layout_id` int(11) NOT NULL DEFAULT 0 AFTER `schedule_detail_ticket_type_id`;
CREATE INDEX `schedule_detail_layout_id_idx` ON `acx_order_details`(`schedule_detail_layout_id`);

drop table `acx_order_detail_layouts`;