<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_projek";
session_start();
// Timeout in seconds
$timeout_duration = 100;

// Redirect ke halaman login jika belum login
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

if ($_SESSION['role'] !== 'user') {
  // If the user is not an admin, redirect to the user dashboard
  header("Location:profil.php");
  exit;
}
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

// Ambil informasi profil user berdasarkan username
$username = $_SESSION['username'];
$query = "SELECT name, phone, address, profile_image FROM users WHERE username = '$username'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $userProfile = $result->fetch_assoc();
} else {
    echo "Profile not found.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update profile
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $profileImage = $userProfile['profile_image']; // Default to existing image

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // Directory for uploads
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if not exists
        }

        $tmpName = $_FILES['profile_image']['tmp_name'];
        $fileName = basename($_FILES['profile_image']['name']);
        $filePath = $uploadDir . $fileName;

        // Check if the file is an image
        $fileType = mime_content_type($tmpName);
        if (in_array($fileType, ['image/jpeg', 'image/png', 'image/gif'])) {
            // Move the file to the uploads directory
            if (move_uploaded_file($tmpName, $filePath)) {
                // Delete old image if not default
                if ($userProfile['profile_image'] !== 'default.jpg' && file_exists($uploadDir . $userProfile['profile_image'])) {
                    unlink($uploadDir . $userProfile['profile_image']);
                }
                $profileImage = $fileName; // Update image name
            } else {
                $errorMessage = "Error uploading image.";
            }
        } else {
            $errorMessage = "Invalid image format. Only JPG, PNG, and GIF are allowed.";
        }
    }

    // Update database
    $updateQuery = "UPDATE users SET name='$name', phone='$phone', address='$address', profile_image='$profileImage' WHERE username='$username'";
    if ($conn->query($updateQuery) === TRUE) {
        $userProfile['name'] = $name;
        $userProfile['phone'] = $phone;
        $userProfile['address'] = $address;
        $userProfile['profile_image'] = $profileImage;
        $successMessage = "Profile updated successfully!";
    } else {
        $errorMessage = "Error updating profile: " . $conn->error;
    }
}

// Handle image deletion
if (isset($_GET['delete_image'])) {
    $uploadDir = 'uploads/';
    if ($userProfile['profile_image'] !== 'default.jpg' && file_exists($uploadDir . $userProfile['profile_image'])) {
        unlink($uploadDir . $userProfile['profile_image']); // Delete image file
        $userProfile['profile_image'] = 'default.jpg'; // Reset to default image
        $conn->query("UPDATE users SET profile_image='default.jpg' WHERE username='$username'");
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
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
      background-color: #f7f7f7;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 20px;
    }

    .profile-header {
      display: flex;
      align-items: center;
      margin-bottom: 40px;
      justify-content: flex-start; /* Agar tombol-tombol berdekatan ke kiri */
      gap: 10px; /* Mengatur jarak antar tombol */
    }

        .profile-header button {
        background-color: #29668f;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 1rem;
        cursor: pointer;
        border-radius: 5px;
        }


    .profile-header button:hover {
      background-color: #245c74;
    }

    .profile-info {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  padding: 30px;
  display: flex;
  align-items: center; /* Menyelaraskan konten secara vertikal */
  justify-content: space-between; /* Membuat gambar dan teks berada dalam ruang yang terpisah */
}

.profile-info .profile-left {
  flex: 0 0 auto;
  display: flex;
  justify-content: center; /* Menempatkan gambar di tengah secara horizontal */
  align-items: center; /* Menyelaraskan gambar di tengah secara vertikal */
  width: 150px; /* Menentukan lebar area untuk gambar */
  height: 150px; /* Menentukan tinggi area untuk gambar */
}

.profile-info .profile-left img {
  width: 100px; /* Ukuran gambar */
  height: 100px; /* Ukuran gambar */
  border-radius: 50%; /* Membuat gambar menjadi bulat */
  object-fit: cover; /* Menjaga gambar tetap terpotong sesuai bentuk bulat */
}


.profile-info .profile-right {
  flex: 1; /* Mengatur agar bagian teks mengisi sisa ruang */
}

.profile-info .profile-right h2 {
  font-size: 2rem;
  color: #333;
  margin-bottom: 10px;
}

.profile-info .profile-right p {
  font-size: 1.2rem;
  color: #777;
  margin: 5px 0;
}


    .profile-info .profile-right h2 {
      font-size: 2rem;
      color: #333;
      margin-bottom: 10px;
    }

    .profile-info .profile-right p {
      font-size: 1.2rem;
      color: #777;
      margin: 5px 0;
    }

    .edit-profile-form {
      background-color: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      margin-top: 40px;
    }

    .edit-profile-form h2 {
      font-size: 2rem;
      color: #333;
      margin-bottom: 20px;
    }

    .edit-profile-form input,
    .edit-profile-form textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 1rem;
    }

    .edit-profile-form button {
      background-color: #29668f;
      color: white;
      border: none;
      padding: 12px 20px;
      font-size: 1rem;
      cursor: pointer;
      border-radius: 5px;
    }

    .edit-profile-form button:hover {
      background-color: #245c74;
    }

    .message {
      font-size: 1.2rem;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 20px;
    }

    .message.success {
      background-color: #d4edda;
      color: #155724;
    }

    .message.error {
      background-color: #f8d7da;
      color: #721c24;
    }

    
  </style>
</head>
<body>
  <div class="container">
  <h1>My Profile</h1>
    <div class="profile-header">
      <button onclick="window.location.href='user_dashboard.php'">Dashboard</button>
      <button onclick="window.location.href='logout.php'">Logout</button>
    </div>

    <div class="profile-info">
      <div class="profile-left">
      <img src="uploads/<?php echo htmlspecialchars($userProfile['profile_image']); ?>" alt="Profile Picture">
    <form action="" method="GET" style="margin-top: 10px;"> 
    </form>
      </div>
      <div class="profile-right">
        <h2><?php echo htmlspecialchars($userProfile['name']); ?></h2>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($userProfile['phone']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($userProfile['address']); ?></p>
      </div>
    </div>

    <?php if (isset($successMessage)): ?>
      <div class="message success"><?php echo $successMessage; ?></div>
    <?php elseif (isset($errorMessage)): ?>
      <div class="message error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    </form>
</div>
<div class="edit-profile-form">
    <h2>Edit Profile</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" value="<?php echo htmlspecialchars($userProfile['name']); ?>" placeholder="Full Name" required>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($userProfile['phone']); ?>" placeholder="Phone Number" required>
        <textarea name="address" placeholder="Address" rows="4" required><?php echo htmlspecialchars($userProfile['address']); ?></textarea>
        <input type="file" name="profile_image" accept="image/*">
        <button type="submit">Update Profile</button>
    </form>
</div>

  </div>
</body>
</html>
<?php $conn->close(); ?>
