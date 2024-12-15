<?php
session_start();
// Timeout in seconds
$timeout_duration = 100;

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';

    // Cek apakah username sudah ada di database
    $stmt_check = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $error = "Username already exists. Please choose another one.";
    } else {
        if (empty($username) || empty($password) || empty($name) || empty($phone) || empty($address)) {
            $error = "All fields are required.";
        } else {
            // Tetapkan role default sebagai "user"
            $role = "user";

            // Hash password sebelum disimpan ke database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Masukkan data pengguna ke database
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, name, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $username, $hashedPassword, $role, $name, $phone, $address);

            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
                header("Location: index.php");
                exit;
            } else {
                $error = "Failed to register. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link rel="stylesheet" href="css/signup.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>

        /* Untuk perangkat dengan lebar maksimal 768px (tablet) */
@media (max-width: 768px) {
    body {
        font-size: 14px;
        padding: 15px;
    }
}

/* Untuk perangkat dengan lebar maksimal 480px (ponsel) */
@media (max-width: 480px) {
    body {
        font-size: 12px;
        padding: 10px;
    }
}
        /* Reset Default Styling */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    display: flex;
    flex-direction: column;
    align-items: center; /* Menyelaraskan konten di tengah secara horizontal */
    min-height: 100vh; /* Tinggi minimum layar penuh */
    background: url(resource/hotel.jpg) no-repeat center center fixed;
    background-size: cover; /* Gradasi warna latar belakang */
    color: #fff; /* Warna teks default */
    backdrop-filter: blur(2px);
    
}

/* Header styling */
header {
    width: 100%;
    text-align: center;
    background-color: #29668f;
    padding: 10px;
    position: fixed;
    top: 0;
    z-index: 1;
}

header h1 {
    font-size: 2em;
    color: #fff;
}

nav ul {
    list-style: none;
    display: flex;
    justify-content: center;
    padding: 0;
    margin: 10px 0;
}

nav li {
    margin: 0 15px;
}

nav a {
    color: #f7e0e0;
    text-decoration: none;
    font-weight: bold;
}

nav a:hover {
    color: #ff6347;
}

/* Signup container */
.signup-container {
    background: #fff;
    color: #333;
    max-width: 500px;
    margin: 100px ;
    margin-bottom: 100px;
    padding: 20px;
    border-radius: 7px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    text-align: center;
}

.signup-container h2 {
    margin-bottom: 10px;
}

.signup-container p {
    color: #666;
    margin-bottom: 20px;
}

.input-group {
    margin-bottom: 15px;
    text-align: left;
}

.input-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.input-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.signup-button {
    background: linear-gradient(135deg, #6e8efb, #29668fe8);
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 10px;
    cursor: pointer;
    width: 100%;
    font-size: 1em;
    font-weight: bold;
}

.signup-button:hover {
    background: linear-gradient(135deg, #5b73db, #264b8e);
}

footer {
  color: #fff;
  padding: 10px 20px;
  text-align: center;
  font-size: 20px;

}
    </style>
</head>
<body>
    <header>
        <h1>Welcome To Four Points by Sheraton Makassar</h1>
    </header>
    <main>
        <div class="signup-container">
            <h2>Create an Account</h2>
            <p>Please fill in the form to create an account</p>
            <?php if (!empty($error)): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="input-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="input-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <div class="input-group">
                    <label for="address">Address</label>
                    <input id="address" name="address" required></input>
                </div>

                <button type="submit" class="signup-button">Sign Up</button>
                <p class="login-link">Already have an account? <a href="index.php">Login</a></p>
            </form> 
        </div>
    <footer>
    <p>Four Points by Sheraton Makassar &copy; 2024</p>
</footer>
    </main>
</body>
</html>
