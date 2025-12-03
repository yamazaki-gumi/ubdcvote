
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
 
$error_message = "";
 
// 認証ボタンが押された場合
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_password = $_POST['password'];
 
    // DB のパスワード取得（※ password_hash で保存されたもの）
    $sql = "SELECT password FROM accounts WHERE account_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $account_number);
    $stmt->execute();
    $result = $stmt->get_result();
 
    if ($row = $result->fetch_assoc()) {
 
        $db_password_hash = $row['password'];
 
        // password_verify でチェック（★ここが重要）
        if (password_verify($input_password, $db_password_hash)) {
 
            // 認証成功 → 削除確認画面へ
            header("Location: deletekakunin.php");
            exit();
 
        } else {
            // 認証失敗
            $error_message = "パスワードが間違っています";
        }
    }
}
 
$conn->close();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="passninsyou.css?v=<?php echo time(); ?>">
<title>アカウント認証</title>
</head>
 
<body>
 
<!-- 戻るボタン（右上固定） -->
<button class="back-button" onclick="location.href='setting.php'">戻る</button>
 
<!-- 中央ウィンドウ -->
<div class="auth-window">
    <h2>アカウント認証</h2>
 
    <form method="POST">
        <label>パスワードを入力してください</label><br>
        <input type="password" name="password" required class="password-box">
 
        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
 
        <button type="submit" class="auth-btn">認証する</button>
    </form>
</div>
 
</body>
</html>