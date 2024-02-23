window.onload = function () {
  let today = new Date();
  let start = new Date(2024, 1, 1, 0, 0, 0);
  let end = new Date(2064, 1, 24, 19, 0, 0);
  const linkBtn = document.querySelector(".link_btn");
  if (start.getTime() <= today.getTime() && end.getTime() >= today.getTime()) {
    linkBtn.classList.add("_off");
  }
};
