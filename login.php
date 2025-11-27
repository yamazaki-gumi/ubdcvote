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

$error_msg = "";

// POST送信時のみ処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $account_number = $_POST['account_number'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT name, account_number, password FROM accounts WHERE account_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $account_number);
    $stmt->execute();
    $result = $stmt->get_result();

    try {

    if ($row = $result->fetch_assoc()) {

        if (password_verify($password, $row['password'])) {
            $_SESSION['account_number'] = $row['account_number'];
            $_SESSION['name'] = $row['name'];

            header("Location: main.php");
            exit();
        } else {
            $error_msg = "※パスワードが間違っています。";
        }

    } else {
        $error_msg = "※該当するアカウントがありません。";
    }

} catch (mysqli_sql_exception $e) {
    $error_msg = "エラーが発生しました: " . $e->getMessage();
}

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
    <style>
    /* 画面上部に固定表示するエラー */
        .error-box {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            color: #ff0000;
            text-align: center;
            font-weight: bold;
            padding: 10px 0;
        }
        /* フォームがエラーに隠れないように少し余白 */
        body {
            padding-top: 30px; /* error-boxの高さに合わせて調整 */
        }
    </style>
</head>
<body>

<?php if (!empty($error)): ?>
    <div class="error-box"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="form-container">
    <h1>ログイン</h1>
    <form method="POST" action="" autocomplete="off">
        <?php if (!empty($error_msg)): ?>
        <p style="color:red; margin-top:10px;"><?php echo $error_msg; ?></p>
        <?php endif; ?>

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
        <p><a href="request_secret.php">パスワードを忘れましたか？</a></p>

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
