-- ----------------------------
-- Table structure for acx_booking_enquiries_equipments
-- ----------------------------
DROP TABLE IF EXISTS `acx_booking_enquiries_equipments`;
CREATE TABLE `acx_booking_enquiries_equipments`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_enquiry_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `booking_enquiry_id_idx`(`booking_enquiry_id`),
  INDEX `equipment_id_idx`(`equipment_id`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
