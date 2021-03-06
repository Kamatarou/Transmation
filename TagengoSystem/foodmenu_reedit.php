<?php
  session_start();

  $user_no = $_POST['user_no'];
  $description_no = $_POST['description_no'];
  $menu_no = $_POST['menu_no'];

  //データベース接続
  require_once('mysql_connect.php');
  $pdo = connectDB();

  $sql = "SELECT * FROM `food_menu` WHERE `user_no` = $user_no AND `description_no` = $description_no AND `menu_no` = $menu_no AND `language` = 'ja';";
  $stmt = $pdo -> query($sql);
  $result = $stmt -> fetch(PDO::FETCH_ASSOC);

  //ピクトグラム選択肢表示用の配列
  $message = array(
                    '&nbsp;&nbsp;&nbsp;卵&nbsp;&nbsp;&nbsp;&nbsp;<img src="food_pictogram/1egg.PNG">' ,
                    '乳製品&nbsp;<img src="food_pictogram/2dairyproduct.PNG">' ,
                    '&nbsp;小麦&nbsp;&nbsp;&nbsp;<img src="food_pictogram/3wheat.PNG">' ,
                    '&nbsp;そば&nbsp;&nbsp;&nbsp;<img src="food_pictogram/4soba.PNG">' ,
                    '落花生&nbsp;<img src="food_pictogram/5peanuts.PNG">' ,
                    '&nbsp;えび&nbsp;&nbsp;<img src="food_pictogram/6shrimp.PNG">' ,
                    '&nbsp;かに&nbsp;&nbsp;<img src="food_pictogram/7crab.PNG">' ,
                    '&nbsp;牛肉&nbsp;&nbsp;<img src="food_pictogram/20beef.PNG">' ,
                    '&nbsp;豚肉&nbsp;&nbsp;<img src="food_pictogram/21pork.PNG">' ,
                    '&nbsp;鶏肉&nbsp;&nbsp;<img src="food_pictogram/22chicken.PNG">'
                  );
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link href="menu.css" rel="stylesheet">
  <title>メニューの編集</title>
</head>
<body class="back">
<form action="foodmenu_reedit2.php" method="POST" enctype="multipart/form-data">
  <table border="1" class="display">
    <tr>
      <td class="display">
        <b><textarea name="menu_name" cols="17" rows="2" style="text-align:center; font-size: 48px;"><?= $result['menu_name'] ?></textarea></b>
      </td>
    </tr>
    <tr>
      <td class="display" style="text-align:center;"><img height = "auto" width = "440" src = "foodmenu/<?= $result['pic_URL'] ?>"></td>
    </tr>
    <tr>
      <td class="display" style="text-align:center;">別の画像に変更したい場合ここからアップロード<input type="file" name="upfile"　height = "400" width = "auto"></td>
    </tr>
  </table>
  <table border="1" class="display">
    <tr height = "48">
      <td style="height: 48px;">
        <b style="vertical-align: center;">料理に使用しているアレルギー主要7品目+肉類</b>
      </td>
      <td class="left">
      <?php
        for($i = 0; $i < 10; $i++){
          if($i == 5){
      ?>
            </td>
            <td class="right">
          <?php
          }
          if( strcmp($result['allergy'][$i], '1') == 0 ){
          ?>
            <input type="checkbox" name="allergy[]" value="<?= $i ?>" checked="checked"><?= $message[$i] ?><br>
      <?php
          }else{
      ?>
            <input type="checkbox" name="allergy[]" value="<?= $i ?>"><?= $message[$i] ?><br>
      <?php
          }
        }
      ?>
      </td>
    </tr>
  </table>
  <table border="1" class="display">
    <tr>
      <td><textarea name="detail" cols="60" rows="8"><?= $result['detail'] ?></textarea></td>
    </tr>
    <tr>
      <td><b>価格：</b><input type="number" name="price" value="<?= $result['price'] ?>">価格を入力してください</td>
    </tr>
  </table>
  <br>
  <table border="0" class="display">
    <tr>
      <td style="text-align: center;">
        <input type="hidden" name="user_no" value="<?= $user_no ?>">
        <input type="hidden" name="description_no" value="<?= $description_no ?>">
        <input type="hidden" name="menu_no" value="<?= $menu_no ?>">
        <input type="hidden" name="pic_URL" value="<?= $result['pic_URL'] ?>">
        <input type="submit" value="再登録">
      </td>
    </tr>
  </table>
</form>
<!-- <br>
  $user_no = <?= $user_no ?><br>
  $menu_no = <?= $menu_no ?><br>
  $language = <?= $_POST['language'] ?><br>
  $menu_name = <?= $_POST['menu_name'] ?><br>
  $pic_URL = <?= $_POST['pic_URL'] ?><br>
  $_POST['allergy'] = <?= $_POST['allergy'] ?><br>
  $detail = <?= $_POST['detail'] ?><br>
  $price = <?= $_POST['price'] ?><br> -->
</body>
</html>