<?php
session_start();

if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['name'];
$account_number = $_SESSION['account_number'];

$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

$last_vote_id = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" ) {
    $title = $_POST['title'];
    $start_date = $_POST['start_date'] ?? NULL;
    $end_date = $_POST['end_date'] ?? NULL;
    $account_id = $account_number;

    $stmt = $conn->prepare("INSERT INTO votes (title, start_date, end_date, account_id, flag) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("ssss", $title, $start_date, $end_date, $account_id);

    if ($stmt->execute()) {
        $last_vote_id = $conn->insert_id; // 新しく挿入されたIDを取得
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
<title>タイトル登録</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
<h2>タイトル登録</h2>
<h1>ようこそ <?php echo htmlspecialchars($name); ?> さん</h1>

<form method="POST" action="testtitle.php">
    タイトル：<input type="text" name="title" required class="form-control mb-2">
    開始日：<input type="date" name="start_date" class="form-control mb-2">
    終了日：<input type="date" name="end_date" class="form-control mb-2">
    <button type="submit" class="btn btn-primary">次へ</button>
</form>
</div>

<?php if ($last_vote_id): ?>
<!-- モーダル -->
<div class="modal fade" id="senntaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">選択肢を追加</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="senntaForm">
            <input type="hidden" name="title_id" value="<?php echo $last_vote_id; ?>">
            選択肢：<input type="text" name="senntaku" class="form-control mb-2" required>
            <button type="submit" class="btn btn-success">追加</button>
        </form>
        <div id="senntaResult" class="mt-2"></div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php if ($last_vote_id): ?>
<script>
var senntaModal = new bootstrap.Modal(document.getElementById('senntaModal'));
senntaModal.show();

$('#senntaForm').on('submit', function(e){
    e.preventDefault();
    $.post('add_sennta.php', $(this).serialize(), function(data){
        $('#senntaResult').html(data);
        $('#senntaForm')[0].reset();
    });
});
</script>
<?php endif; ?>

</body>
</html>
