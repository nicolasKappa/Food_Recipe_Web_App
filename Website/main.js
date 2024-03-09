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
                isFavourite = true; // Update the isFavourite status
            } else {
                favouriteIcon.src = "images/whiteheart.png";
                favouriteText.innerText = "Add to favourites";
                isFavourite = false; // Update the isFavourite status
            }
            // Update the onclick function to reflect the new isFavourite status
            favouriteIcon.onclick = function () { toggleFavourite(user_id, recipe_id, isFavourite); };
        } else {
            console.error("An error occurred during the AJAX request");
        }
    };
    xhr.send("action=" + action + "&user_id=" + user_id + "&recipe_id=" + recipe_id);
}



document.addEventListener('DOMContentLoaded', function() {
  // Functionality for my-star (user interaction for setting a rating)
  (function() {
      let sr = document.querySelectorAll('.my-star');
      sr.forEach(star => {
          star.addEventListener('click', function() {
              let cs = parseInt(this.getAttribute("data-star"));
              document.querySelector('#output').value = cs;

              sr.forEach((s, index) => {
                  if (index < cs) {
                      s.classList.add('is-active');
                  } else {
                      s.classList.remove('is-active');
                  }
              });
          });
      });
  })();

    let ratingFromDatabase = 3; 
    let sr2 = document.querySelectorAll('.my-star-2');
    sr2.forEach((star, index) => {
        if (index < ratingFromDatabase) {
            star.classList.add('is-active');
        }
    });

    // Initialize favourite functionality if favouriteIcon is present
    var favouriteIcon = document.getElementById("favouriteIcon");
    if (favouriteIcon) {
        var user_id = favouriteIcon.getAttribute("data-user-id");
        var recipe_id = favouriteIcon.getAttribute("data-recipe-id");
        var isFavourite = favouriteIcon.src.includes("heart.png");
        favouriteIcon.onclick = function () { toggleFavourite(user_id, recipe_id, isFavourite); };
    }
});


