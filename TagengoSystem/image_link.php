<?php
session_start();
// var_dump($_POST);
$user_no = $_POST['user_no'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link href="top.css" rel="stylesheet">
  <script src="TitleChange.js"></script>
  <script src="image_link.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  <script src="top_modal.js"></script>
  <title>リンク先の指定</title>
  <style>
  #scale{position: absolute; right: 0}
  #img_source{width:800px;height:auto;}
  </style>
</head>
<body ondrop="onDrop(event);" ondragover="onDragOver(event);" >
<div>
<?php
if(strcmp($_POST['first_flg'] , 'true') == 0){
  //データベース接続
  require_once('mysql_connect.php');
  $pdo = connectDB();

  $sql = "SELECT MAX(description_no) AS recent_no FROM `top_menu` WHERE `user_no` = '$user_no' AND `style` = '5';";
  $stmt = $pdo -> query($sql);
  $result = $stmt -> fetch(PDO::FETCH_ASSOC);
  $description_no = $result['recent_no'];
  if(is_null($description_no)){
    //$description_noが0の場合まだ1件も登録されていないということなので1に
    $description_no = 1;
  }else{
    //それ以外の場合は最大値を求めているはずなので+1
    $description_no += 1;
  }
  $caption = '画像リンク'.$description_no;
?>
  <input type="hidden" name="first_flg" value="true">
<?php
}else{
  $caption = $_POST['caption'];
  $description_no = $_POST['description_no'];
}
?>
<div><span id="output" style="font-size: 24px; font-weight: bold;"><?= $caption ?></span>&nbsp;&nbsp;<button type="button" class="modal-syncer" data-target="modal-content-a">タイトルの変更</button></div>
<HR style="margin: 3em 0 ;">
<form action="link_touroku.php" method="POST" enctype="multipart/form-data">
  <input type="hidden" id="formin" name="caption" value="<?= $caption ?>">
  <script>
    getTitle("<?= $caption ?>");
  </script>
  <?php
    if(isset($_POST['inputfile'])){
  ?>
    <img id="img_source" name="img_source" src="image/<?=$_POST['inputfile']?>" style="display:none;">
    <input type="hidden" name="used_img" value="<?=$_POST['inputfile']?>">
  <?php
  }else{
    ?><input type="file" id="inputfile" name="inputfile" accept="image/jpeg,image/png,image/gif,image/bmp,image/x-icon" onchange="onAddFile(event);">
    
  <img id="img_source" name="img_source" style="display:none;">
  <?php
  }
  ?>
    <p></p> 
  <canvas id="SrcCanvas" onmousedown="OnMousedown(event);" onmousemove="OnMousemove(event);" onmouseup="OnMouseup(event);"></canvas>
  <?php
  if(isset($_POST['inputfile'])){
  ?>
    <script>
      sizecheck();
    </script>
  <?php
  }
  ?>
  <span id="scale">
  <h3>左上の座標</h3>
   x: <input type="text" name="left" id="xleft">
   y: <input type="text" name="top" id="ytop">
   <h3>右下の座標</h3>
   x: <input type="text" name="right" id="right">
   y: <input type="text" name="botom" id="ybotom">
   <br>
   <h3>リンク先の指定</h3>
   <select id="sel" name="select">
     <option> --場所を選ぶ-- </option>
     <?php
$user_no = $_POST['user_no'];
require_once('mysql_connect.php');
$pdo = connectDB();

$sql = "SELECT description_no , style , caption FROM top_menu WHERE user_no = $user_no;";
$stmt = $pdo -> query($sql);
$row = $stmt -> rowCount(); //行数のカウント(何件ヒットしたかを調べる)

for($i = 0; $i < $row; $i++){
  $result = $stmt -> fetch(PDO::FETCH_ASSOC); //1件ずつ$resultに収める(今回の場合はdescription_noとdescription_textのセットが1件分ずつ)
?>
    <option value="<?= $result['description_no']?>,<?= $result['style'] ?>,<?= $result['caption'] ?>"><?= $result['caption'] ?></option>
<?php
}
?>
</select>
<input type="hidden" name="user_no" value="<?=$user_no?>">
<input type="hidden" name="description_no" value="<?= $description_no ?>">
<br>登録内容をご確認の上登録のボタンを押してください。<br>
   <input type="submit" value="登録">
  </form>  
  </span>
</div>

<div id="modal-content-a" class="modal-content">
<form id="inputform">
  <h3>タイトルの変更</h3>
  <p>
    <input type="text" id="inputtext" value="<?= $caption ?>">
    <input type="text" name="dummy" style="display:none;">  <!-- Enterで画面遷移してしまうのを防ぐ -->
    </form>
    <button type="button" class="modal-close" onclick="titlechange();">変更</button>
    <button type="button"  class="modal-close" onclick="reset();">キャンセル</button>
  </p>
</div>
</body>
</html>