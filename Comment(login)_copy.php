<?php

session_start();

$errorMessage = "";

//ログイン状態のチェック
if(!isset($_SESSION["NAME"])){
	header("Location: Comment(logout).php");
	exit;
}
//コメント入力
//コメントが入力されているか確認
if(isset($_POST["comment_ins"])){
	if(empty($_POST["comment"])){
		$errorMessage = "コメントが未入力です。";
	}

	if(!empty($_POST["comment"])){

	  $dsn = 'データベース名';
		$user = 'ユーザー名';
		$password = 'パスワード';

		try{
			$pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

			//コメント数のカウント
			$keyid = $_GET["keyid"];
			$sql = $pdo->prepare("SELECT COUNT(*) FROM Main_Comment WHERE keyid='$keyid'");
			$sql->execute();
			$id = $sql->fetchColumn();
			$id++;
			//コメントを格納
			$name = $_SESSION["NAME"];
			$comment = $_POST["comment"];
			$time = date("Y/m/d H:i:s");
			//画像・動画挿入
			//画像・動画のアップロード
			if (isset($_FILES['upfile']['error']) && is_int($_FILES['upfile']['error']) && $_FILES["upfile"]["name"] !== "") {
				//エラーチェック
				switch ($_FILES['upfile']['error']) {
					case UPLOAD_ERR_OK: //OK
						break;
					case UPLOAD_ERR_NO_FILE: //ファイル未選択
						throw new RuntimeException('ファイルが選択されていません');
					case UPLOAD_ERR_INI_SIZE: //php.ini定義の最大サイズ超過
						throw new RuntimeException('ファイルサイズが大きすぎます');
					default:
						throw new RuntimeException('その他のエラーが発生しました');
						break;
				}

				//画像・動画をバイナリデータにする。
				$raw_data = file_get_contents($_FILES['upfile']['tmp_name']);

				//拡張子の確認
				$tmp = pathinfo($_FILES["upfile"]["name"]);
				$extension = $tmp["extension"];
				if ($extension === "jpg" || $extension === "jpeg" || $extension === "JPG" || $extension === "JPEG") {
					$extension = "jpeg";
				}
				elseif ($extension === "png" || $extension === "PNG") {
					$extension = "png";
				}
				elseif ($extension === "gif" || $extension === "GIF") {
					$extension = "gif";
				}
				elseif ($extension === "mp4" || $extension === "MP4") {
					$extension = "mp4";
				}
				else {
					echo "非対応ファイルです。<br/>";
					echo ("<a href=\"Main(login).php\">戻る</a><br/>");
					exit();
				}

				//DBに格納するファイルネームの設定
				//サーバー側の一時的なファイルネームと取得時刻を結合した文字列の生成＊ハッシュ化はしない
				$data = getdate();
				$fname = $_FILES["upfile"]["tmp_name"].$date["year"].$data["mon"].$data["mday"].$data["hours"].$date["minutes"].$date["seconds"];
			}
				//画像・動画をＤＢに格納
				$sql = "INSERT INTO Main_Comment (keyid, id, name, comment, time, fname, extension, raw_data) VALUES (:keyid, :id, :name, :comment, :time, :fname, :extension, :raw_data);";
				$stmt = $pdo->prepare($sql);
				$stmt -> bindvalue(":keyid",$keyid, PDO::PARAM_STR);
				$stmt -> bindvalue(":id",$id, PDO::PARAM_STR);
				$stmt -> bindvalue(":name",$name, PDO::PARAM_STR);
				$stmt -> bindvalue(":comment",$comment, PDO::PARAM_STR);
				$stmt -> bindvalue(":time",$time, PDO::PARAM_STR);
				$stmt -> bindvalue(":fname",$fname, PDO::PARAM_STR);
				$stmt -> bindvalue(":extension",$extension, PDO::PARAM_STR);
				$stmt -> bindvalue(":raw_data",$raw_data, PDO::PARAM_STR);
				$stmt -> execute();


		}catch(PDOException $e){
			$errorMessage = 'データベースエラー';
			echo $e->getMessage();
		}
	}
}

?>


<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="Comment(login).css">

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
	    <div class="form">
	      <form id ="Comment" name="Comment" action=""  enctype="multipart/form-data"  method="POST" >
	        <fieldset>
	          <h3><legend>投稿フォーム</legend></h3>
	          <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
	          <label for="comment">コメント</label>
	          <br>
	          <textarea name="comment" cols="30" rows="5" placeholder="コメントを入力" value=""></textarea>
	          <br>
	          <label for="">画像・動画アップロード</label>
	          <br>
	          <input type="file" name="upfile" value="ファイル">
	          <br>
	          ※画像はjpeg方式，png方式，gif方式に対応しています．動画はmp4方式のみ対応しています．<br>
	          <input type="submit" id="comment_ins" name="comment_ins" value="送信">
	        </fieldset>
	      </form>

	    </div>
    </div>

    <div class="footer">
			<div class="footer-left">
				<ul>
				<li><a href="Main(login).php">ホーム画面</a></li>
				</ul>
			</div>
    </div>
	</body>
</html>
