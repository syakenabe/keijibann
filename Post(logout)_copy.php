<?php

session_start();

$errorMessage = "";

//ログイン状態のチェック
if(isset($_SESSION["NAME"])){
	header("Location: Post.php");
	exit;
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="Post(logout).css">

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
            <a href="Signin.php">新規登録</a>　　<a href="Logout.php"> ログアウト</a>
      </div>
    </div>
    <div class="main">
			<div class="info">
				投稿するにはログインしてください。
			</div>
			<div class="info2">
				アカウントをお持ちの方はこちら
			</div>
			<div class="btn">
				<a href="Login.php">ログイン</a>
			</div>
			<div class="info3">
				アカウントをお持ちでない方はこちら
			</div>
			<div class="btn">
				<a href="Signin.php">新規登録</a>
			</div>

    </div>
    <div class="footer">
      <div class="footer-left">
        <a href="Main(login).php">ホーム</a>
      </div>

    </div>
			 <!--投稿機能-->

	</body>
</html>
