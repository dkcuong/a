ALTER TABLE `acx_members` 
MODIFY COLUMN `title` enum('mr','mrs','ms','miss') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `name`;