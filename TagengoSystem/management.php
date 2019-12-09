<?php
session_start();
header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

//データベースへの接続(別ファイル参照)
require_once('mysql_connect.php');
$pdo = connectDB();

if(isset($_POST['deleteReco'])){
  //deleteRecoがセットされている場合その項目を削除する

  // var_dump($_POST);
  // echo '<br>';
  $d_user_no = $_POST['deleteReco']['user_no'];
  $d_description_no = $_POST['deleteReco']['description_no'];
  $d_style = $_POST['deleteReco']['style'];
  $d_language = $_POST['deleteReco']['language'];;
  $d_menu_no = $_POST['deleteReco']['menu_no'];

  $sql = "DELETE FROM `error_collect` WHERE `user_no` = $d_user_no AND `description_no` = $d_description_no AND `style` = $d_style AND `language` = '$d_language' AND `menu_no` = $d_menu_no;";
  $stmt = $pdo -> query($sql);
  $result = $stmt -> fetch(PDO::FETCH_ASSOC);
  // var_dump($result);
  // echo '<br>';
}

//画面表示用のフラグ
//0は初期状態でテスト用の情報を表示する画面(本来ここには来ないようにしたい)
//1で案内登録者向け,2はログイン失敗画面
$disp_flg = 0;

//ログイン画面を経由してくる場合user_IDとpassというデータがPOSTで送られてくるので
//それがデータベース上に存在するアカウントかどうかをチェック
//user_IDとpassのどちらかがセットされていればとりあえずチェックする
if( isset($_POST['user_ID']) || isset($_POST['pass']) ){

  $user_ID = $_POST['user_ID'];
  $pass = $_POST['pass'];

  $sql = "SELECT `user_no` FROM `login_test` WHERE `user_ID` = '$user_ID' AND `pass` = '$pass';";
  $stmt = $pdo -> query($sql);
  $count = $stmt -> rowCount();	//何件あるか数える

  //アカウントが1件だけヒットした場合管理画面へ
  if($count == 1){

    $disp_flg = 1;
    $_SESSION['manage_flg'] = true;

    //検索してきたuser_noを$user_noへ格納
    $result = $stmt -> fetch(PDO::FETCH_ASSOC);
    $user_no = $result['user_no'];

  }else{

    //それ以外はログイン失敗画面へ(0か2以上かで処理は分けるべき？)
    $disp_flg = 2;

  }

}elseif($_SESSION['manage_flg'] == true){
  //manage_flgは管理者用のフラグ(trueで案内内容の変更や削除が可能になる)

  //trueなら管理者モードなのでdisp_flgを1に
  $disp_flg = 1;
  $user_no = $_POST['user_no'];
  
}

//$disp_flgが変化しなかったとき
//セッションが残っていない状態でいきなりここのURLを指定してきたとき
if($disp_flg == 0){
  ?>
  <!DOCTYPE html>
  <html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>エラー画面</title>
  </head>
  <body>
    こちらはエラー画面です。<br>
    お手数ですが<a href="https://fukuiohr2.sakura.ne.jp/2019/TagengoSystem/login.html">こちら</a>から再度ログインを行ってください<br>
    <br>
    <!-- <b>管理情報</b><br>
    $user_no = <?= $user_no ?><br>
    $user_ID = <?= $user_ID ?><br>
    $language = <?= $language ?><br>
    $_POST['user_no'] = <?= $_POST['user_no'] ?><br>
    $_POST['user_ID'] = <?= $_POST['user_ID'] ?><br>
    $_POST['language'] = <?= $_POST['language'] ?><br>
    $_SESSION['manage_flg'] = <?= $_SESSION['manage_flg'] ?><br> -->
  </body>
  </html>
  <?php
  
  //管理者向け画面
  }elseif($disp_flg == 1){

    //リンク先を決める配列
    $link = ['0' , 'menu_detail.php' , 'preview.php' , 'preview.php' , 'newSwipe.php' , 'image_annai.php'];

    $sql2 = "SELECT * FROM `error_collect` WHERE (`user_no`,`description_no`,`style`,`language`,`menu_no`) IN (SELECT `user_no`,`description_no`,`style`,`language`,`menu_no` FROM `error_collect`GROUP BY `user_no`,`description_no`,`style`,`language`,`menu_no` HAVING COUNT(*) >= 1) ORDER BY `date` ASC;";
    $stmt2 = $pdo -> query($sql2);
    $count = $stmt2 -> rowCount();	//何件あるか数える
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <!-- ビューポートの設定 -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- スタイルシートの読み込み -->
  <link href="top.css" rel="stylesheet">
  <link href="scroll.css" rel="stylesheet">
  <!-- JavaScriptの読み込み -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  <script src="top_modal.js"></script>
  <title>管理画面</title>
</head>
<body>
<div>
  <h3 style="text-align: center;">案内情報の登録・管理</h3>
  <table border="1" style="width: 405px; margin: auto;">
    <tr>
      <td><a class="btn-blue modal-syncer" data-target="modal-content-a" style="cursor: pointer;">メニュー表の作成・編集</a></td>

      <form action="preview_input.php" name="form2" method="POST">
      <input type="hidden" name="user_no" value="<?= $user_no ?>">
      <!-- <input type="hidden" name="description_no" value="<?= $result['description_no'] ?>"> -->
      <!-- <input type="hidden" name="caption" value="<?= $_POST['caption'] ?>"> -->
      <input type="hidden" name="style" value="3">
      <td><a href="javascript:form2.submit()" class="btn-blue">案内情報の登録</a></td>
      </form>
    </tr>
    <tr>
      <form action="Inswipe.php" name="form3" method="POST">
      <input type="hidden" name="user_no" value="<?= $user_no ?>">
      <!-- <input type="hidden" name="description_no" value="<?= $result['description_no'] ?>"> -->
      <!-- <input type="hidden" name="caption" value="<?= $_POST['caption'] ?>"> -->
      <input type="hidden" name="style" value="4">
      <td><a href="javascript:form3.submit()" class="btn-blue">案内情報の登録(スワイプ形式)</a></td>
      </form>

      <form action="image_link.php" name="form4" method="POST">
      <input type="hidden" name="user_no" value="<?= $user_no ?>">
      <!-- <input type="hidden" name="description_no" value="<?= $result['description_no'] ?>"> -->
      <!-- <input type="hidden" name="caption" value="<?= $_POST['caption'] ?>"> -->
      <input type="hidden" name="style" value="5">
      <input type="hidden" name="first_flg" value="true">
      <td><a href="javascript:form4.submit()" class="btn-blue">画像上にリンクを作成する</a></td>
      </form>
    </tr>
    <tr>
      <form action="top.php" name="form5" method="POST">
      <input type="hidden" name="user_no" value="<?= $user_no ?>">
      <td><a href="javascript:form5.submit()" class="btn-orange">プレビュー</a></td>
      </form>
      <form action="http://fukuiohr2.sakura.ne.jp/2019/TagengoSystem/qr_img0.50j/php/qr_img.php?d=https://fukuiohr2.sakura.ne.jp/2019/TagengoSystem/disp.php?shop=<?= $user_no ?>" name="form6" method="POST">
      <td><a href="javascript:form6.submit()" class="btn-pink">QRコード発行</a></td>
      </form>
    </tr>      
  </table>
  <br>
  <h3 style="text-align: center;">誤訳の指摘があったページ</h3>
  <p style="text-align: center;">
  利用者から各ページの翻訳に疑問を感じる部分があった場合にそれを知らせる機能があります。<br>
  以下の表に案内の翻訳がおかしいと感じた利用者から指摘があったページを表示します。<br>
  </p>
  <div class="vertical-scroll-table">
    <table>
      <thead>
        <tr>
          <th>ページタイトル</th>
          <th>指摘日時</th>
          <th>ページ遷移</th>
          <th>削除ボタン</th>
        </tr>
      </thead>
      <tbody>
        <?php
        //誤訳の報告がまだ1件もない場合
        if($count == 0){
        ?>
          <tr>
            <td colspan="3">まだ報告や指摘があったページはありません。</td>
          </tr>
        <?php
        }else{
          //1件以上ある場合
          for($i = 0; $i < $count; $i++){

            $result2 = $stmt2 -> fetch(PDO::FETCH_ASSOC);

            //モーダルでも使用するのでデータのコピーを取っておく
            $mirror[$i]['user_no'] = $result2['user_no'];
            $mirror[$i]['description_no'] = $result2['description_no'];
            $mirror[$i]['style'] = $result2['style'];
            $mirror[$i]['language'] = $result2['language'];
            $mirror[$i]['menu_no'] = $result2['menu_no'];
            $mirror[$i]['remark'] = $result2['remark'];

            //日時を表示するための変数
            $date = "";
            for($j = 0; $j < 12; $j++){
               $date .= $result2['date'][$j];
              if($j == 3 || $j == 5 || $j == 7){
                $date .= "/";
              }elseif($j == 9){
                $date .= ":";
              }
            }
            $mirror[$i]['date'] = $date;
          ?>
            <tr>
              <td style="width: 30%"><?= $result2['remark'] ?></td>
              <td style="width: 30%"><?= $date ?></td>
              <td style="width: 20%">
                <form action="<?= $link[$result2['style']] ?>" method="POST">
                  <input type="hidden" name="user_no" value="<?= $result2['user_no'] ?>">
                  <input type="hidden" name="description_no" value="<?= $result2['description_no'] ?>">
                  <input type="hidden" name="language" value="<?= $result2['language'] ?>">
                  <input type="hidden" name="style" value="<?= $result2['style'] ?>">
                  <?php
                  if($result2['style'] == 1){
                  ?>
                    <input type="hidden" name="menu_no" value="<?= $result2['menu_no'] ?>">
                  <?php  
                  }
                  ?>
                  <input type="submit" value="該当ページへ">
                </form>
              </td>
              <td style="width: 20%">
                <button type="button" class="modal-syncer" data-target="modal-content-<?= $i ?>">削除する</button>
              </td>
            </tr>
        <?php
          }
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<!-- メニュー表に関する部分 [開始] -->
<div id="modal-content-a" class="modal-content">
  <!-- モーダルウィンドウのコンテンツ開始 -->
  <h3>メニュー表の作成・編集</h3>
    <p>
    <table border="1">
    <tr>
      <td>
        <?php
          $sql = "SELECT description_no , caption , MAX(description_no) AS 'recent_no' FROM `top_menu` WHERE user_no = '$user_no' AND style = 1;";
          $stmt = $pdo -> query($sql);
          $result = $stmt -> fetch(PDO::FETCH_ASSOC);
          $input_no = $result['recent_no'];	//style:1が何件あるか数える
          if(is_null($input_no)){      
            //$rowが0の場合style:1のメニューはまだ1件も登録されていないということなので1に
            $input_no = 1;
          }else{
            //それ以外の場合は何件あるか判明しているはずなので+1
            $input_no += 1;
          }
        ?>
        <form action="menu.php" method="POST">
        <input type="hidden" name="user_no" value="<?= $user_no ?>">
        <input type="hidden" name="description_no" value="<?= $input_no ?>">
        <input type="hidden" name="style" value="1">
        <input type="hidden" name="language" value="ja">
        <input type="hidden" name="first_flg" value="true">
        <input type="submit" value="新規にメニュー表を作成する">
        </form>
      </td>
      <td>
        <form action="menu.php" method="POST">
        <input type="hidden" name="user_no" value="<?= $user_no ?>">
        <input type="hidden" name="style" value="1">
        <input type="hidden" name="language" value="ja">
        <input type="hidden" name="first_flg" value="false">
        <select name="description">
          <option> --既存のメニュー表を編集する-- </option>
        <?php
          $sql = "SELECT description_no , caption FROM `top_menu` WHERE user_no = '$user_no' AND style = 1;";
          $stmt = $pdo -> query($sql);
          $row = $stmt -> rowCount();
          for($i = 0; $i < $row; $i++){
            $result = $stmt -> fetch(PDO::FETCH_ASSOC);
        
          echo '<option value="'.$result['description_no'].','.$result['caption'].'">'.$result['caption'].'</option>';
        
          }
        ?>
        </select>
        <input type="submit" value="編集">
        </form>
      </td>
    </tr>
    </table>
    <a class="modal-close button-link">キャンセル</a>
    </p>
  <!-- モーダルウィンドウのコンテンツ終了 -->
</div>
<!-- メニュー表に関する部分 [終了] -->

<!-- 削除の確認部分[開始] -->
<?php
for($i = 0; $i < $count; $i++){
?>
<div id="modal-content-<?= $i ?>" class="modal-content">
  <p>
    <h3 style="text-align: center;">誤訳報告の削除</h3>
    <p style="text-align: center;">以下の項目を削除します。よろしいですか？<br></p>
    <br>
    <table border="0" style="margin: auto;">
      <tr>
        <td>
          <form action="management.php" method="POST">
            <input type="hidden" name="user_no" value="<?= $user_no ?>">
            <input type="submit" value="はい">
            <input type="hidden" name="deleteReco[user_no]" value="<?= $mirror[$i]['user_no'] ?>">
            <input type="hidden" name="deleteReco[description_no]" value="<?= $mirror[$i]['description_no'] ?>">
            <input type="hidden" name="deleteReco[style]" value="<?= $mirror[$i]['style'] ?>">
            <input type="hidden" name="deleteReco[language]" value="<?= $mirror[$i]['language'] ?>">
            <input type="hidden" name="deleteReco[menu_no]" value="<?= $mirror[$i]['menu_no'] ?>">
          </form>
        </td>
        <td><button type="button" class="modal-close">いいえ</button></td>
      </tr>
    </table>
    <div class="vertical-scroll-table">
      <table border="1" style="width: 60%;">
        <thead>
          <tr>
            <th>ページタイトル</th>
            <th>指摘日時</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="width: 50%"><?= $mirror[$i]['remark'] ?></td>
            <td style="width: 50%"><?= $mirror[$i]['date'] ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </p>
</div>
<?php
}
?>
<!-- 削除の確認部分[終了] -->
</body>
</html>
<?php
//ログイン失敗画面
}elseif($disp_flg == 2){
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ログインエラー</title>
</head>
<body>
  こちらはログインエラー画面です。<br>
  お手数ですが、IDとパスワードを確認して再度ログインし直してください。<br>
  <br>
  <a href="login.html">ログイン画面へ戻る</a>
</body>
</html>
<?php

}

?>