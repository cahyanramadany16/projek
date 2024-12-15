<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_projek";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Delete Request
if (isset($_GET['reservation_id'])) {
    $reservation_id = intval($_GET['reservation_id']); // Pastikan input adalah angka
    $deleteQuery = "DELETE FROM reservations WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $reservation_id);

    if ($stmt->execute()) {
        echo "<script>alert('Reservation deleted successfully!'); window.location.href='reservationInfo.php';</script>";
    } else {
        echo "<script>alert('Error deleting reservation: " . $conn->error . "');</script>";
    }
}

// Fetch Reservation Data
$result = $conn->query("SELECT * FROM reservations");
if (!$result) {
    die("Error fetching data: " . $conn->error);
}

session_start();
// Timeout in seconds
$timeout_duration = 100;

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: reservationInfo.php");
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
?>


<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reservation Information</title>
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
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
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

        main {
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #29668f;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
            color: #333;
        }

        footer {
            text-align: center;
            padding: 10px 0;
            background-color: #29668f;
            color: white;
        }

        
        button {
            background-color: #4aa4e0;
            color: white;
            border: none;
            cursor: pointer;
        }

        .user-greeting {
            text-align: right;
            margin: 10px;
            color: #29668f;
        }
    </style>
</head>
<body>

    
<header>
  <div style="display: flex; justify-content: space-between; width: 100%; align-items: center; padding: 10px 20px;">
    <!-- Title aligned to the left -->
    <h1 style="flex-grow: 1; margin: 0;">Reservation Information Four Points by Sheraton Makassar</h1>

    <!-- "Hi, Admin" aligned to the right -->
    <div class="user-greeting" style="font-size: 1.5em; margin-right: 15%; color: white; padding-top: 50px;">
      Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>
    </div>
  </div>
  
  <!-- Navbar centered -->
  <nav>
    <ul>
      <li><a href="admin_dashboard.php">Dashboard Admin</a></li>
      <li><a href="manage_admin.php">Manage Room</a></li>
      <li><a href="reservationInfo.php">Reservation Info</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>
</header>

</header>

<div class="user-greeting">
    Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>
</div>

<main>
    <h2>Reservation Details</h2>
    <table>
        <thead>
            <tr>
                <th>Reservation ID</th>
                <th>Customer Name</th>
                <th>Room Type</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Payment Method</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['room_type']) ?></td>
                    <td><?= htmlspecialchars($row['check_in']) ?></td>
                    <td><?= htmlspecialchars($row['check_out']) ?></td>
                    <td><?= htmlspecialchars($row['payment_method']) ?></td>
                    <td>
                        <button onclick="viewDetails(<?= $row['id'] ?>)">View</button>
                        <button onclick="deleteReservation(<?= $row['id'] ?>)" style="background-color:red;">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>


<script>
function viewDetails(reservationId) {
    alert("View reservation details for ID: " + reservationId);
    // Implement a modal or separate page for viewing full details
}

function deleteReservation(reservationId) {
    if (confirm("Are you sure you want to delete this reservation?")) {
        // Kirim request ke server untuk menghapus reservasi
        window.location.href = "?reservation_id=" + reservationId;
    }
}
</script>


</body>
</html>
