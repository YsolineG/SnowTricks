document.addEventListener("DOMContentLoaded", function () {
  // Gestion des boutons supprimer
  let links = document.querySelectorAll("[data-delete-photo]");

  // On va boucler sur links
  for (link of links) {
    // On va écouter le clic
    link.addEventListener("click", function (e) {
      // On empèche la navigation
      e.preventDefault();

      // On demande confirmation
      if (confirm("Voulez-vous supprimer cette photo ?")) {
        // On envoie une requête Ajax vers le href du lien avec la méthode DELETE
        fetch(this.getAttribute("href"), {
          method: "DELETE",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ _token: this.dataset.token }),
        })
          .then(
            // On récupère la réponse en JSON
            (response) => response.json()
          )
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
