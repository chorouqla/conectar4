<?php
session_start();

$conn = new mysqli("localhost", "root", "", "cnct4");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "DB error"]);
    exit;
}

header("Content-Type: application/json");