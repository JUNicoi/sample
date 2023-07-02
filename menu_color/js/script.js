// アクティブページのリンク色変える
href = location.href;
var links = jQuery("#gnav_list ul li a"); //classを付与したいaタグを含めた階層をカッコ内に記述

console.log(links);
links.each(function (index, value) {
  if (value.href == href) {
    jQuery("#gnav_list ul li").children("a").eq(index).addClass("active"); //classを付与したいaタグまでの階層をjQueryカッコ内に記述
  }
});
