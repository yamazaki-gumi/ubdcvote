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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録完了</title>
    <link rel="stylesheet" href="gamen2-1.css">
</head>
<body>
 
    <div class="container">
        <h1>登録完了しました</h1>
        <button id="backBtn">戻る</button>
    </div>
 
    <script src="gamen2-1.js"></script>
</body>
</html>