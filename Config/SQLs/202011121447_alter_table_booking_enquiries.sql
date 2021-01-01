ALTER TABLE `acx_booking_enquiries`
ADD COLUMN `is_subscribe` tinyint(1) NOT NULL DEFAULT 0 AFTER `special_request`,
ADD COLUMN `is_accept_term` tinyint(1) NOT NULL DEFAULT 0 AFTER `special_request`;