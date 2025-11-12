<?php
header('Content-Type: application/json');
$id = $_GET['id'] ?? '';
if (!$id) { echo json_encode(['likes'=>0,'dislikes'=>0]); exit; }
$db = new SQLite3(__DIR__ . '/votes.db');
$db->exec('CREATE TABLE IF NOT EXISTS votes (movie_id TEXT, user_id TEXT, vote INTEGER, PRIMARY KEY(movie_id,user_id))');
$stmt = $db->prepare('SELECT SUM(CASE WHEN vote=1 THEN 1 ELSE 0 END) as likes, SUM(CASE WHEN vote=-1 THEN 1 ELSE 0 END) as dislikes FROM votes WHERE movie_id=:id');
$stmt->bindValue(':id', $id, SQLITE3_TEXT);
$res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
echo json_encode(['likes'=>(int)$res['likes'],'dislikes'=>(int)$res['dislikes']]);
