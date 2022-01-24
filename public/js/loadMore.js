document.addEventListener("DOMContentLoaded", function () {
    let loadMore = document.querySelector('.load-more');
    let page = 2;

    // Ecoute le clic
    loadMore.addEventListener("click", function (e) {

        fetch('api/figures?page=' + page).then(function (response) {
            return response.json().then(function (figures) {
                page++;
                for (const figure of figures) {
                    let divElement = document.createElement('div');
                    let h3Element = document.createElement('h3');
                    let aElement = document.createElement('a');
                    aElement.innerText = figure.name;
                    aElement.href = 'get/figure/' + figure.id;
                    let pDescriptionElement = document.createElement('p');
                    pDescriptionElement.innerText = figure.description;
                    let pGroupElement = document.createElement('p');
                    pGroupElement.innerText = figure.figureGroup;
                    h3Element.appendChild(aElement);
                    divElement.appendChild(h3Element);
                    divElement.appendChild(pDescriptionElement);
                    divElement.appendChild(pGroupElement);
                    let figuresContainer = document.querySelector('#figures-container');
                    figuresContainer.appendChild(divElement);
                }
            })
        })
    })
});