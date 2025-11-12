<?php
header('Content-Type: application/json');
$id = $_GET['id'] ?? '';
$user = $_GET['user'] ?? '';
if (!$id || !$user) { echo json_encode(['vote'=>0]); exit; }
$db = new SQLite3(__DIR__ . '/votes.db');
$db->exec('CREATE TABLE IF NOT EXISTS votes (movie_id TEXT, user_id TEXT, vote INTEGER, PRIMARY KEY(movie_id,user_id))');
$stmt = $db->prepare('SELECT vote FROM votes WHERE movie_id=:id AND user_id=:user LIMIT 1');
$stmt->bindValue(':id', $id, SQLITE3_TEXT);
$stmt->bindValue(':user', $user, SQLITE3_TEXT);
$res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
$vote = $res ? (int)$res['vote'] : 0;
echo json_encode(['vote'=>$vote]);
