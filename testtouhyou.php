<?php
session_start(); // セッション開始

// DB接続情報
$servername = "localhost";
$username = "root";
$dbpassword = "";
$dbname = "toukounaiyou_db";

// MySQLに接続
$conn = new mysqli($servername, $username, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}
$vote_id = $_GET['vote_id'] ?? null;
if (!$vote_id) {
    die("不正なアクセスです。");
}

// 対応するタイトルと選択肢を取得
$stmt = $conn->prepare("SELECT title FROM votes WHERE id = ?");
$stmt->bind_param("i", $vote_id);
$stmt->execute();
$stmt->bind_result($title);
$stmt->fetch();
$stmt->close();

$result = $conn->query("SELECT id, senntaku, vote_count FROM sennta WHERE title_id = $vote_id");
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($title); ?> に投票</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h2><?php echo htmlspecialchars($title); ?> に投票</h2>

<form action="do_vote.php" method="POST">
<input type="hidden" name="vote_id" value="<?php echo $vote_id; ?>">

    <?php while ($row = $result->fetch_assoc()): ?>
<div class="form-check">
<input type="radio" name="senntaku_id" value="<?php echo $row['id']; ?>" class="form-check-input" required>
<label class="form-check-label">
<?php echo htmlspecialchars($row['senntaku']); ?>
</label>
</div>
<?php endwhile; ?>

    <button type="submit" class="btn btn-success mt-3">投票する</button>
</form>

</body>
</html>