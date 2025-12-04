<?php
#Toluwani Olukayode - Guess The Artist-Start

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$error = ""; // Initialize error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    # Handle form submission
    $userName = htmlspecialchars($_POST['user_name']);
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["profile_icon"]["name"]);

    # Create uploads directory if it does not exist
    if (!is_dir($targetDir)) 
    {
        mkdir($targetDir, 0777, true);
    }

    # Validate file type
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if (!in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) 
    {
        $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
    } 
    
    elseif ($_FILES["profile_icon"]["size"] > 2 * 1024 * 1024) 
    {
        $error = "File size exceeds 2MB.";
    } 
    
    elseif (!getimagesize($_FILES["profile_icon"]["tmp_name"])) 
    {
        $error = "File is not a valid image.";
    } 
    
    else 
    {
        # Move uploaded file
        if (move_uploaded_file($_FILES["profile_icon"]["tmp_name"], $targetFile)) 
        {
            $_SESSION['profile_icon'] = $targetFile;
            $_SESSION['user_name'] = $userName;
            $_SESSION['score'] = 0;
            $_SESSION['round'] = 1;

            # Redirect to the game
            header("Location: guess_the_artist.php");
            exit();
        } 
        
        else 
        {
            $error = "Error uploading profile picture.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Start Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            margin: 0;
            padding: 20px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            text-align: left;
        }

        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1em;
        }

        button:hover {
            background-color: #45a049;
        }
        
        .error {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Welcome to Guess the Artist</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="start_game.php" method="POST" enctype="multipart/form-data">
            <label for="user_name">Enter your name:</label>
            <input type="text" name="user_name" id="user_name" placeholder="Your name" required>

            <label for="profile_icon">Upload a profile picture:</label>
            <input type="file" name="profile_icon" id="profile_icon" accept="image/*" required>

            <button type="submit">Start Game</button>
        </form>
    </div>
</body>
</html>
