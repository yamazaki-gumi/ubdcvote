<?php
session_start();
 
if (!isset($_SESSION['account_number'])) {
    header("Location: testlogin.php");
    exit();
}
 
$account_number = $_SESSION['account_number'];
 
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}
 
$sql = "
SELECT
    v.id,
    v.title,
    v.start_date,
    v.end_date,
    a.name AS creator_name
FROM votes v
LEFT JOIN accounts a
    ON v.account_id = a.account_number
WHERE v.flag = 1 AND end_date >= CURDATE()
ORDER BY v.id DESC
";
$result = $conn->query($sql);
?>
 
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>投票一覧</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="testitiran.css?v=<?php echo time(); ?>">
<style>
    /* スクロール可能な大枠 */
    .scroll-box {
        max-height: 70vh;
        overflow-y: auto;
        padding-right: 10px;
    }
 
    /* カードのデザイン */
    .vote-card {
        border: 1px solid #ccc;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
 
    /* タイトルを大きく */
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
<a href="test_main.php" class="btn btn-secondary return-btn">戻る</a>
<h2>投票一覧</h2>
 
<!-- ▼スクロールできる大枠 ▼ -->
<div class="scroll-box">
 
<?php while ($row = $result->fetch_assoc()): ?>
<?php
    $vote_id = $row['id'];
 
    // 投票済みチェック
    $check = $conn->prepare("SELECT 1 FROM vote_count WHERE vote_id = ? AND account_id = ?");
    $check->bind_param("ii", $vote_id, $account_number);
    $check->execute();
    $already_voted = $check->get_result()->num_rows > 0;
 
    // 状態
    $now = date("Y-m-d");
    if ($now >= $row['start_date'] && $now <= $row['end_date']) {
        $status = "集計中";
    } else {
        $status = "締め切り";
    }
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
 
    <!-- 3段目：作成者と操作ボタン -->
    <div class="d-flex justify-content-between align-items-center mt-3">
 
        <div>作成者：<?= htmlspecialchars($row['creator_name']); ?></div>
 
        <div>
            <?php if ($already_voted): ?>
                <form action="testkekka.php" method="GET" style="display:inline;">
                    <input type="hidden" name="vote_id" value="<?= $row['id']; ?>">
                    <button type="submit" class="btn btn-success btn-lg">結果を見る</button>
                </form>
            <?php else: ?>
                <form action="testtouhyou.php" method="GET" style="display:inline;">
                    <input type="hidden" name="vote_id" value="<?= $row['id']; ?>">
                    <button type="submit" class="btn btn-primary btn-lg">投票する</button>
                </form>
            <?php endif; ?>
        </div>
 
    </div>
</div>
<!-- ▲ 投票カード ▲ -->
 
<?php endwhile; ?>
 
</div> <!-- scroll-box -->
 
</body>
</html>
 
<?php $conn->close(); ?>
 