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
<html>
<head>
    <title>Four Points by Sheraton Makassar - Room Types</title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            font-family: 'Poppins', sans-serif;
        }
        .hero {
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .room-image {
            width: 150px;
            height: auto;
        }
        /* Animasi Fade-In */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .fade-in-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 3s, transform 3s;
        }
        .fade-in-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        p{
          font-weight: normal; /* Atur font-weight menjadi normal */
          font-size: 0.9em; /* Mengurangi ukuran font */
        }
    </style>
</head>
<body>
<header>
<div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
    <h1>Welcome To Four Points by Sheraton Makassar</h1>
    <div class="user-greeting" style="font-size: 1.5em; margin-right: 15%; color: white; padding-top: 50px;">
      Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>
            <a href="profil.php" style="text-decoration: none;">👤</a>
        </div>
    </div>
    <nav>
        <ul>
            <li><a href="#Home">Home</a></li>
            <li><a href="#About">About</a></li>
            <li><a href="#Contact">Contact</a></li>
            <li><a href="tabelRoom.php">Room Types</a></li>
            <li><a href="reservationForm.php" target="_blank">Reservation</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
<div id="Home" class=" hero fade-in-on-scroll" style="background-image: url(resource/hotel.jpg);">
  <div class="hero-content">
    <h1>Welcome To Four Points by Sheraton Makassar</h1>
    <p>Experience the best of Makassar at our hotel</p>
  </div>
</div>
</div>

<table>
    <tr>
        <th>
            <div class="content fade-in-on-scroll">
                <div id="About">
                    <h2>Four Points by Sheraton Makassar</h2>
                    <p>Temukan kenyamanan dan pengalaman menginap tak terlupakan di Four Points by Sheraton Makassar, berlokasi strategis dengan akses mudah ke destinasi wisata seperti Benteng Rotterdam dan Masjid 99 Kubah di Pantai Losari</p>
                </div>
            </div>
        </th>
        <th>
            <img src="resource/hotel.jpg" alt="Hotel Image" width="300" height="300">
        </th>
    </tr>
</table>

<table>
    <tr>
        <th>
            <img src="resource/room.jpg" alt="Room Image">
        </th>
        <th>
            <div class="content fade-in-on-scroll">
                <h2>OUR ROOM</h2>
                <p>Kamar kami dirancang untuk memberikan kenyamanan dan relaksasi terbaik. Setiap kamar dilengkapi dengan fasilitas modern, termasuk TV layar datar, Wi-Fi gratis, dan tempat tidur yang nyaman</p>
            </div>
        </th>
    </tr>
</table>

<table>
    <tr>
        <th>
            <div class="content fade-in-on-scroll">
                <h2>DINING</h2>
                <p>Hotel kami memiliki restoran yang menyajikan berbagai masakan internasional dan lokal. Kami juga menawarkan layanan kamar untuk kenyamanan Anda.</p>
            </div>
        </th>
        <th>
            <img src="resource/dining.jpg" alt="Dining Image">
        </th>
    </tr>
</table>

<div id="Contact" class="fade-in-on-scroll">
    <h2>Contact</h2>
    <p>Four Points by Sheraton Makassar<br>
        Jalan Andi Djemma No. 130 Makassar, South Sulawesi 90222<br>
        Phone: +62 411 8099999<br>
        Email: reservation.makassar@fourpoints.com</p>
    <iframe src="https://www.google.com/maps/embed?..."></iframe>
</div>

<footer>
    <p>&copy; 2023 Four Points by Sheraton Makassar</p>
</footer>

<script>
    // Intersection Observer for Scroll Animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    });
    document.querySelectorAll('.fade-in-on-scroll').forEach(el => observer.observe(el));
</script>
</body>
</html>
<?php $conn->close(); ?>
