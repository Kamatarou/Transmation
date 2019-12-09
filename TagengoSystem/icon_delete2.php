<?php
session_start();
$user_no = $_POST['user_no'];
$description_no = $_POST['description_no'];
$style = $_POST['style'];
$iconURL = $_POST['icon_URL'];

//データベースへの接続(別ファイル参照)
require_once('mysql_connect.php');
$pdo = connectDB();

//styleごとにアクセスするテーブル(1～5が割り当てられているので0は適当にやっておく)
$table = [0 , '`food_menu`' , '`test_table`' , '`test_table`' , '`slide`' , '`scale_link2`'];

//styleに応じたテーブルごとの画像を保存している項目名
$picture = [0 , 'pic_URL' , 'picture_URL' , 'picture_URL' , 'image' , 'img_url'];

//最初から用意されているアイコンの画像ファイル名
$default_icon = ['0' , 'edit.png' , 'foodmenu.png' , 'dinner.png' , 'notcomeyet.png' , 'wrongorder2.png' , 'tip.png' , 'creditcard.png' , 'smoking.png' , 'nonsmoking.png' , 'ticketmachine.png' , 'toilet.png' , 'next.png' , 'recommend.png'];

//-------------------------------------------------------------------------------------------------------------------------------
//  ちゃんと削除するものだけ抜き出せるかテスト

//  $sqlは削除する画像ファイル名を調べるのに使うからいる
//  $sql2はもうアイコン画像のファイル名がPOSTで送られてきてるからいらない
//  style1の場合言語指定しないと下でやってるファイル名記録のループ処理で同じ画像ファイル名が言語の数だけ出てくるので日本語で絞り込み
if($style == 1){
  $sql = "SELECT * FROM $table[$style] WHERE `user_no` = '$user_no' AND `description_no` = '$description_no' AND `language` = 'ja';";
}else{
  $sql = "SELECT * FROM $table[$style] WHERE `user_no` = '$user_no' AND `description_no` = '$description_no';";
}
$stmt = $pdo -> query($sql);
$result = $stmt -> fetch(PDO::FETCH_ASSOC);
$pic_URL = $result[$picture[$style]];
$count = $stmt -> rowCount();	//何件あるか数える
// 動作チェック
// var_dump($result);
// echo '<br>';
// echo '$count = '.$count.'<br><br>';

// $sql2 = "SELECT * FROM `top_menu` WHERE `user_no` = '$user_no' AND `description_no` = '$description_no' AND `style` = '$style';";
// $stmt2 = $pdo -> query($sql2);
// $result2 = $stmt2 -> fetch(PDO::FETCH_ASSOC);

//  テスト終わり
//--------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------
//  削除処理
//  ここの部分をコメントアウトすると削除されなくなる

$sql3 = "DELETE FROM $table[$style] WHERE `user_no` = '$user_no' AND `description_no` = '$description_no';";
$stmt3 = $pdo -> query($sql3);

$sql4 = "DELETE FROM `top_menu` WHERE `user_no` = '$user_no' AND `description_no` = '$description_no' AND `style` = '$style';";
$stmt4 = $pdo -> query($sql4);

//  削除命令とその実行おわり
//--------------------------------------------------------------------------------------------------------------------------------


//-------------------------------------------------------------------------------
//  削除した案内で使ってた画像データのファイル名を記録

if( ( $fp = fopen("deleted.txt", "a+") ) == false ){
  echo '<!-- 読み込みエラー -->';
}else{
  //アイコン画像の処理
  for($i = 0; $i < 14; $i++){
    if(strcmp($_POST['icon_URL'] , $default_icon[$i]) == 0){
      break;
    }
  }
  //ループが最後まで回る=デフォルトで用意した画像ファイル全てとファイル名が一致しなかった
  //⇒独自に用意したアイコン画像なので記録
  if($i == 14){
    fwrite($fp, "top_icon :$iconURL\n");
  }

  //案内表示で使った画像の処理
  //$style=1のときは料理メニューであり複数の画像が使われている可能性がある(0枚とか1枚の可能性もある)
  if($style == 1){
    //34行目で1回$resultをFETCHしてるからそれの処理(でも'0'は画像なしだから何もしない)
    if(strcmp($pic_URL , '0') == 0){

    }else{
      //画像URL名が'0'じゃなくてもそもそもメニュー登録0件(NULL)の場合があるから1件以上のときだけ
      if($count >= 1){
        fwrite($fp, "foodmenu :$pic_URL\n");
        for($i = 1; $i < $count; $i++){
          $result = $stmt -> fetch(PDO::FETCH_ASSOC);
          // var_dump($result);
          // echo '<br>';
          $pic_URL = $result[$picture[$style]];
          if(strcmp($pic_URL , '0') == 0){

          }else{
            fwrite($fp, "foodmenu :$pic_URL\n");
          }
        }
      }
    }
  //styleが4のときも画像が複数使われてる(こっちは2枚からなので確実)
  }elseif($style == 4){
    //1枚目(上でやった$pic_URL = $result[$picture[$style]];)の処理
    if(strcmp($pic_URL , '0') == 0){

    }else{
      fwrite($fp, "image    :$pic_URL\n");      
    }
    //2枚目以降
    for($i = 1; $i < $count; $i++){
      $result = $stmt -> fetch(PDO::FETCH_ASSOC);
      $pic_URL = $result[$picture[$style]];
      if(strcmp($pic_URL , '0') == 0){

      }else{
        fwrite($fp, "image    :$pic_URL\n");
      }
    }
  }else{
    if(strcmp($pic_URL , '0') == 0){

    }else{
      fwrite($fp, "image    :$pic_URL\n");
    }
  }
  fclose($fp);
}
//  画像ファイル名の記録終了
//-------------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登録データの削除</title>
</head>
<body>
削除が完了しました。<br>
<br>
<table border="0">
  <tr>
    <td>
      <form method="POST" name="form1" action="top.php">
        <input type="hidden" name="user_no" value="<?= $user_no ?>">
        <a href="javascript:form1.submit()">プレビュー画面へ戻る</a>
      </form>
    </td>
    <td>
      <form method="POST" name="form2" action="management.php">
        <input type="hidden" name="user_no" value="<?= $user_no ?>">
        <a href="javascript:form2.submit()">案内登録画面へ戻る</a>
      </form>
    </td>
  </tr>
</table>

<?php
//確認用

echo '<!--';
echo '<h3>確認用</h3>';
echo '検索結果:'.$count.'件<br>';
var_dump($stmt);
echo '<br>';
var_dump($result);
echo '<br>';
var_dump($stmt2);
echo '<br>';
var_dump($result2);
echo '<br>';
var_dump($stmt3);
echo '<br>';
var_dump($result3);
echo '<br>';
var_dump($stmt4);
echo '<br>';
var_dump($result4);
echo '<br>';
echo '$iconURL = '.$iconURL;
echo '<br>';
echo '$pic_URL = '.$pic_URL;
echo '<br>';
echo '$picture[$style] = '.$picture[$style];
echo '<br>';
echo '-->';
?>
</body>
</html>