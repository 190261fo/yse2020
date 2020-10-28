<?php
/* 
【機能】
入荷で入力された個数を表示する。入荷を実行した場合は対象の書籍の在庫数に入荷数を加
えた数でデータベースの書籍の在庫数を更新する。

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
	$sql = "SELECT * FROM books WHERE id=$id";
	$result = $con->query($sql);


	//③実行した結果から1レコード取得し、returnで値を返す。
	if($result){
		return $result->fetch_assoc();
	}
}

function updateByid($id,$con,$total){
	/*
	 * ④書籍情報の在庫数を更新するSQLを実行する。
	 * 引数で受け取った$totalの値で在庫数を上書く。
	 * その際にWHERE句でメソッドの引数に$idに一致する書籍のみ取得する。
	*/
	$sql = "UPDATE books SET stock=$total WHERE id=$id";
	$con->query($sql);

}

// ⑤SESSIONの「login」フラグがfalseか判定する。「login」フラグがfalseの場合はif文の中に入る。
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

//⑩書籍数をカウントするための変数を宣言し、値を0で初期化する
$count = 0;

//⑪POSTの「books」から値を取得し、変数に設定する。
foreach($_POST["books"] as $book){
	/*
	 * ⑫POSTの「stock」について⑩の変数の値を使用して値を取り出す。
	 * 半角数字以外の文字が設定されていないかを「is_numeric」関数を使用して確認する。
	 * 半角数字以外の文字が入っていた場合はif文の中に入る。
	 */

	if (/* ⑫の処理を書く */ !is_numeric($_POST["in"][$count])) {
		//⑬SESSIONの「error」に「数値以外が入力されています」と設定する。
		$_SESSION["error"] = "数値以外が入力されています";
		//⑭「include」を使用して「nyuka.php」を呼び出す。
		include "nyuka.php";
		//⑮「exit」関数で処理を終了する。
		exit;
	}

	//⑯「getByid」関数を呼び出し、変数に戻り値を入れる。その際引数に⑪の処理で取得した値と⑧のDBの接続情報を渡す。
		$extract = getByid($book, $mysqli);
	//⑰ ⑯で取得した書籍の情報の「stock」と、⑩の変数を元にPOSTの「stock」から値を取り出し、足した値を変数に保存する。
		$result = $extract["stock"] + $_POST["in"][$count];
	//⑱ ⑰の値が100を超えているか判定する。超えていた場合はif文の中に入る。
	if(/* ⑱の処理を行う */ $result > 100){
		//⑲SESSIONの「error」に「最大在庫数を超える数は入力できません」と設定する。
		$_SESSION["error"] = "最大在庫数を超える数は入力できません";
		//⑳「include」を使用して「nyuka.php」を呼び出す。
		include "nyuka.php";
		//㉑「exit」関数で処理を終了する。
		exit;
	}
	
	// ㉒ ⑩で宣言した変数をインクリメントで値を1増やす。
	//$book++;
	$count++;
}

/*
 * ㉓POSTでこの画面のボタンの「add」に値が入ってるか確認する。
 * 値が入っている場合は中身に「ok」が設定されていることを確認する。
 */
if(/* ㉓の処理を書く */ isset($_POST["add"]) && $_POST["add"] == "ok"){
	//㉔書籍数をカウントするための変数を宣言し、値を0で初期化する。
	$count = 0;
	//㉕POSTの「books」から値を取得し、変数に設定する。
	foreach(/* ㉕の処理を書く */ $_POST["books"] as $book){
		//㉖「getByid」関数を呼び出し、変数に戻り値を入れる。その際引数に㉕の処理で取得した値と⑧のDBの接続情報を渡す。
		$extract = getByid($book, $mysqli);
		//㉗ ㉖で取得した書籍の情報の「stock」と、㉔の変数を元にPOSTの「stock」から値を取り出し、足した値を変数に保存する。
		$result = $extract["stock"] + $_POST["in"][$count];
		//㉘「updateByid」関数を呼び出す。その際に引数に㉕の処理で取得した値と⑧のDBの接続情報と㉗で計算した値を渡す。
		updateByid($book,$mysqli,$result);
		//㉙ ㉔で宣言した変数をインクリメントで値を1増やす。
		$count++;
	}
	//㉚SESSIONの「success」に「入荷が完了しました」と設定する。
	$_SESSION["success"] = "入荷が完了しました";
	//㉛「header」関数を使用して在庫一覧画面へ遷移する。
	header('Location: zaiko_ichiran.php');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>入荷確認</title>
	<link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>
<body>
	<div id="header">
		<h1>入荷確認</h1>
	</div>
	<form action="nyuka_kakunin.php" method="post" id="test">
		<div id="pagebody">
			<div id="center">
				<table>
					<thead>
						<tr>
							<th id="book_name">書籍名</th>
							<th id="stock">在庫数</th>
							<th id="in">入荷数</th>
						</tr>
					</thead>
					<tbody>
						<?php
						//㉜書籍数をカウントするための変数を宣言し、値を0で初期化する。
						$index = 0;

						//㉝POSTの「books」から値を取得し、変数に設定する。
						foreach($_POST["books"] as $book){
							//㉞「getByid」関数を呼び出し、変数に戻り値を入れる。その際引数に㉜の処理で取得した値と⑧のDBの接続情報を渡す。
							$extract = getByid($book, $mysqli);

						?>
						<tr>
							<td><?php echo $extract["title"]; ?></td>
							<td><?php echo $extract["stock"]; ?></td>
							<td><?php echo $_POST["in"][$index]/* ㊱ POSTの「stock」に設定されている値を㉜の変数を使用して呼び出す。 */;?></td>
						</tr>
						<input type="hidden" name="books[]" value="<?php echo $extract["id"];/*㊲㉝で取得した値を設定*/ ?>">
						<input type="hidden" name="in[]" value='<?php echo $_POST["in"][$index]/* ㊳POSTの「stock」に設定されている値を㉜の変数を使用して設定する。 */?>'>
						<?php
							//㊴ ㉜で宣言した変数をインクリメントで値を1増やす。
							$index++;
						}
						?>
					</tbody>
				</table>
				<div id="kakunin">
					<p>
						上記の書籍を入荷します。<br>
						よろしいですか？
					</p>
					<button type="submit" id="message" formmethod="POST" name="add" value="ok">はい</button>
					<button type="submit" id="message" formaction="nyuka.php">いいえ</button>
				</div>
			</div>
		</div>
	</form>
	<div id="footer">
		<footer>株式会社アクロイト</footer>
	</div>
</body>
</html>
