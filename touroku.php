<?php
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ▼ 入力値の取得
    $account_number = trim($_POST['account_number']);
    $name = trim($_POST['name']);
    $class_id = trim($_POST['class_id']);
    $password = trim($_POST['password']);

    // ▼ 未入力チェック
    if ($account_number === "" || $name === "" || $class_id === "" || $password === "") {
        echo "<p class='error-message' style='color:red;'>※すべて入力してください。</p>";
    } else {

        // ▼ パスワードをハッシュ化
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // ▼ DB登録処理
        $stmt = $conn->prepare(
            "INSERT INTO accounts (account_number, name, class_id, password) VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param("isss", $account_number, $name, $class_id, $hashed_password);

        if ($stmt->execute()) {
            header("Location: gamen2-1.php");
            exit();
        } else {
            echo "<p class='error-message'>保存エラー: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>アカウント登録フォーム</title>
    <link rel="stylesheet" href="gamen3.css">
</head>
<body>

<button class="back-button" onclick="location.href='gamen1.php'">戻る</button>

<div class="form-container">
    <h2>アカウント登録</h2>

    <form id="regForm" action="touroku.php" method="POST">

        <label>アカウント番号（4桁）：</label>
        <input type="text" name="account_number" required><br>

        <label>名前：</label>
        <input type="text" name="name" required><br>

        <label>クラス：</label>
        <input type="text" name="class_id" required><br>

        <label>パスワード：</label>
        <!-- セキュリティのため password 型に変更 -->
        <input type="password" name="password" required><br>

        <button type="submit" id="submitBtn">登録</button>
    </form>
</div>

<script src="gamen3.js"></script>
</body>
</html>
