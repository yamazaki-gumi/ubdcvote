<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['account_number'])) {
    echo json_encode(['status' => 'ERROR', 'msg' => 'ログインが必要です']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "toukounaiyou_db");
if ($conn->connect_error) {
    echo json_encode(['status' => 'ERROR', 'msg' => 'DB接続エラー']);
    exit;
}

$vote_id  = $_POST['vote_id'] ?? '';
$title    = trim($_POST['new_title'] ?? '');
$class    = trim($_POST['new_class_id'] ?? '');
$start    = $_POST['new_start'] ?? '';
$end      = $_POST['new_end'] ?? '';

if ($vote_id === '') {
    echo json_encode(['status' => 'ERROR', 'msg' => 'IDが不正です']);
    exit;
}

// 空欄は NULL にする（クラスIDのみ）
$class = ($class === '') ? NULL : $class;

// 必須項目チェック
if ($title === '' || $start === '' || $end === '') {
    echo json_encode(['status' => 'ERROR', 'msg' => 'タイトルと日付は必須です']);
    exit;
}

// 日付チェック
if ($end <= $start) {
    echo json_encode(['status' => 'ERROR', 'msg' => '終了日は開始日より後にしてください']);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE votes
     SET title = ?, class_id = ?, start_date = ?, end_date = ?
     WHERE id = ?"
);
$stmt->bind_param("ssssi", $title, $class, $start, $end, $vote_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'OK']);
} else {
    echo json_encode(['status' => 'ERROR', 'msg' => $stmt->error]);
}

$stmt->close();
$conn->close();
