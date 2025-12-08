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
WHERE v.flag = 1 AND CURDATE() BETWEEN start_date AND end_date
ORDER BY v.id DESC
";
$result = $conn->query($sql);
?>
 
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>投票一覧</title>
 
<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
 
<!-- CSSファイル -->
<link rel="stylesheet" href="itiran.css?v=<?php echo time(); ?>">
 
<style>
    /* スクロールできる領域 */
    .scroll-box {
        max-height: 90vh;
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
    .sp-only {
    display: none;
    }
    .pc-only {
    display: inline;
    } 
    .touhyouzumi {
    background-color: #fc5353ff;
    border: none;
    padding: 9px 15px;
    font-size: 1.3em;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
    display: block;
    margin: 0 auto;
    color: #fff;
}
    @media (max-width: 576px) {
    .vote-title {
    font-size: 1rem;
    font-weight: 400; /* ← 数値指定OK（100～900）*/
    }
    .creator-text{
    font-size: 0.9rem;
    font-weight: 400; /* ← お好きな数字に変更可能 */
    }  
    .vote-period {
    font-size: 0.7rem;
    font-weight: 400; /* ← お好きな数字に変更可能 */
    } 
    .sp-only {
    display: inline;
    } 
    .pc-only {
    display: none;
    } 
    }
</style>
</head>
 
<body class="container mt-4">
 
<a href="main.php" class="btn btn-secondary return-btn">戻る</a>
<h2>投票一覧</h2>
 
<!-- スクロールコンテナ -->
<div class="scroll-box">
 
<?php while ($row = $result->fetch_assoc()): ?>
<?php
    $vote_id = $row['id'];
 
    // 投票済み確認
    $check = $conn->prepare("SELECT 1 FROM vote_count WHERE vote_id = ? AND account_id = ?");
    $check->bind_param("ii", $vote_id, $account_number);
    $check->execute();
    $already_voted = $check->get_result()->num_rows > 0;
 
    // 状態表示
    $now = date("Y-m-d");
    if ($now >= $row['start_date'] && $now <= $row['end_date']) {
        $status = "集計中";
    } else {
        $status = "締め切り";
    }
?>
 
<!-- 1つの投票カード -->
<div class="vote-card">
 
    <!-- タイトルとステータス -->
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
    <span class="pc-only">
    <div class="info-area mt-2 text-muted">
        <div class="creator-text">作成者：<?= htmlspecialchars($row['creator_name']); ?></div>
        <div class="vote-period">投票期間：<?= htmlspecialchars($row['start_date']) ?> ～ <?= htmlspecialchars($row['end_date']) ?></div>   
    </div>
    </span>
    <span class="sp-only"> 
    <div class="info-area mt-2 text-muted">
        <div class="creator-text">作成者：<?= htmlspecialchars($row['creator_name']); ?></div>
    </div>
    <div class="info-area mt-2 text-muted">
        <div class="vote-period">投票期間：<?= htmlspecialchars($row['start_date']) ?> ～ <?= htmlspecialchars($row['end_date']) ?></div>   
    </div>
    </span>
    <!-- 操作用ボタン -->
    <div class="d-flex justify-content-end align-items-center mt-3">
 
        <?php if ($already_voted): ?>
            <form>
                <label class="touhyouzumi">投票済み</label>
            </form>
        <?php else: ?>
            <form action="touhyou.php" method="GET" style="display:inline;">
                <input type="hidden" name="vote_id" value="<?= $row['id']; ?>">
                <button type="submit" class="btn btn-primary btn-lg">投票する</button>
            </form>
        <?php endif; ?>
 
    </div>
 
</div>
<!-- カード終了 -->
 
<?php endwhile; ?>
 
</div><!-- scroll box -->
</body>
</html>
 
<?php $conn->close(); ?>
 
 