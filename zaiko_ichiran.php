<?php

/* 
【機能】
書籍テーブルより書籍情報を取得し、画面に表示する。
商品をチェックし、ボタンを押すことで入荷、出荷が行える。
ログアウトボタン押下時に、セッション情報を削除しログイン画面に遷移する。

【エラー一覧（エラー表示：発生条件）】
入荷する商品が選択されていません：商品が一つも選択されていない状態で入荷ボタンを押す
出荷する商品が選択されていません：商品が一つも選択されていない状態で出荷ボタンを押す
*/

//①セッションを開始する
session_start();

//②SESSIONの「login」フラグがfalseか判定する。「login」フラグがfalseの場合はif文の中に入る。
if($_SESSION['login']==false){
	// ③SESSIONの「error2」に「ログインしてください」と設定する。	
	$_SESSION["error2"] = "ログインしてください";
	// ④ログイン画面へ遷移する。
	header("Location: login.php");
}

//⑤データベースへ接続し、接続情報を変数に保存する
$db_name = 'zaiko2020_yse';
$host = 'localhost';
$user_name = 'zaiko2020_yse';
$password = '2020zaiko';
$mysqli = new mysqli($host, $user_name, $password, $db_name);

if($mysqli->connect_error){
	echo $mysqli->connect_error;
	exit();
}else{
	//⑥データベースで使用する文字コードを「UTF8」にする
	$mysqli->set_charset('utf8');
	//⑦書籍テーブルから書籍情報を取得するSQLを実行する。また実行結果を変数に保存する
	if (isset($_POST["sortbyday"])) {	
		$sql = "SELECT * FROM books  where deleteCheck = 0 ORDER BY salesDate {$_POST["sortbyday"]}";
		$_POST["sortbyday"] = setOrderByPostName($_POST["sortbyday"]);		
	}elseif (isset($_POST["sortbykingaku"])) {
		$sql = "SELECT * FROM books  where deleteCheck = 0 ORDER BY price {$_POST["sortbykingaku"]}";
		$_POST["sortbykingaku"] = setOrderByPostName($_POST["sortbykingaku"]);		
	}elseif (isset($_POST["sortbyzaikosuu"])) {
		$sql = "SELECT * FROM books  where deleteCheck = 0 ORDER BY stock {$_POST["sortbyzaikosuu"]}";
		$_POST["sortbyzaikosuu"] = setOrderByPostName($_POST["sortbyzaikosuu"]);	
	}elseif (isset($_POST["id"])) {
		$sql = "SELECT * FROM books  where deleteCheck = 0 ORDER BY id {$_POST["id"]}";
		$_POST["id"] = setOrderByPostName($_POST["id"]);	
	}else{
		$sql = "SELECT * FROM books  where deleteCheck = 0";
	}
	$result = $mysqli->query($sql);	
}
function setOrderByPostName($postName){
	if ($postName == "ASC") {
		return "DESC";
	}else{
		return "ASC";
	}	
}
function setValueBtn($name){
	if(isset($_POST["{$name}"])){
		return $_POST["{$name}"];
	}else{		
		return "DESC";
	}
}
function setContextBtn($name){
	if(isset($_POST["{$name}"])){
		if($_POST["{$name}"] == "DESC"){
			return "▲";
		}else{
			return "▼";
		}
	}else{		
		return "▲";
	}
}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>書籍一覧</title>
	<link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>
<body>
	<div id="header">
		<h1>書籍一覧</h1>
	</div>
	<form action="zaiko_ichiran.php" method="post" id="myform" name="myform">
		<div id="pagebody">
			<!-- エラーメッセージ表示 -->
			<div id="error">
				<?php
				/*
				 * ⑧SESSIONの「success」にメッセージが設定されているかを判定する。
				 * 設定されていた場合はif文の中に入る。
				*/ 
				if(isset($_SESSION["success"])){
					//⑨SESSIONの「success」の中身を表示する。
					echo $_SESSION["success"];
					$_SESSION["success"] = null; # これないと永遠に「入荷する商品が選択されていません」が表示される
				}
				?>
			</div>
			
			<!-- 左メニュー -->
			<div id="left">
				<p id="ninsyou_ippan">
					<?php
						echo @$_SESSION["account_name"];
					?><br>
					<button type="button" id="logout" onclick="location.href='logout.php'">ログアウト</button>
				</p>
				<button type="submit" id="btn1" formmethod="POST" name="decision" value="3" formaction="nyuka.php">入荷</button>

				<button type="submit" id="btn1" formmethod="POST" name="decision" value="4" formaction="syukka.php">出荷</button>
				<!-- 2つボタン追加 -->
				<button type="submit" id="btn1" formmethod="POST" name="decision" value="5" formaction="new_product.php">新商品追加</button>

				<button type="submit" id="btn1" formmethod="POST" name="decision" value="6" formaction="delete_product.php">商品削除</button>

				<button type="submit" id="btn1" formmethod="POST" name="decision" value="6" formaction="search.php">商品検索</button>
			</div>
			<!-- 中央表示 -->
			<div id="center">

				<!-- 書籍一覧の表示 -->
				<table>
					<thead>
						<tr>
							<th id="check"></th>
							<th id="id">ID
								<button type="submit"  id="sort" formmethod="POST" name="id" value="<?php echo setValueBtn('id'); ?>"><?php  echo setContextBtn('id');?></button>
							</th>
							<th id="book_name">書籍名</th>
							<th id="author">著者名</th>
							<th id="salesDate">発売日
								<button type="submit"  id="sort" formmethod="POST" name="sortbyday" value="<?php echo setValueBtn('sortbyday'); ?>"><?php  echo setContextBtn('sortbyday');?></button>
							</th>
							<th id="itemPrice">金額
								<button type="submit"  id="sort" formmethod="POST" name="sortbykingaku" value="<?php echo setValueBtn('sortbykingaku'); ?>"><?php  echo setContextBtn('sortbykingaku');?></button>
							</th>								
							<th id="stock">在庫数
								<button type="submit"  id="sort" formmethod="POST" name="sortbyzaikosuu" value="<?php echo setValueBtn('sortbyzaikosuu'); ?>"><?php  echo setContextBtn('sortbyzaikosuu');?></button>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						//⑩SQLの実行結果の変数から1レコードのデータを取り出す。レコードがない場合はループを終了する。
						if($result){
							while($extract = $result->fetch_assoc()){
								
								//⑪extract変数を使用し、1レコードのデータを渡す。
								echo "<tr>";
								echo "<td><input type='checkbox' name='books[]' value='{$extract['id']}'></td>";
								echo "<td>"."{$extract['id']}"."</td>";
								echo "<td>"."{$extract['title']}"."</td>";
								echo "<td>"."{$extract['author']}"."</td>";
								echo "<td>"."{$extract['salesDate']}"."</td>";
								echo "<td>"."{$extract['price']}"."</td>";
								echo "<td>"."{$extract['stock']}"."</td>";
								echo "</tr>";
							}
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</form>
	<div id="footer">
		<footer>株式会社アクロイト</footer>
	</div>
</body>
</html>
