<?php
session_start();

if (!isset($_SESSION['account_number'])) {
    header("Location: testlogin.php");
    exit();
}

$name = $_SESSION['name'];
$account_number = $_SESSION['account_number'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ホーム</title>

<style>
/* ページ全体 */
html, body {
    height: 100%;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f7f7f7;
    font-family: sans-serif;
    flex-direction: column;
}

/* タイトル */
h1 {
    margin-bottom: 30px;
    font-size: 28px;
    text-align: center;
}

/* ボタンコンテナ */
.menu-container {
    display: flex;
    flex-direction: column; /* 縦並び */
    gap: 15px;
    width: 250px;
    align-items: center;
}

/* ボタン */
.menu-container button {
    display: block !important; /* 横並び防止のため最優先 */
    width: 100%;
    padding: 12px;
    font-size: 18px;
    border: none;
    border-radius: 8px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    transition: 0.2s;
}

.menu-container button:hover {
    background-color: #0056b3;
}
</style>
</head>
<body>

<h1>ようこそ <?php echo htmlspecialchars($name); ?> さん</h1>

<div class="menu-container">
    <button onclick="location.href='testtitle.php'">投票作成</button>
    <button onclick="location.href='testitiran.php'">投票一覧へ</button>
    <button onclick="location.href='testzumi.php'">作成した投票</button>
    <button onclick="location.href='testsugi.php'">投票結果</button>
    <button onclick="location.href='testsetting.php'">アカウント設定</button>
</div>

</body>
</html>
