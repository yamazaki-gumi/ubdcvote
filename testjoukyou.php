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

$now = date("Y-m-d");

// SQL（終了日が過ぎた投票の結果を取得）
$sql = "
    SELECT votes.title, sennta.senntaku, COUNT(vote_count.id) AS vote_sum
    FROM votes
    JOIN sennta ON votes.id = sennta.title_id
    LEFT JOIN vote_count ON sennta.id = vote_count.sennta_id
    WHERE votes.end_date <= ?
    GROUP BY sennta.id
    ORDER BY votes.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $now);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>投票結果</title>
</head>
<body>

<h1>投票結果</h1>

<?php if ($result->num_rows === 0): ?>
    <p>まだ投票結果は公開されていません。</p>
<?php else: ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <p>
            <strong><?php echo htmlspecialchars($row["title"]); ?></strong>
            （<?php echo htmlspecialchars($row["senntaku"]); ?>）  
            ： <?php echo $row["vote_sum"]; ?> 票
        </p>
    <?php endwhile; ?>
<?php endif; ?>

</body>
</html>
