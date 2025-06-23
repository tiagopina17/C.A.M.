
<?php
function new_db_connection()
{
    // Variables for the database connection
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sam";
    
    try {
        // Create PDO connection instead of MySQLi
        $conn = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        throw new Exception("Connection failed: " . $e->getMessage());
    }
}
?>