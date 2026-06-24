<?php
session_start();
require_once __DIR__ . '/../database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    
    // $_SESSION['user_id'] mengambil ID milik user yang sedang login dari file proses_login.php
    $user_id     = $_SESSION['user_id']; 
    $title       = $_POST['title'];
    $category_id = $_POST['category_id'];
    $urgency_id  = $_POST['urgency_id'];
    $description = $_POST['description'];

    try {
        // Nama kolom di database adalah user_id (bukan id)
        $sql = "INSERT INTO tickets (user_id, title, description, category_id, urgency_id, status) 
                VALUES (:user_id, :title, :description, :category_id, :urgency_id, 'New')";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            'user_id'     => $user_id, // Mengisi kolom user_id di tabel tickets dengan ID user yang login
            'title'       => $title,
            'description' => $description,
            'category_id' => $category_id,
            'urgency_id'  => $urgency_id
        ]);

        echo "<script>alert('Berhasil! Tiket baru telah dikirim.'); window.location.href='../index.php';</script>";
    } catch (PDOException $e) {
        echo "Gagal menyimpan tiket: " . $e->getMessage();
    }
} else {
    header("Location: ../login.php");
}
?>