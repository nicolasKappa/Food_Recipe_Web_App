<?php
// Initialize the session to manage user state throughout the application.
session_start();

// Include database connection configuration.
require_once "../config/dbconfig.php";

// Check if the form has been submitted via POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize the input to protect against potential threats.
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, "psw", FILTER_SANITIZE_STRING);

    // Establish a database connection using the provided credentials.
    $conn = getConnection();

    // Check the database connection status.
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare an SQL statement to execute the stored procedure for user authentication.
    if ($stmt = $conn->prepare("CALL sp_login(?, ?)")) {
        // Bind parameters to the prepared statement.
        $stmt->bind_param("ss", $email, $password);
        // Execute the statement.
        $stmt->execute();
        // Obtain the result set from the statement.
        $result = $stmt->get_result();

        // Check if the result set contains any rows, indicating successful authentication.
        if ($result && ($row = $result->fetch_assoc())) {
            // User authenticated; store user ID and email in session variables.
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["email"] = $email;

            // Redirect the user to the main recipe page.
            header("Location: recipe.html");
            // Terminate script execution.
            exit();
        } else {
            // Authentication failed; notify the user.
            echo "Login failed. Email or password is incorrect.";
        }
        // Release the prepared statement.
        $stmt->close();
    } else {
        // Handle errors in statement preparation.
        echo "Failed to prepare the login statement.";
    }
    // Close the database connection.
    $conn->close();
} else {
    // Redirect to the login page if the form is not submitted via POST.
    header("Location: login.html");
    exit();
}
?>
