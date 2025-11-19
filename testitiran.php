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

// votes テーブル取得
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
</head>
<body class="container mt-4">

<h2>投票一覧</h2>

<table class="table table-bordered">
<thead>
<tr>
<th>タイトル</th>
<th>開始日</th>
<th>終了日</th>
<th>作成者</th>
<th>状態</th>
<th>操作</th>
</tr>
</thead>
<tbody>

<?php while ($row = $result->fetch_assoc()): ?>
<?php
    $vote_id = $row['id'];

    //投票済みチェック (vote_count を参照）
    $check = $conn->prepare("SELECT 1 FROM vote_count WHERE vote_id = ? AND account_id = ?");
    $check->bind_param("ii", $vote_id, $account_number);
    $check->execute();
    $already_voted = $check->get_result()->num_rows > 0;

    //状態の判定＿不要な部分ではある
    $now = date("Y-m-d");
    if ($now >= $row['start_date'] && $now <= $row['end_date']) {
        $status = "集計中";
    } else {
        $status = "締め切り";
    }
?>
<tr>
    <td><?= htmlspecialchars($row['title']); ?></td>
    <td><?= htmlspecialchars($row['start_date']); ?></td>
    <td><?= htmlspecialchars($row['end_date']); ?></td>
    <td><?= htmlspecialchars($row['creator_name']); ?></td>

    <td><?= $status ?></td>

    <td>
        <?php if ($already_voted): ?>
            <!-- 投票済みなら結果ページへ -->
            <form action="testkekka.php" method="GET" style="display:inline;">
                <input type="hidden" name="vote_id" value="<?= $row['id']; ?>">
                <button type="submit" class="btn btn-success btn-sm">結果を見る</button>
            </form>
        <?php else: ?>
            <!-- 未投票なら投票ページへ -->
            <form action="testtouhyou.php" method="GET" style="display:inline;">
                <input type="hidden" name="vote_id" value="<?= $row['id']; ?>">
                <button type="submit" class="btn btn-primary btn-sm">投票する</button>
            </form>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

</body>
</html>

<?php $conn->close(); ?>
