<?php
session_start();
header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

$user_no = $_POST['user_no'];
$description_no = $_POST['description_no'];
$menu_no = $_POST['menu_no'];
$language = $_POST['language'];
$requestLan = $language;
//データベース接続
require_once('mysql_connect.php');
$pdo = connectDB();

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

if($_SESSION['manage_flg'] == true){
  //管理者モードの時は全言語表示する

  //日本語だけ特別
  $sql = "SELECT * FROM `food_menu` WHERE user_no = '$user_no' AND menu_no = '$menu_no' AND language = 'ja';";
  $stmt = $pdo -> query($sql);
  $jaresult = $stmt -> fetch(PDO::FETCH_ASSOC);

  $sql = "SELECT * FROM `food_menu` WHERE user_no = '$user_no' AND menu_no = '$menu_no';";
  $stmt = $pdo -> query($sql);
  $stmtcopy = $pdo -> query($sql);
  $count = $stmt -> rowCount();

}else{
  //一般利用者向け
  $sql = "SELECT * FROM `food_menu` WHERE user_no = '$user_no' AND menu_no = '$menu_no' AND language = '$language';";
  $stmt = $pdo -> query($sql);
  $result = $stmt -> fetch(PDO::FETCH_ASSOC);

}

?>
<?php

if($_SESSION['manage_flg'] == true){
  //管理者モードの時の表示

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link href='tab.css' rel='stylesheet' type='text/css'>
    <link href="menu.css" rel="stylesheet">
    <title>メニュー詳細</title>
</head>
<body class="back">
  <div class="cp_tab" style="width: 100%">

    <input type="radio" name="cp_tab" id="tab1_0" aria-controls="page0_tab01" <?php if(strcmp($requestLan , $language) == 0){ echo 'checked'; } ?>>
    <label for="tab1_0">日本語</label>

    <?php

    $page = 1;
    for($i = 0; $i < $count; $i++){
      $result2 = $stmtcopy -> fetch(PDO::FETCH_ASSOC); //1行分の結果を$resultに代入する
      $language = $result2['language'];

      if( strcmp($language , 'ja') != 0){
        //日本語以外の時

    ?>     
        <input type="radio" name="cp_tab" id="tab1_<?= $page ?>" aria-controls="page<?= $page ?>_tab01" <?php if(strcmp($requestLan , $language) == 0){ echo 'checked'; } ?>>
        <label for="tab1_<?= $page ?>"><?= $landis[$language]; ?></label>
    <?php

        $page++;
      }else{
        //日本語はもう表示したのでなにもしない
      }
    }

    ?>
    <div class="cp_tabpanels" style="width: 100%">
    <!-- 日本語の検索結果だけ必ず最初に出しておきたい -->
      <div id="page0_tab01" class="cp_tabpanel">
        <table border="1" class="display">
          <tr>
            <td class="display" style="text-align:center; font-size: 48px; padding:10px;"><b><?= $jaresult['menu_name'] ?></b></td>
          </tr>
          <tr>
            <td class="display" style="text-align:center;"><img height = "auto" width = "440" src = "foodmenu/<?= $jaresult['pic_URL'] ?>"></td>
          </tr>
          <tr>
            <td style="text-align: center;">
              <b style="font-size: 20px;"><?= $priceA['ja'] ?>： ￥<?= $jaresult['price'] ?>-</b>
            </td>
          </tr>
          <tr height = "48">
            <td style="height: 48px;"><b style="vertical-align: center; font-size: 20px;"><?= $amessage['ja'] ?>：</b><?php
                                                                                                                        for($j = 0; $j < 10; $j++){
                                                                                                                          if( strcmp($jaresult['allergy'][$j] , '1') == 0){
                                                                                                                            echo $pict[$j];
                                                                                                                          }
                                                                                                                        }
                                                                                                                      ?></td>
          </tr>
          <tr>
            <td><?= nl2br($jaresult['detail']) ?></td>
          </tr>
        </table>
        <br>
        <table class="display">
          <tr>
            <td style="text-align: center;">
              <form action="foodmenu_edit.php" method="POST">
                <input type="hidden" name="user_no" value="<?= $user_no ?>">
                <input type="hidden" name="description_no" value="<?= $description_no ?>">
                <input type="hidden" name="menu_no" value="<?= $menu_no ?>">
                <input type="hidden" name="language" value="ja">
                <input type="hidden" name="menu_name" value="<?= $jaresult['menu_name'] ?>">
                <input type="hidden" name="pic_URL" value="<?= $jaresult['pic_URL'] ?>">
                <input type="hidden" name="allergy" value="<?= $jaresult['allergy'] ?>">
                <input type="hidden" name="detail" value="<?= $jaresult['detail'] ?>">
                <input type="hidden" name="price" value="<?= $jaresult['price'] ?>">
                <input type="submit" value="このページを編集">
              </form>
            </td>
            <?php

            if($page < 6){
              //言語ページが6以下=日本語を除いた翻訳可能な6言語の中でまだ翻訳してないものがある

            ?>
              <!-- <td>
                <form action="foodmenu_trans.php" method="POST">
                  <input type="hidden" name="user_no" value="<?= $user_no ?>">
                  <input type="hidden" name="description_no" value="<?= $description_no ?>">
                  <input type="hidden" name="menu_no" value="<?= $menu_no ?>">
                  <input type="hidden" name="language" value="ja">
                  <input type="hidden" name="menu_name" value="<?= $jaresult['menu_name'] ?>">
                  <input type="hidden" name="pic_URL" value="<?= $jaresult['pic_URL'] ?>">
                  <input type="hidden" name="allergy" value="<?= $jaresult['allergy'] ?>">
                  <input type="hidden" name="detail" value="<?= $jaresult['detail'] ?>">
                  <input type="hidden" name="price" value="<?= $jaresult['price'] ?>">
                  <input type="submit" value="別の言語に翻訳">
                </form>
              </td> -->
            <?php

            }

            ?>
            <td style="text-align: center;">
              <form action="menu.php" method="POST">
                <input type="hidden" name="user_no" value="<?= $user_no ?>">
                <input type="hidden" name="description_no" value="<?= $description_no ?>">
                <input type="hidden" name="language" value="ja">
                <input type="submit" value="前の画面へ戻る">
              </form>
            </td>
          </tr>
        </table>
      </div>
    <?php       

    $page = 1;
    for($i = 0; $i < $count; $i++){
      $result = $stmt -> fetch(PDO::FETCH_ASSOC); //1行分の結果を$resultに代入する
      $language = $result['language'];
      //日本語以外の時
      if( strcmp($language , 'ja') != 0){

    ?>
      <div id="page<?= $page ?>_tab01" class="cp_tabpanel">
        <table border="1" class="display">
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
            <td style="height: auto;"><b style="vertical-align: center; font-size: 20px;"><?= $amessage[$language] ?>：</b><?php
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
        <br>
        <table border="0" class="display">
          <tr>
            <td style="text-align: center;">
              <form action="foodmenu_edit.php" method="POST">
                <input type="hidden" name="user_no" value="<?= $user_no ?>">
                <input type="hidden" name="description_no" value="<?= $description_no ?>">
                <input type="hidden" name="menu_no" value="<?= $menu_no ?>">
                <input type="hidden" name="language" value="<?= $language ?>">
                <input type="hidden" name="menu_name" value="<?= $result['menu_name'] ?>">
                <input type="hidden" name="pic_URL" value="<?= $result['pic_URL'] ?>">
                <input type="hidden" name="allergy" value="<?= $result['allergy'] ?>">
                <input type="hidden" name="detail" value="<?= $result['detail'] ?>">
                <input type="hidden" name="price" value="<?= $result['price'] ?>">
                <input type="submit" value="このページを編集">
              </form>
            </td>
            <td style="text-align: center;">
              <form action="foodmenu_retranslate.php" method="POST">
                <input type="hidden" name="user_no" value="<?= $user_no ?>">
                <input type="hidden" name="description_no" value="<?= $description_no ?>">
                <input type="hidden" name="menu_no" value="<?= $menu_no ?>">
                <input type="hidden" name="language" value="<?= $language ?>">
                <input type="hidden" name="menu_name" value="<?= $result['menu_name'] ?>">
                <input type="hidden" name="pic_URL" value="<?= $result['pic_URL'] ?>">
                <input type="hidden" name="allergy" value="<?= $result['allergy'] ?>">
                <input type="hidden" name="detail" value="<?= $result['detail'] ?>">
                <input type="hidden" name="price" value="<?= $result['price'] ?>">
                <input type="submit" value="日本語に再翻訳">
              </form>
            </td>
            <td style="text-align: center;">
              <form action="menu.php" method="POST">
                <input type="hidden" name="user_no" value="<?= $user_no ?>">
                <input type="hidden" name="description_no" value="<?= $description_no ?>">
                <input type="hidden" name="language" value="ja">
                <input type="submit" value="前の画面へ戻る">
              </form>
            </td>
          </tr>
        </table>
      </div>
    <?php

        $page++;
      }else{
        //日本語の時はもう表示したのでなにもしない
      }
    }

    ?>
    </div>    
  </div>
  <!-- $description_no = <?= $description_no ?><br> -->
</body>
</html>

<?php
}else{
  //一般利用者向けの表示
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link href="menu.css" rel="stylesheet">
    <title>メニュー詳細</title>
</head>
<body class="back">
<table border="0">
  <tr>
    <td>
      <table border="1" class="display">
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
    </td>
  </tr>
  <tr>
    <td>
      <table border="1" style="margin: auto">
        <tr>
          <td>
            <form action="menu.php" name="formT" method="POST">
              <input type="hidden" name="user_no" value="<?= $user_no ?>">
              <input type="hidden" name="description_no" value="<?= $description_no ?>">
              <input type="hidden" name="language" value="<?= $language ?>">
              <a href="javascript:formT.submit()" class="btn-link" style="font-size: 20px; font-weight: normal;"><?= $back[$language] ?></a>
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>

<?php
}
?>