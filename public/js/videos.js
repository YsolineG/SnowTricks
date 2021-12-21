document.addEventListener("DOMContentLoaded", function () {
  var buttonAddVideo = document.getElementById("button-add-video");
  var index = 0;
  var template = buttonAddVideo.getAttribute("data-template");
  buttonAddVideo.addEventListener("click", function () {
    template = template.replace(/__name__/g, index);
    var ul = document.querySelector("#list-video");
    var li = document.createElement("li");
    li.innerHTML = template;
    ul.appendChild(li);
    index++;
  });
});
