<?php
session_start();
require_once "../config/dbconfig.php"; // Path to Config File

if(isset($_POST['user_id'], $_POST['recipe_id'], $_POST['action'])) {
    $user_id = $_POST['user_id'];
    $recipe_id = $_POST['recipe_id'];
    $action = $_POST['action'];

    $conn = getConnection();

    if($action == 'add') {
        $stmt = $conn->prepare("CALL `flavour_finds`.`sp_add_to_favourites`(?, ?)");
    } else {
        $stmt = $conn->prepare("CALL `flavour_finds`.`sp_remove_from_favourites`(?, ?)");
    }

    $stmt->bind_param("ii", $user_id, $recipe_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    echo "Success";
}
?>
