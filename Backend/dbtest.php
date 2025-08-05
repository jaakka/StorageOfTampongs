<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

   include("database.php");
   

$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

echo "connected";

?>