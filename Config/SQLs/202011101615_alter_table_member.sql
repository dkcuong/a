ALTER TABLE `acx_members`
ADD COLUMN `is_under_18` tinyint(1) NOT NULL DEFAULT 0 AFTER `qrcode_path`,
ADD COLUMN `is_agreed` tinyint(1) NOT NULL DEFAULT 0 AFTER `qrcode_path`,
ADD COLUMN `is_read` tinyint(1) NOT NULL DEFAULT 0 AFTER `qrcode_path`;