<?php
session_start(); // セッション開始（必ず最初に！）

// ログインしていない人をブロック
if (!isset($_SESSION['account_number'])) {
    header("Location: login.php"); // ← ログインページに戻す
    exit();
}

// ログイン中のユーザー情報を使う
$name = $_SESSION['name'];
$account_number = $_SESSION['account_number'];


$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// POSTが送信されたら保存
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST['title'];  // ← ここが正しい
    $start_date = $_POST['start_date'] ?? NULL;
    $end_date = $_POST['end_date'] ?? NULL;
    $account_id = $account_number; // セッションから取得
    $stmt = $conn->prepare("INSERT INTO votes (title, start_date, end_date, account_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $start_date, $end_date, $account_id);

    if ($stmt->execute()) {
        echo "<p>保存できました！</p>";
    } else {
        echo "<p>保存エラー: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>タイトル登録</title>
</head>
<body>

<h2>タイトル登録</h2>

<h1>ようこそ<?php echo htmlspecialchars($name);?>さん</h1>

<form action="testtitle.php" method="POST">
    タイトル：<input type="text" name="title" required><br><br>
    開始日：<input type="date" name="start_date"><br><br>
    終了日：<input type="date" name="end_date"><br><br>
    <button type="submit">送信</button>
</form>

</body>
</html>