<?php
// dbconfig.php
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'flavourfinds');
define('DB_PASSWORD', 'flavourfindsPassword(123)');
define('DB_DATABASE', 'flavour_finds');

function getConnection() {
    $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    if (mysqli_connect_errno()) {
        die("Failed to connect to MySQL: " . mysqli_connect_error());
    }
    return $connection;
}
?>
