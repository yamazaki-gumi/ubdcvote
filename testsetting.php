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

<!-- Bootstrap（先）-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- 自分のCSS（後） キャッシュ回避パラメータ付き -->
<link rel="stylesheet" href="testsetting.css?ver=20251119">
</head>
<body>
    <h1 class="h1">アカウント設定</h1>
    <div class="button-container">
        <button class="btndelete" onclick="location.href='testdeletekakunin.php'">アカウント削除</button>
        <button class="btnlogout" onclick="location.href='gamen1.php'">ログアウト</button>
    </div>
    <button class="back-button" onclick="location.href='test_main.php'">戻る</butto>
</body>
</html>
