ALTER TABLE `acx_members`
ADD COLUMN `is_receive_promotion` tinyint(1) NOT NULL DEFAULT 1 AFTER `qrcode_path`;