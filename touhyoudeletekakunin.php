<?php
session_start();

// vote_id を受け取れているか確認
if (!isset($_GET['vote_id'])) {
    header("Location: zumi.php");
    exit();
}

$vote_id = (int)$_GET['vote_id'];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="stylesheet" href="deletekakunin.css?v=<?php echo time(); ?>">
<meta charset="UTF-8">
</head>
<body>

<h2 class="h2">投票削除</h2>
<h2>⚠ 投票削除確認</h2>
<p>本当に投票を削除しますか？ この操作は元に戻せません。</p>

<form action="touhyoudelete.php" method="post">
    <input type="hidden" name="vote_id" value="<?= $vote_id ?>">  <!-- ★ これが必須 ★ -->
    <button class="btndelete" type="submit">削除する</button>
    <button class="btncansel" type="button" onclick="location.href='zumi.php'">キャンセル</button>
</form>

</body>
</html>
