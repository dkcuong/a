ALTER TABLE `acx_ticket_types`
ADD COLUMN `is_id_required` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_disability`;