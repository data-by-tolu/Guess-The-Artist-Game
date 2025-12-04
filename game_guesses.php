<?php
// Start session to store guesses and state
session_start();

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "music_game";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Randomly select a song from the database
$sql = "SELECT artist_name, song_title FROM tracks ORDER BY RAND() LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $song = $result->fetch_assoc();
    $artist = $song['artist_name'];
    $song_title = $song['song_title'];
} else {
    die("No songs found in the database.");
}

// Store the correct answer in the session if it's not set
if (!isset($_SESSION['artist'])) {
    $_SESSION['artist'] = $artist;
    $_SESSION['guesses'] = 0;  // Initialize guess count
}

// Check user input
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_guess = trim($_POST['guess']);
    $_SESSION['guesses']++;

    if (strtolower($user_guess) === strtolower($_SESSION['artist'])) {
        $message = "Correct! The artist is " . $_SESSION['artist'] . "!";
        session_destroy();  // Reset the game
    } elseif ($_SESSION['guesses'] >= 5) {
        $message = "Sorry, you're out of guesses! The correct artist was " . $_SESSION['artist'] . ".";
        session_destroy();  // Reset the game
    } else {
        $message = "Incorrect! You have " . (5 - $_SESSION['guesses']) . " guesses left.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guess the Artist Game</title>
</head>
<body>
    <h1>Guess the Artist</h1>
    <p><strong>Song Title:</strong> <?php echo $song_title; ?></p>

    <form method="POST">
        <label for="guess">Your Guess:</label>
        <input type="text" id="guess" name="guess" required>
        <button type="submit">Submit Guess</button>
    </form>

    <p><?php echo $message; ?></p>
</body>
</html>
