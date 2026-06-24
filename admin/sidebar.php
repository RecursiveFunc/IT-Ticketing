<div class="sidebar">
    <div class="sidebar-header">
        <h2>IT Support System</h2>
        <p style="color: #95a5a6; font-size: 12px;">Admin Panel</p>
    </div>
    <div class="sidebar-user">
        <i class="bi bi-person-circle me-1"></i> Halo, <span class="name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
    </div>
    <ul class="sidebar-menu">
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'tickets.php' ? 'active' : ''; ?>">
            <a href="tickets.php"><i class="bi bi-ticket-perforated-fill"></i> Daftar Pekerjaan</a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
            <a href="users.php"><i class="bi bi-people-fill"></i> Manajemen User</a>
        </li>
        <li>
            <a href="../middleware/proses_logout.php" class="btn-logout"><i class="bi bi-box-arrow-left"></i> Keluar</a>
        </li>
    </ul>
</div> 