-- Migration: Tambah kolom status pada tabel ulasan
-- Jalankan query ini di phpMyAdmin atau MySQL sebelum menggunakan website

ALTER TABLE `ulasan` 
ADD COLUMN `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending' AFTER `komentar`,
ADD COLUMN `alasan_tolak` TEXT NULL AFTER `status`;

-- Update ulasan yang sudah ada menjadi approved (agar tidak hilang dari website)
UPDATE `ulasan` SET `status` = 'approved' WHERE `status` = 'pending';

-- Index untuk performa
ALTER TABLE `ulasan` ADD INDEX `idx_status` (`status`);
