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

$stmt = $conn->prepare("
    SELECT
        v.id,
        v.title,
        v.start_date,
        v.end_date,
        a.name AS creator_name
    FROM votes v
    LEFT JOIN accounts a
        ON v.account_id = a.account_number
    WHERE v.account_id = ? AND v.flag = 1
    ORDER BY v.id DESC
");
$stmt->bind_param("i", $account_number);
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

<style>
.scroll-box {
    max-height: 70vh;
    overflow-y: auto;
    padding-right: 10px;
}

.vote-card {
    border: 1px solid #ccc;
    padding: 15px 15px 70px 15px;
    border-radius: 12px;
    margin-bottom: 15px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    background: #fff;
    position: relative;
    min-height: 180px;
}

.vote-title {
    font-size: 2.2rem;
    font-weight: 800;
    word-break: break-word;
}

.status-open { background:#28a745; color:#fff; }
.status-closed { background:#ff9999; color:#fff; }

.return-top-btn {
    position: fixed;
    top: 10px;
    right: 15px;
    z-index: 1000;
}

/* ===== 左下（作成者・期間） ===== */
.info-area {
    position: absolute;
    left: 15px;
    bottom: 15px;
    text-align: left;
    max-width: calc(100% - 230px); /* 右ボタン分を避ける */
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
/* ===== 右下（ボタン） ===== */
.btn-area {
    position: absolute;
    right: 15px;
    bottom: 15px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.resultbtn,
.deletebtn {
    width: 160px;
}

/* ===== スマホ ===== */
@media (max-width: 576px) {

    .vote-title { font-size: 1.2rem; }

    .creator-text,
    .vote-period {
        font-size: 0.9rem;
    }

    .btn-area {
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
    }

    .resultbtn,
    .deletebtn {
        width: 100px;
        font-size: 0.75rem;
    }
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
    if ($now >= $row['start_date'] && $now <= $row['end_date']) {
        $status = "集計中";
    } else if ($now >= $row['start_date'] && $now >= $row['end_date']) {
        $status = "締め切り";
    } else {
        $status = "開始前";
    }
?>

<div class="vote-card">

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

    <!-- 左下：作成者・期間 -->
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
        <?php if ($already_voted): ?>
            <form action="kekka2.php" method="GET">
                <input type="hidden" name="vote_id" value="<?= $row['id']; ?>">
                <button type="submit" class="btn btn-success resultbtn">結果を見る</button>
            </form>
        <?php else: ?>
            <form action="touhyou2.php" method="GET">
                <input type="hidden" name="vote_id" value="<?= $row['id']; ?>">
                <button type="submit" class="btn btn-primary resultbtn">投票する</button>
            </form>
        <?php endif; ?>

        <form action="touhyoudeletekakunin.php" method="GET">
            <input type="hidden" name="vote_id" value="<?= $row['id']; ?>">
            <button type="submit" class="btn btn-danger deletebtn">削除する</button>
        </form>
    </div>

</div>
<?php endwhile; ?>

</div>
</body>
</html>

<?php $conn->close(); ?>
