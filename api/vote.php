<?php
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) { echo json_encode(['error'=>'no input']); exit; }
$id = $input['movie_id'] ?? '';
$user = $input['user'] ?? '';
$vote = isset($input['vote']) ? intval($input['vote']) : 0;
if (!$id || !$user) { echo json_encode(['error'=>'missing']); exit; }
$db = new SQLite3(__DIR__ . '/votes.db');
$db->exec('CREATE TABLE IF NOT EXISTS votes (movie_id TEXT, user_id TEXT, vote INTEGER, PRIMARY KEY(movie_id,user_id))');
// upsert: if vote==0 delete row, else insert or replace
if ($vote === 0) {
  $stmt = $db->prepare('DELETE FROM votes WHERE movie_id=:id AND user_id=:user');
  $stmt->bindValue(':id', $id, SQLITE3_TEXT);
  $stmt->bindValue(':user', $user, SQLITE3_TEXT);
  $stmt->execute();
} else {
  $stmt = $db->prepare('INSERT OR REPLACE INTO votes (movie_id,user_id,vote) VALUES (:id,:user,:vote)');
  $stmt->bindValue(':id', $id, SQLITE3_TEXT);
  $stmt->bindValue(':user', $user, SQLITE3_TEXT);
  $stmt->bindValue(':vote', $vote, SQLITE3_INTEGER);
  $stmt->execute();
}
// return counts
$stmt2 = $db->prepare('SELECT SUM(CASE WHEN vote=1 THEN 1 ELSE 0 END) as likes, SUM(CASE WHEN vote=-1 THEN 1 ELSE 0 END) as dislikes FROM votes WHERE movie_id=:id');
$stmt2->bindValue(':id', $id, SQLITE3_TEXT);
$res = $stmt2->execute()->fetchArray(SQLITE3_ASSOC);
echo json_encode(['likes'=> (int)$res['likes'], 'dislikes'=> (int)$res['dislikes'] ]);
