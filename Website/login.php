<?php
session_start();
require_once "../config/dbconfig.php";

// Redirect to results.php if the user is already logged in
if (isset($_SESSION["user_id"])) {
    header("Location: search_result.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, "psw", FILTER_SANITIZE_STRING);

    $conn = getConnection();

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($stmt = $conn->prepare("CALL sp_login(?, ?)")) {
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && ($row = $result->fetch_assoc())) {
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["email"] = $email;
            header("Location: search_results.php");
            exit;
        } else {
            $error = "Login failed. Email or password is incorrect.";
        }
        $stmt->close();
    } else {
        $error = "Failed to prepare the login statement.";
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
    <style>
        @import url("StylesheetRecipeRegisterLogin.css");
    </style>
</head>
<body>
<header>
    <div id="logo">
        <a href="index.php"><img src="images/logo/logo.png" width="50" height="50"></a>
    </div>
    <div class="simpleSearch">
        <form action="search_results.php" method="get">
            <input type="text" placeholder="What do you want to eat today?" name="search">
            <button type="submit">Go</button>
        </form>
    </div>
    <nav>
        <ul>
            <li><a href="#">All Recipes</a></li>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn">
                    <img src="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fi.pinimg.com%2Foriginals%2Fc7%2Fab%2Fcd%2Fc7abcd3ce378191a3dddfa4cdb2be46f.png&f=1&nofb=1" alt="authorization icon" width="30">
                </a>
                <div class="dropdown-content" id="myDropdown">
                    <a href="#">Your Profile</a>
                    <a href="login.php">Log in</a>
                    <a href="register.html">Register</a>
                </div>
            </li>
        </ul>
    </nav>
</header>
<div class="container">
    <div class="main">
        <div class="login-block">
            <h2>Log in to your account</h2>
            <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form class="login-register-form" method="post">
                <label class="label" for="email"><b>Email</b></label>
                <input class="input" type="text" placeholder="Enter email" name="email" required>
                <label for="psw"><b>Password</b></label>
                <input class="input" type="password" placeholder="Enter Password" name="psw" required>
                <button type="submit">Login</button>
                <!-- <span class="psw">Forgot <a href="#">password?</a></span> -->
                <br> 
                <span class="psw"><a href="register.html">Register here</a></span>
            </form>
        </div>
    <section class="landing-image">
      <img src="images/gx-wc-food-600x375.avif" alt="a neatly served table"
    </section>

  </div>
</div>

<footer class="landing-footer">

</footer>

<script src="main.js"></script>
</body>

</html>
