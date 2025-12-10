-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-12-10 02:46:56
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
(51, 6666, '悪魔', 'US10', '昨日たべたもの', '寿司', '$2y$10$gvlkV3oljzFzrH9qDO8ym.ygyP2/pRHPCKEsl3OCfrVan2pekV//G'),
(53, 9999, 'shibal', 'US10', '韓国のキムチは？', 'キムチ', '$2y$10$sixGUMYME7JBHu5AyLeHn.4pCwJ6zn91i3eGNdZSkMV08bHvLKTTm'),
(55, 402, 'あにし', 'US4A', '今日の朝ごはんは？', 'パン', '$2y$10$PN2KdPe3bKb2dkzEwqZLweh8H0MXc0gUy93/mrFvAOqOseeW9cCRC'),
(57, 910, 'charlie kirk', 'US4A', 'when did kirk die in', '0910', '$2y$10$Go/c.Bi7rKwZ3p2pAEejs.qjcfJ3eyN7DCujx4VaPolxBRMMB7GVC'),
(58, 4321, 'にと', 'US4A', '笠原ケイトとは何人', 'ウイグル人', '$2y$10$UvDYYEuIapDxE80pGhSeTe16j9A/8IMd7GdJFYXX85vYp3um4PmpW'),
(60, 6789, 'にところ', 'US3A', 'ところは何人', 'ウイグル人', '$2y$10$q1eC6BWZrIgNMsmfx0Y0Zu2VtqTamgVv5gCJOqmhdd/lmiEl1bLIO'),
(61, 9876, 'ところに', 'US3A', 'ところは', 'ウイグル人', '$2y$10$1EYBM9ElQU61rb5s072Wn.xo3xn/G7FIP2Sc9uIJRomdfBC2eMDtG');

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
(105, 'GAY', 107, 1),
(106, 'GAY', 107, 0),
(107, '中国人', 108, 2),
(108, '中国人', 108, 0),
(109, '北朝鮮人', 108, 0),
(110, '岡田', 109, 0),
(111, '間井田', 109, 2),
(112, 'かふぁ', 110, 0),
(113, 'ふぁえｇふぁ', 110, 0),
(114, 'YES', 112, 2),
(115, 'NO', 112, 0),
(116, '眠い', 113, 0),
(117, '眠くない', 113, 0),
(118, 'ウイグル人', 114, 0),
(119, '中国人', 114, 0),
(120, 'ねむ', 115, 0),
(121, 'ねむない', 115, 0),
(122, 'する', 117, 0),
(123, 'しない', 117, 0),
(135, 'ｆｈ', 127, 0),
(136, 'ストリーマー', 128, 0),
(137, 'ラッパー', 128, 0),
(138, 'N', 129, 0),
(139, 'C', 129, 0),
(140, 'ｂぎぎうぐう', 130, 0),
(142, '知らねーよ', 132, 0),
(143, '馬鹿野郎', 134, 0),
(144, '大馬鹿野郎', 134, 0);

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
(66, '天使になる', '2026-01-21', '2026-02-26', 6666, 1, ''),
(107, '間井田はMENかGAYか', '2025-12-02', '2025-12-03', 4321, 1, ''),
(108, '戸頃は中国人か北朝鮮人か', '2025-12-02', '2025-12-03', 4321, 1, ''),
(109, '岡田か間井田か', '2025-12-02', '2025-12-03', 4321, 1, ''),
(110, 'あああｆかおｆｋ', '2025-12-02', '2025-12-01', 4321, 1, ''),
(111, 'ｈそｈこｐｈｋｓｐ', '2025-12-02', '2025-12-01', 4321, 0, ''),
(112, 'title.phpは簡潔にすべきか', '2025-12-03', '2025-12-04', 4321, 1, ''),
(113, '眠いか眠くないか', '2025-12-08', '2025-12-09', 4321, 1, 'US4A'),
(114, '二戸頃は何人', '2025-12-07', '2025-12-09', 9876, 1, 'US3A'),
(115, '眠いか眠くないか', '2025-12-08', '2025-12-09', 4321, 1, 'US4A'),
(116, 'ｃ', '2025-12-08', '2025-12-09', 6789, 1, 'US3A'),
(117, 'ヘアセットするか、しないか', '2025-12-09', '2025-12-10', 4321, 1, ''),
(118, '片方だけがやるか、同時進行か', '2025-12-09', '2025-12-10', 4321, 0, ''),
(127, 'ｆかｈ', '2025-12-09', '2025-12-10', 4321, 1, NULL),
(128, 'ｄｄｇはストリーマーか', '2025-12-09', '2025-12-10', 4321, 1, NULL),
(129, 'NかCか', '2025-12-09', '2025-12-10', 6789, 1, ''),
(130, 'んヴぃヴぃう', '2025-12-10', '2025-12-11', 6789, 1, 'US4A'),
(132, 'できたぞ馬鹿野郎', '2025-12-09', '2025-12-10', 6789, 1, 'US3A'),
(134, '馬鹿か神尾は', '2025-12-07', '2025-12-08', 4321, 1, 'US4A');

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
(68, 107, 105, 4321, '2025-12-02 05:06:14'),
(69, 108, 107, 4321, '2025-12-02 06:08:03'),
(70, 109, 111, 4321, '2025-12-02 06:08:12'),
(71, 112, 114, 4321, '2025-12-03 00:43:59'),
(72, 112, 114, 910, '2025-12-03 00:50:15'),
(73, 109, 111, 910, '2025-12-03 04:35:46'),
(74, 108, 107, 910, '2025-12-03 04:36:08');

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- テーブルの AUTO_INCREMENT `sennta`
--
ALTER TABLE `sennta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- テーブルの AUTO_INCREMENT `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- テーブルの AUTO_INCREMENT `vote_count`
--
ALTER TABLE `vote_count`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

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
