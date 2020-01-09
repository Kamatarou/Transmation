<?php
session_start();
header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

//データベースへの接続(別ファイル参照)
require_once('mysql_connect.php');
$pdo = connectDB();

//--------------------------------------------------------------------------------
//  ここからdisp_flgの判定(どの画面を表示するか)


//画面表示用のフラグ
//0は初期状態でテスト用の情報を表示する画面(本来ここには来ないようにしたい)
//1で案内登録者向け,2はログイン失敗画面,3で案内閲覧者向け
//4は言語選択がされてない場合
$disp_flg = 0;

//manage_flgは管理者用のフラグ(trueで案内内容の変更や削除が可能になる)
if($_SESSION['manage_flg'] == true){

  //trueなら管理者モードなのでdisp_flgを1に
  $disp_flg = 1;
  $user_no = $_POST['user_no'];

}else{

  //disp.phpから来た場合
  //user_no(店の識別番号)とlanguage(翻訳する言語)が来ているはずなのでチェック
  if( isset($_POST['user_no']) ){

    $user_no = $_POST['user_no'];

    //user_noが設定されていてかつlanguageも設定されていたら閲覧者向け画面へ
    if( isset($_POST['language']) ){

      $disp_flg = 3;
      $language = $_POST['language'];

    }else{
      //user_noは設定されていたがlanguageが選択されずに送信されてきた場合
      $disp_flg = 4;

    }

  }

}

//ログイン画面を経由してくる場合user_IDとpassというデータがPOSTで送られてくるので
//それがデータベース上に存在するアカウントかどうかをチェック
//user_IDとpassのどちらかがセットされていればとりあえずチェックする
if( isset($_POST['user_ID']) || isset($_POST['pass']) ){

  $user_ID = $_POST['user_ID'];
  $pass = $_POST['pass'];

  $sql = "SELECT `user_no` FROM `login_test` WHERE `user_ID` = '$user_ID' AND `pass` = '$pass';";
  $stmt = $pdo -> query($sql);
  $count = $stmt -> rowCount();	//何件あるか数える

  //アカウントが1件だけヒットした場合登録者向け画面へ
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

}




//--------------------------------------------------------------------------------------------
//  動作テスト用
//
//user_no(変数a)とuser_ID(変数b)をGETでも受け取れるようにしておく
//user_noだけがセットされた場合は閲覧のみ、user_noとuser_IDがセットされた場合は編集も可能にする
if( isset($_GET['a']) ){
	$user_no = $_GET['a'];
  $_SESSION['user_no'] = $user_no;
}
if( isset($_GET['b']) ){
	$_SESSION['user_ID'] = $_GET['b'];
  $_SESSION['manage_flg'] = true;
}
if( isset($_GET['c']) ){
  $language = $_GET['c']; 
}
if( isset($_GET['d']) ){
  //管理者モードを解除したいとき用
	session_destroy();
	session_start();
  $_SESSION['manage_flg'] = false;
}
//  ここまで動作テスト用のデータ管理
//------------------------------------------------------------------------------------------


//  ここまでdisp_flgの判定
//------------------------------------------------------------------------------------------

//リンク先を決める配列を定義
$link = ['0' , 'menu.php' , 'preview.php' , 'preview.php' , 'newSwipe.php' , 'image_annai.php'];
// var_dump($link);

//店名を表示するために検索する
$sql = "SELECT `user_name` FROM `login_test` WHERE `user_no` = '$user_no';";
$stmt = $pdo -> query($sql);
$result = $stmt -> fetch(PDO::FETCH_ASSOC);
$shop_name = $result['user_name'];

//案内の選択肢を検索
$sql = "SELECT description_no , style , icon_URL , caption FROM `top_menu` WHERE user_no = '$user_no';";
$stmt = $pdo -> query($sql);
$row = $stmt -> rowCount();	//何件あるか数える

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
  お手数ですがブラウザの戻るボタンで前の画面へ戻ってください<br>
  <br>
  <b>管理情報</b><br>
  $user_no = <?= $user_no ?><br>
  $user_ID = <?= $user_ID ?><br>
  $language = <?= $language ?><br>
  $_POST['user_no'] = <?= $_POST['user_no'] ?><br>
  $_POST['user_ID'] = <?= $_POST['user_ID'] ?><br>
  $_POST['language'] = <?= $_POST['language'] ?><br>
  $_SESSION['manage_flg'] = <?= $_SESSION['manage_flg'] ?><br>
</body>
</html>
<?php

//登録者向け画面
}elseif($disp_flg == 1){

  //音声翻訳ボタンの文字
  $graybutton = array(
    'ja' => '音声翻訳',
    'en' => 'Speech translation',
    'zh' => '语音翻译',
    'ko' => '음성 번역',
    'th' => 'แปลคำพูด',
    'vi' => 'Dịch giọng nói',
    'ms' => 'Terjemahan suara'
  );

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="robots" content="noindex,nofollow">

		<!-- ビューポートの設定 -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- スタイルシートの読み込み -->
		<link href="top.css" rel="stylesheet">

		<!-- JavaScriptの読み込み -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script src="top_modal.js"></script>
		<script src="icon_preview.js"></script>

		<title>トップページ(管理者用)</title>

	</head>
<body>
	<div style="width: 100%">
  <table border="0" style="margin: auto;">
    <tr>
      <td>    
        <h2 style="text-align: center;">プレビュー画面(管理者向け)</h2>
      </td>
    </tr>
    <tr>
      <td>
        ここには登録した案内一覧が青いボタンで表示されます。<br>
        各種ボタンをクリックするとリンク先に移動したり<br>
        ボタンにアイコン画像を設定したりボタンを削除したりできます。<br>
        <br>
        ボタンの表示言語を切り替えることができます。
      </td>
    </tr>
    <tr>
      <td>
        <form action="top.php" method="POST">
        <input type="hidden" name="user_no" value="<?= $user_no ?>">
        <table style="margin: auto;">
        <tr>
          <td>
            <select name="language">
              <option value="ja"> --言語選択-- </option>
              <option value="ja">日本語</option>
              <option value="en">英語</option>
              <option value="zh">中国語</option>
              <option value="ko">韓国語</option>
              <option value="th">タイ語</option>
              <option value="vi">ベトナム語</option>
              <option value="ms">マレー語</option>
            </select>
          </td>
          <td>
            <input type="submit" value="変更">
          </td>
          </tr>
        </table>
        </form>
      </td>
    </tr>
    <tr>
      <td>
        <br>
        <form method="POST" name="form2" action="management.php">
          <input type="hidden" name="user_no" value="<?= $user_no ?>">
          <div style="text-align: center;"><a href="javascript:form2.submit()">案内登録画面へ戻る</a></div>
        </form>
      </td>
    </tr>
  </table>
  <HR style="margin: 3em 0 ;">
  <?php
    //----------------------------------------------------------------------------------
    //  APIで翻訳してるところ
    if(isset($_POST['language'])){
      if(strcmp($_POST['language'] , 'ja') != 0){
        //お店の名前(shop_name)の翻訳 固有名詞なので今回は英語で固定ということにする
        $data = array(
          'q' => $shop_name,
          'target' => 'en',
          'format' => 'text'
        );			
        $data_json = json_encode($data);

        //-----------------------------------------------------------------------------------
        //ここからphp,curlを利用してGoogleTranslationAPIに$dataで書いた形の連想配列をPOSTする
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, 'https://translation.googleapis.com/language/translate/v2?key=AIzaSyBOzVoDALHdlL3fngB8SaNyPd_X26fpO00');
        $res = curl_exec($ch);
        curl_close($ch);

        //POSTおわり
        //-----------------------------------------------------------------------------------

        $res_json = json_decode($res , true);
        // 翻訳後のshop_name
        $shop_name = $res_json["data"]["translations"][0]["translatedText"];
      }
    }
    //  翻訳おわり
    //-------------------------------------------------------------------------------------
  ?>
  <h2 style="text-align: center;"><?= $shop_name ?></h2>
  <HR style="margin: 3em 0 ;">
		<table border="0" class="button">
    <?php
    //ボタンの表示
    //SQLで検索してヒットしたデータをすべて表示する
		for($i = 0; $i < $row; $i++){

      $result = $stmt -> fetch(PDO::FETCH_ASSOC);      
      //----------------------------------------------------------------------------------
      //  翻訳開始
			if(isset($_POST['language'])){
        if(strcmp($_POST['language'] , 'ja') != 0){
          $language = $_POST['language'];
          //説明文(caption)の翻訳
          $data = array(
            'q' => $result['caption'],
            'target' => $language,
            'format' => 'text'
          );			
          $data_json = json_encode($data);
          
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_URL, 'https://translation.googleapis.com/language/translate/v2?key=AIzaSyBOzVoDALHdlL3fngB8SaNyPd_X26fpO00');
          $res = curl_exec($ch);
          curl_close($ch);

          $res_json = json_decode($res , true);
          // 翻訳後のcaption
          $result['caption'] = $res_json["data"]["translations"][0]["translatedText"];
        }
      }
      //  翻訳おわり
      //-------------------------------------------------------------------------------------
      
      //$resultの内容はもう一度利用したい場面があるので記憶しておく
      $menu_no[$i] = $result['description_no'];
      $style[$i] = $result['style'];
      $icon_URL[$i] = $result['icon_URL'];
      $caption[$i] = $result['caption'];

			// アイコンは2個を1列で表示したいので先頭に来るアイコンの前に<tr>タグをつける
			if( ($i % 2) == 0 ){

		?>
      <tr>
    <?php
    
      }
      
		?>
      <td><a class="btn-menu modal-syncer button-link" data-target="modal-content-<?= $i ?>">
    <?php

        //アイコンのところが0の場合はアイコン画像がないので何もしない
				if(strcmp($result['icon_URL'] , 0) == 0){

				}else{
        //それ以外の場合は画像URLが入っているということなので画像のタグを表示

		?>
					<img src="top_icon/<?= $result['icon_URL'] ?>" width="auto" height="48" style="float: left;">
    <?php
    
        }
        //アイコンの見出し？説明文？
		?>
				<?= $result['caption'] ?></a></td>
    <?php

      //偶数個目の場合はテーブルの改行($iは0からカウントがスタートしているため余り1のときが偶数個目)
			if( ($i % 2) == 1 ){
        echo '</tr>';
      }   
    }

    //$iが奇数個目の場合( ($i % 2) == 0 )</tr>が入っていないので入れておく
    if( ($i % 2) == 0 ){
      echo '</tr>';
    }

		?>
    </table>
    <HR style="margin: 3em 0 ;">
    <a href="https://fukuiohr2.sakura.ne.jp/2019/TagengoSystem/TK/Translator2/index.html" class="btn-gray" style="float: both; margin: auto;"><?php if( isset( $_POST['language']) ){ echo $graybutton[$language]; }else{ echo '音声翻訳'; } ?></a>  
  </div>
  <?php

  //ボタンの数だけモーダルウィンドウのコンテンツを作成する
  for($j = 0; $j < $row; $j++){

  ?>
  <!-- for文で複製した<?= $j + 1 ?>個目のコンテンツ(編集・削除するか普通にリンク先に飛ぶか) [開始] -->
    <div id="modal-content-<?= $j ?>" class="modal-content">
      <!-- モーダルウィンドウのコンテンツ開始 -->
      <h3>管理者モード</h3>
      <p>「リンク先へ」からこのボタン本来のリンク先へ進むか<br>
      「このボタンを編集」からボタンの画像やメッセージを変更したり<br>
      削除したりすることができます。</p>
      <!-- <p>
      $j = <?= $j ?><br>
      $style[$j] = <?= $style[$j] ?><br>
      $menu_no[$j] = <?= $menu_no[$j] ?><br>
      $caption[$j] = <?= $caption[$j] ?><br>
      $link[ $style[$j] ] = <?= $link[ $style[$j] ] ?><br>
      </p> -->
      <table border="0">
      <tr>
        <form action="<?= $link[ $style[$j] ] ?>" name="formM1" method="POST">
          <input type="hidden" name="user_no" value="<?= $user_no ?>">
          <input type="hidden" name="description_no" value="<?= $menu_no[$j] ?>">
          <input type="hidden" name="caption" value="<?= $caption[$j] ?>">
          <input type="hidden" name="style" value="<?= $style[$j] ?>">
          <input type="hidden" name="caption" value="<?= $caption[$j] ?>">
          <!-- <input type="hidden" name="first_flg" value="false"> -->
          <?php
          if(isset($_POST['language'])){
          ?>
            <input type="hidden" name="language" value="<?= $_POST['language'] ?>">
          <?php
          }else{
          ?>
            <input type="hidden" name="language" value="ja">
          <?php
          }
          if($row == 1){
          ?>
          <td><a href="javascript:formM1.submit()" class="btn-link">リンク先へ</a>&nbsp;&nbsp;</td>
          <?php
          }else{
          ?>
          <td><a href="javascript:formM1[<?= $j ?>].submit()" class="btn-link">リンク先へ</a>&nbsp;&nbsp;</td>
          <?php
          }
          ?>
        </form>
        <form action="icon_edit.php" name="formM2" method="POST">
          <input type="hidden" name="user_no" value="<?= $user_no ?>">
          <input type="hidden" name="description_no" value="<?= $menu_no[$j] ?>">
          <input type="hidden" name="style" value="<?= $style[$j] ?>">
          <input type="hidden" name="icon_URL" value="<?= $icon_URL[$j] ?>">
          <input type="hidden" name="caption" value="<?= $caption[$j] ?>">
          <?php
          if($row == 1){
          ?>
            <td><a href="javascript:formM2.submit()" class="button-link">このボタンの編集</a>&nbsp;&nbsp;</td>
          <?php
          }else{
          ?>
            <td><a href="javascript:formM2[<?= $j ?>].submit()" class="button-link">このボタンの編集</a>&nbsp;&nbsp;</td>
          <?php
          }
          ?>
        </form>

        <form action="icon_delete.php" name="formM3" method="POST">
          <input type="hidden" name="user_no" value="<?= $user_no ?>">
          <input type="hidden" name="description_no" value="<?= $menu_no[$j] ?>">
          <input type="hidden" name="style" value="<?= $style[$j] ?>">
          <?php
          if($row == 1){
          ?>
          <td><a href="javascript:formM3.submit()" class="button-link">このボタンの削除</a>&nbsp;&nbsp;</td>
          <?php
          }else{
          ?>
          <td><a href="javascript:formM3[<?= $j ?>].submit()" class="button-link">このボタンの削除</a>&nbsp;&nbsp;</td>
          <?php
          }
          ?>
        </form>

        <td><a class="modal-close button-link">キャンセル</a></td>
      </tr>
      </table>
      <!-- モーダルウィンドウのコンテンツ終了 -->
    </div>
  <!-- コンテンツ [終了] -->
  <?php

  }

  ?>
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

//閲覧者向けページ
}elseif($disp_flg == 3){
$link = ['0' , 'menu.php' , 'preview.php' , 'preview.php' , 'newSwipe.php' , 'image_annai.php'];
$graybutton = array(
  'ja' => '音声翻訳',
  'en' => 'Speech translation',
  'zh' => '语音翻译',
  'ko' => '음성 번역',
  'th' => 'แปลคำพูด',
  'vi' => 'Dịch giọng nói',
  'ms' => 'Terjemahan suara'
);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">

		<!-- ビューポートの設定 -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- スタイルシートの読み込み -->
    <link href="top.css" rel="stylesheet">

		<title>トップページ</title>

	</head>
<body>
	<div>
  <?php
    //----------------------------------------------------------------------------------
    //  APIで翻訳してるところ
    if(isset($_POST['language'])){
      if(strcmp($_POST['language'] , 'ja') != 0){
        //お店の名前(shop_name)の翻訳 固有名詞なので今回は英語で固定ということにする
        $data = array(
          'q' => $shop_name,
          'target' => 'en',
          'format' => 'text'
        );			
        $data_json = json_encode($data);

        //-------------------------------------------------------------------------------
        //ここからphp,curlを利用してGoogleTranslationAPIに$dataで書いた形の連想配列をPOSTする
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, 'https://translation.googleapis.com/language/translate/v2?key=AIzaSyBOzVoDALHdlL3fngB8SaNyPd_X26fpO00');
        $res = curl_exec($ch);
        curl_close($ch);

        //POSTおわり
        //--------------------------------------------------------------------------------

        $res_json = json_decode($res , true);
        // 翻訳後のshop_name
        $shop_name = $res_json["data"]["translations"][0]["translatedText"];
      }
    }
    //  翻訳おわり
    //-------------------------------------------------------------------------------------
  ?>
  <h2 style="text-align: center;"><?= $shop_name ?></h2>
  <HR style="margin: 3em 0 ;">
		<table class="button">
    <?php
    
    //SQLで検索してヒットしたデータをすべて表示する
		for($i = 0; $i < $row; $i++){

      $result = $stmt -> fetch(PDO::FETCH_ASSOC);
			//----------------------------------------------------------------------------------
      //  APIで翻訳してるところ
			if(isset($_POST['language'])){
        $language = $_POST['language'];
				//説明文(caption)の翻訳
				$data = array(
					'q' => $result['caption'],
					'target' => $language,
					'format' => 'text'
				);			
				$data_json = json_encode($data);

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_URL, 'https://translation.googleapis.com/language/translate/v2?key=AIzaSyBOzVoDALHdlL3fngB8SaNyPd_X26fpO00');
				$res = curl_exec($ch);
        curl_close($ch);

				$res_json = json_decode($res , true);
				// 翻訳後のcaption
        $result['caption'] = $res_json["data"]["translations"][0]["translatedText"];
        
      }
      //  翻訳おわり
      //-------------------------------------------------------------------------------------

			// アイコンは2個を1列で表示したいので先頭に来るアイコンの前に<tr>タグをつける
			if( ($i % 2) == 0 ){
		?>
      <tr>
    <?php
    
      }
      
		?>
			<form action="<?= $link[ $result['style'] ] ?>" name="form2" method="POST">
				<input type="hidden" name="user_no" value="<?= $user_no ?>">
				<input type="hidden" name="description_no" value="<?= $result['description_no'] ?>">
        <input type="hidden" name="style" value="<?= $result['style'] ?>">
        <input type="hidden" name="caption" value="<?= $result['caption'] ?>">
        <?php
          if(isset($language)){
          ?>
            <input type="hidden" name="language" value="<?= $language ?>">
          <?php
          }else{
          ?>
            <input type="hidden" name="language" value="ja">
          <?php
          }
          if($row == 1){
          ?>
			      <td><a href="javascript:form2.submit()" class="btn-menu">
          <?
          }else{
          ?>
			      <td><a href="javascript:form2[<?= $i ?>].submit()" class="btn-menu">
          <?php
          }
          ?>
			</form>
		<?php

				//アイコンのところが0の場合はアイコン画像がないので何もしない
				if(strcmp($result['icon_URL'] , 0) == 0){

				}else{
        //それ以外の場合は画像URLが入っているということなので画像のタグを表示

		?>
					<img src="top_icon/<?= $result['icon_URL'] ?>">
    <?php
    
         }
         //説明文

		?>
				<?= $result['caption'] ?></a></td>
    <?php
      //偶数個目の場合はテーブルの改行($iは0からカウントがスタートしているため余り1のときが偶数個目)
			if( ($i % 2) == 1 ){

		?>
				</tr>
    <?php
    
      }
    }
    
      //件数が奇数だった場合</tr>タグで閉じられていないのでタグを追加する
      if( ($row % 2) == 1 ){

		?>
			  </tr>
    <?php
    
      }
      //偶数個目のときはちゃんと</tr>で閉じてあるはずなので特に何もしなくて良い
    
		?>	
		  </tr>
    </table>
    <HR style="margin: 3em 0 ;">
    <a href="https://fukuiohr2.sakura.ne.jp/2019/TagengoSystem/TK/Translator2/index.html" class="btn-gray" style="float: both; margin: auto;"><?= $graybutton[$language] ?></a>
	</div>
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

//languageが選択されなかったときにくる画面
}elseif($disp_flg == 4){

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link href='tab.css' rel='stylesheet' type='text/css'>
  <link rel="shortcut icon" href="">
  <title>LanguageError</title>
</head>
<body>
  <div class="cp_tab">

    <input type="radio" name="cp_tab" id="tab1_0" aria-controls="page0_tab01" checked>
    <label for="tab1_0">English</label>

    <input type="radio" name="cp_tab" id="tab1_1" aria-controls="page1_tab01">
    <label for="tab1_1">中國人</label>

    <input type="radio" name="cp_tab" id="tab1_2" aria-controls="page2_tab01">
    <label for="tab1_2">한국</label>

    <input type="radio" name="cp_tab" id="tab1_3" aria-controls="page3_tab01">
    <label for="tab1_3">ภาษาไทย</label>

    <input type="radio" name="cp_tab" id="tab1_4" aria-controls="page4_tab01">
    <label for="tab1_4">Tiếng việt nam</label>

    <input type="radio" name="cp_tab" id="tab1_5" aria-controls="page5_tab01">
    <label for="tab1_5">Melayu</label>


    <div class="cp_tabpanels">

        <div id="page0_tab01" class="cp_tabpanel">
          <h3>Please check the radio button and press "⇒"</h3>
          <p><a href="disp.php?shop=<?= $user_no ?>">Return to the previous screen</a></p>
        </div>

        <div id="page1_tab01" class="cp_tabpanel">
        <h3>請檢查單選按鈕，然後按“⇒”</h3>
          <p><a href="disp.php?shop=<?= $user_no ?>">返回上一個畫面</a></p>
        </div>

        <div id="page2_tab01" class="cp_tabpanel">
          <h3>라디오 버튼을 체크 한 후 "⇒"를 누르십시오</h3>
          <p><a href="disp.php?shop=<?= $user_no ?>">이전 화면으로 돌아 가기</a></p>
        </div>

        <div id="page3_tab01" class="cp_tabpanel">
          <h3>กรุณาตรวจสอบปุ่มตัวเลือกและกด "⇒"</h3>
          <p><a href="disp.php?shop=<?= $user_no ?>">กลับไปที่หน้าจอก่อนหน้า</a></p>
        </div>

        <div id="page4_tab01" class="cp_tabpanel">
          <h3>Vui lòng kiểm tra nút radio và bấm "⇒"</h3>
          <p><a href="disp.php?shop=<?= $user_no ?>">Quay trở lại màn hình trước</a></p>
        </div>

        <div id="page5_tab01" class="cp_tabpanel">
          <h3>Sila semak butang radio dan tekan "⇒"</h3>
          <p><a href="disp.php?shop=<?= $user_no ?>">Kembali ke screen sebelumnya</a></p>
        </div>

    </div>

  </div>
</body>
</html>
<?php
}
?>