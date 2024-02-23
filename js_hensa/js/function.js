// フォームの値をJavaScriptの変数で受け取る
const form = document.getElementById("form");
const teisu1 = 10;
const teisu2 = 50;
const hensachi = document.getElementById("hensachi");

// サブミットクリックで実行
form.addEventListener("submit", function (event) {
  event.preventDefault(); //ブラウザのリロードかからなくなる（デフォルトのイベントが動かない）
  const hensa = Number(form.hensa.value);
  const ave = Number(form.ave.value);
  const point = Number(form.point.value);

  const hensachiResult = Math.round(((teisu1 * (point - ave)) / hensa + teisu2) * 100) / 100;
  hensachi.innerHTML = `標準偏差 ${hensa} の時の<br>偏差値は<strong> ${hensachiResult}</strong>です`;
});
