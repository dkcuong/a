ALTER TABLE `acx_order_payment_logs`
ADD COLUMN `orig_merRef` varchar(50) COLLATE utf8mb4_unicode_ci NULL AFTER `merRef`,
ADD COLUMN `orig_payRef` varchar(50) COLLATE utf8mb4_unicode_ci NULL AFTER `payRef`;


ALTER TABLE `acx_orders`
ADD COLUMN `refund_log_id` int(11) NULL AFTER `payment_log_id`;
CREATE INDEX `refund_log_id_idx` ON `acx_orders`(`refund_log_id`);