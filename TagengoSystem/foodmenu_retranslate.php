<?php
session_start();
header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

$user_no = $_POST['user_no'];
$description_no = $_POST['description_no'];
$menu_no = $_POST['menu_no'];
$language = $_POST['language'];
$menu_name = $_POST['menu_name']; //要翻訳
$pic_URL = $_POST['pic_URL'];
$allergy = $_POST['allergy'];
$detail = $_POST['detail']; //要翻訳
$price = $_POST['price'];

//-------------------------------------------------------------------
//  日本語への再翻訳(訳の正確さを確かめる)

//翻訳APIへのアクセス
  //menu_nameの翻訳
  $data = array(
    'q' => $menu_name,
    'target' => 'ja',
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
    'target' => 'ja',
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

  // 翻訳後のdetail
  $after_detail = $res_json2["data"]["translations"][0]["translatedText"];

  // 再翻訳終了
  //----------------------------------------------------------------------------

//言語表示用の配列
$landis = array(
                  'ja' => '日本語' ,
                  'en' => '英語' ,
                  'zh' => '中国語' ,
                  'ko' => '韓国語' ,
                  'th' => 'タイ語' ,
                  'vi' => 'ベトナム語' ,
                  'ms' => 'マレー語'
                );

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

//「戻る」メッセージ用の配列
$back = array(
  'ja' => '前の画面へ戻る',
  'en' => 'Return',
  'zh' => '回去',
  'ko' => '돌아 가기',
  'th' => 'กลับ',
  'vi' => 'Quay lại',
  'ms' => 'Kembali'
);

//データベース接続
require_once('mysql_connect.php');
$pdo = connectDB();

$sql = "SELECT * FROM `food_menu` WHERE `user_no` = $user_no AND `description_no` = $description_no AND `menu_no` = $menu_no AND `language` = 'ja';";
$stmt = $pdo -> query($sql);
$result = $stmt -> fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link href='tab.css' rel='stylesheet' type='text/css'>
    <link href="menu.css" rel="stylesheet">
    <title>メニュー詳細(再翻訳)</title>
</head>
<body class="back">
  <div class="cp_tab" style="width: 100%">

    <input type="radio" name="cp_tab" id="tab1_0" aria-controls="page0_tab01" checked>
    <label for="tab1_0">日本語の入力内容</label>

    <input type="radio" name="cp_tab" id="tab1_1" aria-controls="page1_tab01" checked>
    <label for="tab1_1">再翻訳したもの</label>

    <input type="radio" name="cp_tab" id="tab1_2" aria-controls="page2_tab01">
    <label for="tab1_2">翻訳元(<?= $landis[$language]; ?>)</label>


    <div class="cp_tabpanels" style="width: 100%">

      <!-- 日本語の結果 -->
      <div id="page0_tab01" class="cp_tabpanel">
        <table border="1" class="display">
          <tr>
            <td class="display" style="text-align:center; font-size: 48px; padding:10px;"><b><?= $result['menu_name'] ?></b></td>
          </tr>
          <tr>
            <td class="display" style="text-align:center;"><img height = "auto" width = "440" src = "foodmenu/<?= $pic_URL ?>"></td>
          </tr>
          <tr>
            <td style="text-align: center;">
              <b style="font-size: 20px;"><?= $priceA['ja'] ?>： ￥<?= $price ?>-</b>
            </td>
          </tr>
          <tr height = "48">
            <td style="height: 48px;"><b style="vertical-align: center; font-size: 20px;"><?= $amessage['ja'] ?>：</b><?php
                                                                                                                        for($j = 0; $j < 10; $j++){
                                                                                                                          if( strcmp($allergy[$j] , '1') == 0){
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

      <!-- 再翻訳の結果 -->
      <div id="page1_tab01" class="cp_tabpanel">
        <table border="1" class="display">
          <tr>
            <td class="display" style="text-align:center; font-size: 48px; padding:10px;"><b><?= $after_menu ?></b></td>
          </tr>
          <tr>
            <td class="display" style="text-align:center;"><img height = "auto" width = "440" src = "foodmenu/<?= $pic_URL ?>"></td>
          </tr>
          <tr>
            <td style="text-align: center;">
              <b style="font-size: 20px;"><?= $priceA['ja'] ?>： ￥<?= $price ?>-</b>
            </td>
          </tr>
          <tr height = "48">
            <td style="height: 48px;"><b style="vertical-align: center; font-size: 20px;"><?= $amessage['ja'] ?>：</b><?php
                                                                                                                        for($j = 0; $j < 10; $j++){
                                                                                                                          if( strcmp($allergy[$j] , '1') == 0){
                                                                                                                            echo $pict[$j];
                                                                                                                          }
                                                                                                                        }
                                                                                                                      ?></td>
          </tr>
          <tr>
            <td><?= nl2br($after_detail) ?></td>
          </tr>
        </table>
      </div>

      <!-- 翻訳元のデータ -->
      <div id="page2_tab01" class="cp_tabpanel">
        <table border="1" class="display">
          <tr>
            <td class="display" style="text-align:center; font-size: 48px; padding:10px;"><b><?= $menu_name ?></b></td>
          </tr>
          <tr>
            <td class="display" style="text-align:center;"><img height = "auto" width = "440" src = "foodmenu/<?= $pic_URL ?>"></td>
          </tr>
          <tr>
            <td style="text-align: center;">
              <b style="font-size: 20px;"><?= $priceA[$language] ?>： ￥<?= $price ?>-</b>
            </td>
          </tr>
          <tr height = "48">
            <td style="height: auto;"><b style="vertical-align: center; font-size: 20px;"><?= $amessage[$language] ?>：</b><?php
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
      </div>
    </div>    
  </div>

  <HR style="margin: 1em 0 ;">
  
  <table class="display">
    <tr>
    <td style="text-align: center;">
        <form action="foodmenu_reedit.php" method="POST">
          <input type="hidden" name="user_no" value="<?= $user_no ?>">
          <input type="hidden" name="description_no" value="<?= $description_no ?>">
          <input type="hidden" name="menu_no" value="<?= $menu_no ?>">
          <input type="hidden" name="language" value="<?= $language ?>">
          <input type="hidden" name="menu_name" value="<?= $menu_name ?>">
          <input type="hidden" name="pic_URL" value="<?= $pic_URL ?>">
          <input type="hidden" name="allergy" value="<?= $allergy ?>">
          <input type="hidden" name="detail" value="<?= $detail ?>">
          <input type="hidden" name="price" value="<?= $price ?>">
          <input type="submit" value="登録のやり直し">
        </form>
      </td>
      <td style="text-align: center;">
        <form action="menu_detail.php" method="POST">
          <input type="hidden" name="user_no" value="<?= $user_no ?>">
          <input type="hidden" name="description_no" value="<?= $description_no ?>">
          <input type="hidden" name="menu_no" value="<?= $menu_no ?>">
          <input type="hidden" name="language" value="ja">
          <input type="submit" value="前の画面へ戻る">
        </form>
      </td>
    </tr>
  </table>
  <!-- $description_no = <?= $description_no ?><br> -->
</body>
</html>