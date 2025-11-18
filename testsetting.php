<?php
session_start();

if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}

$account_number = $_SESSION['account_number'];

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
<h1>アカウント設定</h1>
<!-- === ページ移動ボタン === -->
<div style="margin-top: 30px;">
    <button onclick="location.href='testdeletekakunin.php'">アカウント削除</button>
    <button onclick="location.href='testlogin.php'">ログアウト</button>
</div>
</body>