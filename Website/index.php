<?php
// Start or continue user session once logged in
session_start();

// Import the database connection settings
require_once "../config/dbconfig.php";

// Check if the user is logged in, using the session variable set during login
if(isset($_SESSION['user_id'])) {
    // Retrieve user ID from the session
    $user_id = $_SESSION['user_id'];
} 

$userLoggedIn = isset($_SESSION['user_id']); //check for if user is logged in, used to control header data


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Best recipes ever</title>
</head>

<body>
<style>
		@import url("landing_page.css");
	</style>

    <header>
      <div id="logo">
        <a href="index.php"><img src="images/logo/logo.png" width="50" height="50"></a>
      </div>
      <nav>
        <ul>
          <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="search_results.php">All Recipes</a></li>
            <li class="dropdown">
              <a href="javascript:void(0)" class="dropbtn">
                <img src="images/icons/auth-icon.png" alt="authorization icon" width="30">
              </a>
              <div class="dropdown-content" id="myDropdown">
                <a href="user_page.php">Your Profile</a>
                <a href="logout.php">Log Out</a>
              </div>
            </li>
          <?php else: ?>
            <li><a href="login.php">Log in</a></li>
            <li><a href="register.php">Register</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </header>

<div class="landing-main-block">
	<div class="landing-heading">
		<h1>WELCOME TO FLAVOURFINDS</h1>
		<p class="landing-text">Leveraging motion graphics in your digital food advertisement is a great way to catch the eye of your scrolling audience.
			What makes the ad so great is it not only used visual storytelling to its maximum but also touched on a subject people care about,
			demonstrating an understanding of its audience and beyond.
			Further, it showed the brand’s personality, and also gave Chipotle an excellent opportunity to display the brand\’s ethics – a selling point
			that’s grown in importance to customers over the years.
			Anyone can take the Chipotle approach in their food ads: use visual storytelling, engage an audience, and create a compelling narrative.
			Then ensure you put the ad on the right channels to get it noticed.
		</p>
	</div>
	<section>
		<img src="images/landing-dishes.png" alt="a neatly served table"  style="width:30em" srcset="images/landing-dishes.png 2x, images/landing-dishes.png 3x"
		class="image-main-landing">
	</section>
</div>


<footer class="landing-footer">

</footer>

<script src="main.js"></script>
</body>

</html>
