<?php

session_start();

if (!isset($_SESSION['account_number'])) {

    header("Location: login.php");

    exit();

}

$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");

if ($conn->connect_error) {

    die("接続失敗: " . $conn->connect_error);

}

// votesテーブルから全件取得

$sql = "SELECT id, title, start_date, end_date FROM votes ORDER BY id DESC";

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
<th>ID</th>
<th>タイトル</th>
<th>開始日</th>
<th>終了日</th>
<th>操作</th>
</tr>
</thead>
<tbody>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
<td><?php echo htmlspecialchars($row['id']); ?></td>
<td><?php echo htmlspecialchars($row['title']); ?></td>
<td><?php echo htmlspecialchars($row['start_date']); ?></td>
<td><?php echo htmlspecialchars($row['end_date']); ?></td>
<td>
<!-- 投票ボタンにIDを渡す -->
<form action="testtouhyou.php" method="GET" style="display:inline;">
<input type="hidden" name="vote_id" value="<?php echo $row['id']; ?>">
<button type="submit" class="btn btn-primary btn-sm">投票する</button>
</form>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</body>
</html>

<?php $conn->close(); ?>

