ALTER TABLE `acx_member_renewals`
ADD COLUMN `is_notification_received` int(11) DEFAULT 0 AFTER `status`;
CREATE INDEX `is_notification_received_idx` ON `acx_member_renewals`(`is_notification_received`);

ALTER TABLE `acx_member_renewals`
ADD COLUMN `is_cms` int(11) DEFAULT 0 AFTER `status`;
CREATE INDEX `is_cms_idx` ON `acx_member_renewals`(`is_cms`);