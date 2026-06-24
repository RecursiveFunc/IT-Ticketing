<?php
session_start();

// Hapus semua data di dalam session
session_unset();
session_destroy();

// Pindahkan user kembali ke halaman login
header("Location: ../login.php");
exit;
?>