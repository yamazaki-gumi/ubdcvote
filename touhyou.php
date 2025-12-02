<?php
session_start(); // セッション開始
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

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
$sql = "SELECT accounts FROM secret_situmon";
/* ---------------------------
   ① タイトル + 期限を取得する
   ---------------------------*/
$stmt = $conn->prepare("
    SELECT title, start_date, end_date
    FROM votes
    WHERE id = ?
");
$stmt->bind_param("i", $vote_id);
$stmt->execute();
$stmt->bind_result($title, $start_date, $end_date);
$stmt->fetch();
$stmt->close();

/* ---------------------------
   ② 期限チェック（★ここが重要★）
   ---------------------------*/
if (!$title) {
    die("この投票は存在しません。");
}

$now = date("Y-m-d H:i:s");

if (!isset($error_message)) {
    if ($now < $start_date) {
        $error_message = "この投票はまだ開始されていません。";
    }

    if ($now > $end_date) {
        $error_message = "この投票は終了しています。";
    }
}
/* ---------------------------
   ③ 選択肢を取得（ここから元の処理）
   ---------------------------*/
$result = $conn->query("SELECT id, senntaku, vote_count FROM sennta WHERE title_id = $vote_id");
?>


<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($title); ?> に投票</title>
<link rel="stylesheet" href="touhyou.css?v=<?php echo time(); ?>"></head>
<style>
.error-box {
    background: #ffe5e5;
    border: 2px solid #ff6b6b;
    padding: 20px;
    margin: 30px auto;
    width: 80%;
    text-align: center;
    border-radius: 10px;
    font-size: 1.2em;
    color: #c0392b;
}
</style>
<body>
<?php if (isset($error_message)): ?>

    <!-- ★ CSS を適用したエラーメッセージ ★ -->
    <div class="error-box">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
    <button class="modoru-button" onclick="location.href='zumi.php'">戻る</button>


<?php else: ?>

    <div class="header">
        <h2><?php echo htmlspecialchars($title); ?> に投票</h2>
    </div>

    <?php
    $result = $conn->query("SELECT id, senntaku, vote_count FROM sennta WHERE title_id = $vote_id");
    ?>

    <form action="kannryo.php" method="POST" class="vote-form">
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

<?php endif; ?>

</body>
</html>