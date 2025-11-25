<?php
session_start();

// ---------------------------
// セッション破棄
$_SESSION = [];
session_destroy();

// ---------------------------
// キャッシュ完全無効化
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// ---------------------------
// 履歴置換＋リダイレクト
echo "<script>
history.replaceState(null, null, 'login.php');
window.location.href = 'login.php';
</script>";
exit();
