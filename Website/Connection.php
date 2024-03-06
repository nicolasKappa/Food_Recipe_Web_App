<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Update the database for newly registered user.
        $Username = $_POST["Username"];
        $Password = $_POST["Password"];
        $Email = $_POST["Email"];

        
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
            
            $stmt = $conn->prepare("Insert into Users(Username,Password,Email)
                values(?,?,?)");
            $stmt->bind_param("sss",$Username,$Password,$Email);
            
            
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
