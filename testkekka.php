<?php
session_start();

if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
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

// 投票タイトルを取得
$stmt = $conn->prepare("SELECT title FROM votes WHERE id = ? AND account_id = ?");
$stmt->bind_param("ii", $vote_id, $account_number);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("この投票は存在しないか、閲覧権限がありません。");
}
$vote = $result->fetch_assoc();

// 選択肢と得票数を取得
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
<title>投票結果 - <?php echo htmlspecialchars($vote['title']); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h2>投票結果：<?php echo htmlspecialchars($vote['title']); ?></h2>
<h4>ようこそ <?php echo htmlspecialchars($name); ?> さん</h4>

<table class="table table-bordered mt-3">
    <thead class="table-light">
        <tr>
            <th>選択肢</th>
            <th>得票数</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result2->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['senntaku']); ?></td>
            <td><?php echo $row['vote_count']; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<a href="testitiran.php" class="btn btn-secondary mt-3">投票一覧に戻る</a>

</body>
</html>
