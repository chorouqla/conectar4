<?php
require "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $conn->prepare("SELECT id, username, avatar, password FROM users WHERE username=?");
$stmt->bind_param("s", $data['username']);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if ($res && password_verify($data['password'], $res['password'])) {
    $_SESSION['user_id'] = $res['id'];
    echo json_encode([
        "status" => "ok",
        "id" => $res['id'],
        "username" => $res['username']
    ]);
} else {
    echo json_encode(["error" => "Invalid"]);
}