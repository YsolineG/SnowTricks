document.addEventListener("DOMContentLoaded", function () {
  let links = document.querySelectorAll("[data-delete]");

  let link;
  for (link of links) {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      if (confirm("Voulez-vous supprimer cette vidéo ?")) {
        fetch(this.getAttribute("href"), {
          method: "DELETE",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ _token: this.dataset.token }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              this.parentElement.parentElement.remove();
            } else {
              alert(data.error);
            }
          })
          .catch((e) => alert(e));
      }
    });
  }
});
