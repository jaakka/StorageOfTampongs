<?php
    header('Content-Type: application/json');
    
    function json_response($data, $status = 200)
    {
        if($status != 200)
        {
            http_response_code($status);
        }
        echo json_encode($data);
    }
?>