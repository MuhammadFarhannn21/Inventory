<?php
$host = 'localhost';
$dbname = 'inventory'; // Sesuaikan nama database
$user = 'root';        // Sesuaikan username
$pass = '';            // Sesuaikan password

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     die("Koneksi Gagal: " . $e->getMessage());
}
?>