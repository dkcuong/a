DROP TABLE IF EXISTS `acx_payment_methods`;
CREATE TABLE `acx_payment_methods`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(12, 2) NOT NULL DEFAULT 0,
  `is_card` tinyint(1) NOT NULL DEFAULT 0,
  `is_coupon` tinyint(1) NOT NULL DEFAULT 0,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `code_idx`(`code`),
  INDEX `is_card_idx`(`is_card`),
  INDEX `is_coupon_idx`(`is_coupon`),
  INDEX `enabled_idx`(`enabled`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;