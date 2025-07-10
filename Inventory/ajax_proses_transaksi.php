<?php
// ajax_proses_transaksi.php
header('Content-Type: application/json');
require 'koneksi.php';

$cart = json_decode(file_get_contents('php://input'), true);

if (empty($cart)) {
    http_response_code(400);
    echo json_encode(['message' => 'Keranjang kosong.']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    $nomor_transaksi = 'INV-' . time();
    $stmt_header = $pdo->prepare("INSERT INTO transaksi_header (nomor_transaksi) VALUES (?)");
    $stmt_header->execute([$nomor_transaksi]);
    $id_transaksi = $pdo->lastInsertId();

    $stmt_detail = $pdo->prepare("INSERT INTO transaksi_detail (id_transaksi, id_barang, jumlah, harga_jual_saat_transaksi, harga_supplier_saat_transaksi) VALUES (?, ?, ?, ?, ?)");
    $stmt_update_stok = $pdo->prepare("UPDATE barang SET stok = stok - ? WHERE id = ?");
    $stmt_get_barang = $pdo->prepare("SELECT harga_jual, harga_supplier, stok FROM barang WHERE id = ?");

    foreach ($cart as $item) {
        $stmt_get_barang->execute([$item['id']]);
        $barang = $stmt_get_barang->fetch();

        if (!$barang || $barang['stok'] < $item['jumlah']) {
            throw new Exception('Stok tidak mencukupi untuk barang ID: ' . $item['id']);
        }
        $stmt_detail->execute([$id_transaksi, $item['id'], $item['jumlah'], $barang['harga_jual'], $barang['harga_supplier']]);
        $stmt_update_stok->execute([$item['jumlah'], $item['id']]);
    }

    $pdo->commit();
    echo json_encode(['message' => 'Transaksi berhasil!', 'nomor_transaksi' => $nomor_transaksi]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(400);
    echo json_encode(['message' => $e->getMessage()]);
}
?>