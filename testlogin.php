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

// POST送信時のみ処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $account_number = $_POST['account_number'] ?? '';
    $password = $_POST['password'] ?? '';

    // ✅ パスワードではなく「学籍番号だけ」で検索する
    $sql = "SELECT name, account_number, password FROM accounts WHERE account_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $account_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        // ✅ ここでハッシュチェック
        if (password_verify($password, $row['password'])) {

            // ログイン成功
            $_SESSION['account_number'] = $row['account_number'];
            $_SESSION['name'] = $row['name'];

            header("Location: test_main.php");
            exit();

        } else {
            $error = "学籍番号またはパスワードが間違っています。";
        }

    } else {
        $error = "学籍番号またはパスワードが間違っています。";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="gamen2.css">
</head>
<body>

<?php
if (!empty($error)) {
    echo "<p style='color:red;'>$error</p>";
}
?>

<div class="form-container">
    <h1>ログイン</h1>
    <form method="POST" action="">
        <label>学籍番号: <input type="text" name="account_number" required></label><br>
        <label>パスワード: <input type="password" name="password" required></label><br>
        <input type="submit" value="ログイン">
    </form>
</div>

<button class="back-button" onclick="location.href='gamen1.php'">戻る</button>

</body>
</html>
