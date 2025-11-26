<?php
session_start();
 
// セッション確認
if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}
 
$name = $_SESSION['name'];
$account_number = $_SESSION['account_number'];
 
// DB接続
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}
 
// 終了済みの votes を取得
$stmt = $conn->prepare("
    SELECT id, title, start_date, end_date, flag
    FROM votes
    WHERE flag = 1 AND end_date < CURDATE()
    ORDER BY id DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>
 
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>あなたの投票結果</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
 <link rel="stylesheet" href="sugi.css?v=<?php echo time(); ?>">
<style>
    /* スクロール可能な大枠 */
    .scroll-box {
        max-height: 70vh;
        overflow-y: auto;
        padding-right: 10px;
    }
 
    /* カードデザイン */
    .vote-card {
        border: 1px solid #ccc;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
 
    /* タイトル大きめ */
    .vote-title {
        font-size: 1.25rem;
        font-weight: bold;
    }
 
    .status-open {
        background-color: #28a745; /* 緑 */
        color: #fff;
    }
 
    .status-closed {
        background-color: #ff9999; /* 薄めの赤 */
        color: #fff;
    }
</style>
</head>
 
<body class="container mt-4">
<a href="main.php" class="btn btn-secondary return-btn">戻る</a>
<h2><?= htmlspecialchars($name) ?> さんの投票結果</h2>
 
<!-- ▼ スクロールできる大枠 ▼ -->
<div class="scroll-box">
 
<?php while ($row = $result->fetch_assoc()): ?>
 
<?php
    $vote_id = $row['id'];
    $now = date("Y-m-d");
 
    // 状態判定
    $status = ($now >= $row['start_date'] && $now <= $row['end_date'])
        ? "集計中"
        : "締め切り";
?>
 
<!-- ▼ 1つの投票カード ▼ -->
<div class="vote-card">
 
    <!-- 1段目：タイトル・状態 -->
    <div class="d-flex justify-content-between">
        <div class="vote-title"><?= htmlspecialchars($row['title']); ?></div>
        <div>
        <?php if ($status === "集計中"): ?>
            <span class="badge status-open"><?= $status ?></span>
        <?php else: ?>
            <span class="badge status-closed"><?= $status ?></span>
        <?php endif; ?>
        </div>
    </div>
 
    <!-- 2段目：期間 -->
    <div class="mt-2 text-muted">
        <?= htmlspecialchars($row['start_date']) ?> ～ <?= htmlspecialchars($row['end_date']) ?>
    </div>
 
    <!-- 3段目：作成者（表示は任意→ここは作成者不明のため非表示に） -->
    <div class="d-flex justify-content-between align-items-center mt-3">
 
        <div></div> <!-- 空：左右バランス用 -->
 
        <div>
            <a href="kekka.php?vote_id=<?= $row['id'] ?>"
               class="btn btn-success btn-lg">
                結果を見る
            </a>
        </div>
 
    </div>
</div>
<!-- ▲ 投票カード ▲ -->
 
<?php endwhile; ?>
 
</div> <!-- scroll-box -->
 
</body>
</html>
 