<?php
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// POSTが送信されたら保存
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $account_number = $_POST['account_number'];
    $name = $_POST['name'] ?? NULL;
    $class_id = $_POST['class_id'] ?? NULL;
    $password = $_POST['password'];

    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO accounts (account_number, name, class_id, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $account_number, $name, $class_id, $hashed_pass);

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
    <title>アカウント登録フォーム</title>
    <link rel="stylesheet" href="gamen3.css"> <!-- 外部CSS -->
</head>
<body>

<h2>アカウント登録</h2>

<form action="testtouroku.php" method="POST">
    アカウント番号（4桁）:<input type="text" name="account_number" required><br><br>
    名前：<input type="text" name="name"><br><br>
    クラス：<input type="text" name="class_id"><br><br>
    パスワード：<input type="text" name="password"><br><br>
    <button type="submit">登録</button>
</form>

</body>
</html>
