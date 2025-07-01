<!-- C:\xampp\htdocs\student_search\search.php -->
<?php
// 入力値の取得とエスケープ
$student_number = htmlspecialchars($_POST['student_number'], ENT_QUOTES, 'UTF-8');

// DB接続情報
$servername = "localhost";
$username = "root";
$password = ""; // XAMPPではパスワードなしがデフォルト
$dbname = "school_db";

// DBに接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続チェック
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// プリペアドステートメントで検索（SQLインジェクション対策）
$sql = "SELECT name FROM students WHERE student_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_number);
$stmt->execute();
$result = $stmt->get_result();

// 結果を表示
if ($row = $result->fetch_assoc()) {
    echo "名前： " . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
} else {
    echo "その学籍番号は登録されていません。";
}

// 接続を閉じる
$stmt->close();
$conn->close();
?>
