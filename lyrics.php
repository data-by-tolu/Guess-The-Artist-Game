<?php
# Toluwani Olukayode - Guess The Artist-Lyrics

# Database connection parameters
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "music_game";

# Create connection
$conn = new mysqli($servername, $username, $password, $dbname, 3307);

# Check connection
if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
} 

else 
{
    echo "Connected successfully<br>";
}

# Function to get lyrics and store them in the database if not already stored
function get_and_store_lyrics($conn, $artist, $song) 
{
    # Check if lyrics already exist in the database
    $check_sql = "SELECT lyrics FROM lyrics WHERE artist_name = ? AND song_title = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $artist, $song);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) 
    {
        # Lyrics found in the database
        $row = $result->fetch_assoc();
        echo "Lyrics already exist for '$song' by '$artist'.<br>";
        return $row['lyrics'];
    } 
    
    else 
    {
        # Fetch lyrics from the API
        $api_url = "https://api.lyrics.ovh/v1/" . urlencode($artist) . "/" . urlencode($song);
        $response = @file_get_contents($api_url); // Suppressing warning with '@'

        if ($response === FALSE) 
        {
            echo "API request failed for '$song' by '$artist'.<br>";
            return "Lyrics not found or API unavailable";
        }

        $data = json_decode($response, true);

        if (isset($data['lyrics'])) 
        {
            $lyrics = $data['lyrics'];

            # Store the lyrics in the database
            $insert_sql = "INSERT INTO lyrics (artist_name, song_title, lyrics) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sss", $artist, $song, $lyrics);

            if ($stmt->execute()) 
            {
                echo "Lyrics for '$song' by '$artist' have been added to the database.<br>";
            } 
            
            else 
            {
                echo "Failed to insert lyrics for '$song' by '$artist': " . $conn->error . "<br>";
            }

            return $lyrics;
        } 
        
        else 
        {
            echo "Lyrics not found for '$song' by '$artist'.<br>";
            return 'Lyrics not found';
        }
    }
}

# Populate the lyrics table with all tracks
$track_sql = "SELECT artist_name, song_title FROM tracks";
$track_result = $conn->query($track_sql);

if ($track_result->num_rows > 0) 
{
    while ($track = $track_result->fetch_assoc()) 
    {
        $artist = $track['artist_name'];
        $song = $track['song_title'];

        # Call the function to fetch and store lyrics
        get_and_store_lyrics($conn, $artist, $song);
    }

    echo "<br>Lyrics table has been populated.<br>";
} 

else 
{
    echo "No tracks found in the database.<br>";
}

# Close the connection
$conn->close();
?>
