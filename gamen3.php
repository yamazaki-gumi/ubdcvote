<?php
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    $account_number = $_POST['account_number'];
    $name = $_POST['name'] ?? NULL;
    $class_id = $_POST['class_id'] ?? NULL;
    $password = $_POST['password'];
 
    $stmt = $conn->prepare("INSERT INTO accounts (account_number, name, class_id, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $account_number, $name, $class_id, $password);
 
    if ($stmt->execute()) {
        echo "<p class='success-message'>保存できました！</p>";
    } else {
        echo "<p class='error-message'>保存エラー: " . $stmt->error . "</p>";
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
 
<button class="back-button" onclick="window.history.back();">戻る</button>
 
<div class="form-container">
    <h2>アカウント登録</h2>
 
    <form id="regForm" action="testtouroku.php" method="POST">
        <label>アカウント番号（4桁）：</label>
        <input type="text" name="account_number" required><br>
 
        <label>名前：</label>
        <input type="text" name="name"><br>
 
        <label>クラス：</label>
        <input type="text" name="class_id"><br>
 
        <label>パスワード：</label>
        <input type="text" name="password"><br>
 
        <button type="submit" id="submitBtn">登録</button>
    </form>
</div>
 
<script src="script.js"></script> <!-- 外部JS -->
</body>
</html>