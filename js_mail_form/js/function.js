// フォーム変数
const form = document.getElementById("form");
const hi_kizon = form.hi_kizon;
const ju_kizon = form.ju_kizon;
const hi_zouchiku = form.hi_zouchiku;
const ju_zouchiku = form.ju_zouchiku;
const bild = form.bild;
// 結果表示変数
const result = document.getElementById("result");
const hi_kizonMensekiResult = document.getElementById("hi_kizonMenseki");
const ju_kizonMensekiResult = document.getElementById("ju_kizonMenseki");
const hi_zouchikuMensekiResult = document.getElementById("hi_zouchikuMenseki");
const ju_zouchikuMensekiResult = document.getElementById("ju_zouchikuMenseki");
const hi_result = document.getElementById("hi_zouchikuMenseki");
const ju_result = document.getElementById("ju_zouchikuMenseki");
const nobe_result = document.getElementById("nobeMenseki");
const judgeResult = document.getElementById("judge");

// サブミットクリックで実行
form.addEventListener("submit", function (event) {
  event.preventDefault(); //ブラウザのリロードかからなくなる（デフォルトのイベントが動かない）
  const hi_kizonMenseki = Number(hi_kizon.value);
  const ju_kizonMenseki = Number(ju_kizon.value);
  const hi_zouchikuMenseki = Number(hi_zouchiku.value);
  const ju_zouchikuMenseki = Number(ju_zouchiku.value);
  const judge = bild.value;
  const hi_nobeMenseki = hi_kizonMenseki + hi_zouchikuMenseki;
  const ju_nobeMenseki = ju_kizonMenseki + ju_zouchikuMenseki;
  const hi_rateMenseki = hi_zouchikuMenseki / hi_nobeMenseki;
  const nobeMenseki = hi_nobeMenseki + ju_nobeMenseki;
  // 結果表示
  result.classList.add("on");
  hi_result.innerHTML = `${hi_nobeMenseki}㎡`;
  ju_result.innerHTML = `${ju_nobeMenseki}㎡`;
  nobe_result.innerHTML = `${nobeMenseki}㎡`;
  // 省エネ法判定式
  if (hi_nobeMenseki >= 300 && judge === "1") {
    judgeResult.innerHTML = "省エネ<strong>適判</strong>が必要です。";
  } else if (hi_nobeMenseki >= 300 && judge === "0" && hi_rateMenseki >= 0.5) {
    judgeResult.innerHTML = "省エネ<strong>適判</strong>が必要です。";
  } else if (hi_nobeMenseki >= 300 && judge === "0" && hi_rateMenseki < 0.5) {
    judgeResult.innerHTML = "省エネ<strong>届出</strong>が必要です。";
  } else if (hi_nobeMenseki < 300 && nobeMenseki >= 300) {
    judgeResult.innerHTML = "省エネ<strong>届出</strong>が必要です。";
  } else if (nobeMenseki < 300 && nobeMenseki >= 10) {
    judgeResult.innerHTML = "省エネ<strong>説明義務</strong>が必要です。";
  } else if (nobeMenseki < 10) {
    judgeResult.innerHTML = "省エネ法の手続きは<strong>不要</strong>です。";
  }
});
