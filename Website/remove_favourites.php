<?php
// Establish connection to MySQL database
$connection = mysqli_connect("127.0.0.1", "root", "tescase", "recipe_app_database");
if(!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve user input
$user_id = $_POST['user_id']; 
$item_id = $_POST['recipe_ID']; 

// Delete entry from favorites table
$query = "DELETE FROM favorites WHERE user_id = '$user_id' AND recipe_ID = '$recipe_ID'";
$result = mysqli_query($connection, $query);

// Check if query was successful
if($result) {
    echo "Item removed from favorites successfully.";
} else {
    echo "Error removing item from favorites: " . mysqli_error($connection);
}

// Close database connection
mysqli_close($connection);
?>
