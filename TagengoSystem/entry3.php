<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ja">
	<?php
		$shop_name = $_POST['shop_name'];
		$user_ID = $_POST['user_ID'];
		$pass = $_POST['pass'];

		if($shop_name == "" || $user_ID == "" || $pass == "" ){
	?>
	<head>
    	<meta charset="utf-8" />
    	<title></title>
    	<script type="text/javascript">
      	document.location.href = "./login.html";
    	</script>
	</head>
	<body>
  		<p>転送ページです。</p>
	</body>
</html>
	<?php
		}else{
	?>
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="./css/entry1.css">
		<title>ユーザー登録完了画面</title>
		</head>
	<body>
		<p><h2>ユーザー情報を登録しました。</h2>
		ユーザーIDとパスワードはログインに必要になる情報なので忘れないよう気を付けてください。</p>
		<!--
		<table border="1">
			<tr>
				<td>ユーザーID</td>
				<td><?= $user_ID ?></td>
			</tr>
			<tr>
				<td>パスワード</td>
				<td><?= str_repeat("●", strlen($pass)); ?></td>
			</tr>
		</table>
		-->
		<input type="button" value="ログイン画面へ戻る" class="btn" id="backhome" onclick="location.href='./login.html'" >
		<?php
			require_once('mysql_connect.php');
			$pdo = connectDB();
		
			$sql = "insert into login_test values(0 , '$shop_name' , '$user_ID' , '$pass')";
			$stmt = $pdo -> query($sql);
		}	
		?>
	</body>
</html>