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

document.addEventListener('DOMContentLoaded', function(){
  (function(){
      let sr = document.querySelectorAll('.my-star');
      let i = 0;
      //loop through stars
      while (i < sr.length){
          //attach click event
          sr[i].addEventListener('click', function(){
              //current star
              let cs = parseInt(this.getAttribute("data-star"));
              //output current clicked star value
              document.querySelector('#output').value = cs;
              /*our first loop to set the class on preceding star elements*/
              let pre = cs; //set the current star value
              //loop through and set the active class on preceding stars
              while(1 <= pre){
                  //check if the classlist contains the active class, if not, add the class
                  if(!document.querySelector('.star-'+pre).classList.contains('is-active')){
                      document.querySelector('.star-'+pre).classList.add('is-active');
                  }
                  //decrement our current index
                  --pre;
              }//end of first loop
          })//end of click event
          i++;
      }//end of while loop
  })();//end of function
})
