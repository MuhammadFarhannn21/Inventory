<?php
// index.php (SEBELUMNYA dashboard.php)
require 'koneksi.php';

// Ambil data ringkasan untuk kartu informasi (KPI)
$total_barang = $pdo->query("SELECT COUNT(*) FROM barang")->fetchColumn();
$transaksi_hari_ini = $pdo->query("SELECT COUNT(*) FROM transaksi_header WHERE DATE(tanggal_transaksi) = CURDATE()")->fetchColumn();
$stok_menipis = $pdo->query("SELECT COUNT(*) FROM barang WHERE stok < 10")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Aplikasi Kasir</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .access-card { 
            display: block;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .access-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            text-decoration: none;
            color: #007bff;
        }
        .access-card h4 { margin-top: 10px; }
    </style>
</head>
<body>
<div class="container mt-5">
    <header class="text-center mb-5">
        <h1>Dashboard Aplikasi Kasir</h1>
        <p class="text-muted">Selamat datang! Silakan pilih menu di bawah ini.</p>
    </header>

    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Jenis Barang</h5>
                    <p class="card-text" style="font-size: 2rem;"><?= $total_barang ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Transaksi Hari Ini</h5>
                    <p class="card-text" style="font-size: 2rem;"><?= $transaksi_hari_ini ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Stok Akan Habis</h5>
                    <p class="card-text" style="font-size: 2rem;"><?= $stok_menipis ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <a href="kelola_barang.php" class="access-card">
                <span style="font-size: 3rem;">📦</span>
                <h4>Kelola Barang</h4>
            </a>
        </div>
        <div class="col-md-4">
            <a href="kasir.php" class="access-card">
                <span style="font-size: 3rem;">🛒</span>
                <h4>Buat Transaksi</h4>
            </a>
        </div>
        <div class="col-md-4">
            <a href="riwayat_transaksi.php" class="access-card">
                <span style="font-size: 3rem;">📜</span>
                <h4>Riwayat Transaksi</h4>
            </a>
        </div>
    </div>
</div>
</body>
</html>