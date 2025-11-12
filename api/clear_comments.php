<?php
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
$movie = $input['movie_id'] ?? '';
if (!$movie) { echo json_encode(['success'=>false]); exit; }
$db = new SQLite3(__DIR__ . '/votes.db');
$stmt = $db->prepare('DELETE FROM comments WHERE movie_id=:movie');
$stmt->bindValue(':movie',$movie,SQLITE3_TEXT);
$res = $stmt->execute();
echo json_encode(['success'=>true]);
