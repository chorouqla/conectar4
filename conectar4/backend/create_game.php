<?php

require "db.php";

$data = json_decode(file_get_contents("php://input"), true);
$player_name = $data['player_name'];

$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
$code = '';
for ($i = 0; $i < 6; $i++) {
    $code .= $chars[random_int(0, strlen($chars) - 1)];
}

$empty_board = str_repeat('0', 42);

$stmt = $conn->prepare("INSERT INTO games (game_code, player1_name, turn, board, status) VALUES (?, ?, 1, ?, 'waiting')");
$stmt->bind_param("sss", $code, $player_name, $empty_board);
$stmt->execute();

echo json_encode(["status" => "ok", "game_id" => $stmt->insert_id, "game_code" => $code]);
?>