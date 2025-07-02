<?php
// 初期化
$error = '';
$name = '';

// フォームが送信されたとき
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 入力値の取得とエスケープ
    $student_number = htmlspecialchars($_POST['student_number'], ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

    // DB接続情報
    $servername = "localhost";
    $username = "root";
    $dbpassword = ""; // XAMPPのデフォルトは空
    $dbname = "school_db";

    // MySQLに接続
    $conn = new mysqli($servername, $username, $dbpassword, $dbname);
    if ($conn->connect_error) {
        die("DB接続失敗: " . $conn->connect_error);
    }

    // SQL実行（プレースホルダ使用でSQLインジェクション対策）
    $stmt = $conn->prepare("SELECT name FROM students WHERE student_number = ? AND password = ?");
    $stmt->bind_param("ss", $student_number, $password);
    $stmt->execute();
    $stmt->store_result();

    // 該当するユーザーが存在すれば、名前を取得して次のページへ
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($name);
        $stmt->fetch();
        // JavaScriptで名前を次のページへ渡してリダイレクト
        echo "<script>
            const userName = " . json_encode($name) . ";
            window.location.href = 'next.php?name=' + encodeURIComponent(userName);
        </script>";
        exit();
    } else {
        $error = "学籍番号またはパスワードが違います。";
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- HTML部分 -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン画面</title>
</head>
<body>
    <h2>学籍番号ログイン</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
        <label>学籍番号: <input type="text" name="student_number" required></label><br><br>
        <label>パスワード: <input type="password" name="password" required></label><br><br>
        <button type="submit">ログイン</button>
    </form>
</body>
</html>
