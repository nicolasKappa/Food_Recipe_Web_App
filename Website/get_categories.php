<?php
// Start or continue user session (assuming user needs to be logged in to see categories)
session_start();

// Import the database connection settings
require_once "../config/dbconfig.php";

// Check if the user is logged in, using the session variable set during login
if(isset($_SESSION['category_id'])) {
    // Retrieve cateogry ID from the session
    $category_id = $_SESSION['category_id'];
}

// Check if the request method is GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Sanitize input data
    $name = filter_input(INPUT_GET, "name", FILTER_SANITIZE_STRING);
    $category_id = filter_input(INPUT_GET, "category_id", FILTER_SANITIZE_NUMBER_INT);

    // Connect to the database
    $conn = getConnection();

    // Prepare the statement to retrieve categories
    if ($stmt = $conn->prepare("CALL sp_get_categories(?, ?)")) {
        $stmt->bind_param("is", $category_id, $name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        

        // Check if categories were retrieved successfully
        if ($result && $result->num_rows > 0) {
            // Fetch the categories
            $categories = $result->fetch_all(MYSQLI_ASSOC);
            if (!isset($categories['ErrorMessage'])) { // Stored procedure will check if the category doesnt exist and return an error message.
                echo "<script>alert('Category does not exists. Please try again'); window.location.href='register.php';</script>";
            } else {
                $_SESSION["category_id"] = mysqli_insert_id($conn);
                $_SESSION["name"] = $name;
                header("Location: search_results.php");
                exit;
            }
        } else {
            echo "<script>alert('Please Insert Valid Category.'); window.location.href='login.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Category Found.');</script>";
    }
    $conn->close()
}
?>