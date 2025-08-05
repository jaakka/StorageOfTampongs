<?php
   include("database.php");

   // Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, username, password FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "id: " . $row["id"]. " - Name: " . $row["username"]. " " . $row["password"]. "<br>";
  
    if (password_verify("test", $row["password"])) {
    echo " Oikea salasana";
}
    }
} else {
  echo "0 results";
}
$conn->close();


$hashed = password_hash("test", PASSWORD_DEFAULT);
echo $hashed;
?>



// Kirjautumisessa
if (password_verify($inputPassword, $user['password'])) {
    // Oikea salasana
}