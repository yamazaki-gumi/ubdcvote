<?php
session_start();

// ログインチェック
if (!isset($_SESSION['account_number'])) {
    die("ログインしてください。");
}

$account_id = $_SESSION['account_number'];

// DB接続
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

$vote_id = $_POST['vote_id'] ?? null;
$sennta_id = $_POST['senntaku_id'] ?? null;

if (!$vote_id || !$sennta_id) {
    die("不正なアクセスです。");
}

$conn->begin_transaction();

try {

    // vote_count に登録
    $stmt = $conn->prepare("INSERT INTO vote_count (vote_id, sennta_id, account_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $vote_id, $sennta_id, $account_id);
    $stmt->execute();
    $stmt->close();

    // sennta.vote_count +1
    $stmt = $conn->prepare("UPDATE sennta SET vote_count = vote_count + 1 WHERE id = ?");
    $stmt->bind_param("i", $sennta_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    echo "<h2>投票が完了しました！</h2>";
    echo "<a href='index.php'>戻る</a><br>";
    echo "<a href='testkekka.php'>投稿状況確認</a>";

} catch (mysqli_sql_exception $e) {

    $conn->rollback();

    // ★ ここが重要：エラーコードは $e->getCode() で検出する！
    if ($e->getCode() == 1062) {
        echo "<h2>すでに投票済みです。</h2>";
        echo "<a href='index.php'>戻る</a>";
    } else {
        echo "<h2>エラー:</h2>";
        echo $e->getMessage();
    }
}

$conn->close();
?>
