<?php
    header('Content-Type: application/json');

    $data = [
        'message' => 'this is backend',
        'status' => 'ok'
    ];

    echo json_encode($data);
?>