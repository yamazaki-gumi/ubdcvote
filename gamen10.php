<?php
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// POSTが送信されたら保存
if (isset($_POST['title'])) {
    $title = $_POST['title'];
    $start_date = $_POST['start_date'] ?? NULL;
    $end_date = $_POST['end_date'] ?? NULL;

    $stmt = $conn->prepare("INSERT INTO title (title, start_date, end_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $start_date, $end_date);

    if ($stmt->execute()) {
        echo "<p>保存できました！</p>";
    } else {
        echo "<p>保存エラー: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>UBDC VOTE</title>
    <link rel="stylesheet" href="gamen10.css">
</head>
<body>

    <button id="backBtn" onclick="back_button()">戻る</button>

    <div class="container">
    <div id="text">投票作成</div>


    <div class="date-row">
        <form action="gamen10.php" method="POST">
            <div class="date-container">
            <label for="startDate">開始日：</label>
            <input type="date" name="start_date"><br><br>
            </div>
            <input type="text" name="title" required><br><br>

            <div class="date-separator">～</div>

            <div class="date-container">
            <label for="endDate">終了日：</label>
            <input type="date" name="end_date"><br><br>
            <button type="submit">送信</button>
        </form>
        </div>
    </div>
    <form>
        <label for="lang">投票者</label>
        <select name="langrange" id="lang">
            <option value="" hidded>投票者を選択してください</option>
            <option value="zentai">全体（学校）</option>
            <option value="class">クラス</option>
            <option value="meibo">名簿作成</option>
        </select>
    </form>
</body>
</html>
<!-- <!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>投票作成フォーム</title>
    <link rel="stylesheet" href="gamen10.css">
</head>
<body>

<h2>投票作成フォーム</h2>

<form action="gamen10.php" method="POST">
    タイトル：<input type="text" name="title" required><br><br>
    開始日：<input type="date" name="start_date"><br><br>
    終了日：<input type="date" name="end_date"><br><br>
    <button type="submit">送信</button>
</form>

</body>
</html>
-->
