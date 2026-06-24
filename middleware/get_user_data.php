<?php
session_start();
require_once __DIR__ . '/../database/koneksi.php';

// Pastikan yang mengakses data ini sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Ambil riwayat tiket berdasarkan ID user yang sedang login
try {
    $stmt = $pdo->prepare("SELECT id, title, status, solution_note, DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as created_at FROM tickets WHERE user_id = :user_id ORDER BY id DESC");
    $stmt->execute(['user_id' => $user_id]);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kirim balasan dalam bentuk JSON
    echo json_encode([
        'status' => 'success',
        'username' => $username,
        'tickets' => $tickets
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>