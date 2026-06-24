<?php
session_start();
require_once __DIR__ . '/../database/koneksi.php';

// Proteksi Admin
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// AKSID 1: TOGGLE / EDIT ROLE
if (isset($_GET['action']) && $_GET['action'] === 'toggle_role') {
    $u_id = $_GET['id'];
    $current_role = $_GET['current'];
    $new_role = ($current_role === 'admin') ? 'employee' : 'admin';

    $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->execute(['role' => $new_role, 'id' => $u_id]);
    header("Location: users.php");
    exit;
}

// AKSI 2: RESET PASSWORD (Default menjadi: password123)
if (isset($_GET['action']) && $_GET['action'] === 'reset_password') {
    $u_id = $_GET['id'];
    $default_password_hash = password_hash('password123', PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
    $stmt->execute(['password' => $default_password_hash, 'id' => $u_id]);
    echo "<script>alert('Password berhasil dureset menjadi: password123'); window.location.href='users.php';</script>";
    exit;
}

// AKSI 3: SUSPEND USER
if (isset($_GET['action']) && $_GET['action'] === 'suspend_user') {
    $u_id = (int)$_GET['id'];
    $current_admin_id = $_SESSION['user_id'];

    // Keamanan: Cegah Admin non-aktif dirinya sendiri secara tidak sengaja
    if ($u_id === $current_admin_id) {
        echo "<script>alert('Gagal! Anda tidak bisa menonaktifkan akun Anda sendiri yang sedang digunakan.'); window.location.href='users.php';</script>";
        exit;
    }

    try {
        // Eksekusi non-aktif user
        $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = :id");
        $stmt->execute(['id' => $u_id]);

        echo "<script>alert('User berhasil dinonaktifkan!'); window.location.href='users.php';</script>";
        exit;
    } catch (PDOException $e) {
        // Menangani jika user gagal disuspend
        echo "<script>alert('Gagal menonaktifkan User ini!'); window.location.href='users.php';</script>";
        exit;
    }
}

// Tarik data seluruh user
$users = $pdo->query("SELECT id, username, fullname, role FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Manajemen User - Admin IT</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
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

        .btn-suspend {
            background-color: #e74c3c;
        }

        /* Style Tabel */
        .table-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin-top: 15px;
        }

        th,
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f8f9fa;
            color: #34495e;
        }

        .btn-action {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 13px;
            color: white;
            display: inline-block;
            margin-right: 5px;
        }

        .btn-role {
            background-color: #3498db;
        }

        .btn-reset {
            background-color: #e67e22;
        }

        .badge-admin {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-emp {
            background: #f5f5f5;
            color: #616161;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header style="margin-bottom: 25px;">
            <h1>Manajemen Pengguna Aplikasi</h1>
            <p>Lihat daftar akun terdaftar, ubah hak akses (Role), dan atur ulang kata sandi yang terlupa</p>
        </header>

        <section class="table-section">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Hak Akses (Role)</th>
                        <th>Tindakan Pengurus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>#USR-<?php echo $user['id']; ?></td>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($user['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td>
                                <span class="<?php echo $user['role'] === 'admin' ? 'badge-admin' : 'badge-emp'; ?>">
                                    <?php echo strtoupper($user['role']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="users.php?action=toggle_role&id=<?php echo $user['id']; ?>&current=<?php echo $user['role']; ?>"
                                    class="btn-action btn-role"
                                    onclick="return confirm('Ubah role user ini?')">
                                    <i class="bi bi-arrow-repeat"></i> Ubah Role
                                </a>

                                <a href="users.php?action=reset_password&id=<?php echo $user['id']; ?>"
                                    class="btn-action btn-reset"
                                    onclick="return confirm('Reset password user ini menjadi password123?')">
                                    <i class="bi bi-key-fill"></i> Reset Pass
                                </a>

                                <a href="users.php?action=suspend_user&id=<?php echo $user['id']; ?>"
                                    class="btn-action btn-suspend"
                                    style="background-color: #e74c3c;"
                                    onclick="return confirm('Apakah Anda yakin ingin SUSPEND user ini?')">
                                    <i class="bi bi-trash-fill"></i> Suspend
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>

</html>