<?php
//ページ1から値引き継ぎ
$title = $_POST["titleFrom1"];
// var_dump($_POST["titleFrom1"]);

//PDOでデータベース接続
try {
    $pdo = new PDO("mysql:host=localhost;dbname=photoBucket_db;charset=utf8","root",""); 
    //XAMPPは最後(password)のrootはいらない。MAMPはいる。
}catch (PDOException $e) {
    exit( 'DbConnectError:' . $e->getMessage());
}

// 実行したいSQL文（最新順番3つ取得）
$sql = "SELECT * FROM photo WHERE title = "."'$title'"."ORDER BY id ASC";


//MySQLで実行したいSQLセット。プリペアーステートメントで後から値は入れる
$stmt = $pdo->prepare($sql);
$flag = $stmt->execute();

if($flag==false){
    $error = $stmt->errorInfo();
    exit("ErrorQuery:".$error[2]);
}else{
	//html全体が含まれている。

?>





<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>

<body>

    <!-- header -->
    <header>
        <div class="header">
            <img src="img/icon_23.png" alt="" class="hamburger">
            <p id="title"></p>
            <!-- <img src="img/logo2.png" alt="" class="header-logo"> -->
            <ul class="header-title">
                <!-- <li class="header-item">
                <a href="#about">ABOUT</a>
            </li>
            <li class="header-item">
                <a href="#course-navi">COURSE</a>
            </li>
            <li class="header-item">
                <a href="#news">NEWS</a>
            </li>
            <li class="header-item">
                <a href="#access">ACCESS</a>
            </li> -->
                <div id="fileSelect">
                    <form name="myform" action="insertDB.php" method="post" enctype="multipart/form-data" class="upload-wrapper" >
                        <input type="file" id="btnUpload" name="upfile" accept="image/jpeg" style="display:none">
                        <input type="text" id="formTitle" name="formTitle" style="display:none" value="title">
                        <img src="img/upload.png" alt="" class="upload-icon">
                        <input type="submit" value="UPLOAD" class="upload-button" id="upload">
                    </form>
                </div>
            </ul>
        </div>
    </header>

    <div class="space"></div>


    <!-- サイドメニュー -->
    <div class='content'>
        <div class='menu-panel'>
            <div class="menu-wrapper">
                <a href='#'>&nbsp;</a>
                <!-- ここにコンテンツを追加 -->
            </div>
        </div>
    </div>

    <!-- コンテンツ表示画面 -->
    <div class="img-list">
        <!-- サンプル -->
        <div>
                <img class="thumbnail" src="img/gari_oshare.jpg" alt="">
        </div>
        <div>
                <img class="thumbnail" src="img/IMG_0707.jpeg" alt="">
        </div>
        <div>
                <img class="thumbnail" src="img/IMG_1116.jpeg" alt="">
        </div>
        <div>
                <img class="thumbnail" src="img/IMG_4290.jpeg" alt="">
        </div>
        <div>
                <img class="thumbnail" src="img/IMG_4878.jpeg" alt="">
        </div>

        <!-- ココにコンテンツを追加してゆく -->
        <?php
			while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
        ?>
        
        <div>
            <img class="thumbnail" src=<?php echo $result["url"]; ?> alt="">
        </div>

        <?php 
			}
		?>

    </div>

    <div class="space"></div>

    <!-- 画面下部アップロードボタン -->
    <div class="upload-wrapper2" id="fileSelect2">
    <form name="myform2" action="insertDB.php" method="post" enctype="multipart/form-data" class="upload-wrapper2" >
            <input type="file" id="btnUpload2" name="upfile" accept="image/jpeg" style="display:none"> 
            <input type="text" id="formTitle2" name="formTitle" style="display:none" value="title">
            <img src="img/upload.png" alt="" class="upload-icon2">
            <input type="submit" value="UPLOAD" class="upload-button2" id="upload2">
        </form>
    </div>









    <!-- 以下JavaScript領域 -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <!-- 以下Firebase -->

    <script src="https://www.gstatic.com/firebasejs/4.8.1/firebase.js"></script>
    <script src="main.js"></script>



</body>

</html>

<?php 
}
?>