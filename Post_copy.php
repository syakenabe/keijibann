<?php

session_start();

$errorMessage = "";

//ログイン状態のチェック
if(!isset($_SESSION["NAME"])){
	header("Location: Post(logout).php");
	exit;
}

//コメント入力
if(isset($_POST["comment_ins"])){
	if(empty($_POST["title"])){
		$errorMessage = "タイトルが未入力です。";
	} elseif(empty($_POST["comment"])){
		$errorMessage = "コメントが未入力です。";
	}

	if(!empty($_POST["title"]) && !empty($_POST["comment"])){
		//コメント・タイトルが入力されている時の動作
	  $dsn = 'データベース';
		$user = 'ユーザー';
		$password = 'パスワード';

		try{
			$pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

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
					//入力されたタイトルとコメントを格納
					$name = $_SESSION["NAME"];
					$title = $_POST["title"];
					$comment = $_POST["comment"];
					$time = date("Y/m/d H:i:s");
					$genre = $_POST["genre"];
					//画像・動画をＤＢに格納
					$sql = "INSERT INTO Main (title, name, comment, time, genre, fname, extension, raw_data) VALUES (:title, :name, :comment, :time, :genre, :fname, :extension, :raw_data);";
					$stmt = $pdo->prepare($sql);
					$stmt -> bindvalue(":title",$title, PDO::PARAM_STR);
					$stmt -> bindvalue(":name",$name, PDO::PARAM_STR);
					$stmt -> bindvalue(":comment",$comment, PDO::PARAM_STR);
					$stmt -> bindvalue(":time",$time, PDO::PARAM_STR);
					$stmt -> bindvalue(":genre",$genre, PDO::PARAM_STR);
					$stmt -> bindvalue(":fname",$fname, PDO::PARAM_STR);
					$stmt -> bindvalue(":extension",$extension, PDO::PARAM_STR);
					$stmt -> bindvalue(":raw_data",$raw_data, PDO::PARAM_STR);
					$stmt -> execute();

          header("Location:Main(login).php");
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
    <link rel="stylesheet" href="Post.css">

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
      <div class="form">
        <form id = "Comment" name="Comment" action="Post.php" enctype="multipart/form-data" method="post">
            <h3><legend>投稿フォーム</legend></h3>
            <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
            <div class="form-title">

              <label for="title">タイトル</label>
              <br>
              <input type="text"name="title" size="40" placeholder="タイトルを入力" value="">
              <br>

            </div>
            <div class="form-comment">
              <label for="comment">コメント</label>
              <br>
              <textarea name="comment" cols="50" rows="6" placeholder="コメントを入力" value=""></textarea>
              <br>

            </div>
            <div class="form-up">
              <label for="">画像・動画アップロード</label>
              <br>
              <div class="">
                <input type="file" name="upfile" value="ファイル">
              </div>
              <br>
              ※画像はjpeg方式，png方式，gif方式に対応しています．動画はmp4方式のみ対応しています．<br>

							<div class="form-tag">
								ジャンルを選択してください。
								<p>
									<select class="" name="genre">
										<option value="雑談">雑談</option>
										<option value="質問">質問</option>
										<option value="趣味">趣味</option>
									</select>
								</p>
							</div>
            </div>
            <div class="form-submit">
              <div class="btn">
                <input type="submit" id="comment_ins" name="comment_ins" value="送信">

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
			 <!--投稿機能-->

	</body>
</html>
