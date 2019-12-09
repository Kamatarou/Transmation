<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>登録画面</title>
</head>
<body>
	<?php
	session_start();
	?>
  <?php
  var_dump($_POST);
  $user_no = $_POST['user_no'];
  $description_no = $_POST['description_no'];
  $caption = $_POST['caption'];
	$right=$_POST['right'];
	$top=$_POST['top'];
	$left=$_POST['left'];
	$botom=$_POST['botom'];
	$sel = isset($_POST['select']) ? $_POST['select'] : '';
	$descript=explode(",",$sel);
	const FILE_FOL = "image/";
	if(isset($_POST['used_img'])){
		$newfilename=$_POST['used_img'];
	}else{
	$check = null;
	$newfilename = null;
	$msg = null;
	//元ファイル名の先頭にアップロード日時を加える
	$ext = pathinfo($_FILES["inputfile"]["name"], PATHINFO_EXTENSION);
	//今日の日付+ランダムな数字列+拡張子
	$newfilename = date("YmdHis").mt_rand().".".$ext;	//こいつがアップロードした画像ファイル名になる
	// ファイルがアップデートされたかを調べる
	if(is_uploaded_file($_FILES["inputfile"]["tmp_name"])) {
		$check = 1;
	} else {
		$check = 0;
		$msg = "ファイルが選択されていません。";
	}
	if ($check == 1) {
		if ($_FILES['inputfile']['size'] > 100000000) {
			$check = 0;
			$msg = 'ファイルサイズを小さくしてください';
		}
	}
	//アップロードされたのが画像か調べる
	$file_pass = $_FILES["inputfile"]["tmp_name"];
	if ($check == 1) {
		if(file_exists($file_pass) && $type = exif_imagetype($file_pass)){
			switch($type){
				//gifの場合
				case IMAGETYPE_GIF:
				break;
				//jpgの場合
				case IMAGETYPE_JPEG:
				break;
				//pngの場合
				case IMAGETYPE_PNG:
				break;
				//どれにも該当しない場合
				default:
				$msg =  "gif、jpg、png以外の画像です";
			}
		}else{
			$msg =  "画像ファイルではありません";
			$check = 0;
		}
	}
}
	require_once('mysql_connect.php');
	$pdo = connectDB();
	// try{
		// $pdo = new PDO('mysql:host=mysql640.db.sakura.ne.jp; dbname=fukuiohr2_tagengo;charaset=UTF8','​fukuiohr2','fki2d2019');
		// $pdo -> setattribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		// $pdo -> setattribute(PDO::ATTR_EMULATE_PREPARES,false);
		// $pdo -> beginTransaction();
		$sql = "INSERT INTO `scale_link2` VALUES('$user_no','$description_no','$top','$right','$left','$botom','$descript[0]','$descript[1]','$descript[2]','$newfilename')";
		$stmt = $pdo -> query($sql);
		if ($check == 1) {
			if (move_uploaded_file($file_pass, FILE_FOL.$newfilename)) {
				chmod(FILE_FOL. $_FILES["inputfile"]["name"], 0644);
				//print $newfilename. "としてファイルをアップロードしました。<br>";
				//print "<a href=".FILE_FOL.$newfilename. ">ファイルを確かめる</a><br>";
				// print "<img src=".FILE_FOL.$newfilename.">";
			} else {
				print "ファイルをアップロードできませんでした。";
			}
		} else {
			print $msg;
		}
		
    if(strcmp($_POST['first_flg'] , 'true') == 0){
      $sql = "INSERT INTO `top_menu` VALUES('$user_no' , '$description_no' , 5 , '0' , '$caption');";
      $stmt = $pdo -> query($sql);
    }
		?>
		登録しました。<br>
		<form action="image_link.php" method="post">
		<input type="hidden" name="user_no" value="<?=$user_no?>">
    	<input type="hidden" name="description_no" value="<?= $description_no ?>">
		<input type="hidden" name="inputfile" value="<?=$newfilename?>">
		<input type="submit" value="もう一度編集する"><br>
		</form>
		<form action="top.php" method="post">
		<input type="hidden" name="user_no" value="<?=$user_no?>">
		<input type="submit" value="トップへ戻る">
		</form>
	
</body>
</html>