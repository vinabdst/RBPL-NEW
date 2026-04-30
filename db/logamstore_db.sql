-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2026 at 02:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `logamstore_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `idBarang` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `jenis_logam` varchar(50) NOT NULL,
  `berat_gram` decimal(10,2) NOT NULL,
  `harga_beli` decimal(15,2) NOT NULL,
  `harga_jual` decimal(15,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`idBarang`, `nama_barang`, `jenis_logam`, `berat_gram`, `harga_beli`, `harga_jual`, `stok`, `created_at`) VALUES
(1, 'Timbal Batangan', 'Timbal', 5000.00, 85000.00, 100000.00, 70, '2026-04-08 15:35:40'),
(2, 'Tembaga Lembaran', 'Tembaga', 2000.00, 120000.00, 150000.00, 4, '2026-04-08 15:35:40'),
(3, 'Kawat Tembaga 1mm', 'Tembaga', 1000.00, 75000.00, 90000.00, 90, '2026-04-08 15:35:40'),
(5, 'Seng Lembaran', 'Seng', 2500.00, 60000.00, 75000.00, 70, '2026-04-08 15:35:40'),
(6, 'Besi Beton 10mm', 'Besi', 6000.00, 45000.00, 55000.00, 40, '2026-04-08 15:35:40'),
(8, 'Kawat Tembaga', 'Kawat', 1.00, 15000.00, 20000.00, 56, '2026-04-22 05:48:16');

-- --------------------------------------------------------

--
-- Table structure for table `detail_beli`
--

CREATE TABLE `detail_beli` (
  `idDetailBeli` int(11) NOT NULL,
  `idTransaksiBeli` int(11) NOT NULL,
  `idBarang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_beli`
--

INSERT INTO `detail_beli` (`idDetailBeli`, `idTransaksiBeli`, `idBarang`, `jumlah`, `harga_satuan`, `subtotal`) VALUES
(1, 1, 1, 20, 85000.00, 1700000.00),
(3, 3, 8, 6, 15000.00, 90000.00),
(4, 3, 2, 10, 120000.00, 1200000.00),
(5, 3, 5, 15, 60000.00, 900000.00),
(6, 4, 8, 50, 15000.00, 750000.00);

-- --------------------------------------------------------

--
-- Table structure for table `detail_jual`
--

CREATE TABLE `detail_jual` (
  `idDetailJual` int(11) NOT NULL,
  `idTransaksiJual` int(11) NOT NULL,
  `idBarang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_jual`
--

INSERT INTO `detail_jual` (`idDetailJual`, `idTransaksiJual`, `idBarang`, `jumlah`, `harga_satuan`, `subtotal`) VALUES
(1, 1, 3, 10, 90000.00, 900000.00),
(2, 1, 6, 30, 55000.00, 1650000.00),
(3, 2, 5, 5, 75000.00, 375000.00);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_beli`
--

CREATE TABLE `transaksi_beli` (
  `idTransaksiBeli` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `supplier` varchar(100) NOT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_beli`
--

INSERT INTO `transaksi_beli` (`idTransaksiBeli`, `tanggal`, `supplier`, `total_harga`, `created_at`) VALUES
(1, '2026-04-22', 'TimbalBalik', 1700000.00, '2026-04-22 05:43:18'),
(3, '2026-04-29', 'Metalica', 2190000.00, '2026-04-29 05:16:59'),
(4, '2026-04-29', 'Metalica', 750000.00, '2026-04-29 05:18:26');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_jual`
--

CREATE TABLE `transaksi_jual` (
  `idTransaksiJual` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `kasir` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_jual`
--

INSERT INTO `transaksi_jual` (`idTransaksiJual`, `tanggal`, `total_harga`, `kasir`, `created_at`) VALUES
(1, '2026-04-29', 2550000.00, 'kasir2', '2026-04-29 05:19:25'),
(2, '2026-04-30', 375000.00, 'Beco', '2026-04-30 11:51:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `idUser` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('Owner','Kasir') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`idUser`, `username`, `password`, `nama`, `role`) VALUES
(1, 'owner', '$2y$10$6XJVuxXtQ/tRMMr4FzDnCeHwWBugbKmyRMQfH9F1YbzKief.sXte2', 'Chika', 'Owner'),
(2, 'kasir', '$2y$10$4aemE/5J86e1kFgaZULvxuYHj8.yVh4zEhng6CCoCKUts1izxrvaW', 'Beco', 'Kasir'),
(7, 'kasir2', '$2y$10$.47X9qFPi7FZNv8bGlX2pume0KKp17he8vfGMXyrGbf42OW7eYJ7e', 'Tara', 'Kasir');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`idBarang`);

--
-- Indexes for table `detail_beli`
--
ALTER TABLE `detail_beli`
  ADD PRIMARY KEY (`idDetailBeli`),
  ADD KEY `idTransaksiBeli` (`idTransaksiBeli`),
  ADD KEY `idBarang` (`idBarang`);

--
-- Indexes for table `detail_jual`
--
ALTER TABLE `detail_jual`
  ADD PRIMARY KEY (`idDetailJual`),
  ADD KEY `idTransaksiJual` (`idTransaksiJual`),
  ADD KEY `idBarang` (`idBarang`);

--
-- Indexes for table `transaksi_beli`
--
ALTER TABLE `transaksi_beli`
  ADD PRIMARY KEY (`idTransaksiBeli`);

--
-- Indexes for table `transaksi_jual`
--
ALTER TABLE `transaksi_jual`
  ADD PRIMARY KEY (`idTransaksiJual`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`idUser`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `idBarang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `detail_beli`
--
ALTER TABLE `detail_beli`
  MODIFY `idDetailBeli` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `detail_jual`
--
ALTER TABLE `detail_jual`
  MODIFY `idDetailJual` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transaksi_beli`
--
ALTER TABLE `transaksi_beli`
  MODIFY `idTransaksiBeli` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transaksi_jual`
--
ALTER TABLE `transaksi_jual`
  MODIFY `idTransaksiJual` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_beli`
--
ALTER TABLE `detail_beli`
  ADD CONSTRAINT `detail_beli_ibfk_1` FOREIGN KEY (`idTransaksiBeli`) REFERENCES `transaksi_beli` (`idTransaksiBeli`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_beli_ibfk_2` FOREIGN KEY (`idBarang`) REFERENCES `barang` (`idBarang`) ON DELETE CASCADE;

--
-- Constraints for table `detail_jual`
--
ALTER TABLE `detail_jual`
  ADD CONSTRAINT `detail_jual_ibfk_1` FOREIGN KEY (`idTransaksiJual`) REFERENCES `transaksi_jual` (`idTransaksiJual`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_jual_ibfk_2` FOREIGN KEY (`idBarang`) REFERENCES `barang` (`idBarang`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
