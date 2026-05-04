<?php
require "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$game_id = (int)$data['game_id'];
$column = (int)$data['column'];
$player_name = $data['player_name'];

// get current game
$stmt = $conn->prepare("SELECT * FROM games WHERE id = ?");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();

if (!$game) {
    echo json_encode(["error" => "Game not found"]);
    exit;
}

// determine player number
if ($player_name == $game['player1_name']) {
    $player_num = 1;  // Red
} elseif ($player_name == $game['player2_name']) {
    $player_num = 2;  // Yellow
} else {
    echo json_encode(["error" => "Not in this game"]);
    exit;
}

// check turn
if ($game['turn'] != $player_num) {
    echo json_encode(["error" => "Not your turn"]);
    exit;
}

// convert board string to array
$board = str_split($game['board']);

// find the lowest spot on the cplumn
$row_placed = -1;
for ($row = 5; $row >= 0; $row--) {
    $index = $row * 7 + $column;
    if ($board[$index] == '0') {
        $board[$index] = ($player_num == 1) ? '1' : '2';
        $row_placed = $row;
        break;
    }
}

if ($row_placed == -1) {
    echo json_encode(["error" => "Column is full"]);
    exit;
}

$new_board = implode("", $board);
$player_value = ($player_num == 1) ? '1' : '2';

// win detection
$winner = null;

// check horizontaly
$count = 0;
for ($c = 0; $c < 7; $c++) {
    $idx = $row_placed * 7 + $c;
    if ($board[$idx] == $player_value) {
        $count++;
        if ($count >= 4) $winner = $player_num;
    } else {
        $count = 0;
    }
}

// chack verticaly
$count = 0;
for ($r = 0; $r < 6; $r++) {
    $idx = $r * 7 + $column;
    if ($board[$idx] == $player_value) {
        $count++;
        if ($count >= 4) $winner = $player_num;
    } else {
        $count = 0;
    }
}

// update database
if ($winner) {
    $stmt = $conn->prepare("UPDATE games SET board = ?, turn = NULL, winner = ?, status = 'finished' WHERE id = ?");
    $stmt->bind_param("sii", $new_board, $winner, $game_id);
} else {
    $next_turn = ($player_num == 1) ? 2 : 1;
    $stmt = $conn->prepare("UPDATE games SET board = ?, turn = ?, status = 'playing' WHERE id = ?");
    $stmt->bind_param("sii", $new_board, $next_turn, $game_id);
}
$stmt->execute();

echo json_encode(["status" => "ok", "winner" => $winner]);
?>