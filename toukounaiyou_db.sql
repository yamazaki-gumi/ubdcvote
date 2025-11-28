-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-11-28 01:36:40
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
-- テーブルの構造 `accounts`
--

CREATE TABLE `accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `account_number` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `class_id` char(4) NOT NULL,
  `secret_situmon` varchar(30) NOT NULL,
  `secret` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `accounts`
--

INSERT INTO `accounts` (`id`, `account_number`, `name`, `class_id`, `secret_situmon`, `secret`, `password`) VALUES
(50, 5656, 'ルヒー', 'US10', '海で一番自由なやつは？', '海賊王', '$2y$10$BvhU5CucykUDvb/FB4VQWesVvkq5dQdP/50BgX0lJBoUFu7MHV8JG'),
(51, 6666, '悪魔', 'US10', '昨日たべたもの', '寿司', '$2y$10$9IEmLvLs9rLqnSHCncSMZuSuS2RgPUGQ2RRE4s1tpfbbytKtiH4rK'),
(53, 9999, 'shibal', 'US10', '韓国のキムチは？', 'キムチ', '$2y$10$zwh42sqC9CdJqlG3rX6Ry.p/CE319crr7VfJ3SL19gj9VYWaOs3te'),
(54, 1111, 'かみお', 'US10', '', '', '$2y$10$E7OgDQj2fJ0VjV/lA6yXG.2rLEntTvzs7R2ADY/6q3LoYvWTRo5Ee'),
(55, 402, 'あにし', 'US4A', '今日の朝ごはんは？', 'パン', '$2y$10$PN2KdPe3bKb2dkzEwqZLweh8H0MXc0gUy93/mrFvAOqOseeW9cCRC');

--
-- トリガ `accounts`
--
DELIMITER $$
CREATE TRIGGER `trg_set_account_number` BEFORE INSERT ON `accounts` FOR EACH ROW BEGIN
  IF NEW.account_number IS NULL OR NEW.account_number = '' THEN
    SET NEW.account_number = LPAD(FLOOR(RAND() * 10000), 4, '0');
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- テーブルの構造 `sennta`
--

CREATE TABLE `sennta` (
  `id` int(10) UNSIGNED NOT NULL,
  `senntaku` varchar(100) NOT NULL,
  `title_id` int(10) UNSIGNED NOT NULL,
  `vote_count` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `sennta`
--

INSERT INTO `sennta` (`id`, `senntaku`, `title_id`, `vote_count`) VALUES
(90, 'だめ', 66, 0),
(91, 'だめじゃない', 66, 0),
(92, 'ああ', 67, 0),
(93, 'あ', 67, 1),
(96, 'りんご', 74, 0),
(97, 'え', 76, 0),
(98, 'なたかた', 100, 0),
(99, '羽下', 104, 0),
(100, 'ガンジー', 104, 1);

-- --------------------------------------------------------

--
-- テーブルの構造 `votes`
--

CREATE TABLE `votes` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `account_id` int(4) UNSIGNED NOT NULL,
  `flag` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `votes`
--

INSERT INTO `votes` (`id`, `title`, `start_date`, `end_date`, `account_id`, `flag`) VALUES
(66, '天使になる', '2026-01-21', '2026-02-26', 6666, 1),
(67, 'あああああ', '2025-11-25', '2025-11-28', 6666, 1),
(69, 'あああ', '2025-11-27', '2025-11-29', 1111, 0),
(70, 'やったー', '2025-11-27', '2025-11-28', 1111, 0),
(71, 'あｄｆ', '2025-11-27', '2025-11-28', 1111, 0),
(72, 'なたなたな', '2025-11-27', '2025-11-28', 1111, 0),
(73, '343648', '2025-11-27', '2025-11-28', 1111, 0),
(74, 'ふぁあｆ', '2025-11-27', '2025-11-28', 1111, 0),
(75, '343648', '2025-11-27', '2025-11-28', 1111, 0),
(76, 'ふぁあｆ', '2025-11-27', '2025-11-28', 1111, 0),
(77, 'ふぁあｆ', '2025-11-27', '2025-11-28', 1111, 0),
(78, 'ふぁあｆ', '2025-11-27', '2025-11-28', 1111, 0),
(79, '343648', '2025-11-27', '2025-11-28', 1111, 0),
(80, 'したはらな', '2025-11-27', '2025-11-28', 1111, 0),
(81, 'あがｇ', '2025-11-27', '2025-11-28', 1111, 0),
(82, 'なたなはな', '2025-11-27', '2025-11-28', 1111, 0),
(83, 'はたはたはた', '2025-11-27', '2025-11-28', 1111, 0),
(84, 'あがｇ', '2025-11-27', '2025-11-28', 1111, 0),
(85, 'はたはたはた', '2025-11-27', '2025-11-28', 1111, 0),
(86, 'さたさはた', '2025-11-27', '2025-11-28', 1111, 0),
(87, 'はなたらた', '2025-11-27', '2025-11-28', 1111, 0),
(88, 'なあなたな', '2025-11-27', '2025-11-28', 1111, 0),
(89, 'ふな', '2025-11-27', '2025-11-27', 1111, 0),
(90, 'ふぁふぁ', '2025-11-27', '2025-11-28', 1111, 0),
(91, 'はまひた', '2025-11-27', '2025-11-28', 1111, 0),
(92, 'ふぁふぁ', '2025-11-27', '2025-11-28', 1111, 0),
(93, 'はまひま', '2025-11-27', '2025-11-28', 1111, 0),
(94, 'ふぁふぁ', '2025-11-27', '2025-11-28', 1111, 0),
(95, 'ふぁふぁ', '2025-11-27', '2025-11-28', 1111, 0),
(96, 'はたはらた', '2025-11-27', '2025-11-28', 1111, 0),
(97, 'ふぁふぁ', '2025-11-27', '2025-11-28', 1111, 0),
(98, 'ふぁふぁ', '2025-11-27', '2025-11-28', 1111, 0),
(99, 'ふぁふぁ', '2025-11-27', '2025-11-28', 1111, 0),
(100, 'ふまひた', '2025-11-27', '2025-11-28', 1111, 0),
(101, 'ゆ', '2025-11-27', '2025-11-28', 1111, 0),
(102, 'に', '2025-11-27', '2025-11-28', 1111, 0),
(103, 'ｇｓｇｇｓ', '2025-11-27', '2025-11-28', 1111, 0),
(104, '羽下かガンジーか', '2025-11-27', '2025-11-28', 1111, 1);

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
(66, 67, 93, 1111, '2025-11-27 02:52:31'),
(67, 104, 100, 1111, '2025-11-27 06:30:27');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_account_number` (`account_number`);

--
-- テーブルのインデックス `sennta`
--
ALTER TABLE `sennta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_title` (`title_id`);

--
-- テーブルのインデックス `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_votes_account` (`account_id`);

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
-- テーブルの AUTO_INCREMENT `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- テーブルの AUTO_INCREMENT `sennta`
--
ALTER TABLE `sennta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- テーブルの AUTO_INCREMENT `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- テーブルの AUTO_INCREMENT `vote_count`
--
ALTER TABLE `vote_count`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `sennta`
--
ALTER TABLE `sennta`
  ADD CONSTRAINT `fk_title` FOREIGN KEY (`title_id`) REFERENCES `votes` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `fk_votes_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_number`) ON DELETE CASCADE;

--
-- テーブルの制約 `vote_count`
--
ALTER TABLE `vote_count`
  ADD CONSTRAINT `fk_vote_count_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_number`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
