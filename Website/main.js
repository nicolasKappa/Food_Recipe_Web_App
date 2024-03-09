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

function toggleFavourite(user_id, recipe_id, isFavourite) {
    var action = isFavourite ? 'remove' : 'add';
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "favourite_action.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (this.status === 200) {
            var favouriteIcon = document.getElementById("favouriteIcon");
            var favouriteText = document.getElementById("favouritesText");
            if (action === 'add') {
                favouriteIcon.src = "images/heart.png";
                favouriteText.innerText = "Remove from favourites";
                isFavourite = true; // Update the isFavourite status to true
            } else {
                favouriteIcon.src = "images/whiteheart.png";
                favouriteText.innerText = "Add to favourites";
                isFavourite = false; // Update the isFavourite status to false
            }
            // Update the onclick function to reflect the new isFavourite status
            favouriteIcon.onclick = function () { toggleFavourite(user_id, recipe_id, isFavourite); };
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


document.addEventListener('DOMContentLoaded', function () {
    // Fetch user and recipe ID attributes 
    const userId = document.body.getAttribute('data-user-id');
    const recipeId = document.body.getAttribute('data-recipe-id');

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
                s.src = 'images/star.png';
            } else {
                s.src = 'images/emptystar.png';
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


