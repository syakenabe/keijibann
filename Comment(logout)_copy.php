<?php

session_start();

$errorMessage = "";

//ログイン状態のチェック
if(isset($_SESSION["NAME"])){
	header("Location: Comment(login).php");
	exit;
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="Comment(logout).css">

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
      <h3>投稿内容</h3>
      	<?php
					$dsn = 'データベース名';
					$user = 'ユーザー名';
					$password = 'パスワード';

	        $pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

	        //エスケープ処理
	        function h($str) {
	          return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
	        }


	        //押された記事番号
	        $keyid = $_GET["keyid"];
	        //元の内容を表示
	        $sql = $pdo->prepare("SELECT*FROM Main WHERE id='$keyid'");
	        $sql->execute();
	        $content = $sql->fetchAll();
	        foreach($content as $row){
	          $target = $row["fname"];
				?>
        <div class="content0">

          <div class="article-title">
            <?php echo h($row['title']); ?>
            <br>
          </div>

					<div class="article-comment">
            <?php echo nl2br(h($row['comment']));?>
            <br>
          </div>

					<div class="article-img">
            <?php
                if ($row["extension"] == "mp4") {
                  echo("<video src=\"mission6_import_media.php?target=$target\" width=\"426\" height=\"240\" controls></video>");
                }
                elseif ($row["extension"] == "jpeg" || $row["extension"] == "png" || $row["extension"] == "gif") {
                  echo ("<img src='mission6_import_media.php?target=$target'>");
                }
            ?>
						<br>
					</div>

          <div class="article-info">
            ユーザー名:<?php echo h($row['name']); ?>　　
            投稿時間：<?php echo h($row['time']);?>　

          </div>
				</div>
      <?php } ?>
      <div class="comment">
        <h3>コメント</h3>
        <?php
          //コメントの表示
          $sql = $pdo->prepare("SELECT*FROM Main_Comment WHERE keyid='$keyid' ORDER BY id ASC");
          $sql->execute();
          $content = $sql->fetchAll();
					foreach($content as $row){
						$target = $row["fname"];

        ?>
				<div class="Comment-all">
					<div class="comment-info">
						<?php
						 echo h($row['id']);
						 echo "　　";
						 echo "投稿者:";
						 echo h($row['name']);
						 echo "　　投稿時間：";
						 echo $row['time'];
						?>
						<br>
					</div>

					<div class="comment-main">
						<?php echo nl2br(h($row['comment'])); ?>
						<br>
					</div>

					<div class="comment-img">
						<?php
							if ($row["extension"] == "mp4") {
								echo("<video src=\"mission6_import_media(comment).php?target=$target\" width=\"426\" height=\"240\" controls></video>");
							}
							elseif ($row["extension"] == "jpeg" || $row["extension"] == "png" || $row["extension"] == "gif") {
								echo ("<img src='mission6_import_media(comment).php?target=$target'>");
							}
						?>
					</div>
				</div>
				<?php } ?>
				</div>

              <h3>投稿フォーム</h3>
          <div class="form">
                コメントを残すにはログインしてください。

          </div>
	      </form>

    </div>

    <div class="footer">
			<div class="footer-left">
				<ul>
				<li><a href="Main(logout).php">ホーム画面</a></li>
				</ul>
			</div>
    </div>
	</body>
</html>
