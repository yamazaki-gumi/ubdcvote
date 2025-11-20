<?php
// update_vote.php
// AjaxからPOSTで送られてくる: vote_id, title, start_date, end_date
header('Content-Type: application/json; charset=utf-8');
 
session_start();
if (!isset($_SESSION['account_number'])) {
    echo json_encode(['status' => 'ERROR', 'msg' => 'ログインが必要です']);
    exit();
}
 
$vote_id = isset($_POST['vote_id']) ? intval($_POST['vote_id']) : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? $_POST['start_date'] : null;
$end_date = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? $_POST['end_date'] : '9999-12-31';
 
if ($vote_id <= 0) {
    echo json_encode(['status' => 'ERROR', 'msg' => '不正なvote_id']);
    exit();
}
 
// DB接続（必要に応じて修正）
$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    echo json_encode(['status' => 'ERROR', 'msg' => 'DB接続失敗']);
    exit();
}
 
// UPDATE 文（vote_id のみを更新）
$stmt = $conn->prepare("UPDATE votes SET title = ?, start_date = ?, end_date = ? WHERE id = ?");
if (!$stmt) {
    echo json_encode(['status' => 'ERROR', 'msg' => 'ステートメント準備失敗']);
    exit();
}
$stmt->bind_param("sssi", $title, $start_date, $end_date, $vote_id);
$ok = $stmt->execute();
if ($ok) {
    echo json_encode(['status' => 'OK']);
} else {
    echo json_encode(['status' => 'ERROR', 'msg' => $stmt->error]);
}
$stmt->close();
$conn->close();
 