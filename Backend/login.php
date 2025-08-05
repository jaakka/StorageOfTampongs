<?php
    // ini_set('display_errors', 1);
    // error_reporting(E_ALL);

    include("database.php");

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data["username"], $data["password"])) {
            verify_password($data["username"], $data["password"]);
        } else {
            json_response(['message' => 'Some keys missing from post request.'], 400);
        }
    } else {
        http_response_code(['message' => 'Only POST requests allowed.'], 405);
    }

    function verify_password($user, $pass)
    {
        // $conn = new mysqli($servername, $username, $password, $dbname);

        // if ($conn->connect_error) {
        //     die("Connection failed: " . $conn->connect_error);
        //     json_output(['message' => 'Only POST requests allowed.']);
        // }

        // $sql = "SELECT id, username, password FROM users";
        // $result = $conn->query($sql);
        
        // (password_verify("test", $row["password"]))

    }
    
    function json_response($data, $status = 200)
    {
        if($status != 200)
        {
            http_response_code($status);
        }
        echo json_encode($data);
    }
?>