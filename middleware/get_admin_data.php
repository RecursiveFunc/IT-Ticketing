<?php
session_start();
require_once __DIR__ . '/../database/koneksi.php';

// Pastikan hanya admin terotentikasi yang bisa menembak API data ini
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

try {
    // 1. Ambil hitungan total per masing-masing status tiket
    $sql_count = "SELECT status, COUNT(*) as total FROM tickets GROUP BY status";
    $stmt_count = $pdo->query($sql_count);
    $counts_raw = $stmt_count->fetchAll(PDO::FETCH_ASSOC);

    // Tata ulang format array agar mudah dibaca di JavaScript (misal: ['New' => 5, 'Done' => 12])
    $counts = ['New' => 0, 'Progress' => 0, 'Wait' => 0, 'Done' => 0];
    foreach ($counts_raw as $row) {
        if ($row['status'] === 'New') $counts['New'] = $row['total'];
        if ($row['status'] === 'On Progress') $counts['Progress'] = $row['total'];
        if ($row['status'] === 'On Wait') $counts['Wait'] = $row['total'];
        if ($row['status'] === 'Done') $counts['Done'] = $row['total'];
    }

    // 2. Ambil seluruh daftar tiket untuk dimuat ke dalam tabel admin (Urutan id terbesar / terbaru)
    $sql_tickets = "SELECT id, title, status FROM tickets ORDER BY id DESC";
    $stmt_tickets = $pdo->query($sql_tickets);
    $tickets = $stmt_tickets->fetchAll(PDO::FETCH_ASSOC);

    // Kirim gabungan data dalam bentuk JSON tunggal yang bersih
    echo json_encode([
        'counts' => $counts,
        'tickets' => $tickets
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database failure: ' . $e->getMessage()]);
}
?>