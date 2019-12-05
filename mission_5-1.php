
<?php
//DBに接続とテーブル作成/////////////////////////////////////////////////////////////////////////////////
	$dsn = "データベース名";
	$user = "ユーザー名";
	$password = "パスワード";
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
	//CREATE TABLE データベース名(tbtestという名前のテーブルが作成される)	
	//IF NOT EXIST：同名のテーブル（今回はtbtest）が存在しないとき、のちのデータベース名のDBを作成
	//INTは整数のデータ型
	//AUTO_INCREMENT：行番号が指定されなかったときに、MySQL側が自動的に値を割り当てるもの、値は1ずつ増えて連番となる
	//char型：文字列型、確保するサイズを設定する分、TEXTよりも軽い動作となる。32文字まで保存できる。
	//TEXT型：可変長文字列
	//DATETIMEは日時のデータ型
	//一章で作成したpdoインスタンス（DB共通語的な最強のクラス）のqueryメソッドを使う
	//queryメソッド：引数に指定したSQL文をデータベースに対して発行してくれる、返り値($stmt)にはSQL文を発行した結果が含まれているPDOSttatementクラスのオブジェクトを返してくれる
	$sql = "CREATE TABLE IF NOT EXISTS tbtest2"
	//こっからはどんな箱をつくりますか～って欄
			." ("
			. "id INT AUTO_INCREMENT PRIMARY KEY,"
			. "name char(32),"	
			. "comment TEXT,"
			. "date DATETIME,"	
			. "pass TEXT"
			.");";
	$stmt = $pdo->query($sql);//SQL文のコマンドをPHPからデータベースを操作するためにデータベースに送る。
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//新規投稿///////////////////////////////////////////////////////////////////////////////////////////////////////
if(!empty($_POST["name"]) and !empty($_POST["comment"]) and empty($_POST["commentNumber"]) and !empty($_POST["password"])){ //もし入力内容が空白でない場合には以下を実行する
		//MYSQLの書き込み/データ入力
//INSERT INTO　テーブル名（"テーブルの中に定義した要素、要素、要素、、、）values(:変数,:変数,:変数,,,)";
//bindParam(prepareで与えられたvalueを指定,それを代入する変数,PDO::PARAM_STR(これは文字列であることを言っている))

		$sql  = $pdo -> prepare("INSERT INTO tbtest2 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
		$sql -> bindParam(":name", $name, PDO::PARAM_STR);//nameパラメータをname変数に代入
		$sql -> bindParam(":comment", $comment, PDO::PARAM_STR);
		$sql -> bindParam(":date", $date, PDO::PARAM_STR);
		$sql -> bindParam(":pass", $pass, PDO::PARAM_STR);
		$name = $_POST["name"];//ここで値を代入
		$comment = $_POST["comment"];
		$date = date("Y/m/d, H:i:s");//日付データを取得して変数に代入
		$pass = $_POST["password"];
		$sql -> execute();
		}	
//削除機能//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(!empty($_POST["delete"]) and !empty($_POST["delpas"])){	
			
		$sql = "SELECT * FROM tbtest2"; //$sqlを選択して、
		$stmt = $pdo->query($sql); //のコマンドをデータベースに送る部分が、$pdo->query(sql文)やexecute()
		$results = $stmt->fetchAll();
		foreach ($results as $row) {
				if($row["pass"] == $_POST["delpas"]) {
						$id = $_POST["delete"];
						$sql = "delete from tbtest2 where id=:id";
						$stmt = $pdo->prepare($sql);
						$stmt ->bindParam(":id", $id, PDO::PARAM_INT);					
						$stmt ->execute();
	}
	}
}
	
//編集番号を獲得////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(!empty($_POST["edit_num"]) and !empty($_POST["editpas"])) {
			
		$sql = "SELECT * FROM tbtest2";
		$stmt =$pdo->query($sql);
		$results = $stmt->fetchAll();
		foreach ($results as $row) {
				if($row["id"] == $_POST["edit_num"] && $row["pass"] == $_POST["editpas"]){
						$edit_number = $row["id"];
						$edit_name = $row["name"];
						$edit_comment = $row["comment"];		
		}

}
}
//編集を実行
if(!empty($_POST["commentNumber"]) and !empty($_POST["name"]) and !empty($_POST["comment"]) and !empty($_POST["password"])){
	
	$sql = "SELECT * FROM tbtest2";
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach($results as $row) {
		if($row["id"] == $_POST["commentNumber"]){
			$id = $_POST["commentNumber"];
			$name = $_POST["name"];
			$comment = $_POST["comment"];
			$date = date("Y/m/d, H:i:s");
			$newpass = $_POST["password"];
			$sql = "update tbtest2 set name=:name,comment=:comment,date=:date,pass=:pass where id=:id";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(":name", $name, PDO::PARAM_STR);
			$stmt->bindParam(":comment", $comment, PDO::PARAM_STR);
			$stmt->bindParam(":date", $date, PDO::PARAM_STR);
			$stmt->bindParam(":pass", $newpass, PDO::PARAM_STR);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();
		}
	}
}


?>




<html>
<head>
</head>
<body>
<form action="mission_5-1.php" method="post">
名前：<input type="text" name="name" placeholder="名前"  value ="<?php if(isset($edit_name)) { 
echo $edit_name;
} ?>"><br> 
コメント：<input type="text" placeholder="コメント" name="comment" value ="<?php if(isset($edit_comment)) { 
echo $edit_comment;
} ?>"><br>
パスワード：<input type="text" name="password" placeholder="パスワード">
<input type="hidden" name="commentNumber" value="<?php if(isset($_POST['send_henshu'])) { echo $edit_number; } ?>">
<input type="submit" name="send" value="送信"><br> 
</form>       
<form action="mission_5-1.php" method="post">
削除対象番号：<input type="text" placeholder="削除対象番号" name="delete"><br>
パスワード：<input type="text" name="delpas" placeholder="パスワード">
<input type="submit"  value="削除"><br>
</form>       
<form action="mission_5-1.php" method="post">
編集対象番号：<input type="text" placeholder="編集対象番号" name="edit_num"><br>
パスワード：<input type="text" name="editpas" placeholder="パスワード">
<input type="submit" name= "send_henshu" value="編集"><br>
</form>
</body>
</html>
<?php
$dsn = "データベース名";
	$user = "ユーザー名";
	$password = "パスワード";
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//select 要素　FROM テーブル名：テーブルに含まれる要素の値を取得
//『*』ペナルティと呼ばれ、今回はテーブル内のすべての要素を取得するという意味
	$sql = "SELECT * FROM tbtest2";
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row["id"]." ";
		echo $row["name"]." ";
		echo $row["comment"]." ";
		echo $row["date"]." ";
		echo $row["pass"]."<br>";
	}
	
?>