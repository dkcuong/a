-- --------------------------------------------------------

--
-- Table structure for table `acx_equipments`
--
DROP TABLE IF EXISTS `acx_equipments`;
CREATE TABLE IF NOT EXISTS `acx_equipments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12, 2) DEFAULT 0,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `code_idx`(`code`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `acx_equipments`(`id`, `code`, `price`, `enabled`, `updated`, `updated_by`, `created`, `created_by`) VALUES (1, '3D Glasses', 0.00, 1, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL);
INSERT INTO `acx_equipments`(`id`, `code`, `price`, `enabled`, `updated`, `updated_by`, `created`, `created_by`) VALUES (2, 'PA System', 0.00, 1, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL);
INSERT INTO `acx_equipments`(`id`, `code`, `price`, `enabled`, `updated`, `updated_by`, `created`, `created_by`) VALUES (3, 'Microphone', 0.00, 1, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL);
INSERT INTO `acx_equipments`(`id`, `code`, `price`, `enabled`, `updated`, `updated_by`, `created`, `created_by`) VALUES (4, 'Reception', 0.00, 1, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL);
INSERT INTO `acx_equipments`(`id`, `code`, `price`, `enabled`, `updated`, `updated_by`, `created`, `created_by`) VALUES (5, 'Table', 0.00, 1, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL);
INSERT INTO `acx_equipments`(`id`, `code`, `price`, `enabled`, `updated`, `updated_by`, `created`, `created_by`) VALUES (6, 'Chair', 0.00, 1, '0000-00-00 00:00:00', NULL, '0000-00-00 00:00:00', NULL);
