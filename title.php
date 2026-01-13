<?php
session_start();
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
$error_msg = '';
$class_id = '';

/* --------------------------------
   投稿完了処理
---------------------------------*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['finish_vote_id'])) {
    $finish_vote_id = $_POST['finish_vote_id'];

    $stmt = $conn->prepare("UPDATE votes SET flag = 1 WHERE id = ?");
    $stmt->bind_param("i", $finish_vote_id);
    $stmt->execute();
    $stmt->close();

    header("Location: sakuseikannryo.php");
    exit();
}

/* --------------------------------
   タイトル登録処理（クラスID空欄OK）
---------------------------------*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    $class_id = trim($_POST['class_id']);

    // ★ 空欄なら NULL にする
    $class_id = ($class_id === '') ? NULL : $class_id;

    $start_date = trim($_POST['start_date']);
    $end_date = trim($_POST['end_date']);

    // ★ class_id は必須にしない
    if (empty($title) || empty($start_date) || empty($end_date)) {
        $error_msg = "タイトルと日付を入力してください";
    } else if ($end_date <= $start_date) {
        $error_msg = "終了日は開始日より後にしてください";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO votes (title, class_id, start_date, end_date, account_id, flag)
             VALUES (?, ?, ?, ?, ?, 0)"
        );
        $stmt->bind_param("sssss", $title, $class_id, $start_date, $end_date, $account_number);

        if ($stmt->execute()) {
            $last_vote_id = $conn->insert_id;
        } else {
            $error_msg = "保存エラー: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>タイトル登録</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="gamen10.css?v=<?= time(); ?>">
</head>
<body>

<a href="main.php" class="btn btn-secondary return-btn">戻る</a>

<div class="container-center">
    <div class="main-box">
        <h2>タイトル登録</h2>
        
        <?php if ($error_msg): ?>
            <p style="color:red; text-align:center;"><?= $error_msg ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-2">
                <label>タイトル：</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <!-- クラスID（空欄OK） -->
            <div class="mb-2">
                <label>クラスID：</label>
                <input type="text" name="class_id" class="form-control" placeholder="例：US4A、未入力で全体公開">
            </div>

            <div class="mb-2">
                <label>開始日：</label>
                <input type="date" name="start_date" class="form-control" required>
            </div>

            <div class="mb-2">
                <label>終了日：</label>
                <input type="date" name="end_date" class="form-control" required>
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
$vote_class_id = "";

$stmt = $conn->prepare("
    SELECT title, class_id, start_date, end_date
    FROM votes
    WHERE id = ?
");
$stmt->bind_param("i", $last_vote_id);
$stmt->execute();
$stmt->bind_result($vote_title, $vote_class_id, $vote_start_date, $vote_end_date);
$stmt->fetch();
$stmt->close();
?>

<div class="option-box">
    <div class="option-header d-flex justify-content-between align-items-start">
        <div>
            <h4>選択肢を追加</h4>
            <div class="small text-muted">
                タイトル：<span id="displayTitle"><?= htmlspecialchars($vote_title); ?></span><br>

                クラスID：
                <span id="displayClassId">
                <?= $vote_class_id === NULL ? '全体公開' : htmlspecialchars($vote_class_id); ?>
                </span><br>

                開始日：<span id="displayStart"><?= htmlspecialchars($vote_start_date); ?></span>
                        &nbsp;|&nbsp;
                終了日：<span id="displayEnd"><?= htmlspecialchars($vote_end_date); ?></span>
            </div>

        </div>

        <div>
            <button id="changeBtn" class="btn btn-outline-primary btn-sm">変更</button>
        </div>
    </div>

    <div id="changeFormWrap" style="display:none; margin-top:12px;">
        <form id="changeForm" class="row g-2">
            <input type="hidden" name="vote_id" value="<?= $last_vote_id; ?>">
            <div class="col-12">
                <input type="text" name="new_title" id="newTitle" class="form-control" placeholder="タイトル" required>
            </div>
            <div class="col-12">
                <input type="text" name="new_class_id" id="newClassId" class="form-control" placeholder="クラスID（空欄で全体公開）">
            </div>
            <div class="col-6">
                <input type="date" name="new_start" id="newStart" class="form-control">
            </div>
            <div class="col-6">
                <input type="date" name="new_end" id="newEnd" class="form-control">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success btn-sm">保存</button>
                <button type="button" id="cancelChangeBtn" class="btn btn-secondary btn-sm">キャンセル</button>
                <span id="changeMsg" class="ms-2"></span>
            </div>
        </form>
    </div>

    <hr>

    <form id="senntaForm" class="mb-2">
        <input type="hidden" name="title_id" value="<?= $last_vote_id; ?>">
        <input type="text" id="senntaInput" name="senntaku" class="form-control mb-2" placeholder="選択肢" required>
        <button type="submit" class="btn btn-success">追加</button>
    </form>

    <h5>追加した選択肢：</h5>
    <div id="senntaList"></div>

    <hr>

    <form method="POST">
        <input type="hidden" name="finish_vote_id" value="<?= $last_vote_id; ?>">
        <button type="submit" class="btn btn-primary w-100">投稿</button>
    </form>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="gamen10.js"></script>
</body>
</html>
