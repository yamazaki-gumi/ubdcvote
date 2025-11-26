<?php
session_start();

//ログイン確認
if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投票削除完了</title>
    <link rel="stylesheet" href="deletekanryou.css">
</head>
<body>

    <div class="container">
        <h1>投票の削除が完了しました</h1>
        <button id="backBtn">戻る</button>
    </div>

    <script src="deletekanryou.js"></script>
</body>
</html>