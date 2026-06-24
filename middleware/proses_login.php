<?php
session_start();
require_once __DIR__ . '/../database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // 1. Cari user berdasarkan username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $currentTime = date('Y-m-d H:i:s');

            // 2. CEK APAKAH AKUN SEDANG DIKUNCI
            if ($user['lockout_time'] && strtotime($user['lockout_time']) > strtotime($currentTime)) {
                $sisaWaktu = strtotime($user['lockout_time']) - strtotime($currentTime);
                $menit = ceil($sisaWaktu / 60);
                echo "<script>alert('Akun Anda dikunci sementara karena 5 kali salah password. Silakan coba $menit menit lagi.'); window.location.href='../login.php';</script>";
                exit;
            }

            // 3. CEK APAKAH AKUN DI SUSPEND
            if ($user['is_active'] == 0) {
                echo "<script>alert('Akun Anda sudah dinonaktifkan. Silakan hubungi Admin.'); window.location.href='../login.php';</script>";
                exit;
            }

            // 4. JIKA PASSWORD BENAR
            if (password_verify($password, $user['password'])) {

                // Reset hitungan salah karena login sukses
                $stmtReset = $pdo->prepare("UPDATE users SET login_attempts = 0, lockout_time = NULL WHERE id = :id");
                $stmtReset->execute(['id' => $user['id']]);

                // Set Sesi
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['fullname']  = $user['fullname'];
                $_SESSION['role']      = $user['role'];

                session_write_close();

                // Redirect sesuai role
                if (strtolower($user['role']) === 'admin') {
                    echo "<script>alert('Login Berhasil sebagai Admin!'); window.location.href='../admin/dashboard.php';</script>";
                } else {
                    echo "<script>alert('Login Berhasil!'); window.location.href='../index.php';</script>";
                }
                exit;
            } else {
                // 5. JIKA PASSWORD SALAH
                $newAttempts = $user['login_attempts'] + 1;

                if ($newAttempts >= 5) {
                    // Set waktu kunci: Kunci selama 10 menit ke depan
                    $lockoutUntil = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                    $stmtLock = $pdo->prepare("UPDATE users SET login_attempts = :attempts, lockout_time = :locktime WHERE id = :id");
                    $stmtLock->execute([
                        'attempts' => $newAttempts,
                        'locktime' => $lockoutUntil,
                        'id'       => $user['id']
                    ]);
                    echo "<script>alert('Salah password ke-5 kali! Akun Anda dikunci selama 10 menit.'); window.location.href='../login.php';</script>";
                } else {
                    // Update hitungan salah biasa
                    $stmtUpdate = $pdo->prepare("UPDATE users SET login_attempts = :attempts WHERE id = :id");
                    $stmtUpdate = $stmtUpdate->execute([
                        'attempts' => $newAttempts,
                        'id'       => $user['id']
                    ]);
                    $sisaKesempatan = 5 - $newAttempts;
                    echo "<script>alert('Password salah! Sisa kesempatan: $sisaKesempatan kali.'); window.location.href='../login.php';</script>";
                }
                exit;
            }
        } else {
            // Username tidak terdaftar
            echo "<script>alert('Username tidak ditemukan.'); window.location.href='../login.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: ../login.php");
    exit;
}
