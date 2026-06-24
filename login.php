<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem IT Support - Login & Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 12px;
        }
        .brand-icon {
            font-size: 2.5rem;
            color: #0d6efd;
        }
        .nav-pills .nav-link {
            color: #6c757d;
            font-weight: 500;
        }
        .nav-pills .nav-link.active {
            background-color: #0d6efd;
        }
    </style>
</head>
<body>

<div class="card auth-card shadow-sm p-4">
    <div class="card-body">
        <div class="text-center mb-4">
            <div class="brand-icon mb-1">
                <i class="bi bi-cpu-fill"></i>
            </div>
            <h4 class="fw-bold text-dark">IT Support Portal</h4>
        </div>

        <ul class="nav nav-pills nav-fill mb-4 p-1 bg-light rounded" id="authTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-pane" type="button" role="tab" aria-controls="login-pane" aria-selected="true">Masuk</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-pane" type="button" role="tab" aria-controls="register-pane" aria-selected="false">Daftar Akun</button>
            </li>
        </ul>

        <div class="tab-content" id="authTabContent">
            
            <div class="tab-pane fade show active" id="login-pane" role="tabpanel" aria-labelledby="login-tab" tabindex="0">
                <form action="/app_ticketing/middleware/proses_login.php" method="POST">
                    <div class="mb-3">
                        <label for="login-username" class="form-label text-secondary small fw-bold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" id="login-username" class="form-control" placeholder="Masukkan username" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="login-password" class="form-label text-secondary small fw-bold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="login-password" class="form-control" placeholder="Masukkan password" required>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2 fw-bold">Masuk</button>
                    </div>
                </form>
            </div>

            <div class="tab-pane fade" id="register-pane" role="tabpanel" aria-labelledby="register-tab" tabindex="0">
                <form action="/app_ticketing/middleware/proses_register.php" method="POST">
                    
                    <div class="mb-3">
                        <label for="reg-fullname" class="form-label text-secondary small fw-bold">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="bi bi-card-text"></i></span>
                            <input type="text" name="fullname" id="reg-fullname" class="form-control" placeholder="Contoh: John Doe" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reg-username" class="form-label text-secondary small fw-bold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" id="reg-username" class="form-control" placeholder="Buat username unik" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="reg-password" class="form-label text-secondary small fw-bold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="reg-password" class="form-control" placeholder="Buat password aman" required>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success py-2 fw-bold">Daftar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>