<!DOCTYPE html>
<html>
	<head>
		<title>課題4-1</title>
		<meta charset = "utf-8" >
	</head>
	<body>
    <?php
    // Mysqlの設定
    $dsn = 'データベース名';
    $user = 'ユーザ名';
    $password = 'パスワード';
    $pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    // テーブルの作成
    $sql = "CREATE TABLE IF NOT EXISTS table_mission4"
    ."("
    ."id INT,"
    ."name char(32),"
    ."comment TEXT,"
    ."password char(32),"
    ."YmdHms char(32)"
    .");";
    mysql_set_charset('utf8');
    $stmt = $pdo->query($sql);

    $name = $_POST['name'];									//入力名前
    $comment = $_POST['comment'];						//入力コメント
    $password_set = $_POST['password_set'];	//入力パスワード
    $password_del = $_POST['password_del'];	//削除時パスワード
    $password_mod = $_POST['password_mod'];	//編集時パスワード
		$num_flag = $_POST['num_flag'];					//編集フラグ
    $num_delete = $_POST['num_delete'];			//削除番号
    $num_change = $_POST['num_change'];			//編集番号


		// 編集フラグが立っているとき、編集番号のファイルの中身を上書きする。
		if (!empty($num_flag) && !empty($name)){
			// データの編集機能***
			$sql = 'update table_mission4 set name=:name,comment=:comment,YmdHms=:YmdHms where id=:id';
			$stmt = $pdo->prepare($sql);
			$date = date('Y年m月d日 H:m:s');
			$stmt->bindParam(':id',$num_flag,PDO::PARAM_INT);
			$stmt->bindParam(':name',$name,PDO::PARAM_STR);
			$stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
			$stmt->bindParam(':YmdHms',$date,PDO::PARAM_STR);
			// // パスワードの書き換えするかどうか。***
			// if (!empty($password_set)) {
			// 	$stmt -> bindParam(':password_set',$password_set,PDO::PARAM_STR);
			// }
			$stmt->execute();

		}
		// 編集フラグが立っていないとき、ファイルに入力内容を追加する。
		elseif(!empty($comment) && !empty($name)){
			// 列の数をカウントする。
			$sql = "SELECT * FROM table_mission4";
			$stmt = $pdo->query($sql);
			$count = $stmt->rowCount();
			$id = $count+1;
			$date = date('Y年m月d日 H:m:s');
			// データの入力
			$sql = $pdo -> prepare("INSERT INTO table_mission4 (id,name,comment,password,YmdHms) VALUES (:id,:name,:comment,:password,:YmdHms)");
			$sql -> bindValue(':id',$id,PDO::PARAM_INT);
			$sql -> bindParam(':name',$name,PDO::PARAM_STR);
			$sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
			$sql -> bindParam(':password',$password_set,PDO::PARAM_STR);
			$sql -> bindParam(':YmdHms',$date,PDO::PARAM_STR);
			$sql -> execute();
		}


		// 指定された編集番号のファイルの中身を出力する。
		if (!empty($num_change)) {
			// 入力データを表示する。
			$sql = 'SELECT*FROM table_mission4 where id=:id';
			$stmt = $pdo->prepare($sql);
			$stmt -> bindValue(':id',$num_change,PDO::PARAM_INT);
			$stmt -> execute();
			foreach ($stmt as $row) {
			  //$rowの中にはテーブルのカラム名が入る。
				if ($password_mod == $row['password'])  {
					$data_num = $row['id'];
					$data_name = $row['name'];
					$data_comment = $row['comment'];
				}
				else {
					echo "パスワードが違います!";
				}
			}
		}


		// 削除実行*
		if (!empty($num_delete)) {
			$sql = 'SELECT password FROM table_mission4 where id=:id';
			$stmt = $pdo -> prepare($sql);
			$stmt -> bindValue(':id',$num_delete,PDO::PARAM_INT);
			$stmt -> execute();
			$password_c = $stmt->fetchColumn();
			if ($password_del == $password_c) {
				$sql = 'delete from table_mission4 where id=:id';
				$stmt = $pdo->prepare($sql);
				$stmt->bindValue(':id',$num_delete,PDO::PARAM_INT);
				$stmt->execute();
			}
			elseif ($password_del != $password_c) {
				echo "パスワードが違います!!!";
			}
		}
    ?>


		<!-- フォーム [名前の入力欄],[コメント入力欄],[パスワード入力欄],[フラグ欄(非表示)],[削除番号入力欄],[編集番号入力欄]-->
		<form method = "post">
			<p>
				<!-- [名前の入力欄] -->
				<input type="text" name="name" value="<?php if (isset($data_name)) {echo $data_name;}?>" placeholder="名前"><br/>
				<!-- [コメント入力欄] -->
				<input type="text" name="comment" value="<?php if (isset($data_comment)) {echo $data_comment;}?>" placeholder="コメント"><br/>
				<!-- [パスワード入力欄]&[送信ボタン] -->
				<input type="text" name="password_set" placeholder="パスワード"><input type="submit" value="送信"><br/>
				<!-- [フラグ欄(非表示)] -->
				<input type="hidden" name="num_flag" value="<?php if (isset($data_num)) {echo $data_num;}?>">
			</p>
			<p>
				<!-- [削除番号入力欄] -->
				<input type="text" name="num_delete" placeholder="削除対象番号"><br/>
				<!-- [パスワード入力欄]&[送信ボタン] -->
				<input type="text" name="password_del" placeholder="パスワード"><input type="submit" value="削除">
			</p>
			<p>
				<!-- [編集番号入力欄] -->
				<input type="text" name="num_change" placeholder="編集対象番号"><br/>
				<!-- [パスワード入力欄]&[送信ボタン] -->
				<input type="text" name="password_mod" placeholder="パスワード"><input type="submit" value="編集">
			</p>
		</form>


    <?php
		// 入力データを表示する。
		$sql = 'SELECT*FROM table_mission4';
		$stmt = $pdo->query($sql);
		$result = $stmt->fetchAll();
		foreach ($result as $row) {
		  //$rowの中にはテーブルのカラム名が入る。
		  echo $row['id'].',';
		  echo $row['name'].',';
		  echo $row['comment'].',';
			echo $row['YmdHms'].'<br>';
		}
     ?>
	</body>
</html>
