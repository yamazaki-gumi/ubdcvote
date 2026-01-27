-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2026-01-27 03:17:20
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
  `secret` varchar(30) NOT NULL,
  `failed_count` int(11) NOT NULL DEFAULT 0,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `locked_at` datetime DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `accounts`
--

INSERT INTO `accounts` (`id`, `account_number`, `name`, `class_id`, `secret_situmon`, `secret`, `failed_count`, `is_locked`, `locked_at`, `password`) VALUES
(73, 1234, 'かみお', 'US4A', '好きな食べもの', 'ラーメン', 0, 0, NULL, '$2y$10$mYs4AYnjhoeBxYfDHVJPpOe.h3..rXdFt.LT9.ScZRdAkvXLQHbVy'),
(74, 1111, 'ゲスト１', 'US4A', '好きな食べもの', 'ラーメン', 0, 0, NULL, '$2y$10$bhR6Sa2S1Z3yWvv0qPsw6e9tv1S.fBG2dGeEw4.NPTE3qEtTXJvx6'),
(77, 4321, 'はが', 'US4A', '好きな食べもの', 'ラーメン', 0, 0, NULL, '$2y$10$mM2j7HDGibCms3/rMcBTdetvoCjQZrUhbv1X7WB27PRzgcH1zhFoq'),
(78, 2222, 'ゲスト２', 'US4A', '好きな食べもの', 'ラーメン', 0, 0, NULL, '$2y$10$cgJRUMtY26xtrbfmDp0tq.DzpQGC/o19ismmHDXUuzbt1qliXMZru');

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
(160, '大阪', 146, 0),
(161, '京都', 146, 1),
(162, '受ける', 147, 2),
(163, '受けたくない', 147, 0),
(164, 'お米', 148, 1),
(165, 'パン', 148, 2),
(166, 'イチゴ', 149, 2),
(167, '餃子', 149, 0),
(168, '日光東照宮', 149, 0);

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
  `flag` tinyint(1) NOT NULL DEFAULT 0,
  `class_id` char(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `votes`
--

INSERT INTO `votes` (`id`, `title`, `start_date`, `end_date`, `account_id`, `flag`, `class_id`) VALUES
(146, '大阪に行くか京都にいくか', '2026-01-27', '2026-01-28', 4321, 1, 'US4A'),
(147, '応用情報の模擬試験受けるか', '2026-01-27', '2026-01-28', 4321, 1, 'US4A'),
(148, '朝ごはんはどっち派？', '2026-01-27', '2026-01-28', 1234, 1, NULL),
(149, '栃木といえば？', '2026-01-27', '2026-01-28', 1234, 1, NULL);

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
(81, 147, 162, 1234, '2026-01-27 00:45:36'),
(82, 146, 161, 1234, '2026-01-27 00:45:49'),
(83, 149, 166, 1234, '2026-01-27 00:45:59'),
(84, 149, 166, 4321, '2026-01-27 00:48:36'),
(85, 147, 162, 4321, '2026-01-27 00:48:45'),
(86, 148, 165, 1111, '2026-01-27 00:54:56'),
(87, 148, 164, 2222, '2026-01-27 00:55:00'),
(88, 148, 165, 1234, '2026-01-27 00:55:01');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_account_number` (`account_number`),
  ADD KEY `uniq_class_id` (`class_id`);

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
  ADD KEY `fk_votes_account` (`account_id`),
  ADD KEY `fk_class_id` (`class_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- テーブルの AUTO_INCREMENT `sennta`
--
ALTER TABLE `sennta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- テーブルの AUTO_INCREMENT `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- テーブルの AUTO_INCREMENT `vote_count`
--
ALTER TABLE `vote_count`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

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
