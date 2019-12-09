<?php

//PDO MySQL接続
function connectDB(){

//ユーザ名やDBアドレスの定義
    $dsn = 'mysql:dbname=fukuiohr2_tagengo;host=mysql640.db.sakura.ne.jp;charset=utf8';
    $username = 'fukuiohr2';
    $password = 'fki2d2019';

    try {
        $pdo = new PDO($dsn, $username, $password);
	    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);//プリペアードステートメント SQLエラーチェック

    } catch (PDOException $e) {
        exit('' . $e->getMessage());
    }
    

    return $pdo;
}