// フォーム変数
const form = document.getElementById("form");
// const kizon = form.kizon;
// const zouchiku = form.zouchiku;
const bild_kizon = form.bild_kizon;
const bild_zouchiku = form.bild_zouchiku;
// 結果表示変数
const result = document.getElementById("result");
const keisuResult = document.getElementById("keisu");
// const kizonMensekiResult = document.getElementById("kizonMenseki");
// const zouchikuMensekiResult = document.getElementById("zouchikuMenseki");
// const nobeMensekiResult = document.getElementById("nobeMenseki");
const ecoLevel = document.getElementById("ecoLevel");
const beiResult = document.getElementById("bei");

// サブミットクリックで実行
form.addEventListener("submit", function (event) {
  event.preventDefault(); //ブラウザのリロードかからなくなる（デフォルトのイベントが動かない）
  // const kizonMenseki = Number(kizon.value);
  // const zouchikuMenseki = Number(zouchiku.value);
  const kizonEra = Number(bild_kizon.value);
  const zouchikuEra = Number(bild_zouchiku.value);
  console.log(kizonEra);
  if (zouchikuEra === 0 && kizonEra === 0) {
    const keisu = 1.2;
    const ecoLevelResult = 1.1;
    // BEI計算式
    // const nobeMenseki = kizonMenseki + zouchikuMenseki;
    // const bei1 = nobeMenseki * ecoLevelResult;
    // const bei2 = kizonMenseki * keisu;
    // const bei3 = bei1 - bei2;
    // const bei4 = (bei3 / zouchikuMenseki) * 100;
    // const bei = Math.floor(bei4) / 100;
    // 結果表示
    result.classList.add("on");
    keisuResult.innerHTML = `${keisu}`;
    // kizonMensekiResult.innerHTML = `${kizonMenseki}㎡`;
    // zouchikuMensekiResult.innerHTML = `${zouchikuMenseki}㎡`;
    // nobeMensekiResult.innerHTML = `${nobeMenseki}㎡`;
    ecoLevel.innerHTML = `BEI ${ecoLevelResult}`;
    // beiResult.innerHTML = `BEI<strong> ${bei}</strong>以下`;
  } else if (zouchikuEra === 0 && kizonEra === 1) {
    const keisu = 1.1;
    const ecoLevelResult = (1.0).toFixed(1);
    result.classList.add("on");
    keisuResult.innerHTML = `${keisu}`;
    ecoLevel.innerHTML = `BEI ${ecoLevelResult}`;
  } else if (zouchikuEra === 0 && kizonEra === 2) {
    const keisu = 1.1;
    const ecoLevelResult = (1.0).toFixed(1);
    result.classList.add("on");
    keisuResult.innerHTML = `${keisu}`;
    ecoLevel.innerHTML = `BEI ${ecoLevelResult}`;
  } else if (zouchikuEra === 0 && kizonEra === 3) {
    result.classList.add("on");
    keisuResult.innerHTML = `規定値の使用はできません。<br>過去に計算した計算書を使用するまたは新たに計算し直す必要があります。`;
    ecoLevel.innerHTML = `工場等	BEI 0.75<br>
    事務所等	BEI 0.8<br>
    ホテル等	BEI 0.8<br>
    百貨店等	BEI 0.8<br>
    学校等	BEI 0.8<br>
    病院等	BEI 0.8<br>
    飲食店等	BEI 0.85<br>
    集会場等	BEI 0.85`;
  } else if (zouchikuEra === 1) {
    result.classList.add("on");
    keisuResult.innerHTML = `既存部分の計算は不要です。`;
    ecoLevel.innerHTML = `工場等	BEI 0.75<br>
    事務所等	BEI 0.8<br>
    ホテル等	BEI 0.8<br>
    百貨店等	BEI 0.8<br>
    学校等	BEI 0.8<br>
    病院等	BEI 0.8<br>
    飲食店等	BEI 0.85<br>
    集会場等	BEI 0.85`;
  }
});
