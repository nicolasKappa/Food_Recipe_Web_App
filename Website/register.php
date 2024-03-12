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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, "psw", FILTER_SANITIZE_STRING);

    $conn = getConnection();

    if ($stmt = $conn->prepare("CALL sp_register_user(?, ?, ?)")) {
        $stmt->bind_param("sss", $name, $password, $email);
        $stmt->execute();
        $result = $stmt->get_result();


        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (isset($user['ErrorMessage'])) { // Stored procedure will check if the user exists and return an error message.
                echo "<script>alert('User already exists. Please try again or Log In'); window.location.href='register.php';</script>";
            } else {
                $_SESSION["user_id"] = mysqli_insert_id($conn);
                $_SESSION["email"] = $email;
                header("Location: search_results.php");
                exit;
            }
        } else {
            echo "<script>alert('Successfully registered. Please login.'); window.location.href='login.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Failed to prepare the registration statement.');</script>";
    }
    $conn->close();
}
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
      <li><a href="#">All Recipes</a></li>
      <li class="dropdown">
          <a href="javascript:void(0)" class="dropbtn">
              <img src="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fi.pinimg.com%2Foriginals%2Fc7%2Fab%2Fcd%2Fc7abcd3ce378191a3dddfa4cdb2be46f.png&f=1&nofb=1" alt="authorization icon" width="30">
          </a>
          <div class="dropdown-content" id="myDropdown" aria-label="User Menu">
              <a href="#">Your Profile</a>
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

<footer class="landing-footer">

</footer>

<script src="main.js"></script>
</body>

</html>
