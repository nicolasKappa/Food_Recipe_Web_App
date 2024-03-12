<?php
session_start();
require_once "../config/dbconfig.php"; 

if(isset($_POST['recipeId'])) {
    $recipeId = $_POST['recipeId'];

    $conn = getConnection();
    $response = ['success' => false, 'averageRating' => 0];

    if($stmt = $conn->prepare("CALL `flavour_finds`.`sp_get_average_rating`(?)")) {
        $stmt->bind_param("i", $recipeId);
        if($stmt->execute()) {
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $response['success'] = true;
                $response['averageRating'] = round($row["average_rating"] * 2) / 2;
            }
        }
        $stmt->close();
    }
    $conn->close();

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required parameter: recipeId']);
}
?>
