<?php
session_start();

if(isset($_SESSION["NAME"])){
   $errorMessage = "ログアウトしました。";
}else{
   $errorMessage ="セッションがタイムアウトしました。";
}

//セッションの変数のクリア
$_SESSION = array();

//セッションクリア
@session_destroy();

?>

<!DOCTYPE html>
<html>
	<head>
	<meta charset = "utf-8">
	<link rel="stylesheet" href="Logout.css">

	</head>
	<title>
	掲示板(仮)
	</title>
	<body>
		<div class="header">
			<div class="header-left">
					雑談広場
			</div>
			<div class="header-right">
						<a href="Signin.php">新規登録</a>　　<a href="Login.php"> ログイン</a>
			</div>
		</div>
		<div class="main">
			<h3>ログアウト画面</h3>
			<div class="message">
				<div><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></div>

			</div>
			<div class="btn">
				<li><a href="Login.php">ログイン画面に戻る</a></li>
			</div>

		</div>
		<div class="footer">
			<div class="footer-left">
				<a href="Main(login).php">ホーム</a>
			</div>

		</div>
	</body>
</html>
