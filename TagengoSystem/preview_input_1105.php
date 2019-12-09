<?php
	session_start();
	
	//データベースへのアクセス
	require_once('mysql_connect.php');
	$pdo = connectDB();
	
	//各言語ごとのカテゴリ分け
	$genre = array(
		"ja" => array("支払い" , "場所" , "その他"),
		"en" => array("payment" , "place" , "others"),
		"zh" => array("付款" , "位置" , "否则"),
		"ko" => array("지불" , "장소" , "기타"),
		"th" => array("การชำระเงิน" , "ที่ตั้ง" , "มิฉะนั้น"),
		"vi" => array("Thanh toán" , "Nơi" , "Khác"),
		"ms" => array("pembayaran" , "Tempat" , "Lain-lain")
	);

	//データベースへの登録が初回かどうかを判定するフラグ
	//登録は最初に翻訳前のもの+翻訳後の2つを一度に登録するようになっているため2つ目の言語に翻訳してからは登録しないようにしたい
	$firstEntry = true;

	//変換した言語を表示するための文字列変数
	$landisp = "";

	//POSTされた情報の受け取り
	$user_no = $_POST['user_no'];
	$category = intval($_POST['category']);	//	文字列で格納されているので数字に変換する
	$description_text = $_POST['description_text'];
	$text = $_POST['text'];
  $style = intval($_POST['style']);
	if( isset($_POST['description_no']) ){

		$description_no = $_POST['description_no'];
	
	}else{
		//POSTにdescription_noがなかった場合データベースを検索して求める(そのアカウントでそれまでに登録した(description_no + 1)となる数)
		//今回登録するものは言語が違うだけで同じものを説明しているためdescription_noは同じにしたい
		//だから1回だけ求めてそのdescription_noを繰り返し使う
		$sql = "SELECT MAX(description_no) AS 'recent_no' FROM `test_table` WHERE user_no = '$user_no';";
		$stmt = $pdo -> query($sql);
		$result = $stmt -> fetch(PDO::FETCH_ASSOC);
		$description_no = $result['recent_no'];
		//var_dump($description_no);
		if(is_null($description_no)){
      
      //$description_noが0の場合まだ1件も登録されていないということなので1に
			$description_no = 1;
    
    }else{

      //それ以外の場合は最大値を求めているはずなので+1
      $description_no += 1;
    
    }

  }
		
	//画像の処理用の定数(アップロード先のフォルダ名)
  const FILE_FOL = "image/";

  //styleが2の時は画像なしなのでなにもしない
	if($style == 2){

		$newfilename = 0;

    //styleが3の時は画像がいるとチェックした人なのでアップした画像を確認する
	}if($style == 3){

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
    //今回はstyleが3(画像を載せる形式)であり画像がアップされなかったということは元々登録していた画像があるはず
		if($check == 0){
      
      $newfilename = $_POST['picture_URL'];
      
		}
	}

	//選択された言語ごとに翻訳してデータベースに登録する
	foreach($_POST['language'] as $language){

		//変換した言語を表示するための変数に代入
		if(strcmp($language , "en") == 0 ){
			$landisp .= '英語 ';
		}elseif(strcmp($language , "zh") == 0 ){
			$landisp .= '中国語 ';
		}elseif(strcmp($language , "ko") == 0 ){
			$landisp .= '韓国語 ';
		}elseif(strcmp($language , "th") == 0 ){
			$landisp .= 'タイ語 ';
		}elseif(strcmp($language , "vi") == 0 ){
			$landisp .= 'ベトナム語 ';
		}elseif(strcmp($language , "ms") == 0 ){
			$landisp .= 'マレー語 ';
		}
		
		//翻訳APIへのアクセス
		//説明文の翻訳
		$data = array(
			'q' => $text,
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
		//  翻訳前の言語
		$beforeLang = $res_json["data"]["translations"][0]["detectedSourceLanguage"];
		//  翻訳後のテキスト
		$afterText = $res_json["data"]["translations"][0]["translatedText"];


		//説明タイトル？見出し？の翻訳
		$data2 = array(
			'q' => $description_text,
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
		$after_d_Text = $res_json2["data"]["translations"][0]["translatedText"];
		
		//	$genreから対応したカテゴリの取り出し
		if($firstEntry == true){
			$genreBf = $genre[$beforeLang][$category];
			$sql = "INSERT INTO `test_table` VALUES('$user_no' , '$description_no' , '$description_text' , '$category' , '$genreBf' , '$beforeLang' , '$newfilename' , '$text' , 0 , 0);";
			$stmt = $pdo -> query($sql);

      $sqlII = "INSERT INTO `top_menu` VALUES('$user_no' , '$description_no' , '$style' , 0 , '$description_text');";
      $stmtII = $pdo -> query($sqlII);

			$firstEntry = false;	//翻訳前の言語での内容登録は最初の１回だけ行う
		}
		$genreAf = $genre[$language][$category];
		$sql2 = "INSERT INTO `test_table` VALUES('$user_no' , '$description_no' , '$after_d_Text' , '$category' , '$genreAf' , '$language' , '$newfilename' , '$afterText' , 0 , 0);";
		$stmt2 = $pdo -> query($sql2);
	}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>登録完了画面</title>
</head>
<body>
登録が完了しました。<br>
<br>
登録したデータを表示します。
<table border = "1">
	<tr>
		<td>説明見出し</td>
		<td><?= $_POST['description_text'] ?></td>
	</tr>
	<tr>
		<td>変換する言語</td>
		<td><?= $landisp ?></td>
	</tr>
	<tr>
		<td>アップロードした<br>
			画像ファイル</td>
		<td><?php
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
			// print $msg;
		}
		?></td>
	</tr>
	<tr>
		<td>説明詳細</td>
		<td><?= $_POST['text'] ?></td>
	</tr>
</table>
<!-- <br>
! テスト用表示部分
翻訳前の言語は「<?= $beforeLang ?>」<br>
翻訳後の見出しは「<?= $after_d_Text ?>」<br>
翻訳後の説明文は「<?= $afterText ?>」<br>
<br>
$user_no = <?= $user_no ?><br>
$description_no = <?= $description_no ?><br>
$result = <?= $result ?><br>
$newfilename = <?= $newfilename ?><br>
$language = <?= $language ?><br>
<br> -->
<form method="POST" name="form1" action="top.php">
  <input type="hidden" name="user_no" value="<?= $user_no ?>">
  <input type="hidden" name="user_ID" value="<?= $_SESSION['user_ID'] ?>">
  <a href="javascript:form1.submit()">トップへ戻る</a>
</form>
</body>
</html>