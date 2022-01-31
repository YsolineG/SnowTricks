document.addEventListener("DOMContentLoaded", function () {
    let loadMore = document.getElementById("load-more-comments");
    let page = 2;

    // Ecoute le clic
    loadMore.addEventListener("click", function (e) {
        // on ne souhaite pas rediriger ici
        e.preventDefault();
        fetch(this.getAttribute("href") + '?page=' + page).then(function (response) {
            return response.json().then(function (comments) {
                console.log(comments)
                page++;
                for (const comment of comments) {
                    let divCommentElement = document.createElement('div');
                    let divRowElement = document.createElement('div');
                    let divCol4Element = document.createElement('div');
                    let imgCardElement = document.createElement('img');
                    let divCol8Element = document.createElement('div');
                    let divCardBodyElement = document.createElement('div');
                    let h5CardTitleElement = document.createElement('h5');
                    let pCardContentElement = document.createElement('p');
                    let pCardDateElement = document.createElement('p');

                    divCommentElement.className = 'card mb-3';
                    divCommentElement.style.cssText = 'max-width: 540px;'
                    divRowElement.className = 'row g-0';
                    divCol4Element.className = 'col-md-4';
                    imgCardElement.className = 'img-fluid rounded-circle'
                    imgCardElement.width = 50;
                    imgCardElement.src = '/uploads/user/' + comment.photo;
                    divCol8Element.className = 'col-md-8';
                    divCardBodyElement.className = 'card-body';
                    h5CardTitleElement.className = 'card-title';
                    h5CardTitleElement.innerText = comment.username;
                    pCardContentElement.className = 'card-text';
                    pCardContentElement.innerText = comment.content;
                    pCardDateElement.className = 'card-text';
                    pCardDateElement.innerText = 'Publié le ' + comment.createdAt;

                    divCommentElement.appendChild(divRowElement);
                    divRowElement.appendChild(divCol4Element);
                    divCol4Element.appendChild(imgCardElement);
                    divRowElement.appendChild(divCol8Element);
                    divCol8Element.appendChild(divCardBodyElement);
                    divCardBodyElement.appendChild(h5CardTitleElement);
                    divCardBodyElement.appendChild(pCardContentElement);
                    divCardBodyElement.appendChild(pCardDateElement);

                    let commentsContainer = document.querySelector('#comments-container');
                    commentsContainer.appendChild(divCommentElement);
                    console.log(commentsContainer)
                }
            })
        })
    })
});