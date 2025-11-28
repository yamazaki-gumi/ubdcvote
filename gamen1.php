<?php
session_start(); // セッション開始

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
    <link rel="stylesheet" href="gamen1.css?v=<?php echo time(); ?>">
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
