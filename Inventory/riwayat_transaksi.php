<?php
require 'koneksi.php';

$sql = "SELECT 
            th.id_transaksi,
            th.nomor_transaksi,
            th.tanggal_transaksi,
            b.nama AS nama_barang,
            b.kode_barang,
            td.jumlah,
            td.harga_jual_saat_transaksi
        FROM transaksi_header th
        JOIN transaksi_detail td ON th.id_transaksi = td.id_transaksi
        JOIN barang b ON td.id_barang = b.id
        ORDER BY th.tanggal_transaksi DESC";
$stmt = $pdo->query($sql);
$semua_detail = $stmt->fetchAll();

$transaksi_terkelompok = [];
foreach ($semua_detail as $detail) {
    $transaksi_terkelompok[$detail['id_transaksi']]['header'] = [
        'nomor_transaksi' => $detail['nomor_transaksi'],
        'tanggal_transaksi' => $detail['tanggal_transaksi']
    ];
    $transaksi_terkelompok[$detail['id_transaksi']]['items'][] = $detail;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .card-header .btn-link { color: #343a40; text-decoration: none; }
        .card-header .btn-link:hover { text-decoration: none; }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📜 Riwayat Semua Transaksi</h2>
            <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>

        <?php if (empty($transaksi_terkelompok)): ?>
            <div class="alert alert-info text-center">Belum ada riwayat transaksi yang tercatat.</div>
        <?php else: ?>
            <div class="accordion" id="accordionRiwayat">
                <?php foreach ($transaksi_terkelompok as $id_transaksi => $transaksi): ?>
                    <div class="card mb-2 shadow-sm">
                        <div class="card-header" id="heading-<?= $id_transaksi ?>">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left d-flex justify-content-between" type="button" data-toggle="collapse" data-target="#collapse-<?= $id_transaksi ?>">
                                    <span><strong>No:</strong> <?= htmlspecialchars($transaksi['header']['nomor_transaksi']) ?></span>
                                    <span class="text-muted"><?= date('d F Y, H:i', strtotime($transaksi['header']['tanggal_transaksi'])) ?></span>
                                </button>
                            </h2>
                        </div>

                        <div id="collapse-<?= $id_transaksi ?>" class="collapse" data-parent="#accordionRiwayat">
                            <div class="card-body">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kode Barang</th>
                                            <th>Nama Barang</th>
                                            <th class="text-center">Jumlah</th>
                                            <th class="text-right">Harga Satuan</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $total_transaksi = 0;
                                        foreach ($transaksi['items'] as $item): 
                                            $subtotal = $item['jumlah'] * $item['harga_jual_saat_transaksi'];
                                            $total_transaksi += $subtotal;
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['kode_barang']) ?></td>
                                            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($item['jumlah']) ?></td>
                                            <td class="text-right">Rp <?= number_format($item['harga_jual_saat_transaksi']) ?></td>
                                            <td class="text-right">Rp <?= number_format($subtotal) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-info font-weight-bold">
                                            <td colspan="4" class="text-right">Total Transaksi</td>
                                            <td class="text-right">Rp <?= number_format($total_transaksi) ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>