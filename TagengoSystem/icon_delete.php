<?php
session_start();
$user_no = $_POST['user_no'];
$description_no = $_POST['description_no'];
$style = $_POST['style'];

//データベースへの接続(別ファイル参照)
require_once('mysql_connect.php');
$pdo = connectDB();

//-------------------------------------------------------------------------------------------------------------------------------
//  ちゃんと削除するものだけ抜き出せるかテスト

$sql = "SELECT * FROM `top_menu` WHERE `user_no` = '$user_no' AND `description_no` = '$description_no' AND `style` = '$style';";
$stmt = $pdo -> query($sql);
$result = $stmt -> fetch(PDO::FETCH_ASSOC);

//  テスト終わり
//--------------------------------------------------------------------------------------------------------------------------------

// DELETE FROM `top_menu` WHERE `user_no` = 8 AND `description_no` = 8;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link href="top.css" rel="stylesheet">
    <title>ボタンの削除</title>
</head>
<body>
    以下のボタンを削除します。<br>
    <a style="color: #FF0000; font-size: 20px">※このボタンのリンク先になっているページのデータも削除されます。</a><br>
    <br>
    <table border="0" style="clear: both;">
      <a class="btn-menu" style="float: both;">
        <p>
        <?php

        //アイコンのところが0の場合はアイコン画像がないので何もしない
        if(strcmp($result['icon_URL'] , 0) == 0){

        }else{
        //それ以外の場合は画像URLが入っているということなので画像のタグを表示

        ?>
          <img src="top_icon/<?= $result['icon_URL'] ?>" width="auto" height="48" style="float: left;">
        <?php

        }
        
        ?>
        </p>
        <?= $result['caption'] ?>
      </a>
    </table>
    <br>
    間違いがなければ「削除」ボタンを押してください。<br>
    <a style="color: #FF0000; font-size: 20px"><img src="image/exclamation.png" width="auto" height="20">削除したものをあとから復元することはできません！</a><br>
    <br>
    <form action="icon_delete2.php" method="POST">
    <input type="hidden" name="user_no" value="<?= $user_no ?>">
    <input type="hidden" name="description_no" value="<?= $description_no ?>">
    <input type="hidden" name="style" value="<?= $style ?>">
    <input type="hidden" name="icon_URL" value="<?= $result['icon_URL'] ?>">
    <table border="0">
      <tr>
        <td><input type="submit" value="削除"></form></td>

        <form action="top.php" method="POST">
        <input type="hidden" name="user_no" value="<?= $user_no ?>">
        <td><input type="submit" value="戻る"></form></td>
      </tr>
    </table>
    

    <!-- <h3>確認用</h3> -->
    <?php
    // var_dump($stmt);
    // echo '<br>';
    // var_dump($result);
    // echo '<br>';
    // var_dump($stmt2);
    // echo '<br>';
    // var_dump($result2);
    ?>

</body>
</html>