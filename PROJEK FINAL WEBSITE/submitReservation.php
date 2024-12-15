<?php
session_start();


// Timeout in seconds
$timeout_duration = 60;

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
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


// Koneksi ke database
$servername = "localhost";
$username_db = "root";
$password = "";
$dbname = "db_projek";

$conn = new mysqli($servername, $username_db, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Tangani form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomType = htmlspecialchars($_POST['roomType']);
    $checkIn = htmlspecialchars($_POST['checkIn']);
    $checkOut = htmlspecialchars($_POST['checkOut']);
    $contact = htmlspecialchars($_POST['contact']);
    $paymentMethod = htmlspecialchars($_POST['paymentMethod']);
    $username = $_SESSION['username']; // Ambil username dari session

    // Proses upload bukti pembayaran (jika metode transfer bank)
    $paymentProofPath = null;
    if ($paymentMethod === 'Transfer Bank' && isset($_FILES['paymentProof']) && $_FILES['paymentProof']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['paymentProof']['name']);
        $paymentProofPath = $uploadDir . $fileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Buat folder jika belum ada
        }

        if (!move_uploaded_file($_FILES['paymentProof']['tmp_name'], $paymentProofPath)) {
            // Redirect ke halaman form dengan error message
            header("Location: reservationForm.php?status=upload_failed");
            exit;
        }
    }

    // Simpan data reservasi ke database
    $stmt = $conn->prepare("INSERT INTO reservations (username, room_type, check_in, check_out, contact, payment_method, payment_proof) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $username, $roomType, $checkIn, $checkOut, $contact, $paymentMethod, $paymentProofPath);

    if ($stmt->execute()) {
        // Redirect ke halaman form dengan success message
        header("Location: reservationForm.php?status=success");
    } else {
        // Redirect ke halaman form dengan error message
        header("Location: reservationForm.php?status=error");
    }

    $stmt->close();
    $conn->close();
}
?>
