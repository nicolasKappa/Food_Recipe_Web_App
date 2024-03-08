<?php
// Establish connection to MySQL database
$connection = mysqli_connect("127.0.0.1", "root", "testcase", "recipe_app_database");
if(!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve user input
$user_id = $_POST['user_id']; 
$item_id = $_POST['recipe_ID']; 
// Insert data into favorites table
$query = "INSERT INTO favorites (user_id, recipe_ID) VALUES ('$user_id', '$recipe_ID')";
$result = mysqli_query($connection, $query);

// Check if query was successful
if($result) {
    echo "Item added to favorites successfully.";
} else {
    echo "Error adding item to favorites: " . mysqli_error($connection);
}

// Close database connection
mysqli_close($connection);
?>
