<?php
session_start();

//エラーメッセージ、登録完了メッセージの初期化
$errorMessage = "";
$signUpMessage = "";

//ログインボタンが押された場合
if(isset($_POST["signUp"])){
    //ユーザーＩＤの入力チェック
    if(empty($_POST["userid"])){
    	$errorMessage = 'ユーザーIDが未入力です。';
	} else if(empty($_POST["username"])){
		$errorMessage = 'ユーザー名が未入力です。';
	} else if(empty($_POST["password"])){
		$errorMessage = 'パスワードが未入力です。';
	} else if(empty($_POST["password2"])){
		$errorMessage = 'パスワードが未入力です。';
	}

    if(!empty($_POST["userid"]) && !empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["password2"]) && $_POST["password"] === $_POST["password2"]){

      $dsn = 'データベース';
  		$user = 'ユーザー';
  		$password = 'パスワード';

    	//ユーザーネームとパスワードが入力されていたら認証する
    	try{
			$pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));


			//入力したユーザーネームとパスワードを格納
    		$username = $_POST["username"];
    		$password = $_POST["password"];
			$userid = $_POST["userid"];

			//同一のユーザIDが存在するか
			$stmt = $pdo->prepare("SELECT COUNT(*) FROM userData WHERE id = '$userid'");
			$stmt->execute();
			$count = $stmt->fetchColumn();

			echo "レコード数は$count"."<br>";
			$count = intval($count);
			if($count === 0){			//同一IDが存在しない
				$stmt = $pdo->prepare("INSERT INTO userData(id, name, password) VALUES(?, ?, ?)");

				$stmt->execute(array($userid, $username, $password));

				$signUpMessage = '登録が完了しました。あなたの登録IDは'.$userid.'です。パスワードは'.$password.'です。';
			}else{			//同一IDが存在する
				$errorMessage = 'このユーザIDは使用できません。';
			}
		}catch(PDOException $e){
			$errorMessage = 'データベースエラー';
			echo $e->getMessage();
		}
	} elseif($_POST["password"] != $_POST["password2"]){
		$errorMessage = 'パスワードに誤りがあります。';
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
	<meta charset = "utf-8">
  <link rel="stylesheet" href="Signin.css">

	</head>
	<title>新規登録</title>
	<body>
    <div class="header">
      <div class="header-left">
          雑談広場
      </div>
      <div class="header-right">
            <a href="Signin.php">新規登録</a>　　<a href="Logout.php"> ログイン</a>
      </div>

    </div>
    <div class="main">
      <div class="form">
        <form id="loginForm" name="loginForm" action="" method="POST">
        　　	  <h3>登録フォーム</h3>
                <div class="message">
                  <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
                  <div><font color="#0000ff"><?php echo htmlspecialchars($signUpMessage, ENT_QUOTES); ?></font></div>

                </div>
                <div class="userid">
                  <label for="userid">ユーザID</label>
                  <input type="text" id="userid" name="userid" placeholder="ユーザーIDを入力" value="<?php if(!empty($_POST["userid"])){echo htmlspecialchars($_POST["userid"], ENT_QUOTES);} ?>">
                  <br>

                </div>
                <div class="username">
                  <label for="username">ユーザ名</label>
                  <input type="text" id="username" name="username" placeholder="ユーザー名を入力" value="<?php if(!empty($_POST["username"])){echo htmlspecialchars($_POST["username"], ENT_QUOTES);} ?>">
                  <br>

                </div>
                <div class="password">
                  <label for="password">パスワード</label>
                  <input type="password" id="password" name="password" value="" placeholder="パスワードを入力">
                  <br>

                </div>
                <div class="password2">
                  <label for="password2">パスワード(確認用)</label>
                  <input type="password" id="password2" name="password2" value="" placeholder="再度パスワードを入力">
                  <br>

                </div>
                <div class="btn">
                  <input type="submit" id="signUp" name="signUp" value="新規登録">

                </div>
            </form>

      </div>
      <div class="login">

        <a href="Login.php">ログイン</a>


      </div>

    </div>
    <div class="footer">
      <div class="footer-left">
        <a href="Main(login).php">ホーム</a>
      </div>
    </div>
	</body>
</html>
