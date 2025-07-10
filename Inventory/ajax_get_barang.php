<?php
// ajax_get_barang.php
header('Content-Type: application/json');
require 'koneksi.php';

$keyword = $_GET['keyword'] ?? '';

if (empty($keyword)) {
    echo json_encode(null);
    exit;
}

$sql = "SELECT id, nama, harga_jual, stok, kode_barang 
        FROM barang 
        WHERE kode_barang = ? OR nama LIKE ? OR id = ?
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([$keyword, "%$keyword%", $keyword]);
$barang = $stmt->fetch();

echo json_encode($barang ?: null);
?>