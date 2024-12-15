<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_projek";
session_start();


// Timeout in seconds
$timeout_duration = 100;

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}
if ($_SESSION['role'] !== 'user') {
    header("Location: tabelRoom.php");
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

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Room Data
$result = $conn->query("SELECT * FROM room_types");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
    <link rel="stylesheet" href="styleTable.css">
</head>
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
  
    body{
        font-size: 12px;
        padding: 10px;
    }
}
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f4f4f4;
        animation: fadeIn 1s ease-in-out;

    }
    

    /* Animasi fade-in saat halaman dimuat */
    @keyframes fadeIn {
        0% { opacity: 0; }
        100% { opacity: 1; }
    }

    /* Gaya Header */
    header {
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: #29668f;
        padding: 10px 20px;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        animation: fadeIn 1s ease-in-out;
    }

    .header-content {
        display: flex;
        align-items: center;
    }

    header h1 {
        margin: 0;
        padding: 5px;
        font-size: 1.8em;
        color: #f8f5f5;
        margin-left: 10px;
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
    text-align: left;
    margin: 0;
    padding: 0;
  }
  
  nav li {
    margin-left: 20px; /* Memberi jarak antar item menu */
  }
  
  nav a {
    text-decoration: none; /* Menghilangkan garis bawah pada link */
    color: #ffffff86; /* Warna teks link */
    font-weight: bold; /* Membuat teks lebih tebal */
    transition: color 0.3s ease; /* Menambahkan transisi saat hover */
  }
  
  nav a:hover {
    color: #ff6347; /* Warna teks saat di-hover */
  }

    /* Section Styles */
    section {
        padding: 500px 40px;
        margin: 20px auto;
        background-color: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        width: 90%;
        max-width: 1200px;
    }

    section h2 {
        margin-top: 100px;
        color: #29668f;
        text-align: center;
        font-size: 2em;
        animation: fadeIn 1s ease-in-out;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 16px;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }

    th {
        background-color: #29668f;
        color: white;
    }

    tbody tr:hover {
        background-color: #f9f9f9;
    }

    .room-image {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 5px;
        transition: transform 0.3s ease-in-out; /* Menambahkan transisi */
    }

    .room-image:hover {
        transform: scale(1.2); /* Zoom saat gambar di-hover */
    }

    /* Form Styles */
    form {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-top: 20px;
        align-items: center;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
        max-width: 600px;
    }

    label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    input, select {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 100%;
        font-size: 16px;
    }

    button {
        background-color: #29668f;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 5px;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #ff6347;
        color: black;
    }

    /* Footer Styles */
    .footer {
        background-color: #29668f;
        color: white;
        text-align: center;
        padding: 15px 0;
        margin-top: 20px;
        font-size: 14px;
    }

    /* Media Query untuk Desktop */
    @media (min-width: 1024px) {
        header h1 {
            font-size: 2em;
        }

        section {
            padding: 60px 50px;
        }

        table {
            font-size: 18px;
        }

        form {
            gap: 25px;
        }

        button {
            font-size: 20px;
            padding: 15px 30px;
        }
    }
</style>
<body>
<header>
<div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
    <h1>Room Information Four Points by Sheraton Makassar</h1>
    <div class="user-greeting" style="font-size: 1.5em; margin-right: 15%; color: white; padding-top: 50px;">
      Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>
            <a href="profil.php" style="text-decoration: none;">👤</a>
        </div>
    </div>
    <nav>
        <ul>
            <li><a href="user_dashboard.php">Home</a></li>
            <li><a href="user_dashboard.php#About">About</a></li>
            <li><a href="user_dashboard.php#Contact">Contact</a></li>
            <li><a href="tabelRoom.php">Room Types</a></li>
            <li><a href="reservationForm.php" target="_blank">Reservation</a></li>
            <li><a href="?logout=true">Logout</a></li>
        </ul>
    </nav>
</header>

<main>
    <section id="RoomTypes">
        <h2>Room Types and Rates</h2>
        <table>
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th>Category</th>
                    <th>Price (per night)</th>
                    <th>Benefits</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['room_type'] ?></td>
                        <td><?= $row['category'] ?></td>
                        <td>Rp. <?= number_format($row['price'], 2) ?></td>
                        <td><?= $row['benefits'] ?></td>
                        <td><img src="<?= $row['image_path'] ?>" alt="Room Image" class="room-image"></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</main>

<div class="footer">
    <p>&copy; 2023 Four Points by Sheraton Makassar</p>
</div>
</body>
</html>
