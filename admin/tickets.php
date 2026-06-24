<?php
session_start();
require_once __DIR__ . '/../database/koneksi.php';

// Proteksi Admin
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// PROSES UPDATE STATUS (Jika form disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $ticket_id = $_POST['ticket_id'];
    $new_status = $_POST['status'];
    $solution_note = trim($_POST['solution_note']); // Ambil input solusi
    
    try {
        // Perbarui status DAN solution_note sekaligus
        $stmt = $pdo->prepare("UPDATE tickets SET status = :status, solution_note = :note WHERE id = :id");
        $stmt->execute([
            'status' => $new_status, 
            'note' => $solution_note, 
            'id' => $ticket_id
        ]);
        echo "<script>alert('Status & Solusi berhasil diperbarui!'); window.location.href='tickets.php';</script>";
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Ambil data tiket gabungan dengan nama pembuatnya
$query = "SELECT t.*, u.fullname FROM tickets t JOIN users u ON t.user_id = u.id ORDER BY t.id DESC";
$tickets = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Manajemen Pekerjaan - IT Support</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* (Gunakan CSS style dasar yang sama dengan dashboard.php Anda) */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background-color: #f4f6f9;
            display: flex;
        }

        /* Style Sidebar */
        .sidebar {
            width: 260px;
            background-color: #2c3e50;
            color: #ecf0f1;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid #34495e;
            text-align: center;
        }

        .sidebar-header h2 {
            font-size: 20px;
            color: #3498db;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .sidebar-user {
            padding: 20px;
            background-color: #1a252f;
            font-size: 14px;
            border-bottom: 1px solid #34495e;
        }

        .sidebar-user .name {
            font-weight: bold;
            color: #2ecc71;
        }

        .sidebar-menu {
            list-style: none;
            padding: 15px 0;
            flex-grow: 1;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #bdc3c7;
            text-decoration: none;
            font-size: 15px;
            transition: all 0.2s;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li.active a {
            background-color: #34495e;
            color: #ffffff;
            border-left: 4px solid #3498db;
        }

        .sidebar-menu li a i {
            margin-right: 12px;
            font-size: 18px;
        }

        .btn-logout {
            background-color: #e74c3c;
            color: white !important;
            margin-top: auto;
        }

        .btn-logout:hover {
            background-color: #c0392b !important;
        }

        .main-content {
            margin-left: 260px;
            padding: 30px;
            width: calc(100% - 260px);
        }

        /* Layout Card & Table Khusus */
        .ticket-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            border-top: 4px solid #34495e;
        }

        .ticket-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .urgency-Critical {
            background: #ffebee;
            color: #c62828;
        }

        .urgency-High {
            background: #fff3e0;
            color: #ef6c00;
        }

        .urgency-Medium {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .urgency-Low {
            background: #e1f5fe;
            color: #0288d1;
        }

        .status-select {
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .btn-save {
            background: #2ecc71;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>



<body>

    <?php include 'sidebar.php';
    ?>

    <div class="main-content">
        <header style="margin-bottom: 25px;">
            <h1>Daftar Seluruh Pekerjaan (Tiket)</h1>
            <p>Kelola, baca detail masalah, dan perbarui status pengerjaan tim IT</p>

            <a href="../middleware/ekspor_excel.php"
                style="display: inline-block; background-color: #2ecc71; color: white; text-decoration: none; padding: 10px 15px; border-radius: 5px; font-weight: bold; margin-top: 15px; font-size: 14px;">
                <i class="bi bi-file-earmark-excel-fill"></i> Unduh Laporan Bulanan (Excel)
            </a>
        </header>

        <?php if (empty($tickets)): ?>
            <p>Belum ada tiket masuk dari karyawan.</p>
        <?php else: ?>
            <?php foreach ($tickets as $ticket): ?>
                <div class="ticket-card">
                    <div class="ticket-header">
                        <div>
                            <span style="font-weight: bold; color: #7f8c8d;">#TK-<?php echo $ticket['id']; ?></span> -
                            <span style="font-weight: bold; font-size: 18px;"><?php echo htmlspecialchars($ticket['title']); ?></span>
                        </div>
                        <div>
                            <span class="badge urgency-<?php echo $ticket['urgency_id'] == 1 ? 'Low' : ($ticket['urgency_id'] == 2 ? 'Medium' : ($ticket['urgency_id'] == 3 ? 'High' : 'Critical')); ?>">
                                Urgensi: <?php echo $ticket['urgency_id'] == 1 ? 'Low' : ($ticket['urgency_id'] == 2 ? 'Medium' : ($ticket['urgency_id'] == 3 ? 'High' : 'Critical')); ?>
                            </span>
                        </div>
                    </div>
                    <p style="margin-bottom: 15px; color: #555;"><strong>Detail Masalah:</strong><br><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>

                    <div style="display: flex; justify-content: space-between; align-items: center; background: #f9f9f9; padding: 10px; border-radius: 6px;">
                        <span style="font-size: 13px; color: #7f8c8d;">Oleh: <strong><?php echo htmlspecialchars($ticket['fullname']); ?></strong> | Kategori ID: <?php echo $ticket['category_id']; ?></span>

                        <form action="" method="POST" style="display: flex; flex-direction: column; gap: 10px; width: 100%; background: #f9f9f9; padding: 15px; border-radius: 6px; margin-top: 10px;">
                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">

                            <div style="display: flex; gap: 15px; align-items: center;">
                                <label style="font-size: 13px; font-weight: bold; color: #34495e;">Pembaruan Status:</label>
                                <select name="status" class="status-select" style="flex-grow: 1;">
                                    <option value="New" <?php echo $ticket['status'] === 'New' ? 'selected' : ''; ?>>Pekerjaan Baru</option>
                                    <option value="On Progress" <?php echo $ticket['status'] === 'On Progress' ? 'selected' : ''; ?>>On Progress</option>
                                    <option value="On Wait" <?php echo $ticket['status'] === 'On Wait' ? 'selected' : ''; ?>>On Wait</option>
                                    <option value="Done" <?php echo $ticket['status'] === 'Done' ? 'selected' : ''; ?>>Selesai</option>
                                </select>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 5px;">
                                <label style="font-size: 13px; font-weight: bold; color: #34495e;">Catatan Solusi / Keterangan (Khusus IT):</label>
                                <textarea name="solution_note" rows="2" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; font-size: 13px;" placeholder="Tuliskan tindakan perbaikan atau alasan jika status On Wait..."><?php echo htmlspecialchars($ticket['solution_note'] ?? ''); ?></textarea>
                            </div>

                            <button type="submit" name="update_status" class="btn-save" style="align-self: flex-end; padding: 8px 20px;">Simpan Perubahan Tiket</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>