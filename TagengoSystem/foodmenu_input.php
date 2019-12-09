<?php
session_start();
header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

$user_no = $_POST['user_no'];
$description_no = $_POST['description_no'];

//データベース接続
require_once('mysql_connect.php');
$pdo = connectDB();

//入力時のmenu_noを求める(すでにあるnoとかぶらないように&その最大値+1)
$sql = "SELECT MAX(menu_no) AS `max_no` FROM `food_menu` WHERE user_no = '$user_no';";
$stmt = $pdo -> query($sql);
$result = $stmt -> fetch(PDO::FETCH_ASSOC);
$input_no = $result['max_no'];

if(is_null($input_no)){
    $input_no = 1;
}else{
    $input_no += 1;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link href="menu.css" rel="stylesheet">
    <title>メニュー登録</title>
</head>
<body class="back">
<form action="foodmenu_input2.php" method="POST" enctype="multipart/form-data">
  <table border="1" class="display">
    <tr>
      <td class="display" style="text-align:center; font-size: 48px;">
        <b><textarea name="menu_name" cols="60" rows="4">ここにメニュー名を入力</textarea></b>
      </td>
    </tr>
    <tr>
      <td class="display" style="text-align:center;">メニューの画像をアップロード<input type="file" name="upfile"　height = "400" width = "auto"></td>
    </tr>
  </table>
  <table border="1" class="display">
    <tr height = "48">
      <td style="height: 48px;">
        <b style="vertical-align: center;">料理に使用しているアレルギー主要7品目+肉類にチェックを入れてください</b>
      </td>
      <td class="left">
        <input type="checkbox" name="allergy[]" value='0'>&nbsp;&nbsp;&nbsp;卵&nbsp;&nbsp;&nbsp;&nbsp;<img src="food_pictogram/1egg.PNG"><br>
        <input type="checkbox" name="allergy[]" value='1'>乳製品&nbsp;<img src="food_pictogram/2dairyproduct.PNG"><br>
        <input type="checkbox" name="allergy[]" value='2'>&nbsp;小麦&nbsp;&nbsp;&nbsp;<img src="food_pictogram/3wheat.PNG"><br>
        <input type="checkbox" name="allergy[]" value='3'>&nbsp;そば&nbsp;&nbsp;&nbsp;<img src="food_pictogram/4soba.PNG"><br>
        <input type="checkbox" name="allergy[]" value='4'>落花生&nbsp;<img src="food_pictogram/5peanuts.PNG"><br>
      </td>
      <td class="right">
        <input type="checkbox" name="allergy[]" value='5'>&nbsp;えび&nbsp;&nbsp;<img src="food_pictogram/6shrimp.PNG"><br>
        <input type="checkbox" name="allergy[]" value='6'>&nbsp;かに&nbsp;&nbsp;<img src="food_pictogram/7crab.PNG"><br>
        <input type="checkbox" name="allergy[]" value='7'>&nbsp;牛肉&nbsp;&nbsp;<img src="food_pictogram/20beef.PNG"><br>
        <input type="checkbox" name="allergy[]" value='8'>&nbsp;豚肉&nbsp;&nbsp;<img src="food_pictogram/21pork.PNG"><br>
        <input type="checkbox" name="allergy[]" value='9'>&nbsp;鶏肉&nbsp;&nbsp;<img src="food_pictogram/22chicken.PNG"><br>
      </td>
    </tr>
  </table>
  <table border="1" class="display">
    <tr>
      <td><textarea name="detail" cols="60" rows="8">ここに料理の説明を入力</textarea></td>
    </tr>
    <tr>
      <td><b>価格：</b><input type="number" name="price">価格を入力してください</td>
    </tr>
  </table>
  <input type="hidden" name="user_no" value="<?= $user_no ?>">
  <input type="hidden" name="description_no" value="<?= $description_no ?>">
  <input type="hidden" name="menu_no" value="<?= $input_no ?>">
  <input type="submit" value="プレビュー">
  </form>
  <!-- $_SESSION['user_no'] = <?= $_SESSION['user_no'] ?><br>
  $_POST['user_no']; = <?= $_POST['user_no']; ?><br>
  $result['max_no'] = <?= $result['max_no'] ?><br>
  $manage_flg = <?= $_SESSION['manage_flg'] ?><br>
  $input_no = <?= $input_no ?><br> -->

</body>
</html>