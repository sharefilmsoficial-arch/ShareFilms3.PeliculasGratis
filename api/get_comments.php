<?php
header('Content-Type: application/json');
$movie = $_GET['id'] ?? '';
if (!$movie) { echo json_encode([]); exit; }
$db = new SQLite3(__DIR__ . '/votes.db');
$db->exec('CREATE TABLE IF NOT EXISTS comments (id INTEGER PRIMARY KEY AUTOINCREMENT, movie_id TEXT, name TEXT, text TEXT, created_at TEXT)');
$stmt = $db->prepare('SELECT name,text,created_at FROM comments WHERE movie_id=:movie ORDER BY id DESC LIMIT 200');
$stmt->bindValue(':movie',$movie,SQLITE3_TEXT);
$res = $stmt->execute();
$rows = [];
while ($r = $res->fetchArray(SQLITE3_ASSOC)) $rows[] = $r;
echo json_encode($rows);
