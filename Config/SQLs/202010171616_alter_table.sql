ALTER TABLE `acx_payment_methods` ADD `type` int(11) DEFAULT 1 AFTER `code`;

ALTER TABLE `acx_order_detail_payments` ADD `value` decimal(12, 2) NOT NULL DEFAULT 0 AFTER `amount`;

ALTER TABLE `acx_orders` ADD `total_discount_coupon` decimal(12, 2) NOT NULL DEFAULT 0 AFTER `total_amount`;


ALTER TABLE `acx_payment_methods` DROP `is_card`;
ALTER TABLE `acx_payment_methods` DROP `is_coupon`;