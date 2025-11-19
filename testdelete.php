<?php
session_start();

// ログイン確認
if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}

$account_number = $_SESSION['account_number'];

// DB接続
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// 削除SQL
$sql = "DELETE FROM accounts WHERE account_number = ? AND account_number=account_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $account_number);

if ($stmt->execute()) {
    // セッション破棄
    session_unset();
    session_destroy();

    echo "アカウントを削除しました。3秒後にログインページへ移動します。";
    echo "<script>
            setTimeout(function(){
                window.location.href = 'testlogin.php';
            }, 3000);
            </script>";
} else {
    echo "削除に失敗しました: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
