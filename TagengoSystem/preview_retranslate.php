<?php
session_start();

header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

// 送られてきたPOSTデータを変数に
$user_no = $_POST['user_no'];
$description_no = $_POST['description_no'];
$description_text = $_POST['description_text'];
$style = $_POST['style'];
$language = $_POST['language'];
$picture_URL = $_POST['picture_URL'];
$text = $_POST['text'];

// var_dump($_POST);
// echo '<br>';

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

//-------------------------------------------------------------------
//  日本語への再翻訳(訳の正確さを確かめる)

//翻訳APIへのアクセス
  //description_text(案内内容の見出し？タイトル？)の翻訳
  $data = array(
    'q' => $description_text,
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
  //  翻訳後のdescription_text
  $after_description = $res_json["data"]["translations"][0]["translatedText"];
  
  //今度はtext(案内内容の詳細説明)の翻訳
  $data2 = array(
    'q' => $text,
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

  // 翻訳後のtext
  $after_text = $res_json2["data"]["translations"][0]["translatedText"];

  // 再翻訳終了
  //----------------------------------------------------------------------------

  //データベース接続
  require_once('mysql_connect.php');
  $pdo = connectDB();

  $sql = "SELECT * FROM `test_table` WHERE `user_no` = $user_no AND `description_no` = $description_no AND `TextLan` = 'ja';";
  $stmt = $pdo -> query($sql);
  $result = $stmt -> fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <link href="table.css" rel="stylesheet"> 
  <link href="tab.css" rel="stylesheet">
  <meta charset="UTF-8">
  <title>案内内容(再翻訳)</title>
</head>
<body>
  <div class="cp_tab" style="width: 100%">

    <input type="radio" name="cp_tab" id="tab1_0" aria-controls="page0_tab01">
    <label for="tab1_0">日本語の入力内容</label>

    <input type="radio" name="cp_tab" id="tab1_1" aria-controls="page1_tab01" checked>
    <label for="tab1_1">再翻訳した内容</label>

    <input type="radio" name="cp_tab" id="tab1_2" aria-controls="page2_tab01">
    <label for="tab1_2">翻訳元(<?= $landis[$language]; ?>)</label>


    <div class="cp_tabpanels" style="width: 100%">

      <!-- 日本語 -->
      <div id="page0_tab01" class="cp_tabpanel">
        <table border = "1">
          <tr height = "auto"><th><?= $result['description_text'] ?></th></tr>
          <?php

          if(strcmp($style , '2') == 0){
            //$styleが2のときは画像を設定していないということなので何もしないことで非表示にする

          }else{
            //それ以外の時は設定された文字列がファイル名を示しているはずなので画像を表示するタグを追加する
          ?>
            <tr>
              <td style="text-align:center; padding: 0px">
                <img height="auto" width="100%" src="image/<?= $picture_URL ?>">
              </td>
            </tr>
          <?php
          }
          ?>
          <tr style="height: auto;">
            <td>
              <?= nl2br($result['text']) ?>
            </td>
          </tr>
        </table>
      </div>

      <!-- 再翻訳結果 -->
      <div id="page1_tab01" class="cp_tabpanel">
        <table border = "1">
          <tr height = "auto"><th><?= $after_description ?></th></tr>
          <?php

          if(strcmp($style , '2') == 0){
            //$styleが2のときは画像を設定していないということなので何もしないことで非表示にする

          }else{
            //それ以外の時は設定された文字列がファイル名を示しているはずなので画像を表示するタグを追加する
          ?>
            <tr>
              <td style="text-align:center; padding: 0px">
                <img height="auto" width="100%" src="image/<?= $picture_URL ?>">
              </td>
            </tr>
          <?php
          }
          ?>
          <tr style="height: auto;">
            <td>
              <?= nl2br($after_text) ?>
            </td>
          </tr>
        </table>
      </div>

      <!-- 翻訳元 -->
      <div id="page2_tab01" class="cp_tabpanel">
        <table border = "1">
          <tr height = "auto"><th><?= $description_text ?></th></tr>
          <?php

          if(strcmp($style , '2') == 0){
            //$styleが2のときは画像を設定していないということなので何もしないことで非表示にする

          }else{
            //それ以外の時は設定された文字列がファイル名を示しているはずなので画像を表示するタグを追加する
          ?>
            <tr>
              <td style="text-align:center; padding: 0px">
                <img height="auto" width="100%" src="image/<?= $picture_URL ?>">
              </td>
            </tr>
          <?php
          }
          ?>
          <tr style="height: auto;">
            <td>
              <?= nl2br($text) ?>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <?php
      echo '<!--';
      echo '$_SESSION[\'manage_flg\'] = '.$_SESSION['manage_flg'].'<br>';
      echo '$user_no = '.$user_no.'<br>';
      echo '$description_no = '.$description_no.'<br>';
      echo '$language = '.$language.'<br>';
      echo '$result[\'picture_URL\'] = '.$result['picture_URL'].'<br>';
      echo '$count = '.$count.'<br>';
      var_dump($result);
      echo '<br>';
      echo '-->';
    ?>
  </div>

  <HR style="margin: 1em 0 ;">

  <table border="0">
    <tr>
      <td style="padding: 0px">
        <form method="POST" action="preview_reedit.php">
          <input type="hidden" name="user_no" value="<?= $user_no ?>">
          <input type="hidden" name="description_no" value="<?= $description_no ?>">
          <input type="hidden" name="style" value="<?= $style ?>">
          <input type="hidden" name="language" value="<?= $language ?>">
          <input type="submit" value="登録のし直し">
        </form>
      </td>
      <td style="padding: 0px">
        <form method="POST" action="preview.php">
          <input type="hidden" name="user_no" value="<?= $user_no ?>">
          <input type="hidden" name="description_no" value="<?= $description_no ?>">
          <input type="hidden" name="style" value="<?= $style ?>">
          <input type="hidden" name="language" value="<?= $language ?>">
          <input type="submit" value="前の画面へ戻る">
        </form>
      </td>          
      </tr>
  </table>
</body>
</html>