<?php
session_start();
 
if (!isset($_SESSION['account_number'])) {
    header("Location: main.php");
    exit();
}
 
$name = $_SESSION['name'];
$account_number = $_SESSION['account_number'];
 
if (!isset($_GET['vote_id'])) {
    die("投票IDが指定されていません。");
}
$vote_id = (int)$_GET['vote_id'];
 
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}
 
// 投票タイトル取得（投稿者 or 終了済みなら閲覧可能）
$stmt = $conn->prepare("SELECT title FROM votes WHERE id = ? AND (account_id = ? OR end_date < CURDATE())");
$stmt->bind_param("ii", $vote_id, $account_number);
$stmt->execute();
$result = $stmt->get_result();
 
if ($result->num_rows === 0) {
    die("この投票は存在しないか、閲覧権限がありません。");
}
$vote = $result->fetch_assoc();
 
// 選択肢と得票数取得
$stmt2 = $conn->prepare("
    SELECT s.senntaku, COUNT(v.id) AS vote_count
    FROM sennta s
    LEFT JOIN vote_count v ON s.id = v.sennta_id
    WHERE s.title_id = ?
    GROUP BY s.id, s.senntaku
");
$stmt2->bind_param("i", $vote_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
?>
 
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>投票結果 - <?= htmlspecialchars($vote['title']); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
 
<style>
    .scroll-box {
        max-height: 70vh;
        overflow-y: auto;
        padding-right: 10px;
    }
 
    .vote-card {
        border: 1px solid #ccc;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 12px;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
 
    .vote-title {
        font-size: 1.35rem;
        font-weight: bold;
    }
 
    .result-row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px solid #eee;
    }
</style>
</head>
 
<body class="container mt-4">
 
<h2 class="mb-3">投票結果：<?= htmlspecialchars($vote['title']); ?></h2>
<p>ようこそ <?= htmlspecialchars($name); ?> さん</p>
 
<!-- スクロール可能領域 -->
<div class="scroll-box">
 
<?php while ($row = $result2->fetch_assoc()): ?>
<div class="vote-card">
 
    <div class="result-row">
        <span class="vote-title"><?= htmlspecialchars($row['senntaku']); ?></span>
        <span class="badge bg-primary fs-5"><?= $row['vote_count']; ?> 票</span>
    </div>
 
</div>
<?php endwhile; ?>
 
</div>
 
<!-- 戻るボタン -->
<a href="itiran.php" class="btn btn-secondary mt-3">← 一覧に戻る</a>
 
</body>
</html>
 
 
