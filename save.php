<?php
// stores drawing + canvas size

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit(json_encode(['success'=>false]));

$input = json_decode(file_get_contents('php://input'), true);

$pdo = new PDO('mysql:host=localhost;dbname=doodlehub', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$title = htmlspecialchars($input['title'] ?? 'Untitled');
$data = json_encode([
    'strokes' => $input['strokes'] ?? [],
    'width'   => $input['width'] ?? 800,
    'height' => $input['height'] ?? 600
]);

$stmt = $pdo->prepare("INSERT INTO drawings (title, data) VALUES (?, ?)");
$stmt->execute([$title, $data]);

echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);