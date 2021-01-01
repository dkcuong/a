ALTER TABLE `acx_member_renewals`
ADD COLUMN `order_id` int(11) DEFAULT 0 AFTER `payment_log_id`;
CREATE INDEX `order_id_idx` ON `acx_member_renewals`(`order_id`);