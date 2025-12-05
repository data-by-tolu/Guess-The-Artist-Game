<?php
// Connect to the database
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "music_game";

$conn = new mysqli($servername, $username, $password, $dbname, 3307);

// Handle game setup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_name'])) {
    session_start();
    $_SESSION['user_name'] = $_POST['user_name'];
    $_SESSION['score'] = 0;
    $_SESSION['round'] = 1;
    header("Location: game.php");
    exit();
}

session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: start_game.php");
    exit();
}

// Fetch a random song for the current round
$sql = "SELECT artist_name, song_title, lyrics FROM lyrics ORDER BY RAND() LIMIT 1";
$result = $conn->query($sql);
$song = $result->fetch_assoc();
$popularLyrics = substr($song['lyrics'], 0, 150) . '...';

echo "<script>const correctArtist = '" . addslashes($song['artist_name']) . "';</script>";
?>

<script>
    let guessesRemaining = 5;
    function makeGuess() {
        const userGuess = document.getElementById("guess").value.trim().toLowerCase();
        const feedback = document.getElementById("feedback");
        const playAgainButton = document.getElementById("play-again");

        if (userGuess === correctArtist.toLowerCase()) {
            feedback.textContent = "Correct! You guessed the artist!";
            feedback.style.color = "green";
            document.getElementById("guess").disabled = true;
            updateScore(true);
        } else {
            guessesRemaining--;
            feedback.textContent = `Incorrect! You have ${guessesRemaining} guesses left.`;
            feedback.style.color = "red";

            if (guessesRemaining === 0) {
                feedback.textContent = `Game Over! The correct artist was ${correctArtist}.`;
                document.getElementById("guess").disabled = true;
                updateScore(false);
            }
        }
    }

    function updateScore(correct) {
        fetch('update_score.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ correct })
        })
        .then(response => response.json())
        .then(data => {
            if (data.roundComplete) {
                if (data.finalRound) {
                    window.location.href = 'end_game.php';
                } else {
                    window.location.reload();
                }
            }
        });
    }
</script>
