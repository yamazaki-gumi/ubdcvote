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
   /* スクロールできる領域 */
    .scroll-box {
        max-height: 70vh;
        overflow-y: auto;
        padding-right: 10px;
    }
 
    /* カード装飾 */
    .vote-card {
        border: 1px solid #ccc;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
 
    /* ▼変更済み：フォント数字指定可能 */
    .vote-title {
        font-size: 2.5rem;
        font-weight: 900;
        position: relative;
        top: -12px;
        left: 5px;
    }
 
    .creator-text,
    .vote-period {
        font-size: 1.4rem;
        font-weight: 500; /* ←変更自由 */
        position: relative;
        top: 70px;
        left: 10px;
    }
 
    .status-open {
        background-color: #28a745;
        color: #fff;
    }
 
    .status-closed {
        background-color: #ff9999;
        color: #fff;
    }
 
    /* 作成者と期間の横幅余白 */
    .info-area {
        display: flex;
        gap: 30px; /* ←調整可能 */
        align-items: center;
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
 
    <!-- 作成者 + 期間 → 横並び変更済み -->
    <div class="info-area mt-2 text-muted">
        <div class="vote-period">投票期間：<?= htmlspecialchars($row['start_date']) ?> ～ <?= htmlspecialchars($row['end_date']) ?></div>
    </div>
 
    <!-- 操作用ボタン -->
    <div class="d-flex justify-content-end align-items-center mt-3">

    <form action="kekka.php" method="GET" style="display:inline;">
        <input type="hidden" name="vote_id" value="<?= $row['id']; ?>">
        <button type="submit" class="btn btn-success btn-lg">結果を見る</button>
    </form>
    </div>
</div>

<!-- ▲ 投票カード ▲ -->
 
<?php endwhile; ?>
 
</div> <!-- scroll-box -->
 
</body>
</html>
