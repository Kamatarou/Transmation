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

	require_once('mysql_connect.php');
	$pdo = connectDB();
	
	//user_IDとpassの組み合わせがデータベースに登録されていないかを調べる
	$sql = "select * from `login_test` where user_ID = '$user_ID' and pass = '$pass'";
		$stmt = $pdo -> query($sql);
		$kensu = $stmt -> rowCount();

	//もしuser_name、user_ID、パスワードの中に一つでも何も入力されていないものがあれば
	//あるいは、直接このスクリプトにアクセスした場合
	if($shop_name == "" || $user_ID == "" || $pass == "" ){
?>
	<!DOCTYPE html>
	<html>
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
	<!--
		未入力項目がある場合	
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		<html>
			<head>
				<meta http-equiv = "Content-Style-Type" content = "text/css"/>
				<link href = "style.css" rel = "stylesheet" type = "text/css"/>
				<title>ユーザー登録入力画面</title>
			</head>
			<body>
				<h2 style = "color:#FF0000;">必須項目が入力されていません。</h2>
				<font color = "#FF0000">「*必須」と書かれた項目には必ず必要事項を記入してください。</p><br>
				<h3 style = "color:#800000;">ユーザ情報を登録します。</h3>
				<form action = "entry2.php" method = "POST">
			<table border = "0">
				<tr>
					<td class = "tblcolor">店名</td>
					<td><input size = "40" type = "text" name = "shop_name" value = "<?php if(isset($_POST['shop_name']) ){ echo $shop_name; } ?>">&nbsp;<font color = "#FF0000">*必須</font></td>
				</tr>
				
				<tr>
					<td class = "tblcolor">希望するユーザーID</td>
					<td><input size = "40" type = "text" name = "user_ID" value = "<?php if(isset($_POST['user_ID']) ){ echo $user_ID; } ?>">&nbsp;<font color = "#FF0000">*必須</font></td>
				</tr>

				<tr>
					<td class = "tblcolor">パスワード</td>
					<td><input size = "40" type = "text" name = "pass" value = "<?php if(isset($_POST['pass']) ){ echo $pass; } ?>">&nbsp;<font color = "#FF0000">*必須</p></td>
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
		-->
<?php
	//規定値を超える文字数の項目があった場合
	}elseif( (mb_strlen($shop_name) > 20) || (mb_strlen($pass) > 20) || (mb_strlen($user_ID) > 20) ){
?>
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		<html lang = "ja">
			<head>
				<meta charset="UTF-8">
				<title>ユーザー登録入力画面</title>
			</head>
			<body>
				<h2 style = "color:#FF0000;">文字数が多すぎる項目があります。</h2>
				<font color = "#FF0000">「*この項目は○○文字以下にしてください」と書かれた項目の文字数を減らしてください。</p><br>
				<h3 style = "color:#800000;">ユーザ情報を登録します。</h3>
				<form action = "entry2.php" method = "POST">
					<table border = "0">
						<tr>
							<td class = "tblcolor">店名</td>
							<td><input size = "40" type = "text" name = "shop_name" value = "<?= $shop_name ?>">
							<?php
								if(mb_strlen($shop_name) > 20){
							?>
									<!--	名前が20文字を超えている場合	-->
									<font color = "#FF0000">*この項目は20文字以下にしてください</font>
							<?php
								}
							?>
							</td>
						</tr>

						<tr>
							<td class = "tblcolor">ユーザーID</td>
							<td><input size = "40" type = "text" name = "user_ID" value = "<?= $user_ID ?>">&nbsp;
							<?php
								if(mb_strlen($user_ID) > 20){
							?>
									<!--	userIDが20文字を超えている場合	-->
									<font color = "#FF0000">*この項目は20文字以下にしてください</font>
							<?php
								}
							?>
							</td>
						</tr>

							<tr>
								<td class = "tblcolor">パスワード</td>
								<td><input size = "70" type = "text" name = "pass" value = "<?= $pass ?>">&nbsp;
									<?php
										if(mb_strlen($pass) > 20){
									?>
											<!--	パスワードが20文字を超えている場合	-->
											<font color = "#FF0000">*この項目は20文字以下にしてください</font>
									<?php
										}
									?>
									</td>
							</tr>
					</table>
					<br>必要事項を記入し「確認」ボタンをクリックしてください。<br>
					<table border = "0">
						<input type = "submit" value = "確認"">&nbsp;&nbsp;
					</form>
					<form action = "entry1.php">
						<input type ="submit" value = "クリア">
					</form>
				</table>
			</body>
		</html>

<?php
	//すでに登録されている氏名とパスワードの組み合わせを登録しようとした場合
	//馬鹿正直にすでに登録されていることを教えてしまうとアカウントの乗っ取りなどがありうるので伝え方や対策を考える必要あり
	//今回は「使えないパスワードを設定しようとしている」という形で警告を出す
	}elseif($kensu > 0){
?>
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		<html lang = "ja">
			<head>
				<meta charset="UTF-8">
				<title>ユーザー登録入力画面</title>
			</head>
			<body class = "bg1">

				<h2 style = "color:#FF0000;">登録できないパスワードが設定されています。</h2>
				<font color = "#FF0000">パスワードを別のものに変えてください。</p><br>
				<h3 style = "color:#800000;">ユーザ情報を登録します。</h3>

				<form action = "touroku2.php" method = "POST">
					<table class = "hyo" border = "0">
						<tr>
							<td class = "tblcolor">店名</td>
							<td><input size = "40" type = "text" name = "shop_name" value = "<?= $shop_name ?>"></td>
						</tr>

						<tr>
							<td class = "tblcolor">ユーザーID</td>
							<td><input size = "40" type = "text" name = "user_ID" value = "<?= $user_ID ?>"></td>
						</tr>

						<tr>
							<td class = "tblcolor">パスワード</td>
							<td><input size = "70" type = "text" name = "pass" value = "<?= $pass ?>">&nbsp;
							<font color = "#FF0000">*このパスワードは使用できません</font></td>
						</tr>
					</table>
				<br>必要事項を記入し「確認」ボタンをクリックしてください。<br>
				<table border = "0">
					<input type = "submit" value = "確認"">&nbsp;&nbsp;
				</form>
				<form action = "entry1.php">
					<input type ="submit" value = "クリア">
				</form>
				</table>
			</body>
		</html>

<?php
	}else{
?>
		<!--	登録情報に問題がない場合	-->
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		<html lang = "ja">
			<head>
				<meta charset="UTF-8">
				<link rel="stylesheet" href="./css/entry2.css">
				<link rel="stylesheet" href="./css/entry1.css">
				<title>ユーザー登録確認画面</title>
			</head>
			<body class="entry-inputcheck">
				<h2>入力したデータを表示します</h2>
				<table id = "shop" border = "1">
					<tr>
						<td class = "tblcolor">店名</td>
						<td><?= $shop_name ?></td>
					</tr>
					<tr>
						<td class = "tblcolor">ユーザーID</td>
						<td><?= $user_ID ?></td>
					</tr>
					<tr>
						<td class = "tblcolor">パスワード</td>
						<td id="cnfpass"><?= str_repeat("●", strlen($pass)); ?></td>
					</tr>
				</table>
					<p>入力内容をご確認いただき、問題なければ「登録」ボタンを押してください<br>
					「戻る」ボタンを押すと登録画面に戻ります</p><br>
					<div style = "display:inline-flex">
						<form action = "entry3.php" method = "POST">
							<input type = "hidden" name = "shop_name" value = "<?= $shop_name ?>">
							<input type = "hidden" name = "user_ID" value = "<?= $user_ID ?>">
							<input type = "hidden" name = "pass" value = "<?= $pass ?>">
							<input type = "submit" value = "登録" class="btn" id="confilm">
						</form>
						<form action = "entry1.php" method = "POST">
						<input type = "hidden" name = "shop_name" value = "<?= $shop_name ?>">
							<input type = "hidden" name = "user_ID" value = "<?= $user_ID ?>">
							<input type = "hidden" name = "pass" value = "<?= $pass ?>">
							<input type = "submit" value = "戻る" class="btn" >
						</form>
					</div>
			</body>
		</html>

<?php
	}
?>
