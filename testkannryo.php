<?php
session_start();

// ログインチェック
if (!isset($_SESSION['account_number'])) {
    die("ログインしてください。");
}

$account_id = $_SESSION['account_number'];

// DB接続
$servername = "localhost";
$username = "root";
$dbpassword = "";
$dbname = "toukounaiyou_db";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// POSTデータ取得
$vote_id = $_POST['vote_id'] ?? null;
$sennta_id = $_POST['senntaku_id'] ?? null;

if (!$vote_id || !$sennta_id) {
    die("不正なアクセスです。");
}

// トランザクション開始
$conn->begin_transaction();

try {
    // vote_count に登録
    $stmt = $conn->prepare("INSERT INTO vote_count (vote_id, sennta_id, account_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $vote_id, $sennta_id, $account_id);
    $stmt->execute();
    $stmt->close();

    // sennta.vote_count を +1
    $stmt = $conn->prepare("UPDATE sennta SET vote_count = vote_count + 1 WHERE id = ?");
    $stmt->bind_param("i", $sennta_id);
    $stmt->execute();
    $stmt->close();

    // コミット
    $conn->commit();

    echo "<h2>投票が完了しました！</h2>";
    echo "<a href='index.php'>戻る</a>";

} catch (mysqli_sql_exception $e) {
    $conn->rollback();

    // 重複投票などのエラー
    if ($conn->errno == 1062) { // UNIQUE制約違反
        echo "<h2>すでに投票済みです。</h2>";
        echo "<a href='index.php'>戻る</a>";
    } else {
        echo "エラー: " . $conn->error;
    }
}

$conn->close();
?>
