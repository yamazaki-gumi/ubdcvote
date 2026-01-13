<?php
session_start();
 
if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}
 
$name           = $_SESSION['name'];
$account_number = $_SESSION['account_number'];
 
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

/* ログインユーザーの class_id を取得 */
$getClass = $conn->prepare("SELECT class_id FROM accounts WHERE account_number = ?");
$getClass->bind_param("s", $account_number);
$getClass->execute();
$getClass->bind_result($my_class_id);
$getClass->fetch();
$getClass->close();

/* 終了済み投票を取得（class_id 制限あり） */
$stmt = $conn->prepare("
    SELECT
        v.id,
        v.title,
        v.start_date,
        v.end_date,
        v.class_id,
        a.name AS creator_name
    FROM votes v
    LEFT JOIN accounts a
        ON v.account_id = a.account_number
    WHERE 
        v.flag = 1
        AND v.end_date < CURDATE()
        AND (v.class_id = ? OR v.class_id IS NULL)
    ORDER BY v.id DESC
");
$stmt->bind_param("s", $my_class_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>投票結果</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: url("img/gamen1.jpg") no-repeat center center fixed;
    background-size: cover;
}

/* スクロール領域 */
.scroll-box {
    max-height: 90vh;
    overflow-y: auto;
    padding-right: 10px;
}

/* カード（白枠） */
.vote-card {
    border: 1px solid #ccc;
    padding: 15px;   /* 下部エリア確保 */
    border-radius: 12px;
    margin-bottom: 15px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    position: relative;             /* ← absolute の基準 */
    min-height: 150px;
}
.return-top-btn {
    position: fixed;
    top: 10px;
    right: 15px;
    z-index: 1000;
}

/* タイトル */
.vote-title {
    font-size: 2.2rem;
    font-weight: 800;
    margin: 0;
}

/* 左下（作成者＋期間） */
.info-area {
    position: absolute;
    left: 15px;
    bottom: 15px;
    text-align: left;
    max-width: calc(100% - 200px);  /* ボタンと被らないように */
}

.creator-text,
.vote-period {
    font-size: 1.2rem;
    font-weight: 500;
    display: block;
    white-space: nowrap;
}

/* 右下（ボタン） */
.btn-area {
    position: absolute;
    right: 15px;
    bottom: 15px;
}

.status-open  { 
    background-color: #28a745; 
    color: #fff; 
    position: relative;
    top: -20px; 
}
.status-closed{ 
    background-color: #ff9999; 
    color: #fff; 
    position: relative;
    top: -20px; 
}

/* スマホ */
@media (max-width: 576px) {
    .vote-title { font-size: 1.4rem; }
    .creator-text { font-size: 1rem; }
    .vote-period { font-size: 0.8rem; }
}
</style>
</head>
 
<body class="container mt-4">

<a href="main.php" class="btn btn-secondary return-top-btn">戻る</a>

<h2>投票結果</h2>
 
<div class="scroll-box">
 
<?php while ($row = $result->fetch_assoc()): ?>
<?php
    $now = date("Y-m-d");
    $status = ($now >= $row['start_date'] && $now <= $row['end_date'])
        ? "集計中"
        : "締め切り";
?>

<div class="vote-card">
 
    <!-- タイトル＋ステータス -->
    <div class="d-flex justify-content-between align-items-center">
        <div class="vote-title"><?= htmlspecialchars($row['title']); ?></div>
        <div>
            <?php if ($status === "集計中"): ?>
                <span class="badge status-open"><?= $status ?></span>
            <?php else: ?>
                <span class="badge status-closed"><?= $status ?></span>
            <?php endif; ?>
        </div>
    </div>
 
    <!-- 左下：作成者＋期間 -->
    <div class="info-area text-muted">
        <div class="creator-text">
            作成者：<?= htmlspecialchars($row['creator_name']); ?>
        </div>
        <div class="vote-period">
            投票期間：<?= htmlspecialchars($row['start_date']) ?> ～ <?= htmlspecialchars($row['end_date']) ?>
        </div>
    </div>

    <!-- 右下：ボタン -->
    <div class="btn-area">
        <form action="kekka3.php" method="GET">
            <input type="hidden" name="vote_id" value="<?= $row['id']; ?>">
            <button type="submit" class="btn btn-success btn-lg">結果</button>
        </form>
    </div>

</div>

<?php endwhile; ?>
 
</div>
</body>
</html>
