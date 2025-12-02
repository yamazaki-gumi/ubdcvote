<?php
session_start();

// ログインチェック
if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}

$account_number = $_SESSION['account_number'];

$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $secret_situmon = trim($_POST['secret_situmon']);
    $secret = trim($_POST['secret']);

    // 未入力チェック
    if ($secret_situmon === "" || $secret === "") {
        echo "<p class='error-message' style='color:red;'>※すべて入力してください。</p>";
    } 
    else {

        $stmt = $conn->prepare(
            "UPDATE accounts SET secret_situmon = ?, secret = ? WHERE account_number = ?"
        );

        if (!$stmt) {
            die("SQLエラー: " . $conn->error);
        }

        $stmt->bind_param("ssi", $secret_situmon, $secret, $account_number);

        try {
            $stmt->execute();
            header("Location: gamen2-2.php");
            exit();

        } catch (mysqli_sql_exception $e) {
            echo "<p style='color:red;'>エラーが発生しました</p>";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アカウント登録フォーム</title>
    <link rel="stylesheet" href="gamen3.css">
</head>
<body>

<button class="back-button" onclick="location.href='gamen1.php'">戻る</button>

<div class="form-container">
    <h2>秘密の質問内容</h2>

    <form id="regForm" action="secret_touroku2.php" method="POST">
        <?php if (!empty($error_msg)): ?>
        <p style="color:red; margin-top:10px;"><?php echo $error_msg; ?></p>
        <?php endif; ?>


        <label>質問内容</label>
        <input type="text" name="secret_situmon" 
        required><br>

        <label>回答</label>
        <input type="text" name="secret" required><br>
        <button type="submit" id="submitBtn">登録</button>
    </form>
</div>

</body>
</html>