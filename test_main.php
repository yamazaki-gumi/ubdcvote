<?php
session_start();

//セッション失敗
if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}

//受け取ったセッションを使えるようにする
$name = $_SESSION['name'];
$account_number = $_SESSION['account_number'];

//データベース接続
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ホーム</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<h1>ようこそ <?php echo htmlspecialchars($name); ?> さん</h1>
<!-- === ページ移動ボタン === -->
<div style="margin-top: 30px;">
    <button onclick="location.href='testtitle.php'">投票作成</button>
    <button onclick="location.href='testitiran.php'">投票一覧へ</button>
    <button onclick="location.href='testzumi.php'">作成した投票</button>
    <button onclick="location.href='testlogin.php'">ログアウト</button>
</div>
</body>