<?php
/* 
【機能】
出荷で入力された個数を表示する。出荷を実行した場合は対象の書籍の在庫数から出荷数を
引いた数でデータベースの書籍の在庫数を更新する。


【エラー一覧（エラー表示：発生条件）】
なし
*/
//①セッションを開始する
session_start();

function getByid($id,$con){
	/* 
	 * ②書籍を取得するSQLを作成する実行する。
	 * その際にWHERE句でメソッドの引数の$idに一致する書籍のみ取得する。
	 * SQLの実行結果を変数に保存する。
	*/
	$sql = "SELECT * FROM books WHERE id={$id}";
	$result = $con->query($sql);

	//③実行した結果から1レコード取得し、returnで値を返す。
	if($result){
		return $result->fetch_assoc();
	}
}

function updateDeleteByid($id,$con){
	/*
	 * ④書籍情報の在庫数を更新するSQLを実行する。
	 * 引数で受け取った$totalの値で在庫数を上書く。
	 * その際にWHERE句でメソッドの引数に$idに一致する書籍のみ取得する。
	*/
	$sql = "UPDATE books SET DeleteCheck=1 WHERE id={$id}";
	$con->query($sql);
}

//⑤SESSIONの「login」フラグがfalseか判定する。「login」フラグがfalseの場合はif文の中に入る。
if ($_SESSION['login']==false){
	//⑥SESSIONの「error2」に「ログインしてください」と設定する。
	$_SESSION["error2"] = "ログインしてください";
	//⑦ログイン画面へ遷移する。
	header("Location: login.php");
}

//⑧データベースへ接続し、接続情報を変数に保存する
$db_name = 'zaiko2020_yse';
$host = 'localhost';
$user_name = 'zaiko2020_yse';
$password = '2020zaiko';
$mysqli = new mysqli($host, $user_name, $password, $db_name);

if($mysqli->connect_error){
	echo $mysqli->connect_error;
	exit();
}else{

//⑨データベースで使用する文字コードを「UTF8」にする
	$mysqli->set_charset('utf8');
}



if(isset($_POST["delete"]) && $_POST["delete"] == "ok"){
	
	foreach($_POST["books"] as $book){
		updateDeleteByid($book,$mysqli);	
	}

	//SESSIONの「success」に「商品削除が完了しました」と設定する。
	$_SESSION["success"] = "商品削除が完了しました";
	//「header」関数を使用して在庫一覧画面へ遷移する。
    header('Location: zaiko_ichiran.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>商品削除確認</title>
<link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>
<body>
<div id="header">
	<h1>商品削除確認</h1>
</div>
<form action="delete_product.php" method="post" id="test">
	<div id="pagebody">
		<div id="center">
			<table>
				<thead>
					<tr>
                        <th id="book_name">書籍名</th>
                        <th id="author">著者名</th>
                        <th id="salesDate">発売日</th>
                        <th id="itemPrice">金額</th>
                        <th id="stock">在庫数</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					
					
					
					foreach($_POST["books"] as $book){
						
						$extract = getByid($book, $mysqli);
					?>
					<tr>
						<td><?php echo $extract["title"]; ?></td>
                        <td><?php echo $extract["author"]; ?></td>
                        <td><?php echo $extract["salesDate"]; ?></td>
						<td><?php echo $extract["price"]; ?></td>
                        <td><?php echo $extract["stock"]; ?></td>
					</tr>
                    <input type="hidden" name="books[]" value="<?php echo $extract["id"];?>">
					<?php		
					}
					?>
				</tbody>
			</table>
			<button type="submit" id="kakutei" formmethod="POST" name="delete" value="ok">確定</button>
		</div>
	</div>
</form>
<div id="footer">
	<footer>株式会社アクロイト</footer>
</div>
</body>
</html>
