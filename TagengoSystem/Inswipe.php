<?php
 $user_no=$_POST['user_no'];
 $caption=$_POST['caption'];

 //var_dump($_POST);

 ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>SwipeSize</title>
</head>
<body>
  
  <div id="outer" border="1">
   <form action="inputsdata.php" method="POST">
   <h1>タイトルを入力してください</h1>
   <input type="text" name="description_text" id="">
   
   <h1>ページ数を選んでください</h1>
     <select name="size" id="size">
       <option value="2">2</option>
       <option value="3">3</option>
       <option value="4">4</option>
       <option value="5">5</option>
       <option value="6">6</option>
       <option value="7">7</option>
       <option value="8">8</option>
       <option value="9">9</option>
       <option value="10">10</option>
      
     </select>
     <input type="hidden" name="user_no" value="<?=$user_no?>">
      <input type="hidden" name="description_no" value="<?=$description_no?>">
     <input type="submit" value="次へ">
   </form>
  </div>
  
</body>
</html>