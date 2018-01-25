<?php
//フォームのデータ受け取り
$name = $_FILES ['upfile']['name'];
$type = $_FILES ['upfile']['type'];
$error = $_FILES ['upfile']['error'];
$size = $_FILES ['upfile']['size'];


$msg = null;



// もし$_FILES['upfile']があって、一時的なファイル名の$_FILES['upfile']が
// POSTでアップロードされたファイルだったら
if (isset ( $_FILES ['upfile'] ) && is_uploaded_file ( $_FILES ['upfile'] ['tmp_name'] )) {
    $old_name = $_FILES ['upfile'] ['tmp_name'];
    //  もしuploadというフォルダーがなければ
    if (! file_exists ( 'upload' )) {
        mkdir ( 'upload' );
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
    if (move_uploaded_file ( $old_name, 'upload/'.$new_name )) {
        $msg = $imageName . 'のアップロードに成功しました';

        // ファイルの取り込み。データベースへ保存。
        $url = 'upload/'.$new_name;
        insertDB();

    } else {
        $msg = 'アップロードに失敗しました';
    }
}


if(isset($msg) && $msg == true){
    echo '<p>'. $msg . '</p>';
    // echo '<p><img src="upload/'.$new_name.'"></p>';
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
    var_dump($name);
    var_dump($url);

    // 実行したいSQL文
    $sql = "INSERT INTO photo(id,url,name,type,error,size,time) VALUES(NULL, :url, :name, :type, :error, :size, sysdate())"; 

    //MySQLで実行したいSQLセット。プリペアーステートメントで後から値は入れる
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':url', $url, PDO::PARAM_STR); // 最後の引数はデータの型。INTなら別のもの
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
        header ( 'Location: index2.php' );
        // exit();
    }
}





?>