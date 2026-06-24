<?php
session_start();

// PROTEKSI HALAMAN: Jika tidak ada session user_id ATAU role-nya bukan employee, tendang ke login.html
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal IT Support</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        .card {
            border: none;
            border-radius: 10px;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            transition: 0.2s;
        }

        .user-greeting {
            font-weight: 600;
            color: #2c3e50;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">
                <i class="bi bi-cpu-fill me-2"></i>IT Support
            </a>
            <div class="d-flex align-items-center">
                <span class="me-3 user-greeting">
                    <span class="me-3 user-greeting">
                        Halo, <span id="displayUsername">Memuat...</span>
                    </span>
                    <a href="/app_ticketing/middleware/proses_logout.php" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i>Keluar
                    </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 text-dark fw-bold">Dashboard Tiket Anda</h2>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#ticketModal">
                <i class="bi bi-plus-lg me-1"></i>Buat Tiket Baru
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="p-4 border-bottom">
                    <h5 class="card-title m-0">Riwayat Tiket Saya</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">ID Tiket</th>
                                <th>Judul Masalah</th>
                                <th>Tanggal Dibuat</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="ticketTableBody">
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    Mengambil data tiket...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ticketModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Form Tiket Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/app_ticketing/middleware/simpan_ticket.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">Judul Masalah</label>
                            <input type="text" name="title" class="form-control" placeholder="Contoh: Koneksi lambat" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small fw-bold">Kategori</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="" selected disabled>Pilih Kategori...</option>
                                    <option value="1">Hardware</option>
                                    <option value="2">Software</option>
                                    <option value="3">Network</option>
                                    <option value="4">Account & Access</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small fw-bold">Tingkat Urgensi</label>
                                <select name="urgency_id" class="form-select" required>
                                    <option value="" selected disabled>Pilih Urgensi...</option>
                                    <option value="1">Low</option>
                                    <option value="2">Medium</option>
                                    <option value="3">High</option>
                                    <option value="4">Critical</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">Deskripsi Detail</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Jelaskan detail kendala yang Anda alami..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Kirim Tiket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Menjalankan script setelah seluruh elemen halaman dimuat
        document.addEventListener("DOMContentLoaded", function() {

            // Melakukan request asinkron ke backend
            fetch('middleware/get_user_data.php')
                .then(response => response.json())
                .then(data => {
                    // 1. Tampilkan Nama Lengkap User di Navbar
                    document.getElementById('displayUsername').textContent = data.username;

                    // 2. Render Data Tiket ke dalam Tabel
                    const tbody = document.getElementById('ticketTableBody');
                    tbody.innerHTML = ''; // Kosongkan animasi loading

                    if (data.tickets.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">Belum ada tiket yang Anda buat.</td></tr>';
                    } else {
                        data.tickets.forEach(ticket => {
                            let badgeClass = 'bg-secondary';
                            if (ticket.status === 'New') badgeClass = 'bg-info text-dark';
                            if (ticket.status === 'On Progress') badgeClass = 'bg-warning text-dark';
                            if (ticket.status === 'On Wait') badgeClass = 'bg-danger';
                            if (ticket.status === 'Done') badgeClass = 'bg-success';

                            let aksiTombol = '';
                            if (ticket.status === 'New') {
                                aksiTombol = `
                                <a href="middleware/batal_tiket.php?id=${ticket.id}" 
                                class="btn btn-sm btn-outline-danger py-0" 
                                style="font-size: 11px;" 
                                onclick="return confirm('Apakah Anda yakin ingin membatalkan tiket ini?')">
                                <i class="bi bi-x-circle"></i> Batalkan
                                </a>
                            `;
                            }

                            // Deteksi jika ada Catatan Solusi dari IT
                            let tampilanSolusi = '';
                            if (ticket.solution_note && ticket.solution_note.trim() !== '') {
                                tampilanSolusi = `
                                <div class="mt-2 p-2 rounded" style="background-color: #f8f9fa; border-left: 3px solid #2ecc71; font-size: 12px; color: #2c3e50;">
                                    <strong>💡 Solusi IT:</strong> ${ticket.solution_note}
                                </div>
                            `;
                            }

                            tbody.innerHTML += `
                                <tr>
                                    <td class="px-4 fw-bold text-secondary">#TK-${ticket.id}</td>
                                    <td>
                                        <span class="fw-bold">${ticket.title}</span>
                                        ${tampilanSolusi} </td>
                                    <td>${ticket.created_at}</td>
                                    <td>
                                        <span class="badge ${badgeClass} me-2">${ticket.status}</span>
                                        ${aksiTombol}
                                    </td>
                                </tr>
                            `;
                        });
                    }
                })
                .catch(error => {
                    console.error('Gagal mengambil data:', error);
                    document.getElementById('ticketTableBody').innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Terjadi kesalahan saat memuat data.</td></tr>';
                });
        });
    </script>
</body>

</html>