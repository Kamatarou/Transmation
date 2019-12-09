<?php
session_start();

//POSTされたデータを変数に
$user_no = $_POST['user_no'] ;
$description_no = $_POST['description_no'];
$style = $_POST['style'];
$icon_URL = $_POST['icon_URL'];
$caption = $_POST['caption'];

$check_flg = false;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex,nofollow">

    <!-- ビューポートの設定 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- スタイルシートの読み込み -->
    <link href="top.css" rel="stylesheet">
    <style>
    a{
      display: block;
    }
    </style>

    <!-- JavaScriptの読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="top_modal.js"></script>
    <script src="icon_preview.js"></script>
        
    <title>ボタンの編集</title>
</head>
<body>
<table border="0" class="button">
<tr>
  <td>
    <h2 style="text-align: center;">ボタンの編集</h2>
  </td>
</tr>
<tr>
  <td>
    ボタンの内容を編集します。<br>
    アイコン画像をクリックするとほかのアイコン画像に変更したり<br>
    自分で用意した画像をアップロードしてアイコンとして使ったりできます。<br>
    <br>
  </td>
</tr>
<tr>
  <td>
    <form action="icon_edit2.php" method="POST" enctype="multipart/form-data">
    <table border="1" style="clear: both; margin: auto;">
      <tr>
        <th>
          編集対象のボタン
        </th>
      </tr>
      <tr>
        <td>
          <a class="btn-menu" style="float: both;">
              <p id="changeB" class="button-link modal-syncer" data-target="modal-content-a"><img src="top_icon/<?= $icon_URL ?>" id="preview" width="auto" height="48" style="float: left;"></p>
              <textarea name="caption" cols="8" rows="3"><?= $caption ?></textarea>
          </a>
        </td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td>
    <table border="0" style="margin: auto;">
      <tr>
        <td style="text-align: center;">
          <a id="uploadZone" class="button-link modal-syncer" data-target="modal-content-b"></a>
          <input type="hidden" name="user_no" value="<?= $user_no ?>">
          <input type="hidden" name="description_no" value="<?= $description_no ?>">
          <input type="hidden" name="style" value="<?= $style ?>">
        </td>
      </tr>
      <tr>
        <td style="text-align: center;">
          <input type="submit" style="height: 3.4em; width: 10em;" value="変 更">
        </td>
      </tr>
    </table>
    </td>
  </tr>
</table>
<br>
<div class="preview">
</div>
  <!-- $_POST['user_no'] = <?= $_POST['user_no'] ?><br>
  $_POST['description_no'] = <?= $_POST['description_no'] ?><br>
  $_POST['style'] = <?= $_POST['style'] ?><br>
  $_POST['icon_URL'] = <?= $_POST['icon_URL'] ?><br>
  $_POST['caption'] = <?= $_POST['caption'] ?><br> -->

<!-- アイコン選択 [開始] -->
<div id="modal-content-a" class="modal-content">
  <!-- モーダルウィンドウのコンテンツ開始 -->
  <h3>アイコン画像を選択</h3>
  <p>アイコンに利用する画像を変更できます。<br>
  手持ちの画像をアイコンとして利用したい場合は「画像をアップロード」を選択してください。</p>
  <p>
    <table border="0">
      <tr>
        <td>
          <table border="1" style="table-layout: fixed; width: 80%; margin: auto;">
            <tr>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[0]" value="0" <?php if(strcmp($icon_URL , '0') == 0){ echo 'checked="checked"'; $check_flg = true; $default_no = 0; } ?> onclick="iconChange(0)"><img src="top_icon/noicon.png" id="icon[0]" style="display: none;" height="48" width="auto">アイコンなし</a></label></td>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[1]" value="1" onclick="buttonChange()" id="icon[1]">画像をアップロード</a></label></td>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[2]" value="edit.png" <?php if(strcmp($icon_URL , 'edit.png') == 0){ echo 'checked="checked"'; $check_flg = true; $default_no = 2; } ?> onclick="iconChange(2)"><img src="top_icon/edit.png" id="icon[2]" width="auto" height="48"></a></label></td>
            </tr>
            <tr>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[3]" value="foodmenu.png" <?php if(strcmp($icon_URL , 'foodmenu.png') == 0){ echo 'checked="checked"'; $check_flg = true; $default_no = 3; } ?> onclick="iconChange(3)"><img src="top_icon/foodmenu.png" id="icon[3]" width="auto" height="48"></a></label></td>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[4]" value="dinner.png" <?php if(strcmp($icon_URL , 'dinner.png') == 0){ echo 'checked="checked"'; $check_flg = true; $default_no = 4; } ?> onclick="iconChange(4)"><img src="top_icon/dinner.png" id="icon[4]" width="auto" height="48"></a></label></td>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[5]" value="notcomeyet.png" <?php if(strcmp($icon_URL , 'notcomeyet.png') == 0){ echo 'checked="checked"'; $check_flg = true; $default_no = 5; } ?> onclick="iconChange(5)"><img src="top_icon/notcomeyet.png" id="icon[5]" width="auto" height="48"></a></label></td>
            </tr>
            <tr>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[6]" value="wrongorder2.png" <?php if(strcmp($icon_URL , 'wrongorder2.png') == 0){ echo 'checked="checked"'; $check_flg = true; $default_no = 6; } ?> onclick="iconChange(6)"><img src="top_icon/wrongorder2.png" id="icon[6]" width="auto" height="48"></a></label></td>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[7]" value="tip.png" <?php if(strcmp($icon_URL , 'tip.png') == 0){ echo 'checked="checked"'; $check_flg = true; $default_no = 7; } ?> onclick="iconChange(7)"><img src="top_icon/tip.png" id="icon[7]" width="auto" height="48"></a></label></td>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[8]" value="creditcard.png" <?php if(strcmp($icon_URL , 'creditcard.png') == 0){ echo 'checked="checked"'; $check_flg = true; $default_no = 8; } ?> onclick="iconChange(8)"><img src="top_icon/creditcard.png" id="icon[8]" width="auto" height="48"></a></label></td>
            </tr>
            <tr>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[9]" value="smoking.png" <?php if(strcmp($icon_URL , 'smoking.png') == 0){ echo 'checked="checked"'; $check_flg = true; $default_no = 9; } ?> onclick="iconChange(9)"><img src="top_icon/smoking.png" id="icon[9]" width="auto" height="48"></a></label></td>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[10]" value="nonsmoking.png" <?php if(strcmp($icon_URL , 'nonsmoking.png') == 0){ echo 'checked="checked"'; $check_flg = true; $default_no = 10; } ?> onclick="iconChange(10)"><img src="top_icon/nonsmoking.png" id="icon[10]" width="auto" height="48"></a></label></td>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[11]" value="ticketmachine.png" <?php if(strcmp($icon_URL , 'ticketmachine.png') == 0){ echo 'checked="checked"'; $check_flg = true; $default_no = 11; } ?> onclick="iconChange(11)"><img src="top_icon/ticketmachine.png" id="icon[11]" width="auto" height="48"></a></label></td>
            </tr>
            <tr>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[12]" value="toilet.png" <?php if(strcmp($icon_URL , 'toilet.png') == 0){ echo 'checked="checked"'; $check_flg = true; $default_no = 12; } ?> onclick="iconChange(12)"><img src="top_icon/toilet.png" id="icon[12]" width="auto" height="48"></a></label></td>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[13]" value="next.png" <?php if(strcmp($icon_URL , 'next.png') == 0){ echo 'checked="checked"'; $check_flg = true; $default_no = 13; } ?> onclick="iconChange(13)"><img src="top_icon/next.png" id="icon[13]" width="auto" height="48"></a></label></td>
              <td><label><a><input type="radio" name="icon_URL" id="inputIcon[14]" value="recommend.png" <?php if(strcmp($icon_URL , 'recommend.png') == 0){ echo 'checked="checked"'; $check_flg = true; $default_no = 14; } ?> onclick="iconChange(14)"><img src="top_icon/recommend.png" id="icon[14]" width="auto" height="48"></a></label></td>
            </tr>
            <?php
            if($check_flg == false){
              $default_no = 15;
            ?>
              <tr>
                <td><label><a><input type="radio" name="icon_URL" id="inputIcon[15]" value="<?= $icon_URL ?>" checked="checekd" onclick="iconChange(15)"><img src="top_icon/<?= $icon_URL ?>" id="icon[15]" width="auto" height="48">アップロードした画像を使う</a></label></td>
              </tr>
            <?php
            }
            ?>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table border="0" style="width: 80%; table-layout: fixed; margin: auto;">
            <tr>
              <td style="text-align: center;">
                <a class="modal-close button-link" onclick="labelChange();">決定</a>
              </td>
              <td style="text-align: center;">
                <label for="inputIcon[<?= $default_no ?>]" id="cancelLabel"><a class="modal-close button-link" onclick="iconChange(<?= $default_no ?>)">キャンセル</a></label>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </p>
  <!-- モーダルウィンドウのコンテンツ終了 -->
</div>
<!-- アイコン選択 [終了] -->

<!-- 画像選択 [開始] -->
<div id="modal-content-b" class="modal-content">
  <!-- モーダルウィンドウのコンテンツ開始 -->
    <h3>アイコン画像のアップロード</h3>
    <p>
      アイコンとして利用する画像はなるべく正方形に近いサイズのものを利用してください。<br>
      縦と横の差が大きくなると表示される画像が小さくなってしまう場合があります。<br>
      <input type="file" name="upfile" id="uploader" size="30"><br>
      <br>
      <a class="modal-close button-link" onclick="choicePicture();">決定</a>&nbsp;&nbsp;
      <!-- <a class="modal-close button-link" onclick="cancelPicture();">キャンセル(選択した画像のリセット)</a> -->
      <a class="modal-close button-link">キャンセル</a>
  </p>
  <!-- モーダルウィンドウのコンテンツ終了 -->
</div>
<!-- 画像選択 [終了] -->
</form>
</body>
</html>