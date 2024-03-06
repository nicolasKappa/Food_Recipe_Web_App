<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Security/Password Hashing
    $PasswordHash = password_hash($_POST["Password"], PASSWORD_BCRYPT);



        
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
        }else{
            
            $stmt = $conn->prepare("INSERT INTO Users(Username,Password,Email) VALUES(?,?,?)");
            $stmt->bind_param("sss",$_POST["Username"], $PasswordHash, $_POST["Email"]);

            
            
        // Behaviour during execution
            if ($stmt->execute()) {
                echo "Successfully registered";
            } else {
                echo "Error: " . $conn->error;
            }
            
            
            $stmt->close();
            $conn->close();

        }
}
 
        
    
?>
