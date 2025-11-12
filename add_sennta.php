<?php
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title_id = $_POST['title_id'];
    $senntaku = $_POST['senntaku'];

    $stmt = $conn->prepare("INSERT INTO sennta (senntaku, title_id, vote_count) VALUES (?, ?, 0)");
    $stmt->bind_param("si", $senntaku, $title_id);

    if ($stmt->execute()) {
        echo "<p class='text-success'>選択肢追加成功: " . htmlspecialchars($senntaku) . "</p>";
    } else {
        echo "<p class='text-danger'>追加エラー: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
$conn->close();
?>
