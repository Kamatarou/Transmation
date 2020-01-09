<?php
session_start();
// URLから直接user_noを取ってきてどこの店のページなのかを決める
$user_no = $_GET['shop'];

$_SESSION['manage_flg'] = false;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link href="country.css" rel="stylesheet">
    <title>言語選択</title>
</head>
<body style="text-align: center;">
    <h3>言語を選択してください<br>
    Please select a language</h3>
    <br>
    <br>
    <form action="top.php" method="POST" style="font-size: 24px">
        <input type="hidden" name="user_no" value="<?= $user_no ?>">
        <label><input type="radio" name="language" value="ja" class="radio"><img src="./country/ja.png" class="radio_image"></label>
        <label><input type="radio" name="language" value="ja">日本語</label><br>&nbsp;&nbsp;
        <label><input type="radio" name="language" value="en" class="radio"><img src="./country/america.png" class="radio_image"></label>
        <label><input type="radio" name="language" value="en">English</label><br>&nbsp;&nbsp;
        <label><input type="radio" name="language" value="zh" class="radio"><img src="./country/chaina.png" class="radio_image"></label>
        <label><input type="radio" name="language" value="zh">中國人</label><br>&nbsp;&nbsp;
        <label><input type="radio" name="language" value="ko">한국</label>&nbsp;&nbsp;
        <label><input type="radio" name="language" value="th">ภาษาไทย</label>&nbsp;&nbsp;
        <label><input type="radio" name="language" value="vi">Tiếng việt nam</label>&nbsp;&nbsp;
        <label><input type="radio" name="language" value="ms">Melayu</label><br>
        <br>
        <input type = "submit" style="padding: 10px 50px; font-size:32px; font-weight:bold;" value = "⇒">
    </form>
    <!-- <br>
    <br>
    GETテスト(URLの末尾に「?shop=X」などを付け足すとXの部分が表示される)<br>
    $_GET = 
    <?php
    $shop_no = $_GET['shop'];
    echo $shop_no;
    ?> -->
</body>
</html>