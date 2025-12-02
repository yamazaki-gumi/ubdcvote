<?php
session_start();
// キャッシュ完全無効化
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// ---------------------------
// セッションチェック
if (!isset($_SESSION['account_number'])) {
    echo "<script>window.location.href='gamen1.php';</script>";
    exit();
}
if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}

$account_number = $_SESSION['account_number'];

$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script>
// bfcache復元対策：戻る・進むでキャッシュから復元された場合に強制リロード
window.addEventListener("pageshow", function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});
</script>
<!-- Bootstrap（先）-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- 自分のCSS（後） キャッシュ回避パラメータ付き -->
<link rel="stylesheet" href="setting.css?v=<?php echo time(); ?>">
</head>
<body>
    <h1 class="h1">アカウント設定</h1>
    <div class="button-container">
        <button class="btndelete" onclick="location.href='passninsyou.php'">アカウント削除</button>
        <button class="btnlogout" onclick="location.href='tlogout.php'">ログアウト</button>
        <button class="btntouroku" onclick="location.href='secret_touroku2.php'">秘密の質問登録</button>
    </div>
    <button class="back-button" onclick="location.href='main.php'">戻る</butto>
</body>
</html>
