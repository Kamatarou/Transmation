<?php
// 翻訳がおかしいところを教えてもらうページ
// どのお店のどこのページがおかしいのかをデータベースに送信する
// ページの特定にはuser_no description_no language styleの4つが最低でも必要(style:1の場合はmenu_noも)
session_start();
header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

$user_no = $_POST['user_no'];
$description_no = $_POST['description_no'];
$style = $_POST['style'];
$language = $_POST['language'];
if(isset($_POST['menu_no'])){
  $menu_no = $_POST['menu_no'];
}

// var_dump($_POST);
// echo '<br>';
// var_dump($_POST['data']);
// echo '<br>';
// var_dump($_POST['data'][0]['image']);
// echo '<br>';
// var_dump($_POST['data'][1]['text']);
// echo '<br>';

//タイトル用の連想配列
$title = array(
  'ja' => '誤訳の指摘',
  'en' => 'Pointing out mistranslation',
  'zh' => '指出误译',
  'ko' => '오역 지적',
  'th' => 'การแปลไม่ถูกต้อง',
  'vi' => 'Thông báo về dịch sai',
  'ms' => 'Maklumkan kesilapan terjemahan'
);

//本文用の配列
$main = array(
  'ja' => 'このページの翻訳におかしなところがありましたか？',
  'en' => 'Was there anything wrong with the translation of this page?',
  'zh' => '本页翻译有什么问题吗？',
  'ko' => '이 페이지의 번역에 잘못된 곳이 있었습니까?',
  'th' => 'มีอะไรผิดปกติในการแปลหน้านี้หรือไม่?',
  'vi' => 'Có lỗi trong bản dịch của trang này',
  'ms' => 'Adakah terdapat sebarang kesalahan dengan terjemahan halaman ini?'
);

//リンク先を決める配列
$link = ['0' , 'menu_detail.php' , 'preview.php' , 'preview.php' , 'newSwipe.php' , 'image_annai.php'];

//価格表示用の配列
$priceA = array(
  'ja' => '価格' ,
  'en' => 'price' ,
  'zh' => '價錢' ,
  'ko' => '가격' ,
  'th' => 'ราคา' ,
  'vi' => 'Giá' ,
  'ms' => 'Harga'
);

//アレルギー物質表示用メッセージの配列
$amessage = array(
    'ja' => '含まれるアレルギー物質' ,
    'en' => 'Allergens included' ,
    'zh' => '包括过敏原' ,
    'ko' => '포함 된 알레르기 물질' ,
    'th' => 'รวมถึงสารก่อภูมิแพ้' ,
    'vi' => 'Bao gồm dị ứng' ,
    'ms' => 'Terdapat alergen'
  );

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

//データベース接続
require_once('mysql_connect.php');
$pdo = connectDB();

// ページのstyleごとに表示を切り替えるための分岐
if($style == 1){
  //表示するデータを検索してくる
  $sql = "SELECT * FROM `food_menu` WHERE user_no = '$user_no' AND menu_no = '$menu_no' AND language = '$language';";
  $stmt = $pdo -> query($sql);
  $result = $stmt -> fetch(PDO::FETCH_ASSOC);
?>
  <!DOCTYPE html>
  <html lang="<?= $language ?>">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="menu.css" rel="stylesheet">
    <title><?= $title[$language] ?></title>
  </head>
  <body style="text-align:center;">
    <h2><?= $title[$language] ?></h2>
    <?= $main[$language] ?><br>
    <br>
    <table border="0" style="width: 16em; margin: auto;">
      <tr>
        <td>
          <form action="missTrans2.php" method="POST">
            <input type="hidden" name="user_no" value="<?= $user_no ?>">
            <input type="hidden" name="description_no" value="<?= $description_no ?>">
            <input type="hidden" name="language" value="<?= $language ?>">
            <input type="hidden" name="style" value="<?= $style ?>">
            <input type="hidden" name="menu_no" value="<?= $menu_no ?>">
            <input type="hidden" name="remark" value="<?= $result['menu_name'] ?>">
            <input type="submit" value="YES">&nbsp;&nbsp;
          </form>
        </td>
        <td>
          <form action="<?= $link[$style] ?>" method="POST">
            <input type="hidden" name="user_no" value="<?= $user_no ?>">
            <input type="hidden" name="description_no" value="<?= $description_no ?>">
            <input type="hidden" name="language" value="<?= $language ?>">
            <input type="hidden" name="style" value="<?= $style ?>">
            <input type="hidden" name="menu_no" value="<?= $menu_no ?>">
            <input type="submit" value="NO">&nbsp;&nbsp;
          </form>
        </td>
      </tr>
    </table>
    <br>
    <div style="border: 5px double #000000; padding: 20px;">
      <table border="1" class="display" style="margin: auto; background:url('picture/back1.jpg') center/cover;">
        <tr>
          <td class="display" style="text-align:center; font-size: 48px; padding:10px;"><b><?= $result['menu_name'] ?></b></td>
        </tr>
        <tr>
          <td class="display" style="text-align:center;"><img height = "auto" width = "440" src = "foodmenu/<?= $result['pic_URL'] ?>"></td>
        </tr>
        <tr>
            <td style="text-align: center;">
              <b style="font-size: 20px;"><?= $priceA[$language] ?>： ￥<?= $result['price'] ?>-<b>
            </td>
          </tr>
        <tr height = "48">
          <td style="height: 48px; vertical-align: middle; font-size: 20px;"><b><?= $amessage[$result['language']] ?>：</b><?php
                                                                                                                  for($j = 0; $j < 10; $j++){
                                                                                                                    if( strcmp($result['allergy'][$j] , '1') == 0){
                                                                                                                      echo $pict[$j];
                                                                                                                    }
                                                                                                                  }
                                                                                                                ?></td>
        </tr>
        <tr>
          <td><?= nl2br($result['detail']) ?></td>
        </tr>
      </table>
    </div>
  </body>
  </html>
<?php
}elseif($style == 2 || $style == 3){
  //style:2とstyle:3のとき(この2つの違いは画像の有無のみでデータベースの
  //テーブルを共有しておりdescription_noもかぶらないように作ってある…はず)
  $sql = "SELECT * FROM `test_table` WHERE `user_no` = '$user_no' AND `description_no` = '$description_no' AND `TextLan` = '$language';";
  $stmt = $pdo -> query($sql);
  $result = $stmt -> fetch(PDO::FETCH_ASSOC);
?>
  <!DOCTYPE html>
  <html lang="<?= $language ?>">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="table.css" rel="stylesheet">
    <title><?= $title[$language] ?></title>
  </head>
  <body style="text-align:center;">
    <h2><?= $title[$language] ?></h2>
    <?= $main[$language] ?>
    <table border="0" style="width: 240px; table-layout: fixed;">
      <tr>
        <td>
          <form action="missTrans2.php" method="POST">
            <input type="hidden" name="user_no" value="<?= $user_no ?>">
            <input type="hidden" name="description_no" value="<?= $description_no ?>">
            <input type="hidden" name="language" value="<?= $language ?>">
            <input type="hidden" name="style" value="<?= $style ?>">
            <input type="hidden" name="remark" value="<?= $result['description_text'] ?>">
            <input type="submit" value="YES">
          </form>
        </td>
        <td>
          <form action="<?= $link[$style] ?>" method="POST">
            <input type="hidden" name="user_no" value="<?= $user_no ?>">
            <input type="hidden" name="description_no" value="<?= $description_no ?>">
            <input type="hidden" name="language" value="<?= $language ?>">
            <input type="hidden" name="style" value="<?= $style ?>">
            <input type="submit" value="NO">
          </form>
        </td>
      </tr>
    </table>
    <table border = "1">
      <tr height = "auto"><th style="font-size: 3em;"><?= $result['description_text'] ?></th></tr>
      <?php

          if(strcmp($style , '2') == 0){
            //$styleが2のときは画像を設定していないということなので何もしないことで非表示にする
          }else{
            //それ以外の時(style:3)は設定された文字列がファイル名を示しているはずなので画像を表示するタグを追加する
      ?>
          <tr><td style="text-align:center; padding: 0px"><img height="auto" width="100%" src="image/<?= $result['picture_URL'] ?>"></td></tr>
      <?php
          }
      ?>
      <tr style="height: auto;"><td style="font-size: 1.5em;"><?= nl2br($result['text']) ?></td></tr>
    </table>
  </body>
  </html>  
<?php
}elseif($style == 4){
  //style:4は外国語に翻訳したデータをデータベースに登録する形式ではないので
  //前のページからそのままデータをPOSTして持ってくる
?>
  <!DOCTYPE html>
  <html lang="<?= $language ?>">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge"><script src="flickslide/js/jquery-1.5.2.min.js" type="application/javascript" charset="UTF-8"></script>
    <script src="flickslide/js/jquery.flickslide.js" type="application/javascript" charset="UTF-8"></script>
    <link rel="stylesheet" type="text/css" href="flickslide/css/flickslide.css" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=yes">
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
    <title><?= $title[$language] ?></title>
  </head>
  <body style="text-align:center;">
    <h2><?= $title[$language] ?></h2>
    <?= $main[$language] ?><br>
    <br>
    <table border="0" style="width: 16em; margin: auto;">
      <tr>
        <td>
          <form action="missTrans2.php" method="POST">
            <input type="hidden" name="user_no" value="<?= $user_no ?>">
            <input type="hidden" name="description_no" value="<?= $description_no ?>">
            <input type="hidden" name="language" value="<?= $language ?>">
            <input type="hidden" name="style" value="<?= $style ?>">
            <input type="hidden" name="remark" value="<?= $_POST['title'] ?>">
            <input type="submit" value="YES">&nbsp;&nbsp;
          </form>
        </td>
        <td>
          <form action="<?= $link[$style] ?>" method="POST">
            <input type="hidden" name="user_no" value="<?= $user_no ?>">
            <input type="hidden" name="description_no" value="<?= $description_no ?>">
            <input type="hidden" name="language" value="<?= $language ?>">
            <input type="hidden" name="style" value="<?= $style ?>">
            <input type="submit" value="NO">&nbsp;&nbsp;
          </form>
        </td>
      </tr>
    </table>
    <br>
    <div style="border: 3px double #000000; padding: 5px;">
      <div id="mainImages" class="mainImageInit">
        <ul>
          <?php
          for($i = 0; $i < $_POST['page']; $i++){
          ?>
          <li>
            <table border="1" style="table-layout: fixed; width: 100%;">
              <tr height = "64"><th style = "font-size:32pt; padding:20px;"><?= $_POST['title'] ?></th></tr>
              <tr><td style = "text-align:center;"><img height = "auto" width = "auto" src = "image/<?= $_POST['data'][$i]['image'] ?>"></td></tr>
              <tr height = "128"><td style = "text-align:center;"><?= $_POST['data'][$i]['text'] ?></td></tr>
            </table>
          </li>
          <?php
          }
          ?>
        </ul>
      </div>
    </div>
  </body>
  </html>
<?php
}
?>