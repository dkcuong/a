-- ----------------------------
-- Table structure for acx_member_verifications
-- ----------------------------
DROP TABLE IF EXISTS `acx_member_verifications`;
CREATE TABLE `acx_member_verifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(1) NOT NULL,
  `country_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verification_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generated_time` datetime DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT 1,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id_idx` (`member_id`),
  KEY `country_code_idx` (`country_code`),
  KEY `phone_idx` (`phone`),
  KEY `verification_code_idx` (`verification_code`),
  KEY `enabled_idx` (`enabled`),
  KEY `updated_by_idx` (`updated_by`),
  KEY `created_by_idx` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;