<?php
#Toluwani Olukayode - Guess The Artist

session_start();

# Connect to the database
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "music_game";

$conn = new mysqli($servername, $username, $password, $dbname, 3307);

# Handle game setup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_name'])) 
{
    $_SESSION['user_name'] = htmlspecialchars($_POST['user_name']);
    $_SESSION['score'] = 0;
    $_SESSION['round'] = 1;
    header("Location: guess_the_artist.php");
    exit();
}

# Redirect to start page if no user session exists
if (!isset($_SESSION['user_name'])) 
{
    header("Location: start_game.php");
    exit();
}

# Check if the game is over
if ($_SESSION['round'] > 5) 
{
    header("Location: end_game.php");
    exit();
}

# Fetch a random song for the current round
$sql = "SELECT artist_name, song_title, lyrics FROM lyrics ORDER BY RAND() LIMIT 1";
$result = $conn->query($sql);
$song = $result->fetch_assoc();
$popularLyrics = substr($song['lyrics'], 0, 150) . '...';

# Send the artist's name and round details to JavaScript
echo "<script>
        const correctArtist = '" . addslashes($song['artist_name']) . "';
        let currentRound = " . $_SESSION['round'] . ";
      </script>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guess the Artist</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            transition: background 1s ease;
        }

        .game-container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 600px;
            text-align: center;
            width: 100%;
        }

        h1 {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 10px;
        }

        p {
            font-size: 1.2em;
            line-height: 1.5;
        }

        .lyrics {
            font-style: italic;
            margin: 20px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        #feedback {
            font-size: 1.2em;
            margin-top: 20px;
        }

        a {
            text-decoration: none;
            color: #4CAF50;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        
        a:hover {
            color: #45a049;
        }
    </style>
</head>
<body>
    <div class="game-container">
        <h1>Guess the Artist</h1>
        <p>Round: <?php echo $_SESSION['round']; ?> of 5</p>
        <div class="lyrics">
            <p><?php echo nl2br(htmlspecialchars($popularLyrics)); ?></p>
        </div>
        <p>Guess which artist sang this song!</p>
        <input type="text" id="guess" placeholder="Enter artist's name">
        <button onclick="makeGuess()">Submit Guess</button>
        <p id="feedback"></p>
        <p class="hint" id="hint" style="display: none;"></p>
    </div>

    <!-- Add Sounds -->
    <audio id="correctSound" src="sounds/correct.mp3"></audio>
    <audio id="wrongSound" src="sounds/wrong.mp3"></audio>

    <script>
        let guessesRemaining = 5;

        const backgrounds = [
            "linear-gradient(135deg, #f6d365 0%, #fda085 100%)",
            "linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)",
            "linear-gradient(135deg, #c2e59c 0%, #64b3f4 100%)",
            "linear-gradient(135deg, #fddb92 0%, #d1fdff 100%)",
            "linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)"
        ];

        document.body.style.background = backgrounds[currentRound - 1];

        function makeGuess() 
        {
            const correctSound = document.getElementById("correctSound");
            const wrongSound = document.getElementById("wrongSound");
            const userGuess = document.getElementById("guess").value.trim().toLowerCase();
            const feedback = document.getElementById("feedback");

            if (userGuess === correctArtist.toLowerCase()) 
            {
                feedback.textContent = "Correct! You guessed the artist!";
                feedback.style.color = "green";
                correctSound.play();
                document.getElementById("guess").disabled = true;

                updateScore(true);
                setTimeout(fetchNewRound, 1000);
            } 
            
            else 
            {
                guessesRemaining--;
                feedback.textContent = `Incorrect! You have ${guessesRemaining} guesses left.`;
                feedback.style.color = "red";
                wrongSound.play();
                document.getElementById("guess").value = "";

                if (guessesRemaining === 3) 
                {
                    document.getElementById("hint").textContent = `Hint: The artist's name starts with "${correctArtist.charAt(0)}".`;
                    document.getElementById("hint").style.display = "block";
                }

                if (guessesRemaining === 0) 
                {
                    feedback.textContent = `Game Over! The correct artist was ${correctArtist}.`;
                    document.getElementById("guess").disabled = true;

                    updateScore(false);
                    setTimeout(fetchNewRound, 1000);
                }
            }
        }

        function updateScore(correct) 
        {
            fetch('update_score.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ correct })
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      currentRound++;
                  }
              });
        }

        function fetchNewRound() 
        {
            window.location.reload();
        }
    </script>
</body>
</html>
