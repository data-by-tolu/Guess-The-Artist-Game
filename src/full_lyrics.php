<?php
#Toluwani Olukayode - Guess The Artist-Full Lyrics

# Connect to the database
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "music_game";

# Create connection
$conn = new mysqli('127.0.0.1', 'root', '', 'music_game', 3307);

# Get song and artist from query parameters
$song = $_GET['song'];
$artist = $_GET['artist'];

# Fetch full lyrics from the database
$sql = "SELECT lyrics FROM lyrics WHERE artist_name = ? AND song_title = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $artist, $song);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Lyrics</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        pre { white-space: pre-wrap; text-align: left; background: #f4f4f4; padding: 10px; margin: 20px; }
    </style>
</head>
<body>
    <h1>Full Lyrics for "<?php echo htmlspecialchars($song); ?>" by <?php echo htmlspecialchars($artist); ?></h1>
    <pre><?php echo nl2br(htmlspecialchars($row['lyrics'])); ?></pre>
</body>
</html>
