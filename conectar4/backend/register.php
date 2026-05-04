<?php
require "db.php";

$data = json_decode(file_get_contents("php://input"), true);
$password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $data['username'], $password_hash);
$stmt->execute();

echo json_encode(["status" => "ok"]);
?>