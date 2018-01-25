//----------------------------------------------------------------------------------
// サムネイル画像のレイアウト指定
//----------------------------------------------------------------------------------



//変数定義
var windowWidth, windowHeight;
var thumbnailWidth, thumbnailHeight;
//1行のサムネイル数
var divideNumber;

//ウィンドウサイズの取得
// getWindowSize();

//Windowリサイズした時の処理
window.onresize = getWindowSize;

//ウィンドウサイズを取得して、サムネイル幅を計算
function getWindowSize() {
    var str;
    windowWidth = window.innerWidth;
    windowHeight = window.innerHeight;

    str = "横幅 = " + windowWidth + " , 高さ = " + windowHeight;
    console.log(str);

    if (windowWidth <= 640) {
        divideNumber = 3;
    } else if (windowWidth > 640) {
        divideNumber = 5;
    }
    //画面幅からマージン2pxの（divideNumber ＋1）倍を引き、そこからdivideNumberで割り算
    thumbnailWidth = (windowWidth - 2 * (divideNumber + 1)) / divideNumber;
    thumbnailHeight = thumbnailWidth;
    console.log("thumbnailWidth:" + thumbnailWidth);

    $(".thumbnail").css({ 'width': `${thumbnailWidth}px`, 'height': `${thumbnailHeight}px` });
}

// コンテンツ追加試験用
// for (var i = 1; i <= 100; i++) {
//     //div内にimg枠を追加
//     const str = `<div>
//                             <a href="img/image2.png">
//                                 <img id="imgSample0" class="thumbnail" src="img/image2.png" alt="">
//                             </a>
//                         </div>
//                         <div>
//                             <a href="img/og_image.jpg">
//                                 <img id="imgSample0" class="thumbnail" src="img/og_image.jpg" alt="">
//                             </a>
//                         </div>                       
//                         <div>
//                             <a href="img/image3.jpg">
//                                 <img id="imgSample0" class="thumbnail" src="img/image3.jpg" alt="">
//                             </a>
//                         </div>`
//     $(".img-list").append(str);
// }



//----------------------------------------------------------------------------------
// Initialize Firebase
//----------------------------------------------------------------------------------

// Initialize Firebase
var config = {
    apiKey: "AIzaSyB-IiwZUD1PJTbukaXEntC9wDPfy0Qt1RI",
    authDomain: "photoshare-496df.firebaseapp.com",
    databaseURL: "https://photoshare-496df.firebaseio.com",
    projectId: "photoshare-496df",
    storageBucket: "photoshare-496df.appspot.com",
    messagingSenderId: "258081021590"
};
firebase.initializeApp(config);


//ストレージのルートのリファレンスを取得
const storageRef = firebase.storage().ref();
//MSG送信準備 接続方法を決めている。databaseに接続する。
// const newPostRef = firebase.database().ref();
const newPostRef = firebase.database();

//ファイルカウンター定義:ファイル数
let fileCounter = [];

//userId定義
let userId = localStorage.getItem("title");
console.log("userId : " + userId);
fileCounter[userId] = 0;
console.log("fileCounter[userId]: " + fileCounter[userId]);

////1.ストレージからダウンロードする(同期処理)
//child_added:毎回1回 //value:毎回全てのデータを取得
newPostRef.ref(`user/${userId}`).on("child_added", function (data) {
    // newPostRef.on("child_added", function (data) {
    // console.log(data);ではよくわからないobjectになっている。
    const v = data.val();//データ取得
    // var k = data.key; //ユニークキー取得
    console.log(v);
    // fileCounter値引き継ぎ
    fileCounter = v.counter;
    console.log(`fileCounter:` + fileCounter);
    // downloadURLの取得
    const downloadURL = v.imageURL;
    console.log(`fileCounter[userId]:` + fileCounter[userId], `userId:` + userId, "downloadURL:" + downloadURL);



    // fileCounterが0のときは、初期時なので無視。image0.jpgがエラーとなる。
    if (fileCounter[userId] > 0) {

        //div内にimg枠を追加
        const str = `<div>
                        <a href="${downloadURL}" target=”_blank”>
                            <img id="imgSample${fileCounter[userId]}" class="thumbnail" src="" alt="">
                        </a>
                    </div>`
        $(".img-list").append(str);

        //サムネイル画像サイズを設定
        $(".thumbnail").css({ 'width': `${thumbnailWidth}px`, 'height': `${thumbnailHeight}px` });

        // imgタグに画像を設定
        document.getElementById(`imgSample${fileCounter[userId]}`).src = `${downloadURL}`;

    }
});





//2.ストレージへアップロードする

//アップロードボタンにイベントを指定
window.onload = function () {
    //htmlロードが完了したらアップロードボタンにイベントを設定
    // document.getElementById("btnUpload").onchange = btnUploadChange;
    // document.getElementById("btnUpload2").onchange = btnUploadChange;

    //ウィンドウサイズの取得、サムネイル画像の設定
    getWindowSize();
    //タイトルの取得
    setTitle();


};

//アップロード関数
var btnUploadChange = function (event) {
    // if (fileCounter[userId] == null) {
    //     fileCounter[userId] = 0;
    //     console.log("fileCounter[userId] == null");
    // }

    //ストレージへアップロードするファイルのパスを生成する
    const uploadRef = storageRef.child(`image${fileCounter[userId] + 1}_${userId}.jpg`);
    const file = event.target.files[0];
    uploadRef.put(file).then(function (snapshot) {
        console.log('Uploaded a blob or file!');

        //アップロードしたファイルを表示してみる
        uploadRef.getDownloadURL().then(function (url) {
            console.log(`imageUploadURL${fileCounter[userId] + 1}: ` + url);

            //fileCounterをインクリメント
            fileCounter[userId] += 1;
            console.log("fileCounter[userId]インクリメント後: " + fileCounter[userId]);

            //Date取得
            let d = new Date();
            let date = d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds();
            console.log(date);

            //databaseにfileCounter保管
            newPostRef.ref(`user/${userId}`).push({
                date: date,
                userId: userId,
                counter: fileCounter,
                imageURL: url,
                text: ""
            });

        }).catch(function (error) {
            // Handle any errors
            console.log(error);
        });
    });
};

//----------------------------------------------------------------------------------
// uploadボタンタップ時の動作
//----------------------------------------------------------------------------------



// var fileElem = document.getElementById("btnUpload");

// $("#fileSelect").on("click", function () {
//     if (fileElem) {
//         fileElem.click();
//     }
//     // e.preventDefault(); // "#" に移動するのを防ぐ
// });

// var fileElem = document.getElementById("btnUpload2");

// $("#fileSelect2").on("click", function () {
//     if (fileElem) {
//         fileElem.click();
//     }
//     // e.preventDefault(); // "#" に移動するのを防ぐ
// });

//----------------------------------------------------------------------------------
// サイドメニュータップ時の動作
//----------------------------------------------------------------------------------



//サイドメニュークエリセレクタ
var querySelector = document.querySelector.bind(document);
var menuPanel = querySelector('.menu-panel');

//サイドメニュー出し入れ
(function () {

    'use strict';
    var hambuger = querySelector('.hamburger');

    function toggleMenu() {
        menuPanel.classList.toggle('menu-open');
        console.log("toggle");
    };

    // Menu open
    hambuger.addEventListener('click', toggleMenu);
})();

//サイドメニューコンテンツ追加
// 実際はここにコンテンツを書いていく。未実装


//----------------------------------------------------------------------------------
// タイトル取得関数
//----------------------------------------------------------------------------------

function setTitle() {
    const title = localStorage.getItem("title");
    $("#title").text(title);
}

//----------------------------------------------------------------------------------
// ファイル選択せずにアップロード
//----------------------------------------------------------------------------------

$("#btnUpload").change(function () {
    // $(this).closest("form").submit();
    document.myform.submit();
});

$("#upload").click(function () {
    $("#btnUpload").click();
    return false; // must!
});

$("#btnUpload2").change(function () {
    // $(this).closest("form").submit();
    document.myform2.submit();
});

$("#upload2").click(function () {
    $("#btnUpload2").click();
    return false; // must!
});

//----------------------------------------------------------------------------------
// 画像タップで拡大
//----------------------------------------------------------------------------------

$('.img-list img').on('click', function () {

    // 背景セット
    $('body').append('<div id="back">');
    $('#back').hide();

    // 画像セット
    $('body').append('<div id="enlarged_image">');
    $('#enlarged_image').html('<img>');
    $('#enlarged_image img').attr('src', $(this).attr('src'));
    $('#enlarged_image').hide();

    // //formセット
    // const str = '<div id="input-form">' +
    //     '<form action = "insert.php" method = "post" >' +
    //     '<input type="text" name="title" id="title" class="uk-input">' +
    //     '<input type="submit" value="送信">' +
    //     '</form>' +
    //     '</div>';
    // $('body').append(str);

    var imgWidth = $(this).width();
    var imgHeight = $(this).height();
    var winWidth = $(window).width();
    var winHeight = $(window).height();

    // 拡大画像のサイズ
    var resizedHeight = winHeight * 0.7;
    var resizedWidth = resizedHeight * imgWidth / imgHeight;

    // 拡大後画像幅がディスプレイ幅を超えていた場合
    if (resizedWidth > winWidth) {
        for (var i = 6; i > 0; i--) {
            tmpResizedHeight = winHeight * i * 0.1;
            tmpResizedWidth = tmpResizedHeight * imgWidth / imgHeight;

            if (tmpResizedWidth < winWidth) {
                resizedHeight = tmpResizedHeight;
                resizedWidth = tmpResizedWidth;
                break;
            }
        }
    }

    // 表示位置を中心にセット
    $('#enlarged_image').css({
        top: winHeight / 2 - resizedHeight / 2,
        left: winWidth / 2 - resizedWidth / 2
    });

    $('#enlarged_image img').css({
        height: resizedHeight,
        width: resizedWidth
    });

    $('#back, #enlarged_image, #input-form').fadeIn();

    $('#back').on('click', function () {
        $(this).fadeOut(function () {
            $(this).remove();
        });

        $('#enlarged_image').fadeOut(function () {
            $(this).remove();
        });

        $('#input-form').fadeOut(function () {
            $(this).remove();
        });
    });
});








