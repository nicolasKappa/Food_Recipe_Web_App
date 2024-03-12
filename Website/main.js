function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function (event) {
    if (!event.target.matches('.dropbtn')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}

function toggleFavourite(user_id, recipe_id) {
    var favouriteIcon = document.getElementById("favouriteIcon");
    var isFavourite = favouriteIcon.getAttribute("data-is-favourite") === "true";

    var action = isFavourite ? 'remove' : 'add';
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "favourite_action.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (this.status === 200) {
            var favouriteText = document.getElementById("favouritesText");
            if (action === 'add') {
                favouriteIcon.src = "images/icons/heart.png";
                favouriteText.innerText = "Remove from favourites";
                favouriteIcon.setAttribute("data-is-favourite", "true");
            } else {
                favouriteIcon.src = "images/icons/whiteheart.png";
                favouriteText.innerText = "Add to favourites";
                favouriteIcon.setAttribute("data-is-favourite", "false");
            }
        } else {
            console.error("An error occurred during the AJAX request");
        }
    };
    xhr.send("action=" + action + "&user_id=" + user_id + "&recipe_id=" + recipe_id);
}

// Function to update user rating on star click
function updateUserRating(userId, recipeId, rating) {
    // AJAX request to update user rating
    fetch('rating_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `userId=${userId}&recipeId=${recipeId}&rating=${rating}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("Rating updated successfully");
            } else {
                console.error("Failed to update rating");
            }
        })
        .catch(error => console.error('Error updating rating:', error));
}

function updateAverageRatingDisplay(averageRating) {
    const averageRatingContainer = document.querySelector('.average-rating .star-rating');
    averageRatingContainer.innerHTML = ''; // Clear current stars

    // Add full, half, and empty stars based on the new average rating
    for (let i = 1; i <= 5; i++) {
        if (i <= Math.floor(averageRating)) {
            averageRatingContainer.innerHTML += '<img class="star" src="images/icons/star.png" alt="Full Star">';
        } else if (i - 0.5 === averageRating) {
            averageRatingContainer.innerHTML += '<img class="star" src="images/icons/halfstar.png" alt="Half Star">';
        } else {
            averageRatingContainer.innerHTML += '<img class="star" src="images/icons/emptystar.png" alt="Empty Star">';
        }
    }
}


document.addEventListener('DOMContentLoaded', function () {
    // Fetch user and recipe ID attributes 
    const userId = document.body.getAttribute('data-user-id');
    const recipeId = document.body.getAttribute('data-recipe-id');
    var favouriteIcon = document.getElementById("favouriteIcon");

    if (favouriteIcon) {
        var user_id = favouriteIcon.getAttribute("data-user-id");
        var recipe_id = favouriteIcon.getAttribute("data-recipe-id");
        favouriteIcon.onclick = function () { toggleFavourite(user_id, recipe_id); };
    }

    // Handler for user rating
    document.querySelectorAll('.rating-box .star').forEach(star => {
        star.addEventListener('click', function () {
            const rating = parseInt(this.getAttribute('data-star'));

            // Update stars visually to reflect the current rating
            updateUserRatingVisuals(rating);

            // Update the user rating in the database
            updateUserRating(userId, recipeId, rating);
        });
    });

    // Function to visually update user rating stars
    function updateUserRatingVisuals(rating) {
        document.querySelectorAll('.rating-box .star').forEach((s, idx) => {
            if (idx < rating) {
                s.src = 'images/icons/star.png';
            } else {
                s.src = 'images/icons/emptystar.png';
            }
        });
    }

    // Function to make AJAX call to rating_action.php
    function updateUserRating(userId, recipeId, rating) {
        fetch('rating_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `userId=${userId}&recipeId=${recipeId}&rating=${rating}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Rating updated successfully");
                    // Get the new average rating
                    fetch('get_average_rating.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `recipeId=${recipeId}`
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update the average rating display after user added/updated a rating
                                updateAverageRatingDisplay(data.averageRating);
                            }
                        });
                } else {
                    console.error("Failed to update rating: ", data.error);
                }
            })
            .catch(error => console.error('Error updating rating:', error));
    }


    // Initialize favourite functionality if favouriteIcon is present on screen
    var favouriteIcon = document.getElementById("favouriteIcon");
    if (favouriteIcon) {
        var user_id = favouriteIcon.getAttribute("data-user-id");
        var recipe_id = favouriteIcon.getAttribute("data-recipe-id");
        var isFavourite = favouriteIcon.src.includes("heart.png");
        favouriteIcon.onclick = function () { toggleFavourite(user_id, recipe_id, isFavourite); };
    }
});


