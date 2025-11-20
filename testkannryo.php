<?php
session_start();

// ---------------------------------------------
// ログインチェック
// ---------------------------------------------
if (!isset($_SESSION['account_number'])) {
    die("ログインしてください。");
}

$account_id = $_SESSION['account_number'];

// ---------------------------------------------
// DB接続
// ---------------------------------------------
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// ---------------------------------------------
// ★ GETモード（リダイレクト後） → 完了画面表示だけ
// ---------------------------------------------
if (isset($_GET['done'])) {

    // メッセージ切り替え
    $message = "投票が完了しました！";
    if ($_GET['done'] === "already") {
        $message = "すでに投票済みです。";
    }
    ?>
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>投票完了</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="d-flex justify-content-center align-items-center vh-100 bg-light">

        <div class="card shadow p-4" style="width: 350px;">
            <h3 class="text-center mb-3"><?php echo $message; ?></h3>

            <div class="d-grid gap-2 mt-3">
                <a href="test_main.php" class="btn btn-primary">メインへ戻る</a>
                <a href="testkekka.php" class="btn btn-secondary">投票結果を見る</a>
            </div>
        </div>

    </body>
    </html>
    <?php
    exit();
}

// ---------------------------------------------
// ★ POSTモード（testtouhyou.php から投票された）
// ---------------------------------------------
$vote_id = $_POST['vote_id'] ?? null;
$sennta_id = $_POST['senntaku_id'] ?? null;

if (!$vote_id || !$sennta_id) {
    die("不正なアクセスです。");
}

$conn->begin_transaction();

try {

    // 投票記録追加
    $stmt = $conn->prepare("INSERT INTO vote_count (vote_id, sennta_id, account_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $vote_id, $sennta_id, $account_id);
    $stmt->execute();
    $stmt->close();

    // 選択肢の vote_count +1
    $stmt = $conn->prepare("UPDATE sennta SET vote_count = vote_count + 1 WHERE id = ?");
    $stmt->bind_param("i", $sennta_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    // ★ PRG：成功後に GET にリダイレクト
    header("Location: testkannryo.php?done=1");
    exit();

} catch (mysqli_sql_exception $e) {

    $conn->rollback();

    if ($e->getCode() == 1062) {
        // 重複投票 → 別メッセージへ
        header("Location: testkannryo.php?done=already");
        exit();
    } else {
        echo "エラー：" . $e->getMessage();
    }
}

$conn->close();
?>
