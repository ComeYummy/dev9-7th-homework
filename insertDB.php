<?php
//フォームのデータ受け取り
$name = $_FILES ['upfile']['name'];
$type = $_FILES ['upfile']['type'];
$error = $_FILES ['upfile']['error'];
$size = $_FILES ['upfile']['size'];
$title = $_POST["formTitle"];


$msg = null;



// もし$_FILES['upfile']があって、一時的なファイル名の$_FILES['upfile']が
// POSTでアップロードされたファイルだったら
if (isset ( $_FILES ['upfile'] ) && is_uploaded_file ( $_FILES ['upfile'] ['tmp_name'] )) {
    $old_name = $_FILES ['upfile'] ['tmp_name'];
    //  もしuploadというフォルダーがなければ
    if (! file_exists ( $_POST["formTitle"] )) {
        mkdir ( $_POST["formTitle"]);
    }
    $new_name = date ( "YmdHis" );
    $new_name .= mt_rand ();
    switch (exif_imagetype ( $_FILES ['upfile'] ['tmp_name'] )) {
        case IMAGETYPE_JPEG :
            $new_name .= '.jpg';
            break;
        case IMAGETYPE_GIF :
            $new_name .= '.gif';
            break;
        case IMAGETYPE_PNG :
            $new_name .= '.png';
            break;
        default :
            // header ( 'Location: upload.php' );
            // exit ();
    }
    $imageName = basename ( $_FILES ['upfile'] ['name'] );
    
   

    //  もし一時的なファイル名の$_FILES['upfile']ファイルを
    //  upload/basename($_FILES['upfile']['name'])ファイルに移動したら
    if (move_uploaded_file ( $old_name, $_POST["formTitle"]."/".$new_name )) {
        $msg = $imageName . 'のアップロードに成功しました';

        // ファイルの取り込み。データベースへ保存。
        $url = $_POST["formTitle"]."/".$new_name;
        insertDB();

    } else {
        $msg = 'アップロードに失敗しました';
    }
}


if(isset($msg) && $msg == true){
    // echo '<p>'. $msg . '</p>';
    // echo '<p><img src='.$title."/".$new_name.'></p>';
    // var_dump($_POST["formTitle"]);
}



function insertDB(){
        //PDOでデータベース接続
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=photoBucket_db;charset=utf8","root",""); 
        //XAMPPは最後(password)のrootはいらない。MAMPはいる。
    }catch (PDOException $e) {
        exit( 'DbConnectError:' . $e->getMessage());
    }

    //変数を関数外のグローバル変数を利用
    global $name;
    global $type;
    global $error;
    global $size;
    global $url;
    global $title;
    // var_dump($name);
    // var_dump($url);

    // 実行したいSQL文
    $sql = "INSERT INTO photo(id,url,title,name,type,error,size,time) VALUES(NULL, :url, :title, :name, :type, :error, :size, sysdate())"; 

    //MySQLで実行したいSQLセット。プリペアーステートメントで後から値は入れる
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':url', $url, PDO::PARAM_STR); // 最後の引数はデータの型。INTなら別のもの
    $stmt->bindValue(":title", $title, PDO::PARAM_STR);
    $stmt->bindValue(":name", $name, PDO::PARAM_STR);
    $stmt->bindValue(":type", $type, PDO::PARAM_STR);
    $stmt->bindValue(":error", $error, PDO::PARAM_INT);
    $stmt->bindValue(":size", $size, PDO::PARAM_INT);

    //実際に実行
    $flag = $stmt->execute();
    //$flagに成功失敗のbool値が入る。

    //失敗した場合はエラーメッセージ表示
    if($flag==false){
        $error = $stmt->errorInfo();
        exit("ErrorQuery:".$error[2]);
    }else{
        // header ( 'Location: index2.php' );
        // exit();
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>PHOTO BUCKET</title>

    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
</head>

<body>

<!-- localstrageの値を引き渡すためだけにform作成 -->
<form action="index2.php" method="post" name="titleSubmit">
    <input type="text" id="title" class="title-field" name="titleFrom1" style="display:none">
</form>

<script>
$("#title").val(localStorage.getItem("title"));
// form送信して画面遷移
document.titleSubmit.submit();


</script>

</body>

</html>