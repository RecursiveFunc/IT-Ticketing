<?php
session_start();
require_once __DIR__ . '/../database/koneksi.php';

// 1. Proteksi Keamanan: Hanya admin yang sah yang boleh mendownload data internal ini
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    die("Akses ditolak. Anda bukan admin.");
}

try {
    // 2. Ambil data gabungan tiket dan nama karyawan dari database
    $query = "SELECT t.id, t.title, t.description, t.status, u.fullname 
              FROM tickets t 
              JOIN users u ON t.user_id = u.id 
              ORDER BY t.id DESC";
    $stmt = $pdo->query($query);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. SETTING HEADER BROWSER: Memaksa browser mengunduh sebagai file Excel (.xls)
    $filename = "Laporan_IT_Support_" . date('Y-m-d') . ".xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    // 4. DESAIN STRUKTUR TABEL EXCEL
    ?>
    <table border="1">
        <thead>
            <tr>
                <th colspan="5" style="font-size: 16px; font-weight: bold; text-align: center; height: 40px; background-color: #2c3e50; color: #ffffff;">
                    LAPORAN DATA TIKET KENDALA IT SUPPORT
                </th>
            </tr>
            <tr style="background-color: #f2f2f2; font-weight: bold; height: 25px;">
                <th style="width: 100px;">ID TIKET</th>
                <th style="width: 250px;">JUDUL PEKERJAAN</th>
                <th style="width: 400px;">DETAIL MASALAH (DESKRIPSI)</th>
                <th style="width: 200px;">NAMA PELAPOR (EMPLOYEE)</th>
                <th style="width: 150px;">STATUS AKHIR</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tickets)): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Belum ada data tiket yang terdata.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($tickets as $ticket): ?>
                    <tr style="height: 25px;">
                        <td style="text-align: center;">#TK-<?php echo $ticket['id']; ?></td>
                        <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['description']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['fullname']); ?></td>
                        <td style="text-align: center; font-weight: bold;">
                            <?php 
                                // Ubah teks status bahasa inggris database ke teks indonesia rapi saat dicetak
                                if($ticket['status'] === 'New') echo 'Pekerjaan Baru';
                                else echo $ticket['status'];
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php

} catch (PDOException $e) {
    die("Gagal mengekspor data: " . $e->getMessage());
}
?>