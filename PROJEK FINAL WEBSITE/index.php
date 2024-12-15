<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_projek";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

// Mengecek apakah ada cookie "remember_me"
if (isset($_COOKIE['remember_me'])) {
    list($cookie_username, $cookie_password_hash) = explode(':', $_COOKIE['remember_me']);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $cookie_username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && password_verify($cookie_password_hash, $user['password'])) {
        // Set session variables
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } elseif ($user['role'] === 'user') {
            header("Location: user_dashboard.php");
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $remember = isset($_POST['remember']) ? $_POST['remember'] : '';

    if (empty($username) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Query to fetch user data based on username
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Check if user exists and password matches
        if ($user) {
            $db_password = $user['password'];

            // Check if password is hashed
            if (password_verify($password, $db_password)) {
                $is_valid = true;
            } else {
                // Check plain password
                $is_valid = $password === $db_password;
            }

            if ($is_valid) {
                // Set session variables
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Set cookie for 24 hours if "Remember Me" is checked
                if ($remember) {
                    $cookie_password_hash = password_hash($db_password, PASSWORD_DEFAULT);
                    setcookie('remember_me', $username . ':' . $cookie_password_hash, time() + 86400, "/");
                }

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } elseif ($user['role'] === 'user') {
                    header("Location: user_dashboard.php");
                }
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/styleLogin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <header>
        <h1>Welcome To Four Points by Sheraton Makassar</h1>
    </header>

    <main>
        <div class="login-container">
            <h2>Welcome Back</h2>
            <p>Please login to continue</p>
            <?php if (!empty($error)): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?= isset($_COOKIE['remember_me']) ? htmlspecialchars($_COOKIE['remember_me']) : '' ?>" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 9px;">
                    <label for="remember_me">Remember Me</label>
                    <input type="checkbox" id="remember_me" name="remember_me" style="margin-left: 7px;" <?= isset($_COOKIE['remember_me']) ? 'checked' : '' ?>>
                </div>
                <button type="submit" class="login-button">Login</button>
                <p class="signup-link">Don’t have an account? <a href="signup.php">Sign up</a></p>
            </form> 
        </div>

    </main>

    <footer>
        <p>Four Points by Sheraton Makassar &copy; 2024</p>
    </footer>

</body>
</html>
