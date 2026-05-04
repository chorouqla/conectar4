<?php
require "db.php";

$game_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM games WHERE id = ?");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();

echo json_encode($game);
?>