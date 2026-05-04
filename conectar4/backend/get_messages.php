<?php
require "db.php";

$game_id = (int)$_GET['game_id'];
$since   = isset($_GET['since']) ? (int)$_GET['since'] : 0;

$stmt = $conn->prepare(
    "SELECT m.id, m.user_id, u.username, u.avatar, m.message
     FROM chat_messages m
     JOIN users u ON m.user_id = u.id
     WHERE m.game_id = ? AND m.id > ?
     ORDER BY m.id ASC"
);
$stmt->bind_param("ii", $game_id, $since);
$stmt->execute();
$res = $stmt->get_result();

$msgs = [];
while ($row = $res->fetch_assoc()) $msgs[] = $row;

echo json_encode($msgs);