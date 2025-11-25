<?php
session_start();
//aaaaa
// ログインチェック
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
    完了ボタン押されたら test_main.php へ
-----------------------------------------------------*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['finish_vote_id'])) {
    $finish_vote_id = $_POST['finish_vote_id'];
 
    $stmt = $conn->prepare("UPDATE votes SET flag = 1 WHERE id = ?");
    $stmt->bind_param("i", $finish_vote_id);
    $stmt->execute();
    $stmt->close();
 
    header("Location: sakuseikannryo.php");
    exit();
}
 
/* ---------------------------------------------------
    タイトルを登録（選択肢入力ボックス表示）
-----------------------------------------------------*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['title'])) {
    $title = $_POST['title'];
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
    $end_date = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';

    // 3つすべて必須
    if (empty($title) || empty($start_date) || empty($end_date)) {
        $show_error = true;
    } else if ($end_date < $start_date) {
        echo "<p style='color:red; text-align:center;'>※終了日は開始日以降の日付を入力してください。</p>";
    } else {
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
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>タイトル登録</title>
 
<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
 
<!-- 外部CSS -->
<link rel="stylesheet" href="gamen10.css?v=<?php echo time(); ?>">
 
</head>
<body>
 
<!-- 画面右上の戻るボタン（常に表示） -->
<a href="main.php" class="btn btn-secondary return-btn">戻る</a>
 
<div class="container-center">
    <div class="main-box">
        <h2>タイトル登録</h2>
        <h1>ようこそ <?php echo htmlspecialchars($name); ?> さん</h1>

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
</div>
 
<?php if ($last_vote_id): ?>
 
<?php
$vote_title = "";
$vote_start_date = "";
$vote_end_date = "";
 
// 投票情報を取得
$stmt = $conn->prepare("
    SELECT title, start_date, end_date
    FROM votes
    WHERE id = ?
");
$stmt->bind_param("i", $last_vote_id);
$stmt->execute();
$stmt->bind_result($vote_title, $vote_start_date, $vote_end_date);
$stmt->fetch();
$stmt->close();
?>
 
<!-- 選択肢追加ボックス（モーダル廃止 → ページ内固定フォーム） -->
<div class="option-box">
    <div class="option-header d-flex justify-content-between align-items-start">
        <div>
            <h4 id="optionHeaderTitle">選択肢を追加（投票ID: <?php echo $last_vote_id; ?>）</h4>
            <div class="small text-muted" id="voteMeta">
                タイトル：<span id="displayTitle"><?php echo htmlspecialchars($vote_title); ?></span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                開始日：<span id="displayStart"><?php echo htmlspecialchars($vote_start_date); ?></span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                終了日：<span id="displayEnd"><?php echo htmlspecialchars($vote_end_date); ?></span>
            </div>
        </div>
 
        <!-- 右上に変更ボタン -->
        <div>
            <button id="changeBtn" class="btn btn-outline-primary btn-sm">変更</button>
        </div>
    </div>
 
    <!-- 変更フォーム（最初は非表示、Ajaxでupdate） -->
    <div id="changeFormWrap" style="display:none; margin-top:12px;">
        <form id="changeForm" class="row g-2">
            <input type="hidden" name="vote_id" value="<?php echo $last_vote_id; ?>">
            <div class="col-12">
                <input type="text" name="new_title" id="newTitle" class="form-control" placeholder="タイトル" required>
            </div>
            <div class="col-6">
                <input type="date" name="new_start" id="newStart" class="form-control" placeholder="開始日">
            </div>
            <div class="col-6">
                <input type="date" name="new_end" id="newEnd" class="form-control" placeholder="終了日">
            </div>
            <div class="col-12">
                <button type="submit" id="saveChangeBtn" class="btn btn-success btn-sm">保存</button>
                <button type="button" id="cancelChangeBtn" class="btn btn-secondary btn-sm">キャンセル</button>
                <span id="changeMsg" class="ms-2"></span>
            </div>
        </form>
    </div>
 
    <hr>
 
    <!-- 選択肢入力フォーム -->
    <form id="senntaForm" class="mb-2">
        <input type="hidden" name="title_id" value="<?php echo $last_vote_id; ?>">
        <input type="text" id="senntaInput" name="senntaku" class="form-control input-small mb-2" placeholder="選択肢" required>
        <button type="submit" id="senntaAddBtn" class="btn btn-success">追加</button>
    </form>
 
    <h5>追加した選択肢：</h5>
    <div id="senntaList"></div>
 
    <hr>
 
    <!-- 投稿ボタン -->
    <form method="POST">
        <input type="hidden" name="finish_vote_id" value="<?php echo $last_vote_id; ?>">
        <button type="submit" class="btn btn-primary w-100">投稿</button>
    </form>
</div>
<?php endif; ?>
 
<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
 
<!-- 外部JS -->
<script src="gamen10.js"></script>
 
</body>
</html>
 
 