<?php
session_start();

$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

$vote_id = $_POST['vote_id'] ?? null;
$senntaku_id = $_POST['senntaku_id'] ?? null;

if (!$vote_id || !$senntaku_id) {
    die("不正なアクセスです。");
}

// vote_count を 1 増やす
$stmt = $conn->prepare("UPDATE sennta SET vote_count = vote_count + 1 WHERE id = ?");
$stmt->bind_param("i", $senntaku_id);
$stmt->execute();
$stmt->close();

echo "投票が完了しました。<br>";
echo "<a href='testmain.php'>戻る</a>";
