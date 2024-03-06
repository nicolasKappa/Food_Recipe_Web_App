<?php
        // Update the database for newly registered user.
        $UserID = $_POST["UserID"];
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
            $stmt = $conn->prepare("Insert into registration(username,password,email)
                values(?,?,?)");
            $stmt->bind_param("i,s,s,s",$UserID,$Username,$Password,$Email);
            echo " Successfully registered";
            $stmt->close();
            $conn->close();

        }
 
        
    
?>
