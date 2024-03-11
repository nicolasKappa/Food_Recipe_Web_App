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
        <a href="index.php"><img src="images/logo/logo.png" width="50" height="50" alt="FF logo"></a>
      </div>
      <nav>
        <ul>
          <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="search_results.php">All Recipes</a></li>
            <li class="dropdown">
              <a href="javascript:void(0)" class="dropbtn">
                <img src="images/icons/auth-icon.png" alt="authorization icon" width="30">
              </a>
              <div class="dropdown-content" id="myDropdown" aria-label="User Menu">
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
    <main>
    <section class="landing-main-block" >
      <h1>WELCOME TO FLAVOURFINDS</h1>
      <p class="landing-text">Welcome to FlavourFinds, your ultimate destination for discovering delicious recipes! Whether you're a seasoned chef or a curious beginner, our app is designed to inspire your culinary journey. Dive into a world of flavours with thousands of recipes at your fingertips. Search by ingredients or meal type to find the perfect dish for any occasion. From quick weeknight dinners to sumptuous feasts, FlavourFinds is here to guide you through every step of your cooking adventure. Let's embark on a flavourful journey together!
      </p>

    </section>
  </main>

<footer class="landing-footer">

</footer>

<script src="main.js"></script>
</body>

</html>
