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

/* ---------------------------------------------------
    1) 完了ボタン押されたら flag=1 にして test_main.php へ
-----------------------------------------------------*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['finish_vote_id'])) {
    $finish_vote_id = $_POST['finish_vote_id'];

    $stmt = $conn->prepare("UPDATE votes SET flag = 1 WHERE id = ?");
    $stmt->bind_param("i", $finish_vote_id);
    $stmt->execute();
    $stmt->close();

    header("Location: test_main.php");
    exit();
}

/* ---------------------------------------------------
    2) タイトルを登録（選択肢モーダルを出す）
-----------------------------------------------------*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['title'])) {
    $title = $_POST['title'];
    
    // 開始日が未入力ならNULL
    $start_date = $_POST['start_date'] ?? NULL;
    // 終了日が未入力なら最大日付（例: 9999-12-31）
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : '9999-12-31';

    $stmt = $conn->prepare(
        "INSERT INTO votes (title, start_date, end_date, account_id, flag) 
        VALUES (?, ?, ?, ?, 0)"
    );
    $stmt->bind_param("ssss", $title, $start_date, $end_date, $account_number);

    if ($stmt->execute()) {
        $last_vote_id = $conn->insert_id;
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

    <form method="POST" action="">
        タイトル：<input type="text" name="title" required class="form-control mb-2">
        開始日：<input type="date" name="start_date" class="form-control mb-2">
        終了日：<input type="date" name="end_date" class="form-control mb-2">
        <button type="submit" class="btn btn-primary">選択肢を追加</button>
    </form>
</div>


<?php if ($last_vote_id): ?>
<!-- 🔽 選択肢追加モーダル -->
<div class="modal fade show" id="senntaModal" tabindex="-1" style="display:block;" aria-modal="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">選択肢を追加（投票ID: <?php echo $last_vote_id; ?>）</h5>
      </div>

      <div class="modal-body">

        <!-- 選択肢入力フォーム -->
        <form id="senntaForm">
            <input type="hidden" name="title_id" value="<?php echo $last_vote_id; ?>">
            選択肢：<input type="text" name="senntaku" class="form-control mb-2" required>
            <button type="submit" class="btn btn-success">追加</button>
        </form>

        <div id="senntaResult" class="mt-2"></div>

        <hr>

        <!-- 🔽 選択肢一覧 -->
        <h5>追加した選択肢：</h5>
        <div id="senntaList">
            <!-- AJAXでここに追加表示される -->
        </div>

        <hr>

        <!-- 🔽 完了ボタン -->
        <form method="POST" class="mt-2">
            <input type="hidden" name="finish_vote_id" value="<?php echo $last_vote_id; ?>">
            <button type="submit" class="btn btn-primary w-100">完了</button>
        </form>

      </div>
    </div>
  </div>
</div>
<?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php if ($last_vote_id): ?>
<script>
// 選択肢追加
$('#senntaForm').on('submit', function(e){
    e.preventDefault();
    $.post('add_sennta.php', $(this).serialize(), function(data){
        $('#senntaResult').html(data);

        // 入力した選択肢を取得して一覧に追加
        const text = $('input[name="senntaku"]').val();
        $('#senntaList').append("<div class='alert alert-secondary py-1 mb-1'>" + text + "</div>");

        $('#senntaForm')[0].reset();
    });
});
</script>
<?php endif; ?>

</body>
</html>
