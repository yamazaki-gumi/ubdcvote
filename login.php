<!-- C:\xampp\htdocs\student_login\login.php -->
<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "不正なアクセスです。<a href='index.html'>戻る</a>";
    exit();
}
// 入力値の取得とエスケープ
$student_number = htmlspecialchars($_POST['student_number'], ENT_QUOTES, 'UTF-8');
$password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

// DB接続情報
$servername = "localhost";
$username = "root";
$dbpassword = ""; // XAMPPでは通常パスワード無し
$dbname = "school_db";

// MySQLに接続
$conn = new mysqli($servername, $username, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// SQLクエリ（パラメータを避けた安全処理に改善推奨）
$sql = "SELECT name FROM students WHERE student_number = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $student_number, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $name = $row['name'];
    $student_number = $row['student_number'];
    // 名前をURLに渡して次のページへ
    header("Location: gamen7.php?name=" . urlencode($name));
    exit();
} else {
    echo "<p>学籍番号またはパスワードが間違っています。</p>";
    echo "<a href='index.html'>戻る</a>";
}

$stmt->close();
$conn->close();
?>
