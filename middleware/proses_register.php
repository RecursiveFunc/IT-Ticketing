<?php
// 1. Panggil file koneksi database
require_once __DIR__ . '/../database/koneksi.php';
// Sekarang Anda bisa menggunakan $pdo dan konstanta BASE_PATH

// 2. Pastikan data dikirim menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 3. Ambil data dari form dan bersihkan (trim spasi)
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validasi sederhana agar tidak ada field yang kosong
    if (empty($fullname) || empty($username) || empty($password)) {
        echo "<script>alert('Semua data wajib diisi!'); window.history.back();</script>";
        exit;
    }

    try {
        // 4. Cek apakah username sudah pernah terdaftar di DB (karena username bersifat UNIQUE)
        $stmt_check = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt_check->execute(['username' => $username]);
        
        if ($stmt_check->rowCount() > 0) {
            echo "<script>alert('Username sudah digunakan, silakan pilih username lain.'); window.history.back();</script>";
            exit;
        }

        // 5. Enkripsi password demi keamanan menggunakan BCRYPT
        // Jangan pernah menyimpan password mentah (plain text) ke database
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        // 6. Siapkan query INSERT untuk memasukkan user baru
        // Kolom 'role' tidak perlu diisi karena otomatis menjadi 'employee' secara default di DB
        $sql = "INSERT INTO users (username, password, fullname) VALUES (:username, :password, :fullname)";
        $stmt = $pdo->prepare($sql);
        
        // 7. Eksekusi query dengan mengikat data secara aman (Prepared Statements)
        $stmt->execute([
            'username' => $username,
            'password' => $password_hashed,
            'fullname' => $fullname
        ]);

        // 8. Jika berhasil, beri notifikasi dan arahkan kembali ke halaman login
        echo "<script>alert('Akun berhasil dibuat! Silakan masuk.'); window.location.href='../login.php';</script>";
        exit;

    } catch (PDOException $e) {
        // Tangani jika terjadi error pada database
        echo "Error: " . $e->getMessage();
    }
} else {
    // Jika file diakses langsung tanpa lewat form POST, tendang kembali ke login
    header("Location: ../login.php");
    exit;
}
?>