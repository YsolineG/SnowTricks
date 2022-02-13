document.addEventListener("DOMContentLoaded", function () {
    let loadMore = document.getElementById("load-more-comments");
    let page = 2;

    // Ecoute le clic
    loadMore.addEventListener("click", function (e) {
        // on ne souhaite pas rediriger ici
        e.preventDefault();
        fetch(this.getAttribute("href") + "?page=" + page).then(function (response) {
            return response.json().then(function (comments) {
                page++;
                for (const comment of comments) {
                    let divCommentElement = document.createElement("div");
                    let imgElement = document.createElement("img");
                    let divElement = document.createElement("div");
                    let h1Element = document.createElement("h1");
                    let pContentElement = document.createElement("p");
                    let pDateElement = document.createElement("p");

                    divCommentElement.className = "d-flex align-items-center mb-4";
                    imgElement.className = "rounded-circle object-fit-cover me-3"
                    imgElement.width = 80;
                    imgElement.height = 80;
                    imgElement.src = "/uploads/user/" + comment.photo;
                    divElement.className = "border flex-grow-1 p-3"
                    h1Element.className = "fs-4 fw-bold mb-0";
                    h1Element.innerText = comment.username;
                    pContentElement.className = "mb-0";
                    pContentElement.innerText = comment.content;
                    pDateElement.className = "text-muted mb-0";
                    pDateElement.innerText = "Publié le " + new Date(comment.createdAt).toLocaleDateString();

                    divCommentElement.appendChild(imgElement);
                    divCommentElement.appendChild(divElement);
                    divElement.appendChild(h1Element);
                    divElement.appendChild(pContentElement);
                    divElement.appendChild(pDateElement);

                    let commentsContainer = document.querySelector("#comments-container");
                    commentsContainer.appendChild(divCommentElement);
                }
            })
        })
    })
});