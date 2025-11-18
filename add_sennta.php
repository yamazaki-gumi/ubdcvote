<?php
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title_id = $_POST['title_id'];
    $senntaku = $_POST['senntaku'];

    // sennta テーブルに vote_count がある前提
    $stmt = $conn->prepare("INSERT INTO sennta (senntaku, title_id, vote_count) VALUES (?, ?, 0)");
    $stmt->bind_param("si", $senntaku, $title_id);

    if ($stmt->execute()) {
        // 成功 → 新規IDだけ返す（JS側で削除に使う）
        //echo $stmt->insert_id;
    } else {
        // 失敗 → エラー文字列
        echo "ERROR";
    }

    $stmt->close();
}

$conn->close();
?>
