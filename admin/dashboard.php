<?php
session_start();

// PROTEKSI HALAMAN: Hanya Admin yang boleh masuk
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Support Ticketing Dashboard - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* --- Reset & Base Styles --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f6f9;
            color: #333;
            display: flex;
            /* Mengaktifkan layout bersebelahan untuk Sidebar & Konten */
            min-height: 100vh;
        }

        /* --- Sidebar Navigation Menu --- */
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

        /* --- Main Content Area --- */
        .main-content {
            margin-left: 260px;
            /* Memberi ruang agar tidak tertutup sidebar */
            padding: 30px;
            width: calc(100% - 260px);
        }

        header {
            margin-bottom: 30px;
        }

        header h1 {
            font-size: 24px;
            color: #2c3e50;
        }

        header p {
            color: #7f8c8d;
            font-size: 14px;
        }

        /* --- Dashboard Indicators (Grid) --- */
        .indicators-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-left: 5px solid;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .card h3 {
            font-size: 14px;
            color: #7f8c8d;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .card .number {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
        }

        .card-new {
            border-left-color: #3498db;
        }

        /* Biru */
        .card-progress {
            border-left-color: #f39c12;
        }

        /* Oranye */
        .card-wait {
            border-left-color: #e74c3c;
        }

        /* Merah */
        .card-done {
            border-left-color: #2ecc71;
        }

        /* Hijau */

        /* --- Table Section --- */
        .table-section {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .table-section h2 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th,
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eeeeee;
        }

        th {
            background-color: #f8f9fa;
            color: #34495e;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        /* --- Status Badges --- */
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        .badge-new {
            background-color: #e1f5fe;
            color: #0288d1;
        }

        .badge-progress {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .badge-wait {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .badge-done {
            background-color: #e8f5e9;
            color: #388e3c;
        }
    </style>
</head>

<body>

    <?php include("sidebar.php"); ?>

    <div class="main-content">
        <header>
            <h1>IT Support Ticketing Dashboard</h1>
            <p>Pantau dan kelola seluruh tiket kendala IT hari ini secara realtime</p>
        </header>

        <section class="indicators-grid">
            <div class="card card-new">
                <h3>Pekerjaan Baru</h3>
                <div class="number" id="countNew">0</div>
            </div>
            <div class="card card-progress">
                <h3>On Progress</h3>
                <div class="number" id="countProgress">0</div>
            </div>
            <div class="card card-wait">
                <h3>On Wait</h3>
                <div class="number" id="countWait">0</div>
            </div>
            <div class="card card-done">
                <h3>Selesai</h3>
                <div class="number" id="countDone">0</div>
            </div>
        </section>

        <section class="table-section">
            <h2>Daftar Pekerjaan Hari Ini</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Ticket</th>
                        <th>Judul Pekerjaan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="adminTicketTable">
                    <tr>
                        <td colspan="3" style="text-align: center; color: #7f8c8d;">Memuat data pekerjaan...</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>

    <audio id="notifSound" src="../assets/audio/notification.mp3" preload="auto"></audio>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let lastTicketId = 0; // Menyimpan acuan ID tertinggi
            let isFirstLoad = true; // Penanda untuk membedakan load pertama dengan real-time update
            const notifAudio = document.getElementById('notifSound');

            function muatDataDashboard() {
                fetch('../middleware/get_admin_data.php')
                    .then(response => response.json())
                    .then(data => {
                        // 1. Update indikator angka
                        document.getElementById('countNew').textContent = data.counts.New || 0;
                        document.getElementById('countProgress').textContent = data.counts.Progress || 0;
                        document.getElementById('countWait').textContent = data.counts.Wait || 0;
                        document.getElementById('countDone').textContent = data.counts.Done || 0;

                        // 2. Render isi tabel
                        const tbody = document.getElementById('adminTicketTable');
                        tbody.innerHTML = '';

                        if (data.tickets.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; color: #7f8c8d;">Hari ini tidak ada antrean pekerjaan.</td></tr>';
                            return;
                        }

                        // KUNCI PERBAIKAN: Set lastTicketId HANYA saat pertama kali admin membuka halaman
                        if (isFirstLoad && data.tickets.length > 0) {
                            lastTicketId = data.tickets[0].id;
                            isFirstLoad = false; // Matikan penanda agar tidak meng-overide ID lagi saat refresh data
                            console.log("ID Acuan Pertama Dikunci pada: #TK-" + lastTicketId);
                        }

                        data.tickets.forEach(ticket => {
                            let badgeClass = 'badge-new';
                            let statusText = ticket.status;

                            if (ticket.status === 'New') {
                                badgeClass = 'badge-new';
                                statusText = 'Pekerjaan Baru';
                            } else if (ticket.status === 'On Progress') {
                                badgeClass = 'badge-progress';
                            } else if (ticket.status === 'On Wait') {
                                badgeClass = 'badge-wait';
                            } else if (ticket.status === 'Done') {
                                badgeClass = 'badge-done';
                                statusText = 'Selesai';
                            }

                            tbody.innerHTML += `
                                <tr>
                                    <td style="font-weight: bold; color: #7f8c8d;">#TK-${ticket.id}</td>
                                    <td>${ticket.title}</td>
                                    <td><span class="badge ${badgeClass}">${statusText}</span></td>
                                </tr>
                            `;
                        });
                    })
                    .catch(err => console.error("Gagal memuat data dashboard:", err));
            }

            // Jalankan muat data awal
            muatDataDashboard();

            // Fungsi Polling Pengecek Tiket Baru
            function cekTiketBaruMasuk() {
                if (lastTicketId === 0) {
                    // console.log("🔍 Debug: Polling dilewati karena lastTicketId masih 0.");
                    return;
                }

                // console.log(`📡 Debug: Memulai Fetch ke cek_tiket_baru.php?last_id=${lastTicketId}`);

                fetch(`../middleware/cek_tiket_baru.php?last_id=${lastTicketId}`)
                    .then(response => {
                        // console.log("🔍 Debug: Respon mentah diterima dari server, status:", response.status);
                        return response.json();
                    })
                    .then(data => {
                        // console.log("📊 Debug: Data JSON yang diterima dari server:", data);

                        if (data.error) {
                            // console.error("❌ Debug ERROR dari server:", data.error);
                            return;
                        }

                        if (data.ada_baru) {
                            // console.log("🎉 SUCCESS: Menemukan tiket baru! ID: " + data.id);

                            lastTicketId = data.id;

                            // 1. Putar suara langsung tanpa ditunda
                            if (notifAudio) {
                                notifAudio.play().catch(e => console.log("🔈 Audio diblokir browser."));
                            }

                            // 2. Berikan jeda 100 milidetik (0.1 detik) baru munculkan alert agar tidak saling mengunci
                            setTimeout(function() {
                                alert(`🔔 TIKET BARU MASUK!\nID: #TK-${data.id}\nKendala: ${data.title}`);
                                muatDataDashboard();
                            }, 100);
                        } else {
                            // console.log("😴 Debug: Tidak ada tiket baru. Polling selesai.");
                        }
                    })
                    .catch(err => {
                        // console.error("💥 Debug CRASH pada Fetch API:", err);
                    });
            }

            // Cek setiap 5 detik sekali agar lebih responsif saat uji coba
            setInterval(cekTiketBaruMasuk, 5000);
        });
    </script>
</body>

</html>