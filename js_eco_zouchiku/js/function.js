const form = document.getElementById("form");
const kizon = form.kizon;
const zouchiku = form.zouchiku;
const bild = form.bild;
const result = document.getElementById("result");
const kizonMensekiResult = document.getElementById("kizonMenseki");
const zouchikuMensekiResult = document.getElementById("zouchikuMenseki");
const nobeMensekiResult = document.getElementById("nobeMenseki");
const ecoLevel = document.getElementById("ecoLevel");
const beiResult = document.getElementById("bei");

// 選択状態の値を取得

form.addEventListener("submit", function (event) {
  event.preventDefault(); //ブラウザのリロードかからなくなる（デフォルトのイベントが動かない）
  const kizonMenseki = Number(kizon.value);
  const zouchikuMenseki = Number(zouchiku.value);
  const bildYear = Number(bild.value);

  const nobeMenseki = kizonMenseki + zouchikuMenseki;
  const bei1 = nobeMenseki * bildYear;
  const bei2 = kizonMenseki * 1.2;
  const bei3 = bei1 - bei2;
  const bei4 = (bei3 / zouchikuMenseki) * 100;
  // const bei4 = (((nobeMenseki * bildYear - kizonMenseki * 1.2) / zouchikuMenseki) * 100) / 100;
  const bei = bei3 / 100;
  kizonMensekiResult.innerHTML = `${kizonMenseki}㎡`;
  zouchikuMensekiResult.innerHTML = `${zouchikuMenseki}㎡`;
  nobeMensekiResult.innerHTML = `${nobeMenseki}㎡`;
  ecoLevel.innerHTML = `BEI ${bildYear}`;
  beiResult.innerHTML = `BEI ${bei}`;
  console.log(bei1);
  console.log(bei2);
  console.log(bei3);
  console.log(bei4);
});
