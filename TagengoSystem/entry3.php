<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<title>ユーザー登録完了画面</title>
		</head>
	<body>
		ユーザー情報を登録しました。<br>
		<br>
		ユーザーIDとパスワードはログインに必要になる情報なので忘れないよう気を付けてください。<br>
		<?php
			$shop_name = $_POST['shop_name'];
			$user_ID = $_POST['user_ID'];
			$pass = $_POST['pass'];
			
			require_once('mysql_connect.php');
			$pdo = connectDB();
		
			$sql = "insert into login_test values(0 , '$shop_name' , '$user_ID' , '$pass')";
			$stmt = $pdo -> query($sql);
		?>
		<table border="1">
			<tr>
				<td>ユーザーID</td>
				<td><?= $user_ID ?></td>
			</tr>
			<tr>
				<td>パスワード</td>
				<td><?= $pass ?></td>
			</tr>
		
		<br><br><a href = "login.html">ログイン画面へ戻る</a>
	</body>
</html>