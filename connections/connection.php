<?php
function new_db_connection()
{

    // Variables for the database connection
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sam";
    // Makes the connection
    $link = mysqli_connect($hostname, $username, $password, $dbname);
    return $link;
}
?>