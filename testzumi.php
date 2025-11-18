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

// ループ前に初期化（Warning 対策）
$already_voted = false;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>あなたの投票状況</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h2><?= htmlspecialchars($_SESSION['name']); ?> さんの投票一覧</h2>

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

            <?php
            $vote_id = $row['id'];

            // 投票済みチェック (vote_count を参照)
            $check = $conn->prepare("SELECT 1 FROM vote_count WHERE vote_id = ? AND account_id = ?");
            $check->bind_param("ii", $vote_id, $account_number);
            $check->execute();
            $already_voted = $check->get_result()->num_rows > 0;
            $check->close();
            ?>

            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['title']); ?></td>
                <td><?= $row['start_date']; ?></td>
                <td><?= $row['end_date']; ?></td>
                <td>
                    <?php
                    $now = date('Y-m-d');
                    echo (is_null($row['start_date']) || is_null($row['end_date']) || ($now >= $row['start_date'] && $now <= $row['end_date']))
                        ? "集計中"
                        : "締め切り";
                    ?>
                </td>
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
