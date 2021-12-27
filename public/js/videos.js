document.addEventListener("DOMContentLoaded", function () {
  const buttonAddVideo = document.getElementById("button-add-video");
  let index = 0;
  const template = buttonAddVideo.getAttribute("data-template");
  buttonAddVideo.addEventListener("click", function () {
    const input = template.replace(/__name__/g, index.toString(10));
    const ul = document.querySelector("#list-video");
    const li = document.createElement("li");
    li.innerHTML = input;
    ul.appendChild(li);
    index++;
  });
});
