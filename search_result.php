<?php
/*
 * ①session_status()の結果が「PHP_SESSION_NONE」と一致するか判定する。
 * 一致した場合はif文の中に入る。
 */
if (session_status() == PHP_SESSION_NONE /* ①.の処理を行う */) {
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
//⑧POSTの「books」の値が空か判定する。空の場合はif文の中に入る。
if (!isset($_POST["books"])) {
	//⑨SESSIONの「success」に「入荷する商品が選択されていません」と設定する。
	$_SESSION["success"] = "入荷する商品が選択されていません";
	//⑩在庫一覧画面へ遷移する。
	header( "Location: ./zaiko_ichiran.php" ) ;
	exit ;
}

function getId($con){
    $search_list = array();
    if (isset($_POST['search']) && $_POST['search'] == "ok") {
        if (!$_POST['keyword'] == "") {
            $search_list[] = "title LIKE %{$_POST['keyword']}% OR author LIKE %{$_POST['keyword']}%";
        }
    }



	$sql = "SELECT * FROM books WHERE ".implode("AND ", $search_list);
	$result = $con->query($sql);

	//⑫実行した結果から1レコード取得し、returnで値を返す。
	if($result){
		return $result->fetch_assoc();
	}
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>検索結果</title>
	<link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>
<body>
	<!-- ヘッダ -->
	<div id="header">
		<h1>検索結果</h1>
	</div>

	<!-- メニュー -->
	<div id="menu">
		<nav>
			<ul>
				<li><a href="zaiko_ichiran.php?page=1">書籍一覧</a></li>
			</ul>
		</nav>
	</div>

	<form action="" method="post">
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
                            <th id="check"></th>
							<th id="id">ID</th>
							<th id="book_name">書籍名</th>
							<th id="author">著者名</th>
							<th id="salesDate">発売日</th>
							<th id="itemPrice">金額(円)</th>
							<th id="stock">在庫数</th>
						</tr>
					</thead>
					<?php 
					/*
					 * ⑮POSTの「books」から一つずつ値を取り出し、変数に保存する。
					*/
    				// foreach(/* ⑮の処理を書く */){
					foreach ($_POST["books"] as $book) {
						// ⑯「getId」関数を呼び出し、変数に戻り値を入れる。その際引数に⑮の処理で取得した値と⑥のDBの接続情報を渡す。
						$extract = getId($book, $mysqli);
					?>
						<input type="hidden" value="<?php echo $extract["id"]; ?>" name="books[]">
						<tr>
                            <td><input type='checkbox' name='' value=''></td>
							<td><?php echo $extract["id"]; ?></td>
							<td><?php echo $extract["title"]; ?></td>
							<td><?php echo $extract["author"]; ?></td>
							<td><?php echo $extract["salesDate"]; ?></td>
							<td><?php echo $extract["price"]; ?></td>
							<td><?php echo $extract["stock"]; ?></td>
						</tr>
					<?php
					}
					?>
				</table>
				<button type="submit" id="kakutei" formmethod="POST" name="decision" value="1" formaction="zaiko_ichiran.php">入荷</button>
                <button type="submit" id="kakutei" formmethod="POST" name="decision" value="2" formaction="zaiko_ichiran.php">出荷</button>
			</div>
		</div>
	</form>
	<!-- フッター -->
	<div id="footer">
		<footer>株式会社アクロイト</footer>
	</div>
</body>
</html>