-- 1. Buat Database
CREATE DATABASE IF NOT EXISTS db_toko_logam;
USE db_toko_logam;

-- 2. Hapus tabel jika ada (untuk bersih-bersih)
DROP TABLE IF EXISTS users;

-- 3. Tabel User
CREATE TABLE users (
    idUser INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('Owner', 'Kasir') NOT NULL
);

-- 4. Masukkan Akun (password: owner123 dan kasir123)
-- Catatan: hash ini untuk password "owner123" dan "kasir123"
INSERT INTO users (username, password, nama, role) VALUES 
('owner', '$2y$10$6XJVuxXtQ/tRMMr4FzDnCeHwWBugbKmyRMQfH9F1YbzKief.sXte2', 'Alpha', 'Owner'),
('kasir', '$2y$10$4aemE/5J86e1kFgaZULvxuYHj8.yVh4zEhng6CCoCKUts1izxrvaW', 'Beta', 'Kasir');