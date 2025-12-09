<?php
session_start();

if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}

$account_number = $_SESSION['account_number'];

$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

$getClass = $conn->prepare("SELECT class_id FROM accounts WHERE account_number = ?");
$getClass->bind_param("s", $account_number);
$getClass->execute();
$getClass->bind_result($my_class_id);
$getClass->fetch();
$getClass->close();

$sql = "
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
    AND CURDATE() BETWEEN v.start_date AND v.end_date
    AND (v.class_id = ? OR v.class_id IS NULL)
ORDER BY v.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $my_class_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>投票一覧</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="itiran.css?v=<?= time(); ?>">

<style>
.scroll-box {
    max-height: 90vh;
    overflow-y: auto;
    padding-right: 10px;
}

.vote-card {
    border: 1px solid #ccc;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 15px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    position: relative; /* ← 右下配置の土台 */
}

.vote-title {
    font-size: 2.2rem;
    font-weight: 800;
    margin: 0;
    word-wrap: break-word;
}

.status-open { 
    background-color: #28a745; 
    color: #fff;
    position: relative;
    top: -20px; 
}
.status-closed { 
    background-color: #ff9999; 
    color: #fff; 
    position: relative;
    top: -20px; 
}

.touhyouzumi {
    background-color: #fc5353ff;
    border: none;
    padding: 9px 15px;
    font-size: 1.2em;
    border-radius: 8px;
    color: #fff;
}

/* 戻るボタン右上 */
.return-top-btn {
    position: fixed;
    top: 10px;
    right: 15px;
    z-index: 1000;
}

.info-area-bottom {
    display: block;
    margin-top: 10px;
    padding-top: 8px;
    /* border-top を削除 */
}

.creator-text,
.vote-period {
    font-size: 1.2rem;
    font-weight: 500;
    margin: 2px 0;
    word-wrap: break-word;
    overflow-wrap: break-word;
    white-space: normal;
    max-width: 100%;
}

/* 投票ボタンを右下に固定 */
.vote-btn-area {
    position: absolute;
    right: 15px;
    bottom: 15px;
}

@media (max-width: 576px) {
    .vote-title { font-size: 1.4rem; }
    .creator-text { font-size: 1rem; }
    .vote-period { font-size: 0.8rem; }
}
</style>
</head>

<body class="container mt-4">

<a href="main.php" class="btn btn-secondary return-top-btn">戻る</a>

<h2 class="mb-3">投票一覧</h2>

<div class="scroll-box">

<?php while ($row = $result->fetch_assoc()): ?>
<?php
    $vote_id = $row['id'];

    $check = $conn->prepare("SELECT 1 FROM vote_count WHERE vote_id = ? AND account_id = ?");
    $check->bind_param("ii", $vote_id, $account_number);
    $check->execute();
    $already_voted = $check->get_result()->num_rows > 0;

    $now = date("Y-m-d");
    $status = ($now >= $row['start_date'] && $now <= $row['end_date']) ? "集計中" : "締め切り";
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

    <!-- 投票ボタンを右下に固定 -->
    <div class="vote-btn-area">
        <?php if ($already_voted): ?>
            <span class="touhyouzumi">投票済み</span>
        <?php else: ?>
            <form action="touhyou.php" method="GET">
                <input type="hidden" name="vote_id" value="<?= $row['id']; ?>">
                <button type="submit" class="btn btn-primary btn-lg">投票する</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- 作成者・投票期間 -->
    <div class="info-area-bottom text-muted">
        <div class="creator-text">
            作成者：<?= htmlspecialchars($row['creator_name']); ?>
        </div>
        <div class="vote-period">
            投票期間：<?= htmlspecialchars($row['start_date']) ?> ～ <?= htmlspecialchars($row['end_date']) ?>
        </div>
    </div>

</div>
<?php endwhile; ?>

</div>
</body>
</html>

<?php $conn->close(); ?>
