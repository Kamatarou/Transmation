<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<title>ユーザー情報入力画面</title>
	</head>
	<body>

	<?php
		if(isset($_POST['shop_name']) ){
			$shop_name = $_POST['shop_name'];
		}
		if(isset($_POST['user_ID']) ){
			$user_ID = $_POST['user_ID'];
		}
		if(isset($_POST['pass']) ){
			$pass = $_POST['pass'];
		}
	?>

		<h3>ユーザ情報を登録します。</h3>
		<form action = "entry2.php" method = "POST">
			<table border = "0">
				<tr>
					<td class = "tblcolor">店名</td>
					<td><input size = "15" type = "text" name = "shop_name" value = "<?php if(isset($_POST['shop_name']) ){ echo $user_name; } ?>">&nbsp;<font color = "#FF0000">*必須</font></td>
				</tr>
				
				<tr>
					<td class = "tblcolor">希望するユーザーID</td>
					<td><input size = "15" type = "text" name = "user_ID" value = "<?php if(isset($_POST['user_ID']) ){ echo $user_ID; } ?>">&nbsp;<font color = "#FF0000">*必須</font></td>
				</tr>

				<tr>
					<td class = "tblcolor">パスワード</td>
					<td><input size = "15" type = "text" name = "pass" value = "<?php if(isset($_POST['pass']) ){ echo $pass; } ?>">&nbsp;<font color = "#FF0000">*必須</p></td>
				</tr>
			</table>
		<br>必要事項を記入し「確認」ボタンをクリックしてください。<br>
		<table border = "0">
				<input type = "submit" value = "確認">&nbsp;&nbsp;
			</form>
			<form action = "entry1.php">
				<input type ="submit" value = "クリア">
			</form>
		</table>
	</body>
</html>
