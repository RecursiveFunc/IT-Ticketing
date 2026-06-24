<?php
session_start();
require_once __DIR__ . '/../database/koneksi.php';

// Proteksi akses API
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Ambil ID terakhir dari kiriman browser admin
$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

try {
    // KUNCI PERBAIKAN: 
    // Kita cari tiket yang statusnya 'New' DAN ID-nya benar-benar LEBIH BESAR dari ID terakhir admin
    $stmt = $pdo->prepare("SELECT id, title FROM tickets WHERE id > :last_id AND status = 'New' ORDER BY id ASC LIMIT 1");
    $stmt->execute(['last_id' => $last_id]);
    $new_ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($new_ticket) {
        echo json_encode([
            'ada_baru' => true,
            'id' => (int)$new_ticket['id'],
            'title' => $new_ticket['title']
        ]);
    } else {
        // Jika tidak ada yang lebih besar, kita kirimkan false agar browser tidur kembali
        echo json_encode(['ada_baru' => false]);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>