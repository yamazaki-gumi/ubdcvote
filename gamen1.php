<?php
session_start(); // セッション開始

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



?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="gamen1.css">
    <title>UBDC VOTE</title>
</head>
<body>
    <h1 class="h1">UBDC VOTE</h1>
    <div class="button-container">
        <button class="btnback12" onclick="location.href='login.php'">ログイン</button>
        <button class="btntoukou" onclick="location.href='touroku.php'">新規登録</button>
    </div>
</body>
</html>

