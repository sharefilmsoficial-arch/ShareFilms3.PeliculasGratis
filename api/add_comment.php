<?php
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) { echo json_encode(['success'=>false,'error'=>'no input']); exit; }
$movie = $input['movie_id'] ?? '';
$name = $input['name'] ?? '';
$text = $input['text'] ?? '';
if (!$movie || !$text) { echo json_encode(['success'=>false,'error'=>'missing']); exit; }
$db = new SQLite3(__DIR__ . '/votes.db');
$db->exec('CREATE TABLE IF NOT EXISTS comments (id INTEGER PRIMARY KEY AUTOINCREMENT, movie_id TEXT, name TEXT, text TEXT, created_at TEXT)');
$stmt = $db->prepare('INSERT INTO comments (movie_id,name,text,created_at) VALUES (:movie,:name,:text,datetime("now","localtime"))');
$stmt->bindValue(':movie',$movie,SQLITE3_TEXT);
$stmt->bindValue(':name',$name,SQLITE3_TEXT);
$stmt->bindValue(':text',$text,SQLITE3_TEXT);
$res = $stmt->execute();
if($res) echo json_encode(['success'=>true]); else echo json_encode(['success'=>false]);
