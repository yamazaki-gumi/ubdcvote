<?php
session_start();

// ログイン(本人確認)チェック
if (!isset($_SESSION['account_number'])) {
    header("Location: gamen1.php");
    exit();
}

// DB接続
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $new_password = $_POST["new_password"] ?? "";

    if ($new_password === "") {
        $error = "パスワードを入力してください。";
    } 
    else if (strlen($new_password) < 8) {
        $error = "パスワードは8文字以上で入力してください。";
    }
    else {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $account_number = $_SESSION["account_number"];

        $stmt = $conn->prepare("UPDATE accounts SET password = ? WHERE account_number = ?");
        if (!$stmt) {
            die("SQLエラー: " . $conn->error);
        }

        $stmt->bind_param("si", $hash, $account_number);

        if ($stmt->execute()) {
            $success = "✅ パスワードを変更しました！";
            session_destroy(); // セキュリティのためログアウト
        } else {
            $error = "変更に失敗しました。";
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
<title>パスワード変更</title>
<link rel="stylesheet" href="gamen2.css?v=<?php echo time();?>">
</head>
<body>

<div class="form-container">
<h2>パスワード変更</h2>

<?php if ($error): ?>
<p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($success): ?>
<p style="color:green;"><?php echo $success; ?></p>
<button class="modoru-button" onclick="location.href='main.php'">戻る</button>

<?php else: ?>
<form method="POST">

        <label>パスワード：</label>
        <!-- セキュリティのため password 型に変更 -->
        <input type="password" name="new_password" minlength="8"
        title="8文字以上で入力してください" required><br>

<input class=hennkou type="submit" value="変更する">

</form>
<?php endif; ?>
</div>

</body>
</html>
