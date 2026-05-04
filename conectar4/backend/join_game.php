<?php
require "db.php";

$data = json_decode(file_get_contents("php://input"), true);
$game_code = $data['game_code'];
$player_name = $data['player_name'];

$stmt = $conn->prepare("SELECT id FROM games WHERE game_code = ? AND player2_name IS NULL AND status = 'waiting'");
$stmt->bind_param("s", $game_code);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();

if (!$game) {
    echo json_encode(["error" => "Game not found"]);
    exit;
}

$stmt = $conn->prepare("UPDATE games SET player2_name = ?, status = 'playing' WHERE id = ?");
$stmt->bind_param("si", $player_name, $game['id']);
$stmt->execute();

echo json_encode(["status" => "ok", "game_id" => $game['id']]);
?>