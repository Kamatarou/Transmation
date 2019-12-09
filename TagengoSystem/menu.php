<?php
session_start();
header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

//データベース接続
require_once('mysql_connect.php');
$pdo = connectDB();

// var_dump($_POST);
if(isset($_POST['user_no'])){
  $user_no = $_POST['user_no'];
}else{
  $user_no = $_SESSION['user_no'];
}

if(strcmp($_POST['first_flg'] , 'false') == 0){
  $composite = $_POST['description'];
  $descript = explode(',' , $composite);
  $description_no = $descript[0];
  $caption = $descript[1];
}else{
  $description_no = $_POST['description_no'];
}
$language = $_POST['language'];

if( isset($_POST['caption']) ){
  $caption = $_POST['caption'];
}elseif( isset($_POST['description_no']) ){
  $description_no = $_POST['description_no'];
  $sql = "SELECT `caption` FROM `top_menu` WHERE `user_no` = $user_no AND `description_no` = $description_no AND `style` = 1;";
  $stmt = $pdo -> query($sql);
  $result = $stmt -> fetch(PDO::FETCH_ASSOC);
  $caption = $result['caption'];
}

//タイトル用の配列
$title = array(
  'ja' => 'メニュー',
  'en' => 'Menu',
  'zh' => '菜单',
  'ko' => '메뉴',
  'th' => 'เมนู',
  'vi' => 'Thực đơn',
  'ms' => 'Menu'
);

//「戻る」メッセージ用の配列
$back = array(
  'ja' => 'プレビュー画面へ戻る',
  'en' => 'Return',
  'zh' => '回去',
  'ko' => '돌아 가기',
  'th' => 'กลับ',
  'vi' => 'Quay lại',
  'ms' => 'Kembali'
);


if( strcmp($_POST['first_flg'] , 'true') == 0 ){
  $caption = 'メニュー'.$description_no;
  //データベースに登録
  $sql = "INSERT INTO `top_menu` VALUES('$user_no' , '$description_no' , '1' , 0 , '$caption');";
  $stmt = $pdo -> query($sql);

}

if( isset( $_POST['change_caption'] ) ){
  $description_no = $_POST['description_no'];
  $sql = "UPDATE `top_menu` SET `caption` = '$caption' WHERE `user_no` = $user_no AND `description_no` = $description_no AND `style` = 1;";
  $stmt = $pdo -> query($sql);
}

$sql = "SELECT menu_no , menu_name , pic_URL , price FROM `food_menu` WHERE user_no = '$user_no' AND description_no = '$description_no' AND language = '$language';";
$stmt = $pdo -> query($sql);
$row = $stmt -> rowCount();	//何件あるか数える
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link href="menu.css" rel="stylesheet">
  <link href="top.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  <script src="top_modal.js"></script>
  <title><?= $title[$language] ?></title>
  <body class="back" style="text-align: center;">
  <div>
  <?php
  if($_SESSION['manage_flg'] == true){
  ?>
  <div><span id="output" style="font-size: 3.5em; font-weight: bold;"><?= $caption ?></span>&nbsp;&nbsp;<button type="button" class="modal-syncer" data-target="modal-content-a">タイトルの変更</button></div>
  <?php

  }else{
  
  ?>
  <b style="font-size: 3.5em"><?= $caption ?></b>
  <?php
  }
  ?>
  <HR style="margin: 3em 0 ;">
  <table border="1" style="margin: auto;">
    <?php

    //データベースをSQLで検索して出てきたデータをすべて表示する
    for($i = 0; $i < $row; $i++){
      $result = $stmt -> fetch(PDO::FETCH_ASSOC);
    
    ?>
    <tr>
      <form action="menu_detail.php" name="form1" method="POST">
        <input type="hidden" name="user_no" value="<?= $user_no ?>">
        <input type="hidden" name="description_no" value="<?= $description_no ?>">
				<input type="hidden" name="menu_no" value="<?= $result['menu_no'] ?>">
        <input type="hidden" name="language" value="<?= $language ?>">
        <?php
        if($row == 1){
        ?>
        <td class="left"><a href="javascript:form1.submit()" class="menu"><img src="foodmenu/<?= $result['pic_URL'] ?>" height="128" width="auto" style="float: left;"></a></td>
        <td class="center"><a href="javascript:form1.submit()" class="menu"><?= $result['menu_name'] ?></a></td>
        <td class="center"><a href="javascript:form1.submit()" class="menu">￥<?= $result['price'] ?>-</a></td>
        <?php
        }else{
        ?>
        <td class="left"><a href="javascript:form1[<?= $i ?>].submit()" class="menu"><img src="foodmenu/<?= $result['pic_URL'] ?>" height="128" width="auto" style="float: left;"></a></td>
        <td class="center"><a href="javascript:form1[<?= $i ?>].submit()" class="menu"><?= $result['menu_name'] ?></a></td>
        <td class="center"><a href="javascript:form1[<?= $i ?>].submit()" class="menu">￥<?= $result['price'] ?>-</a></td>
        <?php
        }
        ?>
      </form>
    </tr>
    <?php

    }

    //管理者モードの場合だけ新規登録を行う項目を追加
    if($_SESSION['manage_flg'] == true){
    ?>
    <tr>
      <form action="foodmenu_input.php" name="formI" method="POST">
        <input type="hidden" name="user_no" value="<?= $user_no ?>">
        <input type="hidden" name="description_no" value="<?= $description_no ?>">
        <input type="hidden" name="menu_no" value="<?= $result['menu_no'] ?>">
        <td class="left"><a href="javascript:formI.submit()" class="menu"> </a></td>
        <td class="center"><a href="javascript:formI.submit()" class="menu">新規登録</a></td>
        <td class="center"><a href="javascript:formI.submit()" class="menu"> </a></td>
      </form>
    </tr>
    <?php
    }
    ?>
  </table>
  <br>
  <table border="0" style="margin: auto;">
    <tr>
      <?php
      if($_SESSION['manage_flg'] == true){
      ?>
      <td>
        <form action="management.php" method="POST">
          <input type="hidden" name="user_no" value="<?= $user_no ?>">
          <input type="hidden" name="language" value="<?= $language ?>">
          <input type="submit" value="案内登録画面へ戻る">
        </form>
      </td>
      <?php
      }
      ?>
      <td>
        <form action="top.php" method="POST">
          <input type="hidden" name="user_no" value="<?= $user_no ?>">
          <input type="hidden" name="language" value="<?= $language ?>">
          <?php
            if($_SESSIOM['manage_flg'] == true){
          ?>
              <input type="submit" style="font-size: 2em;" value="前の画面へ戻る">
          <?php
            }else{
          ?>
              <input type="submit" value="<?= $back[$language] ?>">
          <?php
            }
          ?>
          </a>
        </form>
      </td>
    </tr>
  </table>
<!-- $_SESSION['manage_flg'] = <?= $_SESSION['manage_flg'] ?><br>
$_POST['user_no'] = <?= $_POST['user_no'] ?><br>
$_POST['description_no'] = <?= $_POST['description_no'] ?><br>
$_POST['language'] = <?= $_POST['language'] ?><br>
$language = <?= $language ?><br>
$_POST['caption'] = <?= $_POST['caption'] ?><br>
$composite = <?= $composite ?><br>
$description_no = <?= $description_no ?><br>
$caption = <?= $caption ?><br>
$_POST['first_flg'] = <?= $_POST['first_flg'] ?><br> -->
</div>

<div id="modal-content-a" class="modal-content">
  <h3>タイトルの変更</h3>
  <p>
    <form action="menu.php" method="POST">
    <input type="hidden" name="user_no" value="<?= $user_no ?>">
    <input type="hidden" name="description_no" value="<?= $description_no ?>">
    <!-- <input type="hidden" name="menu_no" value="<?= $result['menu_no'] ?>"> -->
    <input type="hidden" name="language" value="<?= $language ?>">
    <input type="hidden" name="change_caption" value="true">
    <input type="hidden" name="first_flg" value="false">
    <input type="text" name="caption" value="<?= $caption ?>">
    <input type="text" name="dummy" style="display:none;">  <!-- Enterで画面遷移してしまうのを防ぐ -->
    <input type="submit" value="タイトルの変更">
    <button type="button" class="modal-close">キャンセル</button>
    </form>
  </p>
</div>

</body>
</html>