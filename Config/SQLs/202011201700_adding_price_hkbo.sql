ALTER TABLE `acx_schedule_detail_ticket_types`
ADD COLUMN `price_hkbo` decimal(12, 2) DEFAULT 0 AFTER `price`;
ALTER TABLE `acx_order_details`
ADD COLUMN `price_hkbo` decimal(12, 2) DEFAULT 0 AFTER `price`;
ALTER TABLE `acx_halls`
ADD COLUMN `is_using_price_hkbo` tinyint(1) NOT NULL DEFAULT 0 AFTER `max_seat`;
ALTER TABLE `acx_halls`
ADD COLUMN `screen_id` varchar(255) COLLATE utf8mb4_unicode_ci NULL AFTER `max_seat`;