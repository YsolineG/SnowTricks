document.addEventListener("DOMContentLoaded", function () {
    let loadMore = document.getElementById("load-more");
    let page = 2;
    const currentUserId = document.querySelector("body").dataset.currentUserId;


    // Ecoute le clic
    loadMore.addEventListener("click", function (e) {
        e.preventDefault();
        fetch(this.getAttribute("href") + "?page=" + page).then(function (response) {
            return response.json().then(function (figures) {
                page++;
                for (const figure of figures) {
                    let divFigureElement = document.createElement("div");
                    let divFigure = document.createElement("div");
                    let imgCardElement = document.createElement("img");
                    let divCardBodyElement = document.createElement("div");
                    let h5CardTitleElement = document.createElement("h5");
                    let aFigurePathElement = document.createElement("a");
                    let divButtonIconElement = document.createElement("div");
                    let aDeleteElement = document.createElement("a");
                    let iDeleteElement = document.createElement("i");
                    let aEditElement = document.createElement("a");
                    let iEditElement = document.createElement("i");

                    divFigureElement.className = "col";
                    divFigure.className = "card h-100";
                    imgCardElement.className = "card-img-top";
                    imgCardElement.src = "/uploads/snow_tricks_01.jpg";
                    divCardBodyElement.className = "card-body d-flex justify-content-between";
                    h5CardTitleElement.className = "card-title";
                    aFigurePathElement.innerText = figure.name;
                    aFigurePathElement.href = `get/figure/${figure.id}-${figure.slug}`;
                    divButtonIconElement.className = "button-icon";
                    iDeleteElement.className = "bi bi-trash-fill";
                    aDeleteElement.className = "delete";
                    aDeleteElement.href = "delete/" + figure.id;
                    iEditElement.className = "bi bi-pencil-square";
                    aEditElement.className = "edit";
                    aEditElement.href = "update/figure/" + figure.id;

                    divFigureElement.appendChild(divFigure);
                    divFigure.appendChild(imgCardElement);
                    h5CardTitleElement.appendChild(aFigurePathElement);
                    divCardBodyElement.appendChild(h5CardTitleElement);

                    if(currentUserId === figure.userId.toString()) {
                        divCardBodyElement.appendChild(divButtonIconElement);
                        divButtonIconElement.appendChild(aDeleteElement);
                        aDeleteElement.appendChild(iDeleteElement);
                        divButtonIconElement.appendChild(aEditElement);
                        aEditElement.appendChild(iEditElement);
                    }
                    divFigure.appendChild(divCardBodyElement);

                    let figuresContainer = document.querySelector("#figures-container");
                    figuresContainer.appendChild(divFigureElement);
                }
            });
        });
    });
});