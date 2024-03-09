<?php
session_start();
require_once "../config/dbconfig.php"; // Path to Config File

if(isset($_POST['userId'], $_POST['recipeId'], $_POST['rating'])) {
    $userId = $_POST['userId'];
    $recipeId = $_POST['recipeId'];
    $rating = $_POST['rating'];

    $conn = getConnection();
    $response = ['success' => false];

    if($stmt = $conn->prepare("CALL sp_add_rating(?, ?, ?)")) {
        $stmt->bind_param("iii", $userId, $recipeId, $rating);
        if($stmt->execute()) {
            $response['success'] = true;
        }
        $stmt->close();
    }
    $conn->close();

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
}
?>
