<?php
// Import the database connection settings
require_once "../config/dbconfig.php";

// Check if the request method is GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {
  // Connect to the database
  $conn = getConnection();

  // Prepare the statement to retrieve categories
  if ($stmt = $conn->prepare("CALL sp_get_categories()")) {
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if categories were retrieved successfully
    if ($result && $result->num_rows > 0) {
      // Fetch the categories and create an array for the dropdown
      $categories = [];
      while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
      }
    } else {
      echo "<script>alert('No Data found.');</script>"; // Inform user about no data/categories found (DB is empty )
    }
    $stmt->close();
  } else {
    echo "<script>alert('Error preparing statement.');</script>"; // Inform user about an error
  }
  $conn->close();
}
?>
