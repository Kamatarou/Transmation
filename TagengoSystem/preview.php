<?php
session_start();

header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

//データベース接続
require_once('mysql_connect.php');
$pdo = connectDB();

// 送られてきたPOSTデータを変数に
$user_no = $_POST['user_no'];
$description_no = $_POST['description_no'];
$style = $_POST['style'];
$requestLan = $_POST['language'];

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

//title用の配列
$title = array(
  'ja' => '案内内容',
  'en' => 'Guidance contents',
  'zh' => '指導內容',
  'ko' => '안내 내용',
  'th' => 'เนื้อหาคำแนะนำ',
  'vi' => 'Nội dung hướng dẫn',
  'ms' => 'Kandungan bimbingan'
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

if($_SESSION['manage_flg'] == true){

  //管理者用なので登録している全言語分を表示する
  $sql = "SELECT * FROM `test_table` WHERE `user_no` = $user_no AND `description_no` = $description_no;";
  $stmt = $pdo -> query($sql);
  $count = $stmt -> rowCount();

?>
  <!DOCTYPE html>
  <html lang="ja">
  <head>
      <meta charset="UTF-8">
      <link href='tab.css' rel='stylesheet' type='text/css'>
      <link href="table.css" rel="stylesheet"> 
      <title><?= $title[$requestLan] ?></title>
  </head>
  <body>
    <div class="cp_tab">
      <?php
        if(strcmp($requestLan , 'ja') == 0){
          $page = 1;
        }else{
          $page = 2;
        }

        for($i = 0; $i < $count; $i++){
          $result = $stmt -> fetch(PDO::FETCH_ASSOC);
          //配列$mirrorに$resultの内容をコピー
          $mirror[$i] = $result;

          if(strcmp($result['TextLan'] , $requestLan) == 0){
      ?>
            <input type="radio" name="cp_tab" id="tab1_0" aria-controls="page0_tab01" checked>
            <label for="tab1_0"><?= $landis[$requestLan] ?></label>
      <?php
          }elseif(strcmp($result['TextLan'] , 'ja') == 0){
      ?>
            <input type="radio" name="cp_tab" id="tab1_1" aria-controls="page1_tab01">
            <label for="tab1_1">日本語</label>
      <?php
          }else{
      ?>
            <input type="radio" name="cp_tab" id="tab1_<?= $page ?>" aria-controls="page<?= $page ?>_tab01">
            <label for="tab1_<?= $page ?>"><?= $landis[$result['TextLan']] ?></label>
      <?php
            $page++;
          }
        }
        ?>
      <div class="cp_tabpanels">
      <?php
        if(strcmp($requestLan , 'ja') == 0){
          $page = 1;
        }else{
          $page = 2;
        }
   
        for($i = 0; $i < $count; $i++){
          if(strcmp($mirror[$i]['TextLan'] , $requestLan) == 0){
      ?>
            <div id="page0_tab01" class="cp_tabpanel">
          <?php
          }elseif(strcmp($mirror[$i]['TextLan'] , 'ja') == 0){
          ?>
            <div id="page1_tab01" class="cp_tabpanel">
          <?php
          }else{
          ?>
            <div id="page<?= $page ?>_tab01" class="cp_tabpanel">
          <?php
            $page++;
          }
          ?>
          <table border = "1">
            <tr height = "auto"><th><?= $mirror[$i]['description_text'] ?></th></tr>
            <?php
                if(strcmp($style , '2') == 0){
                  //$styleが2のときは画像を設定していないということなので何もしないことで非表示にする
                }else{
                  //それ以外の時は設定された文字列がファイル名を示しているはずなので画像を表示するタグを追加する
            ?>
                <tr><td style="text-align:center; padding: 0px"><img height="auto" width="100%" src="image/<?= $mirror[$i]['picture_URL'] ?>"></td></tr>
            <?php
                }
            ?>
            <tr style="height: auto;"><td><?= nl2br($mirror[$i]['text']) ?></td></tr>
          </table>
          <br>
          <table border="0" style="table-layout: auto;">
            <tr>
              <td style="padding: 0px">
                <form method="POST" action="top.php">
                  <input type="hidden" name="user_no" value="<?= $user_no ?>">
                  <?php
                    if($_SESSION['manage_flg'] == true){
                  ?>
                      <input type="submit" value="前の画面へ戻る">
                  <?php
                    }else{
                  ?>
                      <input type="submit" value="<?= $back[$requestLan] ?>">
                  <?php
                    }
                  ?>
                </form>
              </td>
              <?php
              if(strcmp($mirror[$i]['TextLan'] , 'ja') != 0){
              ?>
                <td style="padding: 0px">
                  <form method="POST" action="preview_retranslate.php">
                    <input type="hidden" name="user_no" value="<?= $user_no ?>">
                    <input type="hidden" name="description_no" value="<?= $description_no ?>">
                    <input type="hidden" name="picture_URL" value="<?= $mirror[$i]['picture_URL'] ?>">
                    <input type="hidden" name="description_text" value="<?= $mirror[$i]['description_text'] ?>">
                    <input type="hidden" name="language" value="<?= $mirror[$i]['TextLan'] ?>">
                    <input type="hidden" name="text" value="<?= $mirror[$i]['text'] ?>">
                    <input type="hidden" name="style" value="<?= $style ?>">
                    <input type="submit" value="日本語に再翻訳">
                  </form>
                </td>
              <?php
              }
              ?>
            </tr>
          </table>
      </div>
    <?php
    }
    ?>
    </div>
  </body>
  </html>
<?php
}else{

  //普通の閲覧用なので要求されたuse_noとdescription_noだけでなく言語も検索に用いて1件に絞り込む
  $sql = "SELECT * FROM `test_table` WHERE `user_no` = '$user_no' AND `description_no` = '$description_no' AND `TextLan` = '$requestLan';";
  $stmt = $pdo -> query($sql);
  $result = $stmt -> fetch(PDO::FETCH_ASSOC);

?>
  <!DOCTYPE html>
  <html lang="<?= $requestLan ?>">
  <head>
    <link href="table.css" rel="stylesheet"> 
    <meta charset="UTF-8">
    <title><?= $title[$requestLan] ?></title>
  </head>
  <body>
      <table border = "1">
              <tr height = "auto"><th><?= $result['description_text'] ?></th></tr>
              <?php

                  if(strcmp($style , '2') == 0){

                    //$styleが2のときは画像を設定していないということなので何もしないことで非表示にする

                  }else{
                    //それ以外の時は設定された文字列がファイル名を示しているはずなので画像を表示するタグを追加する

              ?>
                  <tr><td style="text-align:center; padding: 0px"><img height="auto" width="100%" src="image/<?= $result['picture_URL'] ?>"></td></tr>
              <?php

                  }

              ?>
              <tr style="height: auto;"><td><?= nl2br($result['text']) ?></td></tr>

      </table>
      <br>
      <table border="0" style="table-layout: auto;">
        <tr>
          <td style="padding: 0px">
            <form method="POST" action="top.php">
              <input type="hidden" name="user_no" value="<?= $user_no ?>">
              <input type="hidden" name="language" value="<?= $requestLan ?>"> 
              <input type="submit" style="font-size: 1em;" value="<?= $back[$requestLan] ?>">
            </form>
          </td>
          <td style="padding: 0px">
            <form method="POST" action="missTrans.php">
              <input type="hidden" name="user_no" value="<?= $user_no ?>">
              <input type="hidden" name="description_no" value="<?= $description_no ?>">
              <input type="hidden" name="language" value="<?= $requestLan ?>">
              <input type="hidden" name="style" value="<?= $style ?>">
              <input type="submit" style="font-size: 1em;" value="<?= $missTrans[$requestLan] ?>">
            </form>
          </td>
        </tr>
      </table>
      <br>
      <?php
        echo '<!--';
        echo '$_SESSION[\'manage_flg\'] = '.$_SESSION['manage_flg'].'<br>';
        echo '$user_no = '.$user_no.'<br>';
        echo '$description_no = '.$description_no.'<br>';
        echo '$requestLan = '.$requestLan.'<br>';
        echo '$result[\'picture_URL\'] = '.$result['picture_URL'].'<br>';
        echo '$count = '.$count.'<br>';
        var_dump($result);
        echo '<br>';
        echo '-->';
      ?>
  </body>
  </html>
<?php
}
?>