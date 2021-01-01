ALTER TABLE `acx_items`
ADD COLUMN `sold_in_house_booking` tinyint(1) NOT NULL DEFAULT 0 AFTER `availability`;

UPDATE `acx_items` SET `sold_in_house_booking` = 1 WHERE `id` = 3;
UPDATE `acx_items` SET `sold_in_house_booking` = 1 WHERE `id` = 7;
UPDATE `acx_items` SET `sold_in_house_booking` = 1 WHERE `id` = 10;
UPDATE `acx_items` SET `sold_in_house_booking` = 1 WHERE `id` = 11;