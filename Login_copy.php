<?php
//セッション開始
session_start();

//エラーメッセージの初期化
$errorMessage = "";

//ログインボタンが押されたとき
if(isset($_POST["login"])){
	//ユーザーIDの入力チェック
	if(empty($_POST["userid"])){
		$errorMessage = 'ユーザーIDが未入力です。';
	}elseif(empty($_POST["password"])){
		$errorMessage = 'パスワードが未入力です。';
	}

	if(!empty($_POST["userid"]) && !empty($_POST["password"])){

		// エラー処理
		//ユーザIDとパスワードが入力されていたらデータベースに接続
		try{
			$dsn = 'データベース';
			$user = 'ユーザー名';
			$password = 'パスワード';
			$pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

			//ユーザーIDを代入
			$userid = $_POST["userid"];
			echo "入力ユーザＩＤ= $userid"."<br>";
			$stmt = $pdo->prepare("SELECT*FROM userData WHERE id = '$userid'");
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			var_dump($row);

			$password = $_POST["password"];

			echo "入力パスワード＝"."$password"."<br>";
			//echo 出力パスワード＝;


				if($password === $row['password']){
					echo 'ok'.'<br>';
					//入力したID のユーザー名を取得
					$id = $row['id'];
					$sql = "SELECT*FROM userData WHERE id = '$id'";
					$stmt = $pdo->query($sql);
					foreach ($stmt as $row){
						$row['name'];
					}
					$_SESSION["NAME"] = $row['name'];
					header("Location: Main(login).php"); //メイン画面にいどう
					exit(); //処理終了
				}else{
					//認証失敗
					$errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
				}
		}catch(PDOException $e){
			$errorMessage = 'データベースエラー';
			//$errorMessage = $sql;
			echo $e->getMessage();
		}
	}
}
?>



<!DOCTYPE html>
<html>
<head>
<meta charset = "utf-8">
<link rel="stylesheet" href="Login.css">
</head>
<title>
ログイン
</title>
<body>
	<div class="header">
		<div class="header-left">
				雑談広場
		</div>
		<div class="header-right">
					<a href="Signin.php">新規登録</a>　　<a href="Logout.php"> ログアウト</a>
		</div>
	</div>
	<div class="main">
		<div class="form">
			<form id="loginForm" name="loginForm" action="" method="POST">
					<h3>ログインフォーム</h3>
					<div class="message">
						<div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES);?></font></div>

					</div>
					<div class="userid">
						<label for="userid">  ユーザーID</label>
						<input type="text" id="userid" name="userid" placeholder="ユーザーIDを入力"
						 value = <?php if(!empty($_POST["userid"])){echo htmlspecialchars($_POST["userid"], ENT_QUOTES);} ?>
						 >
						<br>

					</div>
					<div class="password">
						<label for="password">パスワード</label>
						<input type="password" id="password" name="password" placeholder="パスワードを入力" >
						<br>

					</div>
					<div class="submit">
						<div class="btn">
							<input type="submit" id="login" name="login" value="ログイン">

						</div>

					</div>
			</form>

		</div>
	</div>
	<div class="footer">
		<div class="footer-left">
			<a href="Main(login).php">ホーム</a>
		</div>
	</div>
</body>
</html>
