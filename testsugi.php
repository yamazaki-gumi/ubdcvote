<?php
session_start();

// セッション確認
if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['name'];
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
    WHERE flag=1 AND end_date < CURDATE()
    ORDER BY id DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>あなたの投票結果</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h2><?php ?> 投票結果</h2>

<table class="table table-bordered mt-3">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>タイトル</th>
            <th>開始日</th>
            <th>終了日</th>
            <th>状態</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo $row['start_date']; ?></td>
            <td><?php echo $row['end_date']; ?></td>

            <td>
                <?php
                    $now = date('Y-m-d');
                    echo (is_null($row['start_date']) || is_null($row['end_date']) || ($now >= $row['start_date'] && $now <= $row['end_date']))
                        ? "集計中"
                        : "締め切り";
                ?>
            </td>

            <td>
                <a href="testkekka.php?vote_id=<?php echo $row['id']?>" 
                    class="btn btn-success btn-sm">
                    結果を見る
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
