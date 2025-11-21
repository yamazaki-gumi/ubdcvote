<?php
session_start();
 
// セッション確認
if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}
 
$account_number = $_SESSION['account_number'];
 
// DB接続
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}
 
// ログイン中アカウントが作成した votes を取得
$stmt = $conn->prepare("
    SELECT id, title, start_date, end_date, flag
    FROM votes
    WHERE account_id = ? AND flag=1
    ORDER BY id DESC
");
$stmt->bind_param("i", $account_number);
$stmt->execute();
$result = $stmt->get_result();
?>
 
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>あなたの投票状況</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="testzumi.css?v=<?php echo time(); ?>">
 
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
<a href="test_main.php" class="btn btn-secondary return-btn">戻る</a>
<h2><?= htmlspecialchars($_SESSION['name']); ?> さんの投票一覧</h2>
 
<!-- ▼ スクロールできる大枠 ▼ -->
<div class="scroll-box">
 
<?php while ($row = $result->fetch_assoc()): ?>
 
<?php
    $vote_id = $row['id'];
 
    // 投票済みチェック
    $check = $conn->prepare("SELECT 1 FROM vote_count WHERE vote_id = ? AND account_id = ?");
    $check->bind_param("ii", $vote_id, $account_number);
    $check->execute();
    $already_voted = $check->get_result()->num_rows > 0;
    $check->close();
 
    // 状態判定
    $now = date("Y-m-d");
    $status = ($now >= $row['start_date'] && $now <= $row['end_date']) ? "集計中" : "締め切り";
?>
 
<!-- ▼ 1つの投票カード ▼ -->
<div class="vote-card">
 
    <!-- 1段目：タイトル・状態 -->
    <div class="d-flex justify-content-between">
        <div class="vote-title"><?= htmlspecialchars($row['title']); ?></div>
        <?php if ($status === "集計中"): ?>
            <span class="badge status-open"><?= $status ?></span>
        <?php else: ?>
            <span class="badge status-closed"><?= $status ?></span>
        <?php endif; ?>
    </div>
 
    <!-- 2段目：期間 -->
    <div class="mt-2 text-muted">
        <?= htmlspecialchars($row['start_date']) ?> ～ <?= htmlspecialchars($row['end_date']) ?>
    </div>
 
    <!-- 3段目：作成者（自分）＋操作ボタン -->
    <div class="d-flex justify-content-between align-items-center mt-3">
 
        <div>作成者：<?= htmlspecialchars($_SESSION['name']); ?></div>
 
        <div>
            <?php if ($already_voted): ?>
                <form action="testkekka.php" method="GET" style="display:inline;">
                    <input type="hidden" name="vote_id" value="<?= $row['id']; ?>">
                    <button type="submit" class="btn btn-success btn-sm">結果を見る</button>
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