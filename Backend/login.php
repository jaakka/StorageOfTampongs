<?php
    // ini_set('display_errors', 1);
    // error_reporting(E_ALL);

    include("json_response.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data["username"], $data["password"])) {
            handle_password($data["username"], $data["password"]);
        } 
        else 
        {
            json_response(['message' => 'Some keys missing from post request.'], 400);
        }
    } 
    else 
    {
        json_response(['message' => 'Only POST requests allowed.'], 405);
    }

    function handle_password($user, $pass)
    {
        include("login_ip_protect.php");
        $ip = $_SERVER['REMOTE_ADDR'];
        if (too_many_attempts($ip)) {
            json_response(['message' => 'Too many login attempts. Try again later.'], 429);
            return;
        }

        include("database.php");
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            json_output(['message' => "Connection failed: " . $conn->connect_error]);
            return;
        }

        // Sql-inject protected
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) 
        {
            // Server side salt password verify
            if (password_verify($pass, $row["password"])) 
            {
                user_success_login($row["id"], $user, "");
            } 
            else 
            {
                log_login_attempt($ip, $user);
                json_response(['message' => 'Invalid password'], 401);
            }
        } 
        else 
        {
            json_response(['message' => 'User not found'], 404);
        }
        $stmt->close();
        $conn->close();
    }

   function user_success_login($user_id, $user, $device_name)
    {
        session_start(); 
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $user;

        $device_token = bin2hex(random_bytes(32));

        include("database.php");
        $conn = new mysqli($servername, $username, $password, $dbname);
        $stmt = $conn->prepare("INSERT INTO devices (user_id, name, token) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $device_name, $device_token);
        $success = $stmt->execute();
        $stmt->close();
        $conn->close();

        setcookie('device_token', $device_token, time() + 60*60*24*30, '/', '', true, true);

        clear_user_attempts($user);

        if ($success) {
            json_response([
                'message' => 'Login and device registration successful',
                'device_name' => $device_name,
                'device_token' => $device_token
            ]);
        } else {
            json_response(['message' => 'Login ok, but device registration failed'], 500);
        }
    }
?>