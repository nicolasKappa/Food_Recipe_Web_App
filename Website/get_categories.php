<?php
// Establish connection to MySQL database
$connection = mysqli_connect("127.0.0.1", "root", "testcase", "recipe_app_database");
if(!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all categories
$query = "SELECT * FROM lkp_categories";
$result = mysqli_query($connection, $query);

// Check if query was successful
if($result) {
    // Output categories data
    while($row = mysqli_fetch_assoc($result)) {
        echo "Category ID: " . $row['category_id'] . "<br>";
        echo "Name: " . $row['name'] . "<br><br>";
    }
} else {
    echo "Error fetching categories: " . mysqli_error($connection);
}

// Close database connection
mysqli_close($connection);
?>
