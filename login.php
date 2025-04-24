<?php
session_start();
include 'db_connect.php';

$error = '';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        // Check user credentials
        $sql = "SELECT id, email, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                
                echo "<script>window.location.href = 'index.php';</script>";
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NetflixClone - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('/placeholder.svg?height=1080&width=1920');
            background-size: cover;
            background-position: center;
            height: 100vh;
            color: #fff;
        }
        
        .container {
            max-width: 450px;
            margin: 0 auto;
            padding: 60px 68px 40px;
            background-color: rgba(0, 0, 0, 0.75);
            border-radius: 4px;
            margin-top: 100px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            color: #e50914;
            font-size: 2.5rem;
            font-weight: bold;
            text-decoration: none;
        }
        
        h1 {
            font-size: 2rem;
            margin-bottom: 28px;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-control {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 4px;
            background-color: #333;
            color: #fff;
            padding: 0 20px;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            background-color: #444;
        }
        
        .btn {
            width: 100%;
            height: 50px;
            background-color: #e50914;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 24px;
            margin-bottom: 12px;
        }
        
        .btn:hover {
            background-color: #f40612;
        }
        
        .error {
            color: #e87c03;
            margin-bottom: 16px;
        }
        
        .signup-link {
            color: #737373;
            font-size: 1rem;
        }
        
        .signup-link a {
            color: #fff;
            text-decoration: none;
        }
        
        .signup-link a:hover {
            text-decoration: underline;
        }
        
        .footer {
            text-align: center;
            padding: 20px 0;
            margin-top: 50px;
            color: #737373;
            font-size: 0.8rem;
        }
        
        @media (max-width: 768px) {
            .container {
                max-width: 100%;
                padding: 40px 20px;
                margin-top: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="#" class="logo">NETFLIXCLONE</a>
        </div>
        
        <h1>Sign In</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email or phone number" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            
            <button type="submit" class="btn">Sign In</button>
            
            <div class="signup-link">
                New to NetflixClone? <a href="signup.php">Sign up now</a>
            </div>
        </form>
    </div>
    
    <div class="footer">
        <p>&copy; 2025 NetflixClone. All rights reserved.</p>
    </div>

    <script>
        // JavaScript for page redirection
        document.addEventListener('DOMContentLoaded', function() {
            const signupLink = document.querySelector('.signup-link a');
            
            signupLink.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = 'signup.php';
            });
        });
    </script>
</body>
</html>
