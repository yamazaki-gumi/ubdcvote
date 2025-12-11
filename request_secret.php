<?php
session_start();

// キャッシュ無効化
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $account_number = $_POST['account_number'] ?? '';
    $secret = $_POST['secret'] ?? '';

    $sql = "SELECT name, account_number, secret FROM accounts WHERE account_number = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL準備失敗: " . $conn->error);
    }

    $stmt->bind_param("s", $account_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        $_SESSION['account_number'] = $row['account_number'];
        $_SESSION['name'] = $row['name'];

        header("Location: situmon.php");
        exit();


    } else {
        $error = "アカウント番号が間違っています。";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワードリセット確認</title>
    <link rel="stylesheet" href="gamen2.css">

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>

<?php
if (!empty($error)) {
    echo "<p style='color:red;'>$error</p>";
}
?>

<div class="form-container">
    <h1>パスワード変更</h1>

    <form method="POST" action="" autocomplete="off">

        <input type="text" style="display:none">
        <input type="password" style="display:none">

        <label>アカウント番号
            <input type="text" name="account_number" required>
        </label><br>
        <input type="submit" id="tourokuBtn" value="確認">
    </form>
</div>

<button class="back-button" onclick="location.href='gamen1.php'">戻る</button>

<script>
window.addEventListener("pageshow", function(event) {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => form.reset());

    if (event.persisted) {
        window.location.reload();
    }
});
</script>

</body>
</html>
