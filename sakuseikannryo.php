<?php
session_start();

// ログインチェック
if (!isset($_SESSION['account_number'])) {
    die("ログインしてください。");
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>投稿完了</title>
    <link rel="stylesheet" href="gamen9-1.css">
</head>
<body>

    <div class="container">
        <h1>投稿しました！</h1>
        <button onclick="location.href='main.php'">戻る</button>
    </div>

</body>
</html>
