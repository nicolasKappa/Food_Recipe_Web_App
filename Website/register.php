<?php

// Include the database configuration file to utilize the database connection settings
require_once "../config/dbconfig.php";

// Check if the request method is POST which indicates that form data has been sent
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and collect input data to prevent XSS and other vulnerabilities
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, "psw", FILTER_SANITIZE_STRING);

    // Establish a database connection
    $conn = getConnection();

    // Prepare a SQL statement to call the stored procedure for user registration
    if ($stmt = $conn->prepare("CALL sp_register_user(?, ?, ?)")) {
        // Bind user input parameters to the prepared SQL statement
        $stmt->bind_param("sss", $name, $password, $email);
        // Execute the prepared statement
        $stmt->execute();
        // Retrieve the result set from the stored procedure
        $result = $stmt->get_result();

        // Check if there's any result returned from the stored procedure
        if ($result && $result->num_rows > 0) {
            // Fetch the associative array from the result
            $user = $result->fetch_assoc();
            // Check if the stored procedure returned an error message indicating the user exists
            if (isset($user['ErrorMessage'])) {
                // Alert the user that the account already exists and keep on the registration page to try again
                echo "<script>alert('User already exists. Please try again or Log In'); window.location.href='register.php';</script>";
            }
        } else {
            // If no result is returned, registration was successful but without an immediate login
            echo "<script>alert('Successfully registered. Please login.'); window.location.href='login.php';</script>";
        }
        // Close the statement
        $stmt->close();
    } else {
        // If the SQL statement fails to prepare, alert the user
        echo "<script>alert('Failed to prepare the registration statement.');</script>";
    }
    // Close the database connection
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Best recipes ever</title>
	<link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">
</head>

<body>
<style>
		@import url("StylesheetRecipeRegisterLogin.css");
	</style>
<header>

  <div id="logo">
    <a href="index.php"><img src="images/logo/logo.png" width="50" height="50" alt="FF logo">
    <span></span>
  </a>
  </div>
	<nav>
    <ul>
      <li class="dropdown">
          <a href="javascript:void(0)" class="dropbtn">
               <img src="images/icons/auth-icon.png" alt="authorization icon" width="30">
          </a>
          <div class="dropdown-content" id="myDropdown" aria-label="User Menu">
              <a href="login.php">Log in</a>
              <a href="register.php">Register</a>
          </div>
      </li>
  </ul>
	</nav>
</header>
<div class="container">
<div class ="main">


  <section class="login-block">
  <h2>Create an account</h2>
  <form class="login-register-form" action="" method="post">



      <label for="name"><b>Name</b></label>
      <input class="input" type="text" placeholder="Entername" name="name" required>

      <label for="email"><b>Email</b></label>
      <input class="input" type="text" placeholder="Enter email" name="email" required>

      <label for="psw"><b>Password</b></label>
      <input class="input" type="password" placeholder="Enter Password" name="psw" required>

      <button type="submit">Register</button>

      <span class="login">Already have an account? <a href="login.php">Login here</a></span>


  </form>
</section>

<section class= "landing-image">
  <img src="images/landing-images/landing-dishes.png" alt="a neatly served table"  srcset="images/landing-images/landing-dishes.png 2x, images/landing-dishes.png 3x"
		class="image-main-landing">
</section>
</div>
</div>

<footer class="footer">

        <p>© 2024 Flavour Finds</p>
    </footer>

<script src="main.js"></script>
</body>

</html>
