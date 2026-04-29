ALTER TABLE `ulasan` 
ADD COLUMN `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending' AFTER `komentar`,
ADD COLUMN `alasan_tolak` TEXT NULL AFTER `status`;

UPDATE `ulasan` SET `status` = 'approved' WHERE `status` = 'pending';

ALTER TABLE `ulasan` ADD INDEX `idx_status` (`status`);