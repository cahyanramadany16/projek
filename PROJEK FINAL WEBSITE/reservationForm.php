<?php
session_start();
// Timeout in seconds
$timeout_duration = 100;

// Redirect ke halaman login jika belum login
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

if ($_SESSION['role'] !== 'user') {
    header("Location: reservationForm.php");
    exit;
}

// Tangani username dengan aman
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest';

// Proses logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: logout.php');
    exit;
}

$servername = "localhost";
$username_db = "root";
$password = "";
$dbname = "db_projek";

$conn = new mysqli($servername, $username_db, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['status'])) {
    $status = $_GET['status'];
    if ($status === 'success') {
        echo "<script>alert('Reservasi berhasil!');</script>";
    } elseif ($status === 'upload_failed') {
        echo "<script>alert('Gagal mengunggah bukti pembayaran.');</script>";
    } elseif ($status === 'error') {
        echo "<script>alert('Terjadi kesalahan. Coba lagi.');</script>";
    }
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
    <link rel="stylesheet" href="css/styleForm.css">
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
        /* Animasi global */
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

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

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
        }

        header h1 {
            margin: 0;
            font-size: 2em;
            color: #f8f5f5;
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
        .user-greeting {
            font-size: 1.5em;
            color: white;
            margin-right: 15%;
            animation: fadeIn 1.5s ease-out;
        }

        .container {
            padding: 100px 20px 20px;
            animation: fadeIn 1s ease-out;
        }

        .section {
            background-color: #70e7f717;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0.1, 0.1, 0.1, 0.1);
        }

        form {
            animation: fadeInScale 1s ease-out;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input, select {
            width: auto;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .reserve-button {
            background-color: #29668f;
            color: white;
            font-size: 1em;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .reserve-button:hover {
            background-color: #1a4869;
            transform: scale(1.05);
        }

        footer {
            background-color: #29668f;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }

        #bankDetails, #creditCardDetails {
            display: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<header>
<div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
    <h1>Reservation Form Four Points by Sheraton Makassar</h1>
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

<main class="container">
    <section id="Reservation" class="section">
        <h2>Make a Reservation</h2>
        <form action="submitReservation.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="roomType">Room Type:</label>
                <select id="roomType" name="roomType" required>
                    <option value="">Select a room type</option>
                    <?php
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['room_type']) . '">' . htmlspecialchars($row['room_type']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="checkIn">Check-In Date:</label>
                <input type="date" id="checkIn" name="checkIn" required>
            </div>
            <div class="form-group">
                <label for="checkOut">Check-Out Date:</label>
                <input type="date" id="checkOut" name="checkOut" required>
            </div>
            <div class="form-group">
                <label for="contact">Contact Info:</label>
                <input type="text" id="contact" name="contact" placeholder="Enter your contact info" required>
            </div>
            <div class="form-group">
                <label for="paymentMethod">Payment Method:</label>
                <select id="paymentMethod" name="paymentMethod" required onchange="showPaymentDetails(this.value)">
                    <option value="">Select payment method</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Cash">Cash</option>
                </select>
            </div>
            <div id="bankDetails">
                <p><strong>Bank Account Details:</strong></p>
                <p>Bank: Bank Central Asia (BCA)</p>
                <p>Account Number: 1234567890</p>
                <p>Account Name: Four Points by Sheraton Makassar</p>
                <div class="form-group">
                    <label for="paymentProof">Upload Payment Proof:</label>
                    <input type="file" id="paymentProof" name="paymentProof" accept="image/*">
                </div>
            </div>
            <div id="creditCardDetails">
                <div class="form-group">
                    <label for="creditCardNumber">Credit Card Number:</label>
                    <input type="text" id="creditCardNumber" name="creditCardNumber" placeholder="Enter your credit card number" maxlength="16" pattern="\d{16}">
                </div>
            </div>
            <button type="submit" class="reserve-button">Reserve Now</button>
        </form>
    </section>
</main>

<footer>
    <p>&copy; 2023 Four Points by Sheraton Makassar</p>
</footer>

<script>
    function showPaymentDetails(method) {
        const bankDetails = document.getElementById('bankDetails');
        const creditCardDetails = document.getElementById('creditCardDetails');

        if (method === 'Transfer Bank') {
            bankDetails.style.display = 'block';
            creditCardDetails.style.display = 'none';
        } else if (method === 'Credit Card') {
            creditCardDetails.style.display = 'block';
            bankDetails.style.display = 'none';
        } else {
            bankDetails.style.display = 'none';
            creditCardDetails.style.display = 'none';
        }
    }
</script>

</body>
</html>
