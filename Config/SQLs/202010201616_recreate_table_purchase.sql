DROP TABLE IF EXISTS `acx_purchases`;
DROP TABLE IF EXISTS `acx_purchases`;
CREATE TABLE `acx_purchases`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `inv_number` varchar(20) COLLATE utf8mb4_unicode_ci NULL,
  `total_amount` decimal(12, 2) DEFAULT 0,
  `total_discount_coupon` decimal(12, 2) DEFAULT 0,
  `discount_amount` decimal(12, 2) DEFAULT 0,
  `discount_percentage` decimal(12, 2) DEFAULT 0,
  `grand_total` decimal(12, 2) DEFAULT 0,
  `paid_amount` decimal(12, 2) DEFAULT 0,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
  `is_paid` tinyint(1) DEFAULT 1,
  `void` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(10) NOT NULL DEFAULT 0,
  `qrcode_path` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `member_id_idx`(`member_id`),
  INDEX `staff_id_idx`(`staff_id`),
  INDEX `date_idx`(`date`),
  INDEX `inv_number_idx`(`inv_number`),
  INDEX `phone_idx`(`phone`),
  INDEX `email_idx`(`email`),
  INDEX `is_paid_idx`(`is_paid`),
  INDEX `void_idx`(`void`),
  INDEX `status_idx`(`status`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_purchase_detail
-- ----------------------------
DROP TABLE IF EXISTS `acx_purchase_detail`;
DROP TABLE IF EXISTS `acx_purchase_details`;
CREATE TABLE `acx_purchase_details`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `price` decimal(12, 2) DEFAULT 0,
  `service_charge` decimal(12, 2) DEFAULT 0,
  `service_charge_percentage` int(11) DEFAULT 0,
  `discount` decimal(12, 2) DEFAULT 0,
  `subtotal` decimal(12, 2) DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `purchase_id_idx`(`purchase_id`),
  INDEX `item_id_idx`(`item_id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_purchase_detail_payments
-- ----------------------------
DROP TABLE IF EXISTS `acx_purchase_detail_payments`;
DROP TABLE IF EXISTS `acx_purchase_detail_payments`;
CREATE TABLE `acx_purchase_detail_payments`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `amount` decimal(12, 2) DEFAULT 0,
  `value` decimal(12, 2) DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `purchase_id_idx`(`purchase_id`),
  INDEX `payment_method_id_idx`(`payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;