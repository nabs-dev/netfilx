<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php';

$error = '';
$success = '';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}

// Process signup form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields";
    } elseif ($password != $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        try {
            // Check if email already exists
            $check_sql = "SELECT id FROM users WHERE email = ?";
            $check_stmt = $conn->prepare($check_sql);
            
            if (!$check_stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }
            
            $check_stmt->bind_param("s", $email);
            
            if (!$check_stmt->execute()) {
                throw new Exception("Execute failed: " . $check_stmt->error);
            }
            
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $error = "Email already exists";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $insert_sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                
                if (!$insert_stmt) {
                    throw new Exception("Prepare statement failed: " . $conn->error);
                }
                
                $insert_stmt->bind_param("sss", $name, $email, $hashed_password);
                
                if ($insert_stmt->execute()) {
                    $success = "Registration successful! You can now login.";
                    // Redirect to login page after 2 seconds
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 2000);
                    </script>";
                } else {
                    throw new Exception("Insert failed: " . $insert_stmt->error);
                }
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NetflixClone - Sign Up</title>
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
            min-height: 100vh;
            color: #fff;
        }
        
        .container {
            max-width: 450px;
            margin: 0 auto;
            padding: 60px 68px 40px;
            background-color: rgba(0, 0, 0, 0.75);
            border-radius: 4px;
            margin-top: 50px;
            margin-bottom: 50px;
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
            padding: 10px;
            background-color: rgba(232, 124, 3, 0.1);
            border-radius: 4px;
        }
        
        .success {
            color: #2ecc71;
            margin-bottom: 16px;
            padding: 10px;
            background-color: rgba(46, 204, 113, 0.1);
            border-radius: 4px;
        }
        
        .login-link {
            color: #737373;
            font-size: 1rem;
        }
        
        .login-link a {
            color: #fff;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .footer {
            text-align: center;
            padding: 20px 0;
            color: #737373;
            font-size: 0.8rem;
        }
        
        @media (max-width: 768px) {
            .container {
                max-width: 100%;
                padding: 40px 20px;
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="#" class="logo">NETFLIXCLONE</a>
        </div>
        
        <h1>Sign Up</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <input type="text" name="name" class="form-control" placeholder="Full Name" required>
            </div>
            
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email Address" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            </div>
            
            <button type="submit" class="btn">Sign Up</button>
            
            <div class="login-link">
                Already have an account? <a href="login.php">Sign in now</a>
            </div>
        </form>
    </div>
    
    <div class="footer">
        <p>&copy; 2025 NetflixClone. All rights reserved.</p>
    </div>

    <script>
        // JavaScript for page redirection
        document.addEventListener('DOMContentLoaded', function() {
            const loginLink = document.querySelector('.login-link a');
            
            loginLink.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = 'login.php';
            });
        });
    </script>
</body>
</html>
