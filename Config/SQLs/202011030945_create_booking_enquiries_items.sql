-- ----------------------------
-- Table structure for acx_booking_enquiries_items
-- ----------------------------
DROP TABLE IF EXISTS `acx_booking_enquiries_items`;
CREATE TABLE `acx_booking_enquiries_items`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_enquiry_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `booking_enquiry_id_idx`(`booking_enquiry_id`),
  INDEX `item_id_idx`(`item_id`),
  INDEX `updated_by_idx`(`updated_by`),
  INDEX `created_by_idx`(`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
