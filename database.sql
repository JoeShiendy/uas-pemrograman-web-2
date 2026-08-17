-- Database: perpustakaan_db

CREATE DATABASE IF NOT EXISTS `perpustakaan_db`;
USE `perpustakaan_db`;

-- 1. Struktur Tabel Users (Session)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL
);

-- 2. Struktur Tabel Buku (CRUD)
CREATE TABLE IF NOT EXISTS `buku` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `judul` VARCHAR(255) NOT NULL,
  `penulis` VARCHAR(100) NOT NULL,
  `penerbit` VARCHAR(100) NOT NULL,
  `tahun_terbit` INT(4) NOT NULL,
  `kategori` VARCHAR(50) NOT NULL
);

-- 3. Data Default Akun Admin
INSERT INTO `users` (`username`, `password`) VALUES
('admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7w8q2f.yG7h/9pE2x6q1Y3mZ.K2S');

-- 4. Sample Data Buku
INSERT INTO `buku` (`judul`, `penulis`, `penerbit`, `tahun_terbit`, `kategori`) VALUES
('Pemrograman Web II', 'Budi Raharjo', 'Informatika', 2022, 'Teknologi'),
('Belajar Database MySQL', 'Abdul Kadir', 'Andi Publisher', 2021, 'Teknologi'),
('Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'Novel'),
('Bumi Manusia', 'Pramoedya A. Toer', 'Hasta Mitra', 1980, 'Sastra'),
('Filosofi Teras', 'Henry M.', 'Kompas', 2018, 'Pengembangan Diri');
