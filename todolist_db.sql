-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2025 at 06:21 AM
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
-- Database: `todolist_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tugas`
--

CREATE TABLE `tugas` (
  `id_tugas` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_tugas` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `status_tugas` enum('belum','selesai') DEFAULT 'belum',
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_deadline` datetime DEFAULT NULL,
  `prioritas` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tugas`
--

INSERT INTO `tugas` (`id_tugas`, `id_user`, `nama_tugas`, `deskripsi`, `status_tugas`, `tanggal_dibuat`, `tanggal_deadline`, `prioritas`) VALUES
(32, 7, 'Tugas RPL', 'Tugas SKPL kelompok slebew', 'selesai', '2025-06-07 13:15:27', '2025-06-15 15:00:00', 'medium'),
(33, 7, 'Tugas Sistem Terintegrasi', 'Membuat tugas individu sistem terintegrasi slebew', 'selesai', '2025-06-07 13:17:28', '2025-06-14 01:30:00', 'medium'),
(38, 7, 'Tugas PBO', 'membuat tugas PBO', 'selesai', '2025-06-07 14:14:31', '2025-06-09 20:00:00', 'high'),
(39, 11, 'mtk', '', 'selesai', '2025-06-08 08:38:34', '2006-02-20 12:01:00', 'medium'),
(43, 13, 'Main valo', 'Main valo with ngorte ul', 'selesai', '2025-06-19 13:15:48', '2025-06-20 20:00:00', 'high'),
(51, 7, 'Main Valoran w UG234', 'Bermain Valorant', 'selesai', '2025-06-25 01:32:50', '2025-06-25 21:00:00', 'high'),
(52, 7, 'Menghadiri Kegiaitan', 'Menghadiri kegiatan seminar nasional', 'selesai', '2025-06-25 04:01:13', '2025-07-01 09:30:00', 'high');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `username`, `avatar`, `email`, `password`, `tanggal_daftar`) VALUES
(7, 'Agus pranata', 'agus', 'avatar_7_1750824491.jpg', 'agusprnta@gmail.com', '$2y$10$KWi2PrMIj3H.wqqq98JAoeP7wOAuuFoUzJU3PqGO4kqiBvQNHXG2S', '2025-05-28 16:54:26'),
(8, 'Sasi Kirana', 'Sasikirana', '0', 'sasikirana@gmail.com', '$2y$10$aO9uH6Vnj4bgpoZ7PgKznerC6LwSXO93bQ0sS7K6P6xxENJzv555i', '2025-05-29 17:10:37'),
(9, 'Sasi Kirana', 'sasi', '0', 'niluhpuspitasari@gmail.com', '$2y$10$q81DtUeQeixyRHxmdbWlq.btw.GenzhZb1KCUTQW0rq40nqD2G78C', '2025-06-03 11:36:21'),
(10, 'Adi Pranata', 'adiprnta', '0', '230040167@stikom-bali.ac.id', '$2y$10$aYUZZsIY3uAGvlFoG1CUqOF0C1uL7QA3sUX9RW5r5Hq8WemI6RT0m', '2025-06-07 14:04:13'),
(11, 'Ni Luh Eka Sasikirana', 'sasyi', '0', 'ekasasikirana01@gmail.com', '$2y$10$tFMwUOevWSKw61OyMxbuJeEIvAP4GrDEkg/fdWlwkTCmgfsNVWrTy', '2025-06-08 08:37:03'),
(12, 'Yonacaa', 'Yonacaa', '0', 'Yonaca22@gmail.com', '$2y$10$.QLgU2.R0eNT1SAH.BFOceLmKXJIPtEZdwN1.ZM1a.ZvK.nAVWDIO', '2025-06-09 01:27:40'),
(13, 'perdi', 'kakak', 'avatar_13_1750339380.jpg', 'mangperdi28@gmail.com', '$2y$10$5zPWiKxoZiSyb9KuBvlRLeyurvW21JsLIO2Ej.BTWQrzSNBHheJ0a', '2025-06-19 13:14:06'),
(14, 'Sutanto', 'Sutantoslebew', 'avatar_14_1750811769.jpg', 'sutanto@gmail.com', '$2y$10$TlN1sS5J9V0rMhbRQ5O8heoXrku/dKmwrU1WJxFrYliyhc9/ijZTy', '2025-06-25 00:33:43'),
(15, 'gusde', 'gusdeanjay', '', 'gusde@gmail.com', '$2y$10$jWYlndRm.btO/bZpveRGPebXFQLdHVvhRBabNYMDQgOsvwaAn0XoS', '2025-06-25 03:38:36'),
(16, 'Anggara', 'anggaraanjay', '', 'Anggara@gmail.com', '$2y$10$pKvqWmqa8CKbmzBorpNiqevbT2b3n8RI2cxo4XNHLgKn7KcTYiWTe', '2025-06-25 04:05:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tugas`
--
ALTER TABLE `tugas`
  ADD PRIMARY KEY (`id_tugas`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tugas`
--
ALTER TABLE `tugas`
  MODIFY `id_tugas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tugas`
--
ALTER TABLE `tugas`
  ADD CONSTRAINT `tugas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
