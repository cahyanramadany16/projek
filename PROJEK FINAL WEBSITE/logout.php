<?php
session_start();

// Hapus semua data session
session_unset();

// Hancurkan session
session_destroy();

// Hapus cookie "remember_me" jika ada
if (isset($_COOKIE['remember_me'])) {
    // Set cookie dengan waktu kadaluarsa di masa lalu untuk menghapusnya
    setcookie('remember_me', '', time() - 3600, "/"); // Menghapus cookie
}

// Redirect ke halaman login
header("Location: index.php");
exit;
?>
