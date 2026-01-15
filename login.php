<?php
session_start(); // セッション開始

// ---------------------------
// ----キャッシュ無効化
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// DB接続情報
$servername = "localhost";
$username = "root";
$dbpassword = "";
$dbname = "toukounaiyou_db";

// MySQLに接続
$conn = new mysqli($servername, $username, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

$error_msg = "";
$LOCK_MINUTES = 30; // 🔒 ロック時間（分）

// POST送信時のみ処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $account_number = $_POST['account_number'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT name, account_number, password,
                   failed_count, is_locked, locked_at
            FROM accounts
            WHERE account_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $account_number);
    $stmt->execute();
    $result = $stmt->get_result();

    try {

        if ($row = $result->fetch_assoc()) {

            /* =========================
               🔓 ロック自動解除チェック
               ========================= */
            if ($row['is_locked'] == 1 && $row['locked_at'] !== null) {

                $locked_time = strtotime($row['locked_at']);
                $now_time = time();

                if (($now_time - $locked_time) >= ($LOCK_MINUTES * 60)) {
                    // ロック解除
                    $unlockSql = "UPDATE accounts
                                  SET is_locked = 0,
                                      failed_count = 0,
                                      locked_at = NULL
                                  WHERE account_number = ?";
                    $unlockStmt = $conn->prepare($unlockSql);
                    $unlockStmt->bind_param("i", $account_number);
                    $unlockStmt->execute();

                    // DB更新後の状態を反映
                    $row['is_locked'] = 0;
                    $row['failed_count'] = 0;
                }
            }

            /* =========================
               🔒 ロック中なら終了
               ========================= */
            if ($row['is_locked'] == 1) {
                $error_msg = "※このアカウントは一時的にロックされています。30分後に再試行してください。";
            }
            /* =========================
               🔑 パスワード正解
               ========================= */
            else if (password_verify($password, $row['password'])) {

                // 成功 → 失敗回数リセット
                $resetSql = "UPDATE accounts
                             SET failed_count = 0
                             WHERE account_number = ?";
                $resetStmt = $conn->prepare($resetSql);
                $resetStmt->bind_param("i", $account_number);
                $resetStmt->execute();

                $_SESSION['account_number'] = $row['account_number'];
                $_SESSION['name'] = $row['name'];

                header("Location: main.php");
                exit();

            }
            /* =========================
               ❌ パスワード不正
               ========================= */
            else {

                $failed = $row['failed_count'] + 1;

                if ($failed >= 3) {
                    // ロックする
                    $lockSql = "UPDATE accounts
                                SET failed_count = ?,
                                    is_locked = 1,
                                    locked_at = NOW()
                                WHERE account_number = ?";
                    $lockStmt = $conn->prepare($lockSql);
                    $lockStmt->bind_param("ii", $failed, $account_number);
                    $lockStmt->execute();

                    $error_msg = "※パスワードを3回間違えたため、30分間アカウントをロックしました。";

                } else {
                    // 失敗回数更新
                    $updateSql = "UPDATE accounts
                                  SET failed_count = ?
                                  WHERE account_number = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("ii", $failed, $account_number);
                    $updateStmt->execute();

                    $error_msg = "※パスワードが間違っています。（あと " . (3 - $failed) . " 回）";
                }
            }

        } else {
            $error_msg = "※該当するアカウントがありません。";
        }

    } catch (mysqli_sql_exception $e) {
        $error_msg = "エラーが発生しました。";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="gamen2.css">

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <style>
        .error-box {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            color: red;
            text-align: center;
            font-weight: bold;
            padding: 10px 0;
            background: rgba(255,255,255,0.8);
            z-index: 999;
        }
    </style>
</head>
<body>



<div class="form-container">
    <h1>ログイン</h1>

    <form method="POST" action="" autocomplete="off">

        <?php if (!empty($error_msg)): ?>
            <p class="error-message"><?= htmlspecialchars($error_msg) ?></p>
        <?php endif; ?>

        <input type="text" style="display:none">
        <input type="password" style="display:none">

        <label>アカウント番号:
            <input type="text" name="account_number"
                readonly onfocus="this.removeAttribute('readonly');"
                required>
        </label><br>

        <label>パスワード:
            <input type="password" name="password"
                readonly onfocus="this.removeAttribute('readonly');"
                required>
        </label><br>

        <button type="submit" id="tourokuBtn">ログイン</button>


        <p><a href="request_secret.php">パスワードを忘れましたか？</a></p>
    </form>
</div>

<button class="back-button" onclick="location.href='gamen1.php'">戻る</button>

<script>
window.addEventListener("pageshow", function(event) {
    document.querySelectorAll("form").forEach(f => f.reset());
    if (event.persisted) location.reload();
});
</script>

</body>
</html>
