ALTER TABLE acx_members ADD `district_id` int(11) DEFAULT NULL AFTER `age_group_id`;

ALTER TABLE acx_member_renewals ADD `inv_number` varchar(20) COLLATE utf8mb4_unicode_ci NULL AFTER `member_id`;
ALTER TABLE acx_member_renewals ADD `date` datetime NOT NULL AFTER `member_id`;
ALTER TABLE acx_member_renewals ADD `amount` decimal(12, 2) DEFAULT 0 AFTER `inv_number`;
ALTER TABLE acx_member_renewals ADD `status` tinyint(10) NOT NULL DEFAULT 0 AFTER `expired_date`;


CREATE INDEX `inv_number_idx` ON `acx_member_renewals`(`inv_number`);
CREATE INDEX `date_idx` ON `acx_member_renewals`(`date`);
CREATE INDEX `status_idx` ON `acx_member_renewals`(`status`);

ALTER TABLE acx_member_renewals ADD `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `expired_date`;
CREATE INDEX `token_idx` ON `acx_member_renewals`(`token`);

ALTER TABLE acx_member_renewals ADD `payment_log_id` int(11) DEFAULT NULL AFTER `member_id`;
CREATE INDEX `payment_log_ididx` ON `acx_member_renewals`(`payment_log_id`);


ALTER TABLE acx_member_coupons ADD `physical_coupon_number` varchar(255) COLLATE utf8mb4_unicode_ci NULL AFTER `expired_date`;
ALTER TABLE acx_member_coupons ADD `convert_date` datetime NULL AFTER `physical_coupon_number`;
CREATE INDEX `physical_coupon_number_idx` ON `acx_member_coupons`(`physical_coupon_number`);
