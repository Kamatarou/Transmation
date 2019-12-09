<?php
session_start();

// POSTされたデータの受け取り
$user_no = $_POST['user_no'];
$description_no = $_POST['description_no'];
$style = intval($_POST['style']);
$requestLan = $_POST['language'];

//データベースへのアクセス
require_once('mysql_connect.php');
$pdo = connectDB();

$sql = "SELECT * FROM `test_table` WHERE `user_no` = $user_no AND `description_no` = $description_no AND `TextLan` = 'ja';";
$stmt = $pdo -> query($sql);
$result = $stmt -> fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>編集・再翻訳および登録画面</title>
</head>
<body>
  <h2 style="text-align: center;">編集・再翻訳および登録画面</h2>
  <p style="text-align: center;">ここでは編集した項目をもとに再度各種言語への翻訳を行い、登録し直す画面です。<br>
  編集したい項目を書き換えて「変更・翻訳して再登録」ボタンを押してください。<br></p>
  <form action="preview_reedit2.php" method="POST" enctype="multipart/form-data">
    <table border="1" style="margin: auto;">
    <tr>
      <td style="text-align: center;">説明見出し</td>
      <td>
        <textarea name="description_text" cols="28" rows="2" style="text-align:center; font-size:32px; padding:20px;"><?= $result['description_text'] ?></textarea>
      </td>
    </tr>
    <?php
    //styleが3の時だけ画像を表示する
    if($style == 3){
    ?>
    <tr>
      <td style="width:200px; overflow-wrap : break-word; text-align: center;">画像(変更したい画像を選択してください。変更したくない場合は何も選択しないでください。)<br>
        <input type="file" name="upfile" size="30"></td>
      <td style="text-align: center;"><img height = "auto" width = "400" src = "image/<?= $result['picture_URL'] ?>"></td>
    </tr>
    <?php
    }
    ?>
    <tr>
      <td style="text-align: center;">説明テキスト</td>
      <td style="text-align: center;"><textarea name="text" cols="69" rows="11"><?= $result['text'] ?></textarea></td>
    </tr>
    </table>
    <input type="hidden" name="user_no" value="<?= $user_no ?>">
    <input type="hidden" name="language" value="ja">
    <input type="hidden" name="description_no" value="<?= $description_no ?>">
    <input type="hidden" name="picture_URL" value="<?= $result['picture_URL'] ?>">       
    <input type="hidden" name="style" value="<?= $style ?>">
    <table border="0" style="margin: auto;">
      <tr>
        <td style="text-align: center;">
        <input type="submit" value="変更・翻訳して再登録">
        </form>
        </td>
        <form action="preview.php" method="POST">
        <input type="hidden" name="user_no" value="<?= $user_no ?>">
        <input type="hidden" name="description_no" value="<?= $description_no ?>">
        <input type="hidden" name="language" value="<?= $requestLan ?>">
        <input type="hidden" name="style" value="<?= $style ?>">
        <td style="text-align: center;">
            <input type="submit" value="前に戻る">
        </td>
      </tr>
    </form>
    <?php
  //   echo '$user_no = '.$user_no.'<br>';
  //   echo '$description_no = '.$description_no.'<br>';
  //   echo '$language = '.$language.'<br>';
  //   echo '$picture_URL = '.$picture_URL.'<br>';
  ?>
</body>
</html>