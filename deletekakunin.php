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
<link rel="stylesheet" href="deletekakunin.css?v=<?php echo time(); ?>">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
<h2 class="h2">⚠ アカウント削除確認</h>
<br>

<p>本当にアカウントを削除しますか？ 
<br>この操作は元に戻せません。</p>

<form action="delete.php" method="post">
    <div class="button-container">
        <button class="btndelete"type="submit" name="delete" value="1">削除する</button>
        <button class="btncansel"type="button" onclick="location.href='setting.php'">キャンセル</button>
    </div>
</form>

</body>
</html>