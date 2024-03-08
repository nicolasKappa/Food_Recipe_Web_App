<?php
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Database connection parameters
    $servername = "localhost"; 
    $db_username = "root"; 
    $db_password = ""; 
    $dbname = "recipe_app_database"; 

    // Create connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        // Prepare and execute SQL statement to retrieve user information
        $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ?");
        $stmt->bind_param("s", $_POST["username"]);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows == 1) {
            // User found, now check if password is correct
            $row = $result->fetch_assoc();
            if (password_verify($_POST["password"], $row["Password"])) {
                // Password is correct, set session variables or perform login action
                $_SESSION["username"] = $_POST["username"];
                echo "Login successful. Redirecting...";
                // Redirect to a logged-in page
                // header("Location: dashboard.php");
                // exit();
            } else {
                // Password is incorrect
                echo "Incorrect password.";
            }
        } else {
            // User not found
            echo "User not found.";
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    }
}
?>
