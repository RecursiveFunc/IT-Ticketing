<?php
// Melacak posisi folder 'database', lalu naik satu tingkat ke folder 'app_ticketing'
define('BASE_PATH', dirname(__DIR__) . '/');

$host     = "localhost";
$database = "it_support";
$username = "root"; // default (root)
$password = "";     // deafult ()

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    // Mengatur error mode ke exception untuk keamanan dan debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>