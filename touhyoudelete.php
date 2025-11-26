<?php
session_start();

var_dump($_POST);


$vote_id = (int)$_POST['vote_id'];

// DB 接続
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");

if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// votes テーブルから削除
$stmt = $conn->prepare("DELETE FROM votes WHERE id = ?");
$stmt->bind_param("i", $vote_id);

if ($stmt->execute()) {
    // 成功時
    header("Location: zumi.php?msg=deleted");
    exit();
} else {
    echo "削除失敗：" . $conn->error;
}

$stmt->close();
$conn->close();
?>
