<?php
session_start();
header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

require_once('mysql_connect.php');
$pdo = connectDB();

// 送られてきたPOSTデータを変数に
$user_no = $_POST['user_no'];
$description_no = $_POST['description_no'];
$language = $_POST['language'];
//var_dump($_POST);
// それらをもとにデータベースから検索
//データベースからsizeを参照する
$sizesql="SELECT DISTINCT size FROM slide WHERE description_no=$description_no AND user_no=$user_no ";
$sizestmt=$pdo->query($sizesql);
$sizeresult=$sizestmt->fetch(PDO::FETCH_ASSOC);
$size=$sizeresult['size'];

//
/*$sql1 = "SELECT * FROM `test_table` WHERE user_no = '$user_no' AND description_no = '$description_no' AND TextLan = '$language' AND type='slide' AND slide_no='1'";
$sql2 = "SELECT * FROM `test_table` WHERE user_no = '$user_no' AND description_no = '$description_no' AND TextLan = '$language' AND type='slide' AND slide_no='2'";
$sql3 = "SELECT * FROM `test_table` WHERE user_no = '$user_no' AND description_no = '$description_no' AND TextLan = '$language' AND type='slide' AND slide_no='3'";*/

/*$stmt1 = $pdo -> query($sql1);
$stmt2 = $pdo -> query($sql2);
$stmt3 = $pdo -> query($sql3);
$result1= $stmt1 -> fetch(PDO::FETCH_ASSOC);
$result2= $stmt2 -> fetch(PDO::FETCH_ASSOC);
$result3= $stmt3 -> fetch(PDO::FETCH_ASSOC);*/

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

//「誤訳報告」ボタン用の配列
$missTrans = array(
  'ja' => '誤訳がある',
  'en' => 'There is a mistranslation',
  'zh' => '翻译是错误的',
  'ko' => '번역이 잘못되었습니다',
  'th' => 'การแปลไม่ถูกต้อง',
  'vi' => 'Có sự dịch sai',
  'ms' => 'Terjemahan itu salah'
);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=yes">
<title>Flick Slide ki Test1</title>
<script src="flickslide/js/jquery-1.5.2.min.js" type="application/javascript" charset="UTF-8"></script>
<script src="flickslide/js/jquery.flickslide.js" type="application/javascript" charset="UTF-8"></script>
<link rel="stylesheet" type="text/css" href="flickslide/css/flickslide.css" />

<script type="application/javascript">
<!--//
$(function(){
    $('#mainImages ul li').flickSlide({target:'#mainImages>ul', duration:5000});
});
//-->
</script>
<style>
body {
	margin:0;
	padding:0;
	width:100%;
}
</style>
</head>

<body>

<div id="mainImages" class="mainImageInit">
    <ul>
      <?php
      for($i=1;$i<=$size;$i++){
        $allsql="SELECT * FROM slide WHERE user_no=$user_no AND description_no=$description_no AND slide_no=$i";
        $allstmt=$pdo->query($allsql);
        $result=$allstmt->fetch(PDO::FETCH_ASSOC);
        $trsdes = array(
          'q' => $result['description_text'],
          'target' => $language,
          'format' => 'text'
        );
        $trstex = array(
          'q' => $result['text'],
          'target' => $language,
          'format' => 'text'
        );
        $trsdes_json = json_encode($trsdes);
        $trstex_json = json_encode($trstex);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $trsdes_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, 'https://translation.googleapis.com/language/translate/v2?key=AIzaSyBOzVoDALHdlL3fngB8SaNyPd_X26fpO00');
        $trsdes_result = curl_exec($ch);
        curl_close($ch);
        $trsdes_res_json = json_decode($trsdes_result , true);
        
        $trsdes_afterText = $trsdes_res_json["data"]["translations"][0]["translatedText"];

        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $trstex_json);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_URL, 'https://translation.googleapis.com/language/translate/v2?key=AIzaSyBOzVoDALHdlL3fngB8SaNyPd_X26fpO00');
        $trstex_result = curl_exec($ch2);
        curl_close($ch2);

        $trstex_res_json = json_decode($trstex_result , true);
        $trstex_afterText = $trstex_res_json["data"]["translations"][0]["translatedText"];

        //別ページにデータを送信するためタイトルと翻訳後の説明、画像URLを記憶しておく
        $title = $trsdes_afterText;
        $data[$i - 1]['image'] = $result['image'];
        $data[$i - 1]['text'] = $trstex_afterText;

        ?>
     <li>
     <table border="1" style="table-layout: fixed; width: 100%;">
       <tr height = "64"><th style = "font-size:32pt; padding:20px;"><?=$trsdes_afterText?></th></tr>
       <tr><td style = "text-align:center;"><img height = "auto" width = "auto" src = "image/<?=$result['image']?>"></td></tr>
       <tr height = "128"><td style = "text-align:center;"><?=$trstex_afterText?></td></tr>
      </table>
    </li>
    <?php
      }
      ?>
       
    </ul>
</div>

<table border="0" style="margin: auto">
<form method="POST" name="form1" action="top.php">
  <input type="hidden" name="user_no" value="<?= $user_no ?>">
  <input type="hidden" name="language" value="<?= $language ?>">
  <tr>
    <td>
      <a href="javascript:form1.submit()" style="text-align: center;">
      <?php
        if($_SESSIOM['manage_flg'] == true){
          echo '前の画面へ戻る';
        }else{
          echo $back[$language];
        }
      ?>
      </a>&nbsp;&nbsp;
      </form>
    </td>
    <td>
      <form method="POST" name="form2" action="missTrans.php">
        <input type="hidden" name="user_no" value="<?= $user_no ?>">
        <input type="hidden" name="description_no" value="<?= $description_no ?>">
        <input type="hidden" name="menu_no" value="<?= $menu_no ?>">
        <input type="hidden" name="language" value="<?= $language ?>">
        <input type="hidden" name="style" value="4">
        <input type="hidden" name="title" value="<?= $title ?>">
        <input type="hidden" name="page" value="<?= $size ?>">
        <?php
        for($i = 0; $i < $size; $i++){
        ?>
          <input type="hidden" name="data[<?= $i ?>][image]" value="<?= $data[$i]['image'] ?>">
          <input type="hidden" name="data[<?= $i ?>][text]" value="<?= $data[$i]['text'] ?>">
        <?php
        }
        ?>
        &nbsp;&nbsp;<a href="javascript:form2.submit()" style="text-align: center;"><?= $missTrans[$language] ?></a>
      </form>
    </td>
  </tr>
  </form>
</table>
</body>
</html>
