<?php
session_start();

// ログインチェック
if (!isset($_SESSION['account_number'])) {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['name'];
$account_number = $_SESSION['account_number'];

// ---------------------------
// DB接続
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

$last_vote_id = null;
$show_error = false;

// ---------------------------
// 完了ボタン押されたら test_main.php へ
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['finish_vote_id'])) {
    $finish_vote_id = $_POST['finish_vote_id'];

    $stmt = $conn->prepare("UPDATE votes SET flag = 1 WHERE id = ?");
    $stmt->bind_param("i", $finish_vote_id);
    $stmt->execute();
    $stmt->close();

    header("Location: test_main.php");
    exit();
}

// ---------------------------
// タイトル・日付登録
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['title'])) {

    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
    $end_date = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';

    // 3つすべて必須
    if (empty($title) || empty($start_date) || empty($end_date)) {
        $show_error = true;
    } else {
        // 投票データを登録
        $stmt = $conn->prepare(
            "INSERT INTO votes (title, start_date, end_date, account_id, flag)
             VALUES (?, ?, ?, ?, 0)"
        );
        $stmt->bind_param("ssss", $title, $start_date, $end_date, $account_number);

        if ($stmt->execute()) {
            $last_vote_id = $conn->insert_id; // モーダル表示用
        } else {
            echo "<p>保存エラー: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// ---------------------------
// DB接続終了
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

    <!-- エラーメッセージ -->
    <?php if ($show_error): ?>
        <div class="alert alert-danger">
            タイトル・開始日・終了日のすべてを入力してください。
        </div>
    <?php endif; ?>

    <!-- タイトル入力フォーム -->
    <form method="POST" action="">
        <div class="mb-2">
            <label>タイトル：</label>
            <input type="text" name="title" required class="form-control">
        </div>
        <div class="mb-2">
            <label>開始日：</label>
            <input type="date" name="start_date" required class="form-control">
        </div>
        <div class="mb-2">
            <label>終了日：</label>
            <input type="date" name="end_date" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">選択肢を追加</button>
    </form>
</div>

<?php if ($last_vote_id): ?>
<!-- モーダル（選択肢追加） -->
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
            <div class="mb-2">
                <label>選択肢：</label>
                <input type="text" name="senntaku" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">追加</button>
        </form>

        <div id="senntaResult" class="mt-2"></div>

        <hr>

        <!-- 選択肢一覧 -->
        <h5>追加した選択肢：</h5>
        <div id="senntaList">
            <!-- ここに動的に追加 -->
        </div>

        <hr>

        <!-- 完了ボタン -->
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
/* ----------------------------------------
   選択肢 最大 5 個制限 + 削除対応
----------------------------------------- */
const MAX_SENNTAX = 5;

// 個数チェック（追加・削除のたびに呼ぶ）
function checkSenntaLimit() {
    const count = $('#senntaList').children().length;

    if (count >= MAX_SENNTAX) {
        $('input[name="senntaku"]').prop('disabled', true);
        $('#senntaForm button').prop('disabled', true).addClass('btn-secondary');
    } else {
        $('input[name="senntaku"]').prop('disabled', false);
        $('#senntaForm button').prop('disabled', false).removeClass('btn-secondary');
    }
}

// 選択肢追加
$('#senntaForm').on('submit', function(e){
    e.preventDefault();

    $.post('add_sennta.php', $(this).serialize(), function(data){
        $('#senntaResult').html(data);

        const text = $('input[name="senntaku"]').val();

        // 表示追加（削除ボタン付き）
        $('#senntaList').append(`
            <div class="alert alert-secondary py-1 mb-1 d-flex justify-content-between align-items-center">
                ${text}
                <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">
                    削除
                </button>
            </div>
        `);

        $('#senntaForm')[0].reset();

        checkSenntaLimit();
    });
});

// 削除ボタン（動的要素のため on で処理）
$('#senntaList').on('click', '.delete-btn', function(){
    const sennta_id = $(this).data('id');
    const targetDiv = $(this).closest('.alert');

    $.post('delete_sennta.php', { id: sennta_id }, function(res){
        if (res.trim() === 'OK') {
            targetDiv.remove();
            checkSenntaLimit();
        }
    });
});

// 初期ロード時にチェック
checkSenntaLimit();
</script>
<?php endif; ?>

</body>
</html>
