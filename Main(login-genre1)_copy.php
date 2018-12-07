<?php

session_start();

$errorMessage = "";

//ログイン状態のチェック
if(!isset($_SESSION["NAME"])){
	header("Location: Main(logout).php");
	exit;
}
?>
<!DOCTYPE html>
<html>
	<head>
	<meta charset = "utf-8">
  <link rel="stylesheet" href="Main(login).css">

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
      <div class="genre">
				<ul>
					<li><a href="Main(login).php">すべて</a></li>　　　
					<li class="current"><a href="Main(login-genre1).php">雑談</a></li>　　　　
					<li> <a href="Main(login-genre2).php">質問</a> </li>　　　
					<li><a href="Main(login-genre3).php">趣味</a> </li>　　　
				</ul>
      </div>
			<br>
			<div class="post">
        <?php
        //データベースに接続
    		  $dsn = 'データベース名';
    			$user = 'ユーザー名';
    			$password = 'パスワード';
    			$pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

    			//htmlエスケープ
    			function h($str) {
    				return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    			}
    			//内容の表示
          //内容を取得
    			$sql = $pdo->prepare("SELECT*FROM Main WHERE genre='雑談' ORDER BY id DESC");
    			$sql->execute();
    			$content = $sql->fetchAll();
    		?>
        <h3>投稿内容</h3>
        <form action="Comment(login).php" method="GET">
          <?php
    				foreach($content as $row){
    					$id =$row['id'];
    					$target = $row["fname"];
    					$sql = $pdo->prepare("SELECT COUNT(*) FROM Main_Comment WHERE keyid='$id'");
    					$sql->execute();
    					$count = $sql->fetchColumn();

    			?>
    			<a href="Comment(login).php?keyid=<?php echo urlencode($id); ?>" >


				<div class="content">
					<div class="article-title">
						<?php echo h($row['title']);?>
            <br>
					</div>
					<div class="article-comment">
            <?php echo nl2br(h($row['comment']));  ?>
            <br>
					</div>
					<div class="article-info">
						ユーザー名:<?php echo h($row['name']);?>
						ジャンル：<?php echo h($row['genre']);?>　　
            投稿時間：<?php echo h($row['time']);?>　
            コメント数: <?php  echo $count;?>
             <br>
					</div>
				</div>
			</a>
      <?php } ?>

			</div>
    </div>

		<div class="footer">
			<div class="footer-left">
				<a href="Main(login).php">ホーム</a>
			</div>
			<div class="footer-right">
				<a href="Post.php"> 投稿 </a>
			</div>
		</div>
	</body>
</html>
