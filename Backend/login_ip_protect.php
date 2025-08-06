<?php
    function log_login_attempt($ip, $user) 
    {
        include("database.php");
        $conn = new mysqli($servername, $username, $password, $dbname);
        $stmt = $conn->prepare("INSERT INTO login_attempts (ip_address, username, attempt_time) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $ip, $user);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    function too_many_attempts($ip, $limit = 5, $minutes = 15) 
    {
        include("database.php");
        $conn = new mysqli($servername, $username, $password, $dbname);
        $stmt = $conn->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND attempt_time > (NOW() - INTERVAL ? MINUTE)");
        $stmt->bind_param("si", $ip, $minutes);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        $conn->close();
        return $count >= $limit;
    }

    function clear_user_attempts($user)
    {
        include("database.php");
        $conn = new mysqli($servername, $username, $password, $dbname);
        $stmt = $conn->prepare("DELETE FROM login_attempts WHERE username = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
?>