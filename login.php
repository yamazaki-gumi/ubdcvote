<?php
session_start(); // セッション開始

// ---------------------------
// キャッシュ無効化
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
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

// POST送信時のみ処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $account_number = $_POST['account_number'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT name, account_number, password FROM accounts WHERE account_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $account_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        if (password_verify($password, $row['password'])) {

            // ログイン成功
            $_SESSION['account_number'] = $row['account_number'];
            $_SESSION['name'] = $row['name'];

            header("Location: main.php");
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

    <!-- bfcache・フォーム入力残り防止 -->
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
    <h1>ログイン</h1>
    <form method="POST" action="" autocomplete="off">
        <!-- 自動入力吸収用ダミー -->
        <input type="text" style="display:none">
        <input type="password" style="display:none">

        <label>学籍番号:
            <input type="text" name="account_number" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" required>
        </label><br>
        <label>パスワード:
            <input type="password" name="password" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" required>
        </label><br>
        <input type="submit" id="submitBtn"value="ログイン">
    </form>
</div>

<button class="back-button" onclick="location.href='gamen1.php'">戻る</button>

<script>
// bfcache復元時のフォームリセット
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
