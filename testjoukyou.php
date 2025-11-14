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
$now = date("Y-m-d H:i:s");
$sql = "SELECT votes.title, sennta.vote_count 
        FROM votes 
        JOIN sennta ON votes.id = sennta.title_id
        WHERE votes.end_date <= ?";

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
        <p><?php echo $row["title"]; ?> : <?php echo $row["vote_count"]; ?> 票</p>
    <?php endwhile; ?>
<?php endif; ?>

</body>
</html>
