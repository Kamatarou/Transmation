<?php
  session_start();
  
  $user_no = $_POST['user_no'];
  $description_no = $_POST['description_no'];
  $menu_no = $_POST['menu_no'];
  $menu_name = $_POST['menu_name'];
  $detail = $_POST['detail'];
  $price = $_POST['price'];

  $allergy = "0000000000";
  
  foreach($_POST['allergy'] as $value){
    $allergy[$value] = '1';
  }

  //ピクトグラム表示用の配列
  $pict = array(
                  '<img src="food_pictogram/1egg.PNG">' ,
                  '<img src="food_pictogram/2dairyproduct.PNG">' ,
                  '<img src="food_pictogram/3wheat.PNG">' ,
                  '<img src="food_pictogram/4soba.PNG">' ,
                  '<img src="food_pictogram/5peanuts.PNG">' ,
                  '<img src="food_pictogram/6shrimp.PNG">' ,
                  '<img src="food_pictogram/7crab.PNG">' ,
                  '<img src="food_pictogram/20beef.PNG">' ,
                  '<img src="food_pictogram/21pork.PNG">' ,
                  '<img src="food_pictogram/22chicken.PNG">'
                );

  //データベースへの登録が初回かどうかを判定するフラグ
	$firstEntry = true;

	//データベースへのアクセス
	require_once('mysql_connect.php');
	$pdo = connectDB();

	//画像の処理
	const FILE_FOL = "foodmenu/";
	//変数の初期化
	$check = null;
	$newfilename = null;
	$msg = null;
	//元ファイル名の先頭にアップロード日時を加える
	$ext = pathinfo($_FILES["upfile"]["name"], PATHINFO_EXTENSION);
	//今日の日付+ランダムな数字列+拡張子
	$newfilename = date("YmdHis").mt_rand().".".$ext;	//こいつがアップロードした画像ファイル名になる
	// ファイルがアップデートされたかを調べる
	if(is_uploaded_file($_FILES["upfile"]["tmp_name"])) {
		$check = 1;
	} else {
		$check = 0;
		$msg = "ファイルが選択されていません。";
	}
	if ($check == 1) {
		if ($_FILES['upload']['size'] > 1000000) {
			$check = 0;
			$msg = 'ファイルサイズを小さくしてください';
		}
	}
	//アップロードされたのが画像か調べる
	$file_pass = $_FILES["upfile"]["tmp_name"];
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
  //$checkが0…画像がアップロードされてなかったり対応した拡張子のものが選択されていない場合
	if($check == 0){
		$newfilename = '';
  }

	//6種の翻訳先となる言語
	$trans = ['en' , 'zh' , 'ko' , 'th' , 'vi' , 'ms'];

  //選択された言語ごとに翻訳してデータベースに登録する
	foreach($trans as $language){
    //翻訳APIへのアクセス
		//説明文の翻訳
		$data = array(
			'q' => $menu_name,
			'target' => $language,
			'format' => 'text'
		);			
		$data_json = json_encode($data);

		//ここからphp,curlを利用してのPOST
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, 'https://translation.googleapis.com/language/translate/v2?key=AIzaSyBOzVoDALHdlL3fngB8SaNyPd_X26fpO00');
		$result = curl_exec($ch);
		curl_close($ch);

		$res_json = json_decode($result , true);
    //  翻訳後のmenu_name
    $after_menu = $res_json["data"]["translations"][0]["translatedText"];
    
    //今度はdetail(料理の詳細説明)の翻訳
    $data2 = array(
			'q' => $detail,
			'target' => $language,
			'format' => 'text'
		);			
		$data_json2 = json_encode($data2);

		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch2, CURLOPT_POSTFIELDS, $data_json2);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch2, CURLOPT_URL, 'https://translation.googleapis.com/language/translate/v2?key=AIzaSyBOzVoDALHdlL3fngB8SaNyPd_X26fpO00');
		$result2 = curl_exec($ch2);
		curl_close($ch2);

		$res_json2 = json_decode($result2 , true);

		//  翻訳後の見出し
    $after_detail = $res_json2["data"]["translations"][0]["translatedText"];
    
    //	初回判定がtrueの場合
		if($firstEntry == true){
			$sql = "INSERT INTO `food_menu` VALUES('$user_no' , '$description_no' , '$menu_no' , '$menu_name' , 'ja' , '$newfilename' , '$price' , '$allergy' , '$detail' , 'false');";
			$stmt = $pdo -> query($sql);
			$firstEntry = false;	//翻訳前の言語での内容登録は最初の１回だけ行う
		}
		$sql2 = "INSERT INTO `food_menu` VALUES('$user_no' , '$description_no' , '$menu_no' , '$after_menu' , '$language' , '$newfilename' , '$price' , '$allergy' , '$after_detail' , 'false');";
    $stmt2 = $pdo -> query($sql2);
}
    
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link href="menu.css" rel="stylesheet">
    <title>登録内容確認</title>
</head>
<body class="back">
  <table border="1" class="display">
    <tr>
      <td class="display" style="text-align:center; font-size: 48px;"><b><?= $menu_name ?></b></td>
    </tr>
    <tr>
      <td class="display" style="text-align:center;"><?php
	  //例外処理を全てクリアしたらファイルをアップする
		if ($check == 1) {
			if (move_uploaded_file($file_pass, FILE_FOL.$newfilename)) {
				chmod(FILE_FOL. $_FILES["upfile"]["name"], 0644);
				//print $newfilename. "としてファイルをアップロードしました。<br>";
				//print "<a href=".FILE_FOL.$newfilename. ">ファイルを確かめる</a><br>";
				print "<img src=".FILE_FOL.$newfilename." width=400>";
			} else {
				print "ファイルをアップロードできませんでした。";
			}
		} else {
			
		}
		?></td>
    </tr>
    <tr height = "48">
      <td style="height: 48px;"><b style="vertical-align: center; font-size: 20px;">アレルギー主要7品目：</b><?php
                                                                                                              for($j = 0; $j < 10; $j++){
                                                                                                                if( strcmp($allergy[$j] , '1') == 0){
                                                                                                                  echo $pict[$j];
                                                                                                                }
                                                                                                              }
                                                                                                            ?></td>
    </tr>
    <tr>
      <td><?= nl2br($detail) ?></td>
    </tr>
  </table>
  <table>
    <tr>
      <td><form action="top.php" name="form1" method="POST">
            <input type="hidden" name="user_no" value="<?= $user_no ?>">
            <a href="javascript:form1.submit()" class="btn-link" style="font-size:24px;">トップに戻る</a>
          </form></td>
      <td><form action="menu.php" name="form2" method="POST">
            <input type="hidden" name="user_no" value="<?= $user_no ?>">
			<input type="hidden" name="style" value="1">
			<input type="hidden" name="description_no" value="<?= $description_no ?>">
            <input type="hidden" name="language" value="ja">
            <a href="javascript:form2.submit()" class="btn-link" style="font-size:24px;">メニュー一覧で確認</a>
          </form></td>
    </tr>
  </table>
  <!-- $manage_flg = <?= $_SESSION['manage_flg'] ?><br> -->
</body>
</html>