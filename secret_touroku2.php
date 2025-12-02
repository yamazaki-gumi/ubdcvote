<?php
session_start();

// ログインチェック
if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}

$account_number = $_SESSION['account_number'];

$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

$error_msg = "";
$already_registered = false;

// まず登録済みかチェック
$stmt = $conn->prepare("SELECT secret_situmon, secret FROM accounts WHERE account_number = ?");
$stmt->bind_param("i", $account_number);
$stmt->execute();
$stmt->bind_result($old_q, $old_a);
$stmt->fetch();
$stmt->close();

if (!empty($old_q) && !empty($old_a)) {
    $already_registered = true; // 登録済みフラグ
}

// POST送信処理（未登録の場合のみ）
if ($_SERVER["REQUEST_METHOD"] === "POST" && !$already_registered) {

    $secret_situmon = trim($_POST['secret_situmon']);
    $secret = trim($_POST['secret']);

    // 入力チェック
    if ($secret_situmon === "" || $secret === "") {
        $error_msg = "※すべて入力してください。";
    } else {
        // UPDATE
        $stmt = $conn->prepare("UPDATE accounts SET secret_situmon = ?, secret = ? WHERE account_number = ?");
        $stmt->bind_param("ssi", $secret_situmon, $secret, $account_number);
        $stmt->execute();
        $stmt->close();

        header("Location: gamen2-2.php");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>秘密の質問登録</title>
<link rel="stylesheet" href="gamen3.css">
</head>
<body>

<button class="back-button" onclick="location.href='main.php'">戻る</button>

<div class="form-container">
    <h2>秘密の質問内容</h2>

    <?php if ($already_registered): ?>
        <p style="color:red;">※すでに秘密の質問は登録済みです   </p>
        <p style="color:red;">※変更はできません</p>
    <?php else: ?>
        <form id="regForm" method="POST">
            <?php if (!empty($error_msg)): ?>
                <p style="color:red;"><?php echo $error_msg; ?></p>
            <?php endif; ?>

            <label>質問内容</label>
            <input type="text" name="secret_situmon" value="<?php echo isset($secret_situmon) ? htmlspecialchars($secret_situmon) : ''; ?>" required><br>

            <label>回答</label>
            <input type="text" name="secret" value="<?php echo isset($secret) ? htmlspecialchars($secret) : ''; ?>" required><br>

            <button class="submitBtn" type="submit">登録</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
