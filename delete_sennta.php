<?php
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("DB接続失敗");
}

$id = $_POST['id'];

$stmt = $conn->prepare("DELETE FROM sennta WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "NG";
}

$stmt->close();
$conn->close();
?>
