<?php
#Toluwani Olukayode - Guess The Artist-End

session_start();
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "music_game";

$conn = new mysqli($servername, $username, $password, $dbname, 3307);

# Check connection
if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}

# Save the final score
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "Anonymous";
$finalScore = isset($_SESSION['score']) ? $_SESSION['score'] : 0;
$profileIcon = isset($_SESSION['profile_icon']) ? $_SESSION['profile_icon'] : "default_icon.png";

$stmt = $conn->prepare("INSERT INTO scores (user_name, score, profile_icon) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $userName, $finalScore, $profileIcon);
$stmt->execute();

# Fetch the leaderboard
$sql = "SELECT user_name, score, profile_icon FROM scores ORDER BY score DESC LIMIT 10";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Game Over</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 80%;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
        }

        a:hover {
            background-color: #45a049;
        }
        
        img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1>Game Over!</h1>
    <p>Your final score: <?php echo htmlspecialchars($finalScore); ?></p>
    <h2>Leaderboard</h2>
    <table>
        <tr>
            <th>Rank</th>
            <th>Profile</th>
            <th>Name</th>
            <th>Score</th>
        </tr>
        <?php
        $rank = 1;
        while ($row = $result->fetch_assoc()) 
        {
            $userName = htmlspecialchars($row['user_name']);
            $score = htmlspecialchars($row['score']);
            $icon = htmlspecialchars($row['profile_icon']);
            $scoreColor = ($score >= 50) ? "green" : (($score >= 30) ? "orange" : "red");

            echo "<tr style='color: $scoreColor;'>
                    <td>{$rank}</td>
                    <td><img src='{$icon}' alt='Profile Icon'></td>
                    <td>{$userName}</td>
                    <td>{$score}</td>
                  </tr>";
            $rank++;
        }
        ?>
    </table>
    <a href="start_game.php">Play Again</a>
</body>
</html>
