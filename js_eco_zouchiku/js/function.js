// フォーム変数
const form = document.getElementById("form");
const kizon = form.kizon;
const zouchiku = form.zouchiku;
const bild1 = form.bild1;
const bild2 = form.bild2;
// 結果表示変数
const result = document.getElementById("result");
const keisuResult = document.getElementById("keisu");
const kizonMensekiResult = document.getElementById("kizonMenseki");
const zouchikuMensekiResult = document.getElementById("zouchikuMenseki");
const nobeMensekiResult = document.getElementById("nobeMenseki");
const ecoLevel = document.getElementById("ecoLevel");
const beiResult = document.getElementById("bei");

// サブミットクリックで実行
form.addEventListener("submit", function (event) {
  event.preventDefault(); //ブラウザのリロードかからなくなる（デフォルトのイベントが動かない）
  const kizonMenseki = Number(kizon.value);
  const zouchikuMenseki = Number(zouchiku.value);
  const ecoLevelResult1 = Number(bild1.value);
  const ecoLevelResult2 = Number(bild2.value);
  if (ecoLevelResult1 === 0 && ecoLevelResult2 === 0) {
    const ecoLevelResult3 = 
  }
  // BEI計算式
  const nobeMenseki = kizonMenseki + zouchikuMenseki;
  const bei1 = nobeMenseki * ecoLevelResult;
  const bei2 = kizonMenseki * keisu;
  const bei3 = bei1 - bei2;
  const bei4 = (bei3 / zouchikuMenseki) * 100;
  const bei = Math.floor(bei4) / 100;
  // 結果表示
  result.classList.add("on");
  keisuResult.innerHTML = `${keisu}`;
  kizonMensekiResult.innerHTML = `${kizonMenseki}㎡`;
  zouchikuMensekiResult.innerHTML = `${zouchikuMenseki}㎡`;
  nobeMensekiResult.innerHTML = `${nobeMenseki}㎡`;
  ecoLevel.innerHTML = `BEI ${ecoLevelResult}`;
  beiResult.innerHTML = `BEI<strong> ${bei}</strong>以下`;
});
