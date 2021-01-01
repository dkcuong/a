-- --------------------------------------------------------

--
-- Table structure for table `acx_renewal_payment_logs`
--
drop table if exists `acx_order_payment_logs`;
CREATE TABLE IF NOT EXISTS `acx_order_payment_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trans_token` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
  `date` datetime NOT NULL,
  `amt` int(11) NOT NULL,
  `authId` varchar(200) COLLATE utf8mb4_unicode_ci NULL,
  `chRef` varchar(200) COLLATE utf8mb4_unicode_ci NULL,
  `chResCode` varchar(200) COLLATE utf8mb4_unicode_ci NULL,
  `curr` varchar(20) COLLATE utf8mb4_unicode_ci NULL,
  `eci` varchar(10) COLLATE utf8mb4_unicode_ci NULL,
  `merData` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
  `merRef` varchar(50) COLLATE utf8mb4_unicode_ci NULL,
  `panHash` varchar(200) COLLATE utf8mb4_unicode_ci NULL,
  `panHashType` varchar(200) COLLATE utf8mb4_unicode_ci NULL,
  `payRef` varchar(50) COLLATE utf8mb4_unicode_ci NULL,
  `payType` varchar(50) COLLATE utf8mb4_unicode_ci NULL,
  `resMsg` text CHARACTER SET utf8 NULL,
  `sign` varchar(200) COLLATE utf8mb4_unicode_ci NULL,
  `signType` varchar(50) COLLATE utf8mb4_unicode_ci NULL,
  `state` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `trans_token_idx`(`trans_token`),
  INDEX `merRef_idx`(`merRef`),
  INDEX `payRef_idx`(`payRef`),
  INDEX `state_idx`(`state`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;



