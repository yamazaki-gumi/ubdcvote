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

echo "投票が完了しました。<br>";
echo "<a href='main.php'>戻る</a>";
