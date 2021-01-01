ALTER TABLE `acx_order_detail_payments` CHANGE `payment_type_id` `payment_method_id` INT(11) NOT NULL;
ALTER TABLE `acx_purchase_detail_payments` CHANGE `payment_type_id` `payment_method_id` INT(11) NOT NULL;
