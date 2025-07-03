<!-- C:\xampp\htdocs\student_login\welcome.php -->
<?php
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8') : 'ゲスト';
$student_number = isset($_GET['student_number']) ? htmlspecialchars($_GET['student_number'], ENT_QUOTES, 'UTF-8') : '0';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ようこそ</title>
</head>
<body>
  <h1><?php echo $name; ?>さん、ようこそ！</h1>
</body>
</html>
