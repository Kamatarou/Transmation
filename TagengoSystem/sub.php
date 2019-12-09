<?php
  session_start();
  $size=$_POST['size'];
  require_once('mysql_connect.php');
  $pdo = connectDB();
  $user_no=$_POST['user_no'];
  $description_text=$_POST['description_text'];
  $description_no=$_POST['description_no'];
 var_dump($_POST);

  	//画像の処理
	const FILE_FOL = "image/";
	//変数の初期化
	$check = null;
	$newfilename[$size] = null;
  $msg = null;

  for($t=0;$t<$size;$t++){
	//元ファイル名の先頭にアップロード日時を加える
  $ext = pathinfo($_FILES["image"]["name"][$t], PATHINFO_EXTENSION);
 // var_dump($_FILES);
	//今日の日付+ランダムな数字列+拡張子
	$newfilename[$t] = date("YmdHis").mt_rand().".".$ext;	//こいつがアップロードした画像ファイル名になる
	// ファイルがアップデートされたかを調べる
	if(is_uploaded_file($_FILES["image"]["tmp_name"][$t])) {
		$check = 1;
	} else {
		$check = 0;
		$msg = "ファイルが選択されていません。";
	}
	if ($check == 1) {
		if ($_FILES["image[$t]"]["size"] > 1000000) {
			$check = 0;
			$msg = 'ファイルサイズを小さくしてください';
		}
	}
	//アップロードされたのが画像か調べる
	$file_pass[$t] = $_FILES["image"]["tmp_name"][$t];
	if ($check == 1) {
		if(file_exists($file_pass[$t]) && $type = exif_imagetype($file_pass[$t])){
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
	//print "<a href=uploader_test.html>戻る</a>";
	//$checkが0…画像がアップロードされてなかったり対応した拡張子のものが選択されていない場合、ファイルネームを表す変数を0にする
	if($check == 0){
		$newfilename[$t] = 0;
  }
 /* if ($check == 1) {
    if (move_uploaded_file($file_pass, FILE_FOL.$newfilename)) {
      chmod(FILE_FOL. $_FILES["image"]["name"][$t], 0644);
      //print $newfilename. "としてファイルをアップロードしました。<br>";
      //print "<a href=".FILE_FOL.$newfilename. ">ファイルを確かめる</a><br>";
      print "<img src=".FILE_FOL.$newfilename." width=400>";
    } else {
      print "ファイルをアップロードできませんでした。";
    }
  } else {
    print $msg;
  }*/
  }
  ?>

  <!DOCTYPE html>
  <html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
  </head>
  <body>
    <h1>以下の内容で登録しました</h1>

    <table border="1">
<?php
$i = 0;

foreach( $_POST['text'] as $value){
  echo '<tr>';
  if ($check == 1) {
    if (move_uploaded_file($file_pass[$i], FILE_FOL.$newfilename[$i])) {
      chmod(FILE_FOL. $_FILES["image"]["name"][$i], 0644);
      //print $newfilename. "としてファイルをアップロードしました。<br>";
      //print "<a href=".FILE_FOL.$newfilename. ">ファイルを確かめる</a><br>";
    
      echo "<td><img src=".FILE_FOL.$newfilename[$i]." ></td>";
    } else {
      //print "ファイルをアップロードできませんでした。";
    }
  } else {
   // print $msg;
  }
  echo '  <td>text['.$i.'] = '.$value.'</td>';
  echo '</tr>';
  $i++;
}
?>
</table>
  </body>
  </html>
    <?php
      
      $dissql = "SELECT MAX(description_no) AS recent_no FROM slide WHERE user_no = $user_no";

      $stmt1 = $pdo -> query($dissql);
      $result = $stmt1 -> fetch(PDO::FETCH_ASSOC);
      $description_no = $result['recent_no'];


      var_dump($description_no);
      if(is_null($description_no)){
        $description_no = 1;
      }else{
        $description_no += 1;
      }
      
      $i = 0;
      foreach($_POST['text'] as $value2){
        $slide_no=$i+1;
        $sql="INSERT INTO slide(user_no,description_no,description_text,size,slide_no,text,image) VALUES ($user_no,$description_no,'$description_text',$size,$slide_no,'$value2','$newfilename[$i]')";
        $stmt=$pdo->query($sql);
        $i++;
      }

      $sub="INSERT INTO top_menu(user_no,description_no,style,icon_URL,caption) VALUES ($user_no,$description_no,4,'0','$description_text')";
      $stmt2=$pdo->query($sub);
    ?>