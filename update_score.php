<?php
#Toluwani Olukayode - Guess The Artist-Update Score

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_name'])) 
{
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$correct = json_decode(file_get_contents('php://input'), true)['correct'];
if ($correct) 
{
    $_SESSION['score'] += 10; // Add points for correct answer
}

$_SESSION['round']++;

echo json_encode([
    'roundComplete' => true,
    'finalRound' => $_SESSION['round'] > 5
]);
?>