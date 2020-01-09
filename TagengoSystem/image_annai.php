<?php
header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

$user_no = $_POST['user_no'];
$description_no = $_POST['description_no'];
$language = $_POST['language'];
require_once('mysql_connect.php');
$pdo = connectDB();
$sql = "SELECT * FROM `scale_link2` WHERE `user_no` = '$user_no' AND `description_no` = '$description_no';";
 $stmt = $pdo -> query($sql);
 $rows=$stmt->rowCount();
     
// リンク先を決定するための配列(styleの数字に応じて変化する)
$link = ['0' , 'menu.php' , 'preview.php' , 'preview.php' , 'newSwipe.php' , 'image_annai.php'];

//「戻る」メッセージ用の配列
$back = array(
  'ja' => '戻る',
  'en' => 'Return',
  'zh' => '回去',
  'ko' => '돌아 가기',
  'th' => 'กลับ',
  'vi' => 'Quay lại',
  'ms' => 'Kembali'
);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <script src="image_annai.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  <title>Document</title>
</head>
<body>
<table border="0" style="margin: auto">
<tr>
  <th style="font-size: 2em;">
    <?= $_POST['caption'] ?>
  </th>
</tr>
<tr>
  <td>
  <div>
  <canvas id="imgCanvas" style="z-index: 1;"></canvas>
  <?php
  for($i = 0; $i < $rows; $i++){
    $result = $stmt -> fetch(PDO::FETCH_ASSOC);
    if($i==0){
      ?>
      <img id="img_source" src="image/<?=$result['img_url']?>" usemap="#Map" alt="メニュー" style="z-index: 0;">
      <map name="Map">
  <?php
    }

    ?>
    <form action="<?= $link[ $result['link_style'] ] ?>" name="form" method="post">
    <input type="hidden" name="user_no" value="<?=$user_no?>">
    <input type="hidden" name="style" value="<?=$result['link_style']?>">
    <input type="hidden" name="language" value="<?=$language?>">
    <input type="hidden" name="description_no" value="<?=$result['link_description_no']?>">
    <?php
    //1件しかリンク先がないとき、formのnameは"form[0]"にならず"form"のままなので処理を変える
    if($rows == 1){
    ?>
    <script>Draw(<?=$result['rect_rigth']?>,<?=$result['rect_top']?>,<?=$result['rect_left']?>,<?=$result['rect_botom']?>)</script>
    <area shape="rect" coords="<?=$result['rect_rigth']?>,<?=$result['rect_top']?>,<?=$result['rect_left']?>,<?=$result['rect_botom']?>" href="javascript:form.submit()">
    <?php
    }else{
    ?>
    <script>Draw(<?=$result['rect_rigth']?>,<?=$result['rect_top']?>,<?=$result['rect_left']?>,<?=$result['rect_botom']?>)</script>
    <area shape="rect" coords="<?=$result['rect_rigth']?>,<?=$result['rect_top']?>,<?=$result['rect_left']?>,<?=$result['rect_botom']?>" href="javascript:form[<?= $i ?>].submit()">
    
    <?php
    }
    ?>
    </form>
    <?php
}
?>
  </map>
  </img>
  </div>
  </td>
</tr>
<tr>
  <td style="font-size: 1em; text-align: center;">
    <form method="POST" name="form1" action="top.php">
    <input type="hidden" name="user_no" value="<?= $user_no ?>">
      <a href="javascript:form1.submit()">
      <?php
        if($_SESSION['manage_flg'] == true){
          echo '前の画面へ戻る';
        }else{
          echo $back[$language];
        }
      ?>
      </a>
    </form>
  </td>
</tr>
</table>
</body>
</html>