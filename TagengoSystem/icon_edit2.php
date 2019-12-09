<?php
session_start();

//POSTされたデータを変数に
$user_no = $_POST['user_no'] ;
$description_no = $_POST['description_no'];
$style = $_POST['style'];
$icon_URL = $_POST['icon_URL'];
$caption = $_POST['caption'];

//画像のアップロード処理に使う定数
const FILE_FOL = "top_icon/";
//画像なしを選択した場合icon_URLは0が入っている
if( strcmp($icon_URL , '0') == 0 ){

    //画像なしを意味する0を代入
    $newfilename = 0;

//アップロードを選択したときicon_URLには1が入っている
}elseif( strcmp($icon_URL , '1') == 0 ){

    //変数の初期化
    $check = null;
    $newfilename = null;
    $msg = null;
    //元ファイル名の先頭にアップロード日時を加える
    $ext = pathinfo($_FILES["upfile"]["name"], PATHINFO_EXTENSION);
    //今日の日付+ランダムな数字列+拡張子
    $newfilename = date("YmdHis").mt_rand().".".$ext;	//こいつがアップロードした画像ファイル名になる
    // ファイルがアップデートされたかを調べる
    if(is_uploaded_file($_FILES["upfile"]["tmp_name"])) {
        $check = 1;
    } else {
        $check = 0;
        $msg = "ファイルが選択されていません。";
    }
    if ($check == 1) {
        if ($_FILES['upload']['size'] > 10000000) {
            $check = 0;
            $msg = 'ファイルサイズを小さくしてください';
        }
    }
    //アップロードされたのが画像か調べる
    $file_pass = $_FILES["upfile"]["tmp_name"];
    if ($check == 1) {
        if( file_exists($file_pass) && ($type = exif_imagetype($file_pass) ) ){
            switch($type){
                //gifの場合
                case IMAGETYPE_GIF:
                break;
                //jpgの場合
                case IMAGETYPE_JPEG:
                break;
                //pngの場合
                case IMAGETYPE_PNG:
                break;
                //どれにも該当しない場合
                default:
                $msg =  "gif、jpg、png以外の画像です";
            }
        }else{
            $msg =  "画像ファイルではありません";
            $check = 0;
        }
    }
    //$checkが0…画像がアップロードされてなかったり対応した拡張子のものが選択されていない
    if($check == 0){
        $newfilename = 0;
    }

    //例外処理を全てクリアしたらファイルをアップする
    if ($check == 1) {
        if ( move_uploaded_file($file_pass, FILE_FOL.$newfilename) ) {
            chmod(FILE_FOL. $_FILES["upfile"]["name"], 0644);
            //print $newfilename. "としてファイルをアップロードしました。<br>";
            //print "<a href=".FILE_FOL.$newfilename. ">ファイルを確かめる</a><br>";
            // print "<img src=".FILE_FOL.$newfilename." width=\"auto\" height=\"48\" style=\"float: left\">";
        } else {
            print "<p>ファイルをアップロードできませんでした。</p>";
        }
    } else {
        //  print "<p>".$msg."</p>";
    }

}else{
    
    //それ以外を選択した場合はファイル名がPOSTされているのでそのまま使う
    $newfilename = $icon_URL;

}

//データベースへのアクセス
require_once('mysql_connect.php');
$pdo = connectDB();

//データベースに登録
$sql = "UPDATE `top_menu` SET `icon_URL` = '$newfilename' , `caption` = '$caption' WHERE `user_no` = '$user_no' AND `description_no` = '$description_no' AND `style` = '$style';";
$stmt = $pdo -> query($sql);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link href="top.css" rel="stylesheet">
    <title>編集内容の登録</title>
</head>
<body>
    登録し直したボタンの内容は以下の通りです。<br>
    <table border="0" style="clear: both;">
    <a class="btn-menu" style="float: both;">
        <?php
        if(strcmp($icon_URL , '0') == 0){
            //画像なし
        }elseif(strcmp($icon_URL , '1') == 0){
        ?>
            <img src="top_icon/<?= $newfilename ?>" width="auto" height="48" style="float: left">
        <?php
        }else{
        ?>
            <img src="top_icon/<?= $icon_URL ?>" width="auto" height="48" style="float: left;">
        <?php
        }
        ?>
        <?= $caption ?>
    </a>
    </table>

    <form action="top.php" name="form1" method="POST">
        <input type="hidden" name="user_no" value="<?= $user_no ?>">
        <a href="javascript:form1.submit()" class="btn-link">トップに戻る</a>
    </form>
    <br>
    <!-- $_POST['user_no'] = <?= $_POST['user_no'] ?><br>
    $_SESSION['user_ID'] = <?= $_SESSION['user_ID'] ?><br>
    $_POST['description_no'] = <?= $_POST['description_no'] ?><br>
    $_POST['style'] = <?= $_POST['style'] ?><br>
    $_POST['icon_URL'] = <?= $_POST['icon_URL'] ?><br>
    $newfilename = <?= $newfilename ?><br>
    $_POST['caption'] = <?= $_POST['caption'] ?><br> -->
</body>
</html>