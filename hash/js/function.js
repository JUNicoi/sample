// フォーム変数
const form = document.getElementById("form");
const rHash = document.getElementById("rHash");

// サブミットクリックで実行
form.addEventListener("submit", function (event) {
  event.preventDefault(); //ブラウザのリロードかからなくなる（デフォルトのイベントが動かない）

  const id = form.id.value;
  const text = form.pass.value;

  if (id !== "" && text !== "") {
    function async_digestMessage(message) {
      return new Promise(function (resolve) {
        let msgUint8 = new TextEncoder("utf-8").encode(message);
        crypto.subtle.digest("SHA-256", msgUint8).then(function (hashBuffer) {
          let hashArray = Array.from(new Uint8Array(hashBuffer));
          let hashHex = hashArray
            .map(function (b) {
              return b.toString(16).padStart(2, "0");
            })
            .join("");
          return resolve(hashHex);
        });
      });
    }

    result.classList.add("on");

    function getHashText(text) {
      // ハッシュ化後の文字列を表示
      // console.log(text);
      rHash.innerHTML = `${id}:${text}`;
    }

    if (window.Promise && window.crypto) {
      async_digestMessage(text)
        .then(function (shatxt) {
          getHashText(shatxt);
        })
        .catch(function (e) {
          console.log("エラー：", e.message);
        });
    } else {
      console.log("Promiseかcryptoに非対応");
    }
  } else if (id !== "" && text === "") {
    alert("PASSを入力してください");
  } else if (id === "" && text !== "") {
    alert("IDを入力してください");
  } else {
    alert("IDとPASSを入力してください");
  }
});
