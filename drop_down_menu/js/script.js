// ドロップダウンメニューの設定
function mediaQueriesWin() {
  var width = $(window).width();
  if (width <= 768) {
    //画面の横幅が768px以下の場合
    $(".parent>a").off("click");
    $(".parent>a").on("click", function () {
      var parentElem = $(this).parent();
      $(parentElem).toggleClass("active");
      $(parentElem).children("ul").stop().slideToggle(500);
      return false;
    });
  } else {
    // 画面の横幅が768px以上の時
    $(".parent>a").off("click");
    $(".parent>a").removeClass("active");
    $(".parent").children("ul").css("display", "");
  }
}

// ページがリサイズされたら動かす機能
$(window).resize(function () {
  mediaQueriesWin();
});

// ページが読み込まれたらすぐ動かす機能
$(window).on("load", function () {
  mediaQueriesWin();
});
