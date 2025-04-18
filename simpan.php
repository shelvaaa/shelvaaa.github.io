<?php
// Database connection settings
$host = "localhost";
$user = "root";
$pass = "";
$db = "kontak_db";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    header("Location: index.php?message=" . urlencode("Koneksi database gagal: " . $conn->connect_error));
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $nama = filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telepon = filter_input(INPUT_POST, 'telepon', FILTER_SANITIZE_NUMBER_INT);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $pesan = filter_input(INPUT_POST, 'pesan', FILTER_SANITIZE_STRING);

    // Validate required fields
    if (empty($nama) || empty($email) || empty($telepon) || empty($subject) || empty($pesan)) {
        header("Location: index.php?message=" . urlencode("Semua field harus diisi"));
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?message=" . urlencode("Format email tidak valid"));
        exit();
    }

    // Validate name length
    if (strlen($nama) > 50) {
        header("Location: index.php?message=" . urlencode("Nama maksimal 50 karakter"));
        exit();
    }

    // Validate email length
    if (strlen($email) > 50) {
        header("Location: index.php?message=" . urlencode("Email maksimal 50 karakter"));
        exit();
    }

    // Validate subject length
    if (strlen($subject) > 100) {
        header("Location: index.php?message=" . urlencode("Subject maksimal 100 karakter"));
        exit();
    }

    // Validate message length
    if (strlen($pesan) > 500) {
        header("Location: index.php?message=" . urlencode("Pesan maksimal 500 karakter"));
        exit();
    }

    // Validate phone number
    if (!preg_match('/^[0-9]{10,15}$/', $telepon)) {
        header("Location: index.php?message=" . urlencode("Nomor telepon harus 10-15 digit angka"));
        exit();
    }

    // Prepare and bind
    $sql = "INSERT INTO kontak (nama, email, telepon, subject, pesan) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("Prepare statement failed: " . $conn->error);
        header("Location: index.php?message=" . urlencode("Error preparing statement: " . $conn->error));
        exit();
    }

    $stmt->bind_param("sssss", $nama, $email, $telepon, $subject, $pesan);

    // Execute and check result
    if ($stmt->execute()) {
        header("Location: index.php?message=" . urlencode("Pesan berhasil dikirim"));
    } else {
        error_log("Execute failed: " . $stmt->error);
        header("Location: index.php?message=" . urlencode("Terjadi kesalahan: " . $stmt->error));
    }

    // Close statement
    $stmt->close();
} else {
    header("Location: index.php?message=" . urlencode("Metode pengiriman tidak valid"));
}

// Close connection
$conn->close();
?>