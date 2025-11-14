-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-11-14 01:27:29
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
-- テーブルの構造 `vote_count`
--

CREATE TABLE `vote_count` (
  `id` int(10) UNSIGNED NOT NULL,
  `vote_id` int(10) UNSIGNED NOT NULL,
  `sennta_id` int(10) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `vote_count`
--

INSERT INTO `vote_count` (`id`, `vote_id`, `sennta_id`, `account_id`, `created_at`) VALUES
(1, 22, 14, 2323, '2025-11-13 06:22:14');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `vote_count`
--
ALTER TABLE `vote_count`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote_account` (`vote_id`,`account_id`),
  ADD KEY `sennta_id` (`sennta_id`),
  ADD KEY `account_id` (`account_id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `vote_count`
--
ALTER TABLE `vote_count`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `vote_count`
--
ALTER TABLE `vote_count`
  ADD CONSTRAINT `vote_count_ibfk_1` FOREIGN KEY (`vote_id`) REFERENCES `votes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vote_count_ibfk_2` FOREIGN KEY (`sennta_id`) REFERENCES `sennta` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vote_count_ibfk_3` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_number`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
