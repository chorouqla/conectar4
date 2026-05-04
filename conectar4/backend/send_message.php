<?php
require "db.php";

if (!isset($_SESSION['user_id'])) { http_response_code(401); exit; }

$data = json_decode(file_get_contents("php://input"), true);
$msg = trim($data['message'] ?? '');

if (!$msg || !isset($data['game_id'])) { http_response_code(400); exit; }

// Limitar longitud para evitar abusos
$msg = mb_substr($msg, 0, 500);

$stmt = $conn->prepare("INSERT INTO chat_messages (game_id, user_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $data['game_id'], $_SESSION['user_id'], $msg);
$stmt->execute();

echo json_encode(["status" => "ok"]);