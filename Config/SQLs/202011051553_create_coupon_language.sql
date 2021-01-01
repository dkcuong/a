-- ----------------------------
-- Table structure for acx_coupon_languages
-- ----------------------------
DROP TABLE IF EXISTS `acx_coupon_languages`;
CREATE TABLE `acx_coupon_languages`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) DEFAULT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `des` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `terms` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `coupon_id_idx`(`coupon_id`),
  INDEX `language_idx`(`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;