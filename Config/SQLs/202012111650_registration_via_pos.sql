ALTER TABLE `acx_orders`
ADD COLUMN `is_member_register` int(11) DEFAULT 0 AFTER `status`;
CREATE INDEX `is_member_register_idx` ON `acx_orders`(`is_member_register`);

ALTER TABLE `acx_orders`
ADD COLUMN `phone_registration` varchar(20) COLLATE utf8mb4_unicode_ci NULL AFTER `is_member_register`;
ALTER TABLE `acx_orders`
ADD COLUMN `country_code_registration` varchar(20) COLLATE utf8mb4_unicode_ci NULL AFTER `is_member_register`;


ALTER TABLE `acx_orders`
ADD COLUMN `registration_fee` decimal(12, 2) DEFAULT 0 AFTER `discount_percentage`;


DROP TABLE IF EXISTS `acx_member_pos_registrations`;
CREATE TABLE `acx_member_pos_registrations`  (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) DEFAULT NULL,
    `staff_id` int(11) DEFAULT NULL,
    `member_id` int(11) DEFAULT NULL,
    `country_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `verification_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `date` datetime NOT NULL,
    `expiry_date` datetime NOT NULL,
    `amount` decimal(12, 2) DEFAULT 0,
    `registration_date` datetime NULL,
    `void` tinyint(1) NOT NULL DEFAULT 0,
    `updated` datetime NOT NULL,
    `updated_by` int(11) DEFAULT NULL,
    `created` datetime NOT NULL,
    `created_by` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `order_id_idx`(`order_id`),
    INDEX `staff_id_idx`(`staff_id`),
    INDEX `member_id_idx`(`member_id`),
    INDEX `country_code_idx`(`country_code`),
    INDEX `phone_idx`(`phone`),
    INDEX `verification_code_idx`(`verification_code`),
    INDEX `void_idx`(`void`),
    INDEX `updated_by_idx`(`updated_by`),
    INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;