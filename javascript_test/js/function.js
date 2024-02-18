// フォームの値をJavaScriptの変数で受け取る
const form = document.getElementById("form");
const teihen = form.teihen;
const takasa = form.takasa;
const waru = 2;
const mensekiResult = document.getElementById("menseki");

// サブミットクリックで実行
form.addEventListener("submit", function (event) {
  event.preventDefault(); //ブラウザのリロードかからなくなる（デフォルトのイベントが動かない）
  const teihen = Number(form.teihen.value);
  const takasa = Number(form.takasa.value);

  const menseki = (teihen * takasa) / waru;
  mensekiResult.innerHTML = `三角形の面積は<strong> ${menseki}m2</strong>です`;
});
