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
$sql = "DELETE FROM accounts WHERE account_number = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $account_number);  // account_number が文字列なら s

if ($stmt->execute()) {
    // セッション破棄
    session_unset();
    session_destroy();
    echo "<script>
            setTimeout(function(){
                window.location.href = 'gamen1.php';
            });
          </script>";
} else {
    echo "削除に失敗しました: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
