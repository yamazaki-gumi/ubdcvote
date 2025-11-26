<?php
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

$error_msg = "";  // ← ★ ここにエラーメッセージを入れる


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $account_number = trim($_POST['account_number']);
    $name = trim($_POST['name']);
    $class_id = trim($_POST['class_id']);
    $password = trim($_POST['password']);

    if ($account_number === "" || $name === "" || $class_id === "" || $password === "") {

        $error_msg = "※すべて入力してください。";

    } else {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO accounts (account_number, name, class_id, password)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("isss", $account_number, $name, $class_id, $hashed_password);

        try {
            $stmt->execute();
            header("Location: gamen2-1.php");
            exit();

        } catch (mysqli_sql_exception $e) {

            if ($e->getCode() == 1062) {
                // ★ UNIQUEエラー（アカウント番号が重複）
                $error_msg = "※このアカウント番号は既に使用されています。";
            } else {
                $error_msg = "保存エラー: " . $e->getMessage();
            }
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
        <?php if (!empty($error_msg)): ?>
        <p style="color:red; margin-top:10px;"><?php echo $error_msg; ?></p>
        <?php endif; ?>


        <label>アカウント番号（4桁）：</label>
        <input type="text" name="account_number" 
        pattern="\d{4}" maxlength="4"
        title="4桁の数字を入力してください"
        required><br>

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
