<?php
  session_start();
  $size=$_POST['size'];
  $user_no=$_POST['user_no'];
  $description_text=$_POST['description_text'];
  $description_no=$_POST['description_no'];
  //var_dump($_POST);
 
  ?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>内容を入力してください</title>
</head>
<body>
  <div id="outer" border="1">
    <form action="sub.php" method="POST" enctype="multipart/form-data">
      <!-- <textarea name="discription_text"  cols="200" rows="4">説明タイトルを入力</textarea><br> -->
    
        <input type="hidden" name="size" value="<?= $size ?>">
        <table border="1">
        <tr>
            <th></th>
            <th>画像ファイル</th>
            <th>説明文</th>
        </tr>
        <?php
        for($i = 1; $i <= $size; $i++){
        ?>
        <!-- 勝手にdataA[]やdataB[]の[]内に数字を入れていって配列にしてくれる(０から) -->
            
            <tr>
                <td><?=$i?>個目</td>
                <td><input type="file" name="image[]"></td>
                <td><textarea name="text[]"  cols="30" rows="10" style=" margin: 0px; padding: 0px; width: 100%; height: 100%; box-sizing: border-box;"></textarea></td>
            </tr>
        <?php
        }
        ?>
        
        </table>
        <input type="hidden" name="user_no" value="<?=$user_no?>">
      <input type="hidden" name="description_text" value="<?=$description_text?>">
      <input type="hidden" name="description_no" value="<?=$description_no?>">
      <input type="hidden" name="size" value="<?=$size?>">
      <input type="submit" value="送信">
        

     
    </form>
        

</body>
</html>