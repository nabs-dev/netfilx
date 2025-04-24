<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit();
}

// Fetch movies by condition
function fetchMovies($conn, $condition, $limit = 10) {
    $query = "SELECT * FROM movies WHERE $condition LIMIT $limit";
    return $conn->query($query);
}

// Display a movie section
function displaySection($title, $result, $fallbackStartId = 1, $fallbackLabel = 'Movie') {
    echo "<h2 class='section-title'>$title</h2><div class='movie-list'>";
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="movie-item">';
            echo '<a href="watch.php?id=' . $row['id'] . '">';
            echo '<img src="' . $row['thumbnail'] . '" alt="' . htmlspecialchars($row['title']) . '">';
            echo '<h3 class="movie-item-title">' . htmlspecialchars($row['title']) . '</h3>';
            echo '</a></div>';
        }
    } else {
        for ($i = 0; $i < 8; $i++) {
            $id = $fallbackStartId + $i;
            echo '<div class="movie-item">';
            echo '<a href="watch.php?id=' . $id . '">';
            echo '<img src="/placeholder.svg?height=120&width=200" alt="' . $fallbackLabel . ' ' . ($i + 1) . '">';
            echo '<h3 class="movie-item-title">' . $fallbackLabel . ' ' . ($i + 1) . '</h3>';
            echo '</a></div>';
        }
    }
    echo "</div>";
}

// Queries
$trending = fetchMovies($conn, "category='trending'");
$new = fetchMovies($conn, "category='new'");
$action = fetchMovies($conn, "genre='action'");
$comedy = fetchMovies($conn, "genre='comedy'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Netflix Clone</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #141414;
            color: #fff;
        }
        header {
            background-color: #000;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 26px;
            color: #e50914;
            font-weight: bold;
        }
        .nav a {
            color: #fff;
            margin-left: 20px;
            text-decoration: none;
        }
        .container {
            padding: 20px 30px;
        }
        .section-title {
            margin-top: 30px;
            font-size: 22px;
            border-left: 5px solid #e50914;
            padding-left: 10px;
        }
        .movie-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }
        .movie-item {
            background-color: #222;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s;
        }
        .movie-item:hover {
            transform: scale(1.05);
        }
        .movie-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .movie-item-title {
            text-align: center;
            padding: 8px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">Netflix</div>
    <div class="nav">
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</header>

<div class="container">
    <?php
        displaySection("Trending Now", $trending, 1, "Trending Movie");
        displaySection("New Releases", $new, 11, "New Release");
        displaySection("Action Movies", $action, 21, "Action Movie");
        displaySection("Comedy Movies", $comedy, 31, "Comedy Movie");
    ?>
</div>

</body>
</html>
