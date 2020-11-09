
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
	<title>商品検索</title>
	<link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>
<body>
	<!-- ヘッダ -->
	<div id="header">
		<h1>商品検索</h1>
	</div>

	<!-- メニュー -->
	<div id="menu">
		<nav>
			<ul>
				<li><a href="zaiko_ichiran.php?page=1">書籍一覧</a></li>
			</ul>
		</nav>
	</div>

	<form action="search_result.php" method="post">
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
				$_SESSION["error"] = null;
			}
			?>
			</div>
			<div id="center">
				<table>
					<thead>
						<tr>
							<th id="id">キーワード</th>
							<th id="isbn">発売年代</th>
							<th id="book_name">金額</th>
							<th id="author">在庫数</th>
						</tr>
					</thead>
					<?php 
					// ⑯「getId」関数を呼び出し、変数に戻り値を入れる。その際引数に⑥のDBの接続情報を渡す。
					$extract = getId($mysqli);
					?>
					<input type="hidden" value="<?php echo $extract["id"]; ?>" name="books[]">
					<tr>
                        <td><input type='text' name='keyword' size='13' maxlength='13' required></td>
                        <td>
                            <select name="example" required>
								<option value=""></option>
                                <option value="">1970年代</option>
                                <option value="">1980年代</option>
                                <option value="">1990年代</option>
                                <option value="">2000年代</option>
                                <option value="">2010年代</option>
                                <option value="">2020年代</option>
                            </select>
                        </td>
						<td>
                            <select name="price" required>
								<option value=""></option>
                                <option value="">400円代</option>
                                <option value="">500円代</option>
                                <option value="">600円代</option>
                                <option value="">700円代</option>
                                <option value="">800円代</option>
                                <option value="">900円代</option>
								<option value="">1000円代</option>
								<option value="">2000円代</option>
                            </select>
                        </td>
						<td>
                            <select name="stock" required>
								<option value=""></option>
                                <option value="">10冊未満</option>
                                <option value="">20冊未満</option>
                                <option value="">30冊未満</option>
                                <option value="">40冊未満</option>
                                <option value="">50冊未満</option>
                                <option value="">50冊以上</option>
                            </select>
                        </td>
					</tr>
				</table>
				<button type="submit" id="kakutei" formmethod="POST" name="add" value="ok">検索</button>
			</div>
		</div>
	</form>
	<!-- フッター -->
	<div id="footer">
		<footer>株式会社アクロイト</footer>
	</div>
</body>
</html>