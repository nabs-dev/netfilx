<?php
session_start();
include 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

// Get movie ID
$movie_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch movie details from DB
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $movie = $result->fetch_assoc();
} else {
    echo "<script>alert('Movie not found.'); window.location.href='index.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($movie['title']); ?> - Watch</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #111; color: #fff; }
        .nav { background: #000; padding: 15px 4%; display: flex; justify-content: space-between; align-items: center; position: fixed; width: 100%; top: 0; z-index: 100; }
        .logo { color: #e50914; font-size: 24px; font-weight: bold; text-decoration: none; }
        .nav-links a { color: #fff; text-decoration: none; margin-left: 20px; font-size: 14px; }
        .nav-links a:hover { color: #aaa; }
        .video-container { margin-top: 70px; display: flex; justify-content: center; align-items: center; background: black; }
        video { width: 100%; max-height: 75vh; }
        .movie-info { padding: 30px 4%; }
        .movie-title { font-size: 2.5rem; margin-bottom: 10px; }
        .movie-meta span { margin-right: 20px; color: #aaa; font-size: 14px; }
        .movie-description { margin-top: 15px; max-width: 800px; line-height: 1.6; }
        .back-btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #e50914; color: white; border: none; border-radius: 4px; text-decoration: none; }
        .back-btn:hover { background: #f40612; }
    </style>
</head>
<body>

<div class="nav">
    <a href="index.php" class="logo">NETFLIXCLONE</a>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="video-container">
    <video controls>
        <source src="<?php echo htmlspecialchars($movie['video_url']); ?>" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>

<div class="movie-info">
    <h1 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h1>
    <div class="movie-meta">
        <span>Genre: <?php echo htmlspecialchars($movie['genre']); ?></span>
        <span>Year: <?php echo htmlspecialchars($movie['year']); ?></span>
        <span>Rating: <?php echo htmlspecialchars($movie['rating']); ?></span>
        <span>Duration: <?php echo htmlspecialchars($movie['duration']); ?></span>
    </div>
    <p class="movie-description"><?php echo htmlspecialchars($movie['description']); ?></p>
    <a href="index.php" class="back-btn">← Back to Browse</a>
</div>

</body>
</html>
