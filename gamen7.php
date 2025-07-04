<?php
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8') : 'ゲスト';
$student_number = isset($_GET['student_number']) ? htmlspecialchars($_GET['student_number'], ENT_QUOTES, 'UTF-8') : '0';
?>
<script>
    function vote_start(){
        window.location.href="http://localhost/ubdcvote/gamen10.html"
    }
</script>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBDC VOTE</title>
    <link rel="stylesheet" href="gamen7.css">
</head>
<body>
    <p id="text">UBDC VOTE</p>
    <div class="centering_parent ">
        <div class="button-wrap">
            <button class="button1">投票する</button>
            <div class="text1">
                <p>投票したい投票箱を選択し</p>
                <p>投票することができます。</p>
            </div>
        </div>
        <div class="button-swap">
            <button class="button2" onclick="vote_start()">投票を作成する</button>
            <div class="text1">
                <p>投票箱の作成や</p>
                <p>名簿の作成ができます</p>
            </div>
        </div>
    </div>
    <div id="btn">
        <button><?php echo $name; ?></button><br>
        <button >ログアウト</button>
    </div>
</body>