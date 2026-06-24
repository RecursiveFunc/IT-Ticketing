<?php
session_start();
require_once __DIR__ . '/../database/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Ambil status tiket terlebih dahulu untuk validasi ganda
        $stmt_check = $pdo->prepare("SELECT user_id, status FROM tickets WHERE id = :id");
        $stmt_check->execute(['id' => $ticket_id]);
        $ticket = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($ticket) {
            // Validasi keamaan: Pastikan pembuat tiket adalah user yang sedang login & status masih 'New'
            if ($ticket['user_id'] == $user_id && $ticket['status'] === 'New') {
                
                // Eksekusi penghapusan tiket (atau Anda bisa mengubah statusnya menjadi 'Cancelled' jika ingin mempertahankan history)
                $stmt_delete = $pdo->prepare("DELETE FROM tickets WHERE id = :id");
                $stmt_delete->execute(['id' => $ticket_id]);

                echo "<script>alert('Tiket berhasil dibatalkan dan dihapus.'); window.location.href='../index.php';</script>";
                exit;
            } else {
                echo "<script>alert('Gagal! Tiket tidak bisa dibatalkan karena sedang diproses oleh IT.'); window.location.href='../index.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Tiket tidak ditemukan.'); window.location.href='../index.php';</script>";
            exit;
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>