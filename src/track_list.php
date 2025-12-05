<?php
# Toluwani Olukayode - Guess The Artist-Track List

# Database Connection
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

# Fetch Data from 'tracks' and 'lyrics' Tables
$sql = "SELECT t.id, t.artist_name, t.song_title, t.playcount, t.listeners, l.lyrics 
        FROM tracks AS t 
        LEFT JOIN lyrics AS l 
        ON t.artist_name = l.artist_name AND t.song_title = l.song_title";

$result = $conn->query($sql);

if (!$result) 
{
    die("Error fetching tracks: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guess the Artist - Tracks and Lyrics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            color: #333;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .table-container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 90%;
            max-width: 1200px;
        }

        h1 {
            text-align: center;
            font-size: 2em;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
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

        .lyrics-container {
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="table-container">
        <h1>Track List with Lyrics</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Artist Name</th>
                <th>Song Title</th>
                <th>Playcount</th>
                <th>Listeners</th>
                <th>Lyrics</th>
            </tr>
            <?php
            if ($result->num_rows > 0) 
            {
                while ($row = $result->fetch_assoc()) 
                {
                    echo "<tr>
                            <td>" . htmlspecialchars($row["id"]) . "</td>
                            <td>" . htmlspecialchars($row["artist_name"]) . "</td>
                            <td>" . htmlspecialchars($row["song_title"]) . "</td>
                            <td>" . htmlspecialchars($row["playcount"]) . "</td>
                            <td>" . htmlspecialchars($row["listeners"]) . "</td>
                            <td>
                                <div class='lyrics-container'>" . nl2br(htmlspecialchars($row["lyrics"])) . "</div>
                            </td>
                          </tr>";
                }
            } 
            
            else 
            {
                echo "<tr><td colspan='6'>No tracks found</td></tr>";
            }

            $conn->close();
            ?>
        </table>
    </div>
</body>
</html>
