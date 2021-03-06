<?php
/*
 * ①session_status()の結果が「PHP_SESSION_NONE」と一致するか判定する。
 * 一致した場合はif文の中に入る。
*/
if (session_status() == PHP_SESSION_NONE) {
	//②セッションを開始する
	session_start();
}

//③SESSIONの「login」フラグがfalseか判定する。「login」フラグがfalseの場合はif文の中に入る。
if ($_SESSION['login']==false){
	//④SESSIONの「error2」に「ログインしてください」と設定する。
	$_SESSION["error2"] = "ログインしてください";
	//⑤ログイン画面へ遷移する。
	header("Location: login.php");
}

//⑥データベースへ接続し、接続情報を変数に保存する
$db_name = 'zaiko2020_yse';
$host = 'localhost';
$user_name = 'zaiko2020_yse';
$password = '2020zaiko';
$mysqli = new mysqli($host, $user_name, $password, $db_name);

if($mysqli->connect_error){
	echo $mysqli->connect_error;
	exit();
}else{	
//⑦データベースで使用する文字コードを「UTF8」にする
	$mysqli->set_charset('utf8');
}

function getId($con){
	$sql = "SELECT * FROM books ORDER BY id DESC LIMIT 1";
	$result = $con->query($sql);

	//⑫実行した結果から1レコード取得し、returnで値を返す。
	if($result){
		return $result->fetch_assoc();
	}
}

function addData($con){
	// 入力値を変数に
	$title = $_POST["title"];
	$author = $_POST["author"];
	$salesDate = date('Y年m月d日', strtotime($_POST["salesDate"]));
	$isbn = $_POST["isbn"];
	$price = $_POST["price"];
	$stock = $_POST["in"];
	
	# 勝手にidは設定されるので書かない
	$sql = "INSERT INTO books(title, author, salesDate, isbn, price, stock, deleteCheck) ";
	$sql .= "VALUES('{$title}', '{$author}', '{$salesDate}', '{$isbn}', '{$price}', '{$stock}', '0')";
	$con->query($sql);
}

if (isset($_POST["add"]) && $_POST["add"] == "ok") {
	addData($mysqli);	

	//SESSIONの「success」に「新商品の追加が完了しました」と設定する。
	$_SESSION["success"] = "新商品の追加が完了しました";
	//「header」関数を使用して在庫一覧画面へ遷移する。
	header('Location: zaiko_ichiran.php');
	exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>新商品追加</title>
	<link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>
<body>
	<!-- ヘッダ -->
	<div id="header">
		<h1>新商品追加</h1>
	</div>

	<!-- メニュー -->
	<div id="menu">
		<nav>
			<ul>
				<li><a href="zaiko_ichiran.php?page=1">書籍一覧</a></li>
			</ul>
		</nav>
	</div>

	<form action="new_product.php" method="post">
		<div id="pagebody">
			<!-- エラーメッセージ -->
			<div id="error">
			<?php
			/*
			 * ⑬SESSIONの「error」にメッセージが設定されているかを判定する。
			 * 設定されていた場合はif文の中に入る。
			*/ 
			if(isset($_SESSION["error"])){
				//⑭SESSIONの「error」の中身を表示する。
				echo $_SESSION["error"];
				$_SESSION["error"] = null; # これないと永遠に表示されそう…
			}
			?>
			</div>
			<div id="center">
				<table>
					<thead>
						<tr>
							<th id="id">ID</th>
							<th id="isbn">ISBN</th>
							<th id="book_name">書籍名</th>
							<th id="author">著者名</th>
							<th id="salesDate">発売日</th>
							<th id="itemPrice">金額(円)</th>
							<th id="stock">在庫数</th>
							<th id="in">入荷数</th>
						</tr>
					</thead>
					<?php 
					// ⑯「getId」関数を呼び出し、変数に戻り値を入れる。その際引数に⑥のDBの接続情報を渡す。
					$extract = getId($mysqli);
					?>
					<input type="hidden" value="<?php echo $extract["id"]; ?>" name="books[]">
					<tr>
						<td><?php echo $extract['id'] + 1; ?></td>
						<td><input type='text' name='isbn' size='13' maxlength='13' oninput="value = value.replace(/[^0-9]+/i,'');" required></td>
						<td><input type='text' name='title' size='20' maxlength='40' required></td>
						<td><input type='text' name='author' size='15' maxlength='30' required></td>
						<td><input type='date' name='salesDate' required></td>
						<td><input type='text' name='price' size='8' maxlength='11' oninput="value = value.replace(/[^0-9]+/i,'');" required></td>
						<td>0</td><!-- <td><input type='text' name='stock' size='8' maxlength='11' required></td> -->
						<td><input type='text' name='in' size='3' maxlength='11' oninput="value = value.replace(/[^0-9]+/i,'');" required></td>
					</tr>
				</table>
				<button type="submit" id="kakutei" formmethod="POST" name="add" value="ok">確定</button>
			</div>
		</div>
	</form>
	<!-- フッター -->
	<div id="footer">
		<footer>株式会社アクロイト</footer>
	</div>
</body>
</html>