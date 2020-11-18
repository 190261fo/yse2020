<?php
//①セッションを開始する
session_start();

function getByid($id,$con){
	$sql = "SELECT * FROM books WHERE id={$id}";
	$result = $con->query($sql);

	//③実行した結果から1レコード取得し、returnで値を返す。
	if($result){
		return $result->fetch_assoc();
	}
}

function updateDeleteByid($id,$con){
	$sql = "UPDATE books SET deleteCheck=1 WHERE id={$id}";
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

//POSTの「books」の値が空か判定する。空の場合はif文の中に入る。
if (!isset($_POST["books"])) {
	//SESSIONの「success」に「削除する商品が選択されていません」と設定する。
	$_SESSION["success"] = "削除する商品が選択されていません";
	//在庫一覧画面へ遷移する。
	header( "Location: ./zaiko_ichiran.php" ) ;
	exit ;
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

<!-- メニュー -->
<div id="menu">
	<nav>
		<ul>
			<li><a href="zaiko_ichiran.php?page=1">書籍一覧</a></li>
		</ul>
	</nav>
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
			<div id="kakunin">
				<br>
					<p　id="delete">
						上記の書籍を削除します。<br>
						よろしいですか？
					</p>
					<button type="submit" id="message" formmethod="POST" name="delete"" value="ok">はい</button>
					<button type="submit" id="message" formaction="zaiko_ichiran.php">いいえ</button>
			</div>
		</div>
	</div>
</form>
<div id="footer">
	<footer>株式会社アクロイト</footer>
</div>
</body>
</html>
