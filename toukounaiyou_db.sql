-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-11-11 02:24:19
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `toukounaiyou_db`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `title`
--

CREATE TABLE `title` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `title`
--

INSERT INTO `title` (`id`, `title`, `start_date`, `end_date`) VALUES
(1, 'ああああ', '2025-11-06', '2025-11-12'),
(2, 'ああああ', '2025-11-07', '2025-11-21'),
(3, 'えええ', '2025-10-28', '2025-11-12'),
(4, 'ふぁふぁ', '2025-10-29', '2025-11-27'),
(5, 'ふぁふぁ', '2025-10-29', '2025-11-27'),
(6, 'あおがく', '2025-11-08', '2025-11-14'),
(7, 'あおがく', '2025-11-08', '2025-11-14'),
(8, 'あおがく', '2025-11-08', '2025-11-14'),
(9, 'あおがく', '2025-11-08', '2025-11-14'),
(10, 'ああああああ', '2025-11-05', '2025-11-21'),
(11, 'ああああああ', '2025-11-05', '2025-11-21'),
(12, 'ああああああ', '2025-11-05', '2025-11-21'),
(13, 'えにしかえにおか', '2025-11-11', '2025-11-12'),
(14, 'はねしいた', '2025-11-06', '2025-11-26');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `title`
--
ALTER TABLE `title`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `title`
--
ALTER TABLE `title`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
