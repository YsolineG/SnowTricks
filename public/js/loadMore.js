document.addEventListener("DOMContentLoaded", function () {
    let loadMore = document.getElementById("load-more");
    let page = 2;

    // Ecoute le clic
    loadMore.addEventListener("click", function (e) {
        fetch('api/figures?page=' + page).then(function (response) {
            return response.json().then(function (figures) {
                page++;
                for (const figure of figures) {
                    let divFigureElement = document.createElement('div');
                    let divFigure = document.createElement('div');
                    let imgCardElement = document.createElement('img');
                    let divCardBodyElement = document.createElement('div');
                    let h5CardTitleElement = document.createElement('h5');
                    let aFigurePathElement = document.createElement('a');
                    let aDeleteElement = document.createElement('a');
                    let iDeleteElement = document.createElement('i');
                    let aEditElement = document.createElement('a');
                    let iEditElement = document.createElement('i');

                    divFigureElement.className = 'col';
                    divFigure.className = 'card';
                    divFigure.style.cssText = 'width: 18rem;'
                    imgCardElement.className = 'card-img-top'
                    imgCardElement.src = '/uploads/snow_tricks_01.jpg';
                    divCardBodyElement.className = 'card-body';
                    h5CardTitleElement.className = 'card-title';
                    aFigurePathElement.innerText = figure.name;
                    aFigurePathElement.href = 'get/figure/' + figure.id;
                    iDeleteElement.className = 'bi bi-trash-fill';
                    aDeleteElement.className = 'delete';
                    aDeleteElement.href = 'delete/' + figure.id;
                    iEditElement.className = 'bi bi-pencil-square';
                    aEditElement.className = 'edit';
                    aEditElement.href = 'update/figure/' + figure.id;

                    divFigureElement.appendChild(divFigure);
                    divFigure.appendChild(imgCardElement);
                    h5CardTitleElement.appendChild(aFigurePathElement);
                    divCardBodyElement.appendChild(h5CardTitleElement);
                    aDeleteElement.appendChild(iDeleteElement);
                    divCardBodyElement.appendChild(aDeleteElement);
                    aEditElement.appendChild(iEditElement);
                    divCardBodyElement.appendChild(aEditElement);
                    divFigure.appendChild(divCardBodyElement);

                    let figuresContainer = document.querySelector('#figures-container');
                    figuresContainer.appendChild(divFigureElement);
                    console.log(figuresContainer)
                }
            })
        })
    })
});