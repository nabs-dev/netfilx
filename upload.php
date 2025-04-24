<?php
session_start();
include 'db_connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit();
}

$error = '';
$success = '';

// Handle video upload
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $genre = trim($_POST['genre']);
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($description) || empty($genre)) {
        $error = "All fields are required.";
    } elseif (!isset($_FILES['video']) || $_FILES['video']['error'] !== 0) {
        $error = "Please upload a video file.";
    } elseif (!isset($_FILES['thumbnail']) || $_FILES['thumbnail']['error'] !== 0) {
        $error = "Please upload a thumbnail image.";
    } else {
        $allowed_video_types = ['video/mp4', 'video/avi', 'video/mov', 'video/mpeg'];
        $allowed_image_types = ['image/jpeg', 'image/png', 'image/jpg'];

        $video = $_FILES['video'];
        $thumbnail = $_FILES['thumbnail'];

        if (!in_array($video['type'], $allowed_video_types)) {
            $error = "Invalid video format. Only mp4, avi, mov allowed.";
        } elseif (!in_array($thumbnail['type'], $allowed_image_types)) {
            $error = "Invalid image format. Only jpg, png allowed.";
        } elseif ($video['size'] > 104857600) {
            $error = "Video file size exceeds 100MB.";
        } else {
            $video_name = time() . '_' . basename($video['name']);
            $thumb_name = time() . '_' . basename($thumbnail['name']);

            $video_path = "uploads/videos/$video_name";
            $thumb_path = "uploads/thumbnails/$thumb_name";

            // Create directories
            if (!is_dir('uploads/videos')) mkdir('uploads/videos', 0777, true);
            if (!is_dir('uploads/thumbnails')) mkdir('uploads/thumbnails', 0777, true);

            if (move_uploaded_file($video['tmp_name'], $video_path) &&
                move_uploaded_file($thumbnail['tmp_name'], $thumb_path)) {

                $category = (rand(0, 1) == 0) ? 'trending' : 'new';

                $stmt = $conn->prepare("INSERT INTO movies (title, description, genre, video_url, thumbnail, user_id, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssis", $title, $description, $genre, $video_path, $thumb_path, $user_id, $category);

                if ($stmt->execute()) {
                    $success = "Video uploaded successfully!";
                    echo "<script>
                        setTimeout(() => { window.location.href = 'index.php'; }, 2000);
                    </script>";
                } else {
                    $error = "Database error: " . $stmt->error;
                }
            } else {
                $error = "Error moving uploaded files.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Video - NetflixClone</title>
    <style>
        * {margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif;}
        body {
            background: #141414;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 80px;
            min-height: 100vh;
        }
        .nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #000;
            display: flex;
            justify-content: space-between;
            padding: 20px 40px;
            z-index: 999;
        }
        .nav a {color: #fff; text-decoration: none; margin: 0 10px;}
        .nav .logo {color: #e50914; font-weight: bold; font-size: 1.5rem;}
        .container {
            width: 100%; max-width: 600px;
            background: rgba(0,0,0,0.8);
            padding: 30px;
            border-radius: 8px;
        }
        h1 {text-align: center; margin-bottom: 20px;}
        form input, form textarea, form select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: #222;
            border: none;
            border-radius: 4px;
            color: #fff;
        }
        form input[type="file"] {
            background: #333;
            padding: 8px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #e50914;
            color: #fff;
            border: none;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn:hover {background: #f40612;}
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .error {background: rgba(255,0,0,0.2); color: #ff4d4d;}
        .success {background: rgba(0,255,0,0.2); color: #2ecc71;}
        .footer {
            margin-top: auto;
            padding: 20px;
            text-align: center;
            color: #777;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<div class="nav">
    <a class="logo" href="index.php">NETFLIXCLONE</a>
    <div>
        <a href="index.php">Home</a>
        <a href="#">TV Shows</a>
        <a href="#">Movies</a>
        <a href="#">My List</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h1>Upload Your Video</h1>

    <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Video Title" required>
        <textarea name="description" placeholder="Video Description" rows="3" required></textarea>
        <input type="text" name="genre" placeholder="Genre (e.g. Action, Drama)" required>
        <label style="margin-top: 10px;">Select Video (MP4, AVI, MOV):</label>
        <input type="file" name="video" accept="video/*" required>
        <label>Upload Thumbnail (JPG, PNG):</label>
        <input type="file" name="thumbnail" accept="image/*" required>
        <button class="btn" type="submit">Upload</button>
    </form>
</div>

<div class="footer">
    <p>&copy; 2025 NetflixClone. All rights reserved.</p>
</div>

</body>
</html>
