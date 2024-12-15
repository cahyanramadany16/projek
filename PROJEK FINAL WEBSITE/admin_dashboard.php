<?php

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_projek";
session_start();

// Timeout in seconds
$timeout_duration = 100;
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to the login page
    header("Location: index.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    // If the user is not an admin, redirect to the user dashboard
    header("Location: user_dashboard.php");
    exit;
}

// Check for session timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    header('Location: index.php?message=session_expired');
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time

// Proses logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: logout.php');
    exit;
}

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user profile information
$username = $_SESSION['username'];
$query = "SELECT name, phone, address FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userProfile = $result->fetch_assoc();
} else {
    echo "Profile not found.";
}

// Fetch room data
$roomQuery = "SELECT * FROM room_types";
$roomResult = $conn->query($roomQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Four Points by Sheraton Makassar</title>
  <link rel="stylesheet" href="css/style.css">
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
     body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        overflow-x: hidden;
     }
 /* Gaya Header */
 header {
    display: flex;
    flex-direction: column; /* Mengatur nav berada di bawah logo/judul */
    align-items: center; /* Menyelaraskan item ke tengah secara horizontal */
    background-color: #29668f; /* Warna latar belakang header */
    padding: 10px 20px;
    position: fixed; /* Membuat header tetap di posisi atas */
    top: 0; /* Menempatkan header di bagian paling atas halaman */
    width: 100%; /* Membuat header meluas ke seluruh lebar layar */
    z-index: 1000; /* Memastikan header berada di atas konten lain */
    }

    /* Menambahkan padding ke atas body untuk menghindari konten tertutup oleh header */
    body {
    padding-top: 100px; /* Sesuaikan dengan tinggi header */
    }

    /* Gaya Konten Header */
    .header-content {
    display: flex; /* Menggunakan flexbox untuk logo dan judul */
    align-items: center; /* Menyelaraskan logo dan judul secara vertikal */
    }

    /* Gaya Logo dan Judul */
    header h1 {
    margin: 0; /* Menghapus margin default */
    padding: 5px; /* Menambahkan padding untuk memberi jarak */
    font-size: 1.5em; /* Menyesuaikan ukuran font */
    color: #f8f5f5; /* Warna teks */
    margin-left: 10px; /* Memberi jarak antara logo dan judul */
    }

    nav {
  display: flex;
  justify-content: center; /* Menyelaraskan item ke tengah */
  align-items: center;

  padding: 0 20px;
}

nav ul {
  list-style: none; /* Menghilangkan gaya list */
  display: flex;
  margin: 0;
  padding: 0;
}

nav li {
  margin-left: 20px; /* Memberi jarak antar item menu */
}

nav a {
  text-decoration: none; /* Menghilangkan garis bawah pada link */
  color: #f7e0e0; /* Warna teks link */
  font-weight: bold; /* Membuat teks lebih tebal */
  transition: color 0.3s ease; /* Menambahkan transisi saat hover */
}

nav a:hover {
  color: #ff6347; /* Warna teks saat di-hover */
}
     .hero {
        background-image: url(resource/hotel.jpg);
        background-size: cover;
        background-position: center;
        height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        animation: zoomIn 3s ease-in-out;
     }

     .hero-content {
        animation: fadeInUp 2s ease-in-out;
     }

     footer {
        text-align: center;
        padding: 10px 0;
        background-color: #29668f;
        color: white;
        margin-top: 20px;
        animation: fadeIn 2s ease-in-out;
     }

     /* Keyframes for animations */
     @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
     }

     @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
     }

     @keyframes zoomIn {
        from {
            transform: scale(1.2);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
     }

     @keyframes fadeInUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
     }
  </style>
</head>
<body>

<header>
  <div style="display: flex; justify-content: space-between; width: 100%; align-items: center; padding: 10px 20px;">
    <!-- Title aligned to the left -->
    <h1 style="flex-grow: 1; margin: 0;">Welcome To Four Points by Sheraton Makassar</h1>

    <!-- "Hi, Admin" aligned to the right -->
    <div class="user-greeting" style="font-size: 1.5em; margin-right: 15%; color: white; padding-top: 50px;">
      Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>
    </div>
  </div>
  <nav>
    <ul>
      <li><a href="admin_dashboard.php">Dashboard Admin</a></li>
      <li><a href="manage_admin.php">Manage Room</a></li>
      <li><a href="reservationInfo.php">Reservation Info</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>
</header>

<div id="Home" class="hero">
  <div class="hero-content">
    <h1>Room Management at Four Points by Sheraton Makassar</h1>
    <p>Experience the best of Makassar at our hotel</p>
  </div>
</div>

<footer>
    <p>Four Points by Sheraton Makassar &copy; 2024</p>
</footer>

</body>
</html>
