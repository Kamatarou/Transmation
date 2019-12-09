<?php
// 送られてきたPOSTデータを変数に
$user_no = $_POST['user_no'];
$style = $_POST['style'];

// var_dump($_POST);
// echo '<br>';<!DOCTYPE html>

?>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>案内登録画面</title>
</head>
<body>
    <h2>ここは案内の登録画面です</h2>
    以下の項目を登録してください。<br>
    <!-- <br>
    <br>
    確認用<br>
    $_SESSION['user_no'] = <?= $_SESSION['user_no'] ?><br>
    <br> -->
    <form action = "preview_input2.php" method = "POST" enctype="multipart/form-data">
    <table border = "1">
        <tr height = "32">
            <th>
                <textarea style = "font-size:32px;" name="description_text" cols="31" rows="2"><?= $_POST['caption'] ?></textarea>
            </th>
        </tr>
        <tr>
            <td style = "text-align:center;">
                アップロードしたい画像(不要の場合は何もしないでください)<br><input type="file" name="upfile" height = "400" width = "auto">
            </td>
        </tr>
        <tr height = "128">
            <td style = "text-align:center;">
                <textarea name="text" cols="71" rows="11">ここに案内内容を入力してください
                例)この店でクレジットカードを使用することはできません</textarea>
            </td>
        </tr>
    </table>
    <div style="width: 500px; text-align: center; margin: 10px;">
      <input type="hidden" name="user_no" value="<?= $user_no ?>">
      <input type="hidden" name="style" value="<?= $style ?>">
      <input type="submit" value="登録">
    </div>
    </form>
    <?php
      // echo '$user_no = '.$user_no;
      // echo '$description_no = '.$description_no;
      // echo '$language = '.$language;
    ?>
</body>
</html>