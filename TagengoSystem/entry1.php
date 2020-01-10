<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="./css/entry1.css">
	<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
	<title>ユーザー情報入力画面</title>
</head>
<body>
	<h1>ユーザ情報を登録します</h1>
	<form action="entry2.php" method="POST">
	<table border="1" id="shop">
		<tr>
			<td class="tblcolor">店名</td>
			<td><input type="text" name="shop_name" required maxlength="20" autocomplete="off" id="input">
			<span id="alert">*必須</span>
			<span id="warm">上限20文字まで</span>
			</td>
		</tr>
		<tr>
			<td class="tblcolor">ユーザID</td>
			<td><input type="text" name="user_ID" required maxlength="20" pattern="^[0-9-Za-z]+$" autocomplete="off" id="input">
			<span id="alert">*必須</span>
			<span id="warm">上限20文字</span>
			<span id="warm">半角英数字</span>
		</td>
		</tr>
		<tr>
			<td class="tblcolor">パスワード</td>
			<td><input type="password" name="pass" required minlength="8" pattern="^[0-9-Za-z]+$" autocomplete="off" id="inputpass">
			<span id="alert">*必須</span>
			<span id="warm">上限20文字</span>
			<span id="warm">半角英数字</span>
			<br>
			<div id="footer-password">
				<input type="checkbox" id="show-password">
				<label for="show-password">パスワードを表示する</label>
			</div>
			</td>
		</tr>
		</table>
		<br>
		<h3>必要事項を記入し「確認」ボタンを<br>クリックしてください。</h3>
		<input type ="submit" value = "確認" class="btn" id="confilm">
		<input type ="reset" value = "クリア" class="btn">
	</form>
	
	<script src="./hideShowPassword.min.js"></script>
	<script>
	$('#show-password').change(function(){
		$('#inputpass').hideShowPassword($(this).prop('checked'));
	});
	</script>
</body>
</html>