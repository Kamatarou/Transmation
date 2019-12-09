<?php
$user_no = $_POST['user_no'];
$description_no = $_POST['description_no'];
$style = $_POST['style'];
$language = $_POST['language'];
$remark = $_POST['remark'];
if(isset($_POST['menu_no'])){
  $menu_no = $_POST['menu_no'];
}else{
  $menu_no = NULL;
}

// タイトル用の連想配列
$title = array(
  'ja' => '誤訳の指摘',
  'en' => 'Pointing out mistranslation',
  'zh' => '指出误译',
  'ko' => '오역 지적',
  'th' => 'การแปลไม่ถูกต้อง',
  'vi' => 'Thông báo về dịch sai',
  'ms' => 'Maklumkan kesilapan terjemahan'
);

// 本文用の連想配列
$main = array(
  'ja' => '教えてくださりありがとうございます',
  'en' => 'Thank you for teaching me.',
  'zh' => '谢谢你教我',
  'ko' => '가르쳐 주셔서 감사합니다',
  'th' => 'ขอบคุณที่สอนฉัน',
  'vi' => 'Cảm ơn bạn đã dạy tôi',
  'ms' => 'Terima kasih kerana mengajar saya'
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

//トップ画面へ戻すボタン用の配列
$top = array(
  'ja' => '最初の画面へ戻る',
  'en' => 'Return to the first screen',
  'zh' => '返回第一个画面',
  'ko' => '첫 화면 가기',
  'th' => 'กลับไปที่หน้าจอแรก',
  'vi' => 'Quay trở lại màn hình đầu tiên',
  'ms' => 'Kembali ke skrin pertama'
);

//タイムゾーン設定
date_default_timezone_set ('Asia/Tokyo');

//現在の日時を取得
$date = date('YmdHi');

//リンク先を決める配列
$link = ['0' , 'menu_detail.php' , 'preview.php' , 'preview.php' , 'newSwipe.php' , 'image_annai.php'];

//データベース接続
require_once('mysql_connect.php');
$pdo = connectDB();

$sql = "INSERT INTO `error_collect` VALUES('$user_no' , '$description_no' , '$style' , '$language' , '$menu_no' , '$remark' , $date);";
$stmt = $pdo -> query($sql);
$result = $stmt -> fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $language ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link href="menu.css" rel="stylesheet">
  <title><?= $title[$language] ?></title>
</head>
<body style="text-align:center;">
<h2><?= $main[$language] ?></h2>
  <table border="0" style="width: 16em; margin: auto;">
    <tr>
      <td>
        <form action="<?= $link[$style] ?>" method="POST">
          <input type="hidden" name="user_no" value="<?= $user_no ?>">
          <input type="hidden" name="description_no" value="<?= $description_no ?>">
          <input type="hidden" name="language" value="<?= $language ?>">
          <input type="hidden" name="style" value="<?= $style ?>">
          <input type="hidden" name="menu_no" value="<?= $menu_no ?>">
          <input type="submit" value="<?= $back[$language] ?>">&nbsp;&nbsp;
        </form>
      </td>
      <td>
        <form action="top.php" method="POST">
          <input type="hidden" name="user_no" value="<?= $user_no ?>">
          <input type="hidden" name="language" value="<?= $language ?>">
          <input type="submit" value="<?= $top[$language] ?>">
        </form>
      </td>
    </tr>
  </table>
</body>
</html>