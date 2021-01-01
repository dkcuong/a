-- ----------------------------
-- Table structure for acx_cinemas
-- ----------------------------
DROP TABLE IF EXISTS `acx_cinemas`;
CREATE TABLE `acx_cinemas`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
  `description` text CHARACTER SET utf8 NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `code_idx`(`code`),
  INDEX `enabled_idx`(`enabled`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_coupons
-- ----------------------------
DROP TABLE IF EXISTS `acx_coupons`;
CREATE TABLE `acx_coupons`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL DEFAULT 1,
  `description` text CHARACTER SET utf8 NULL,
  `expired_date` datetime DEFAULT NULL,
  `expiry_range` int(11) NOT NULL DEFAULT 0,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `type_idx`(`type`),
  INDEX `enabled_idx`(`enabled`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_member_coupons
-- ----------------------------
DROP TABLE IF EXISTS `acx_member_coupons`;
CREATE TABLE `acx_member_coupons`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_date` datetime DEFAULT NULL,
  `code_path` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
  `status` int(11) DEFAULT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `coupon_id_idx`(`coupon_id`),
  INDEX `member_id_idx`(`member_id`),
  INDEX `code_idx`(`code`),
  INDEX `expired_date_idx`(`expired_date`),
  INDEX `status_idx`(`status`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_device_type
-- ----------------------------
DROP TABLE IF EXISTS `acx_device_types`;
CREATE TABLE `acx_device_types`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `slug_idx`(`slug`),
  INDEX `enabled_idx`(`enabled`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_hall_details
-- ----------------------------
DROP TABLE IF EXISTS `acx_hall_details`;
CREATE TABLE `acx_hall_details`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hall_id` int(11) DEFAULT NULL,
  `column_number` int(11) DEFAULT NULL,
  `row_number` int(11) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `hall_id_idx`(`hall_id`),
  INDEX `column_number_idx`(`column_number`),
  INDEX `row_number_idx`(`row_number`),
  INDEX `enabled_idx`(`enabled`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_halls
-- ----------------------------
DROP TABLE IF EXISTS `acx_halls`;
CREATE TABLE `acx_halls`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cinema_id` int(11) DEFAULT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_seat` int(11) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `cinema_id_idx`(`cinema_id`),
  INDEX `code_idx`(`code`),
  INDEX `enabled_idx`(`enabled`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_item_groups
-- ----------------------------
DROP TABLE IF EXISTS `acx_item_groups`;
CREATE TABLE `acx_item_groups`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `code_idx`(`code`),
  INDEX `enabled_idx`(`enabled`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_item_group_languages
-- ----------------------------
DROP TABLE IF EXISTS `acx_item_group_languages`;
CREATE TABLE `acx_item_group_languages`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_group_id` int(11) DEFAULT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `item_group_id_idx`(`item_group_id`),
  INDEX `language_idx`(`language`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_item
-- ----------------------------
DROP TABLE IF EXISTS `acx_items`;
CREATE TABLE `acx_items`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_group_id` int(11) DEFAULT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12, 2) DEFAULT 0,
  `availability` int(11) DEFAULT 0,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `item_group_id_idx`(`item_group_id`),
  INDEX `code_idx`(`code`),
  INDEX `availability_idx`(`availability`),
  INDEX `enabled_idx`(`enabled`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_item_languages
-- ----------------------------
DROP TABLE IF EXISTS `acx_item_languages`;
CREATE TABLE `acx_item_languages`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `item_id_idx`(`item_id`),
  INDEX `language_idx`(`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_member
-- ----------------------------
DROP TABLE IF EXISTS `acx_members`;
CREATE TABLE `acx_members`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birthday` date DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_verification` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_verified` datetime NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verification` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `birthday_idx`(`birthday`),
  INDEX `token_idx`(`token`),
  INDEX `country_code_idx`(`country_code`),
  INDEX `phone_idx`(`phone`),
  INDEX `email_idx`(`email`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_movie_type
-- ----------------------------
DROP TABLE IF EXISTS `acx_movie_types`;
CREATE TABLE `acx_movie_types`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `enabled_idx`(`enabled`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for acx_ticket_types
-- ----------------------------
DROP TABLE IF EXISTS `acx_ticket_types`;
CREATE TABLE `acx_ticket_types`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `code_idx`(`code`),
  INDEX `enabled_idx`(`enabled`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_ticket_type_languages
-- ----------------------------
DROP TABLE IF EXISTS `acx_ticket_type_languages`;
CREATE TABLE `acx_ticket_type_languages`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_type_id` int(11) DEFAULT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `ticket_type_id_idx`(`ticket_type_id`),
  INDEX `language_idx`(`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for acx_member_devices
-- ----------------------------
DROP TABLE IF EXISTS `acx_member_devices`;
CREATE TABLE `acx_member_devices`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `device_type_id` int(11) NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_code` varchar(200) COLLATE utf8mb4_unicode_ci NULL,
  `os_version` varchar(200) COLLATE utf8mb4_unicode_ci NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `member_id_idx`(`member_id`),
  INDEX `device_type_id_idx`(`device_type_id`),
  INDEX `token_idx`(`token`),
  INDEX `enabled_idx`(`enabled`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_member_notifications
-- ----------------------------
DROP TABLE IF EXISTS `acx_member_notifications`;
CREATE TABLE `acx_member_notifications`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `notification_id` int(11) NOT NULL,
  `pushed` datetime DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `member_id_idx`(`member_id`),
  INDEX `notification_id_idx`(`notification_id`),
  INDEX `read_at_idx`(`read_at`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_member_renewal
-- ----------------------------
DROP TABLE IF EXISTS `acx_member_renewals`;
CREATE TABLE `acx_member_renewals`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `renewal_date` datetime DEFAULT NULL,
  `expired_date` datetime DEFAULT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `member_id_idx`(`member_id`),
  INDEX `expired_date_idx`(`expired_date`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_movie
-- ----------------------------
DROP TABLE IF EXISTS `acx_movies`;
CREATE TABLE `acx_movies`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `director` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `writer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `star` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `poster` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` int(11) DEFAULT NULL,
  `video` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int(11) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `code_idx`(`code`),
  INDEX `slug_idx`(`slug`),
  INDEX `enabled_idx`(`enabled`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_movie_languages
-- ----------------------------
DROP TABLE IF EXISTS `acx_movie_languages`;
CREATE TABLE `acx_movie_languages`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movie_id` int(11) NOT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_storyline` text CHARACTER SET utf8 NULL,
  `storyline` text CHARACTER SET utf8 NULL,
  PRIMARY KEY (`id`),
  INDEX `movie_id_idx`(`movie_id`),
  INDEX `language_idx`(`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for acx_movies_movie_types
-- ----------------------------
DROP TABLE IF EXISTS `acx_movies_movie_types`;
CREATE TABLE `acx_movies_movie_types`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movie_id` int(11) NOT NULL,
  `movie_type_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `publish_date` datetime NOT NULL,
  `open_order_date` datetime DEFAULT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `movie_id_idx`(`movie_id`),
  INDEX `movie_type_id_idx`(`movie_type_id`),
  INDEX `publish_date_idx`(`publish_date`),
  INDEX `start_date_idx`(`start_date`),
  INDEX `open_order_date_idx`(`open_order_date`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for acx_notification
-- ----------------------------
DROP TABLE IF EXISTS `acx_notifications`;
CREATE TABLE `acx_notifications`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `push_method` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_notification_languages
-- ----------------------------
DROP TABLE IF EXISTS `acx_notification_languages`;
CREATE TABLE `acx_notification_languages`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_id` int(11) NOT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8 NULL,
  PRIMARY KEY (`id`),
  INDEX `notification_id_idx`(`notification_id`),
  INDEX `language_idx`(`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_orders
-- ----------------------------
DROP TABLE IF EXISTS `acx_orders`;
CREATE TABLE `acx_orders`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `inv_number` varchar(20) COLLATE utf8mb4_unicode_ci NULL,
  `total_amount` decimal(12, 2) DEFAULT 0,
  `discount_amount` decimal(12, 2) DEFAULT 0,
  `discount_percentage` decimal(12, 2) DEFAULT 0,
  `grand_total` decimal(12, 2) DEFAULT 0,
  `paid_amount` decimal(12, 2) DEFAULT 0,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
  `is_paid` tinyint(1) DEFAULT 1,
  `void` tinyint(1) NOT NULL DEFAULT 0,
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
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_order_details
-- ----------------------------
DROP TABLE IF EXISTS `acx_order_details`;
CREATE TABLE `acx_order_details`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `schedule_detail_ticket_type_id` int(11) NOT NULL,
  `price` decimal(12, 2) DEFAULT 0,
  `service_charge` decimal(12, 2) DEFAULT 0,
  `discount` decimal(12, 2) DEFAULT 0,
  `subtotal` decimal(12, 2) DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `order_id_idx`(`order_id`),
  INDEX `schedule_detail_ticket_type_id_idx`(`schedule_detail_ticket_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_order_detail_layouts
-- ----------------------------
DROP TABLE IF EXISTS `acx_order_detail_layouts`;
CREATE TABLE `acx_order_detail_layouts`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `schedule_detail_layout_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `order_id_idx`(`order_id`),
  INDEX `schedule_detail_layout_id_idx`(`schedule_detail_layout_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for acx_order_detail_payments
-- ----------------------------
DROP TABLE IF EXISTS `acx_order_detail_payments`;
CREATE TABLE `acx_order_detail_payments`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `payment_type_id` int(11) NOT NULL,
  `number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `amount` decimal(12, 2) DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `order_id_idx`(`order_id`),
  INDEX `payment_type_id_idx`(`payment_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_payment_method
-- ----------------------------
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

-- ----------------------------
-- Table structure for acx_purchases
-- ----------------------------
DROP TABLE IF EXISTS `acx_purchases`;
CREATE TABLE `acx_purchases`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `inv_number` varchar(20) COLLATE utf8mb4_unicode_ci NULL,
  `total_amount` decimal(12, 2) DEFAULT 0,
  `discount_amount` decimal(12, 2) DEFAULT 0,
  `discount_percentage` decimal(12, 2) DEFAULT 0,
  `grand_total` decimal(12, 2) DEFAULT 0,
  `paid_amount` decimal(12, 2) DEFAULT 0,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
  `is_paid` tinyint(1) DEFAULT 1,
  `void` tinyint(1) NOT NULL DEFAULT 0,
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
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_purchase_detail
-- ----------------------------
DROP TABLE IF EXISTS `acx_purchase_detail`;
CREATE TABLE `acx_purchase_detail`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `price` decimal(12, 2) DEFAULT 0,
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
CREATE TABLE `acx_purchase_detail_payments`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) NOT NULL,
  `payment_type_id` int(11) NOT NULL,
  `number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `amount` decimal(12, 2) DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `purchase_id_idx`(`purchase_id`),
  INDEX `payment_type_id_idx`(`payment_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for acx_schedules
-- ----------------------------
DROP TABLE IF EXISTS `acx_schedules`;
CREATE TABLE `acx_schedules`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movies_movie_type_id` int(11) NOT NULL,
  `hall_id` int(11) NOT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `movies_movie_type_id_idx`(`movies_movie_type_id`),
  INDEX `hall_id_idx`(`hall_id`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




-- ----------------------------
-- Table structure for acx_schedule_details
-- ----------------------------
DROP TABLE IF EXISTS `acx_schedule_details`;
CREATE TABLE `acx_schedule_details`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `time` datetime NOT NULL,
  `capacity` int(11) DEFAULT 0,
  `attendance_rate` int(11) DEFAULT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `schedule_id_idx`(`schedule_id`),
  INDEX `date_idx`(`date`),
  INDEX `time_idx`(`time`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for acx_schedule_detail_layouts
-- ----------------------------
DROP TABLE IF EXISTS `acx_schedule_detail_layouts`;
CREATE TABLE `acx_schedule_detail_layouts`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_detail_id` int(11) NOT NULL,
  `column_number` int(11) NOT NULL,
  `row_number` int(11) NOT NULL,
  `status` tinyint(10) NOT NULL DEFAULT 1,
  `order_time` datetime DEFAULT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `schedule_detail_id_idx`(`schedule_detail_id`),
  INDEX `column_number_idx`(`column_number`),
  INDEX `row_number_idx`(`row_number`),
  INDEX `status_idx`(`status`),
  INDEX `order_time_idx`(`order_time`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_schedule_detail_ticket_types
-- ----------------------------
DROP TABLE IF EXISTS `acx_schedule_detail_ticket_types`;
CREATE TABLE `acx_schedule_detail_ticket_types`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_detail_id` int(11) NOT NULL,
  `ticket_type_id` int(11) NOT NULL,
  `price` decimal(12, 2) DEFAULT 0,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `schedule_detail_id_idx`(`schedule_detail_id`),
  INDEX `ticket_type_id_idx`(`ticket_type_id`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;






-- ----------------------------
-- Table structure for acx_staffs
-- ----------------------------
DROP TABLE IF EXISTS `acx_staffs`;
CREATE TABLE `acx_staffs`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `code_idx`(`code`),
  INDEX `enabled_idx`(`enabled`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_staff_logs
-- ----------------------------
DROP TABLE IF EXISTS `acx_staff_logs`;
CREATE TABLE `acx_staff_logs`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) DEFAULT NULL,
  `clock_in` datetime DEFAULT NULL,
  `clock_out` datetime DEFAULT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `staff_id_idx`(`staff_id`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acx_settings
-- ----------------------------
DROP TABLE IF EXISTS `acx_settings`;
CREATE TABLE `acx_settings` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8 NULL,
	`enabled` tinyint(1) DEFAULT 1,
	`updated` datetime DEFAULT NULL,
	`updated_by` int(11) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`created_by` int(11) DEFAULT NULL,
	PRIMARY KEY (`id`),
  INDEX `slug_idx`(`slug`),
  INDEX `enabled_idx`(`enabled`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;