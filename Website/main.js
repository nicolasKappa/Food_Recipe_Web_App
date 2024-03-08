function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
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

  // Functionality for my-star-2
  (function() {
      let ratingFromDatabase = 3; // Example static rating - would get this from the database
      let sr = document.querySelectorAll('.my-star-2');
      sr.forEach((star, index) => {
          if (index < ratingFromDatabase) {
              star.classList.add('is-active');
          } // No else part as we're just displaying the rating, not interacting
      });
  })();
});
