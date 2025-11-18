<?php
session_start();

//ログイン確認
if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>アカウント削除</title>
</head>
<body>
<h2>⚠ アカウント削除確認</h2>
<p>本当にアカウントを削除しますか？ この操作は元に戻せません。</p>

<form action="testdelete.php" method="post">
    <button type="submit" name="delete" value="1">削除する</button>
    <button type="button" onclick="location.href='home.php'">キャンセル</button>
</form>

</body>
</html>