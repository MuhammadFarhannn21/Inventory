<?php
require 'koneksi.php';

// Inisialisasi variabel
$pesan = '';
$barang_diedit = null;
$search_query = $_GET['q'] ?? ''; // Ambil query pencarian dari URL

// --- LOGIKA HAPUS BARANG ---
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_hapus = $_GET['id'] ?? 0;
    try {
        $stmt = $pdo->prepare("DELETE FROM barang WHERE id = ?");
        $stmt->execute([$id_hapus]);
        header("Location: kelola_barang.php?status=sukses_hapus");
        exit();
    } catch (PDOException $e) {
        header("Location: kelola_barang.php?status=gagal_hapus");
        exit();
    }
}

// --- LOGIKA SIMPAN (TAMBAH / UPDATE) BARANG ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $kode_barang = $_POST['kode_barang'] ?? null;
    $nama = $_POST['nama'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $harga_supplier = $_POST['harga_supplier'] ?? 0;
    $harga_jual = $_POST['harga_jual'] ?? 0;
    $stok = $_POST['stok'] ?? 0;
    $satuan = $_POST['satuan'] ?? '';

    // Logika untuk UPDATE BARANG
    if (isset($_POST['update_barang'])) {
        try {
            $sql = "UPDATE barang SET kode_barang=?, nama=?, satuan=?, kategori=?, harga_supplier=?, harga_jual=?, stok=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$kode_barang, $nama, $satuan, $kategori, $harga_supplier, $harga_jual, $stok, $id]);
            header("Location: kelola_barang.php?status=sukses_update");
            exit();
        } catch (PDOException $e) {
            $pesan = '<div class="alert alert-danger">Gagal memperbarui barang: ' . $e->getMessage() . '</div>';
        }
    } 
    // Logika untuk TAMBAH BARANG BARU
    elseif (isset($_POST['tambah_barang'])) {
        if (!empty($nama) && !empty($kode_barang)) {
            try {
                $sql = "INSERT INTO barang (kode_barang, nama, satuan, kategori, harga_supplier, harga_jual, stok) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$kode_barang, $nama, $satuan, $kategori, $harga_supplier, $harga_jual, $stok]);
                header("Location: kelola_barang.php?status=sukses_tambah");
                exit();
            } catch (PDOException $e) {
                $pesan = ($e->errorInfo[1] == 1062) 
                    ? '<div class="alert alert-danger">Gagal: Kode Barang sudah ada.</div>'
                    : '<div class="alert alert-danger">Gagal: ' . $e->getMessage() . '</div>';
            }
        } else {
            $pesan = '<div class="alert alert-warning">Kode Barang dan Nama Barang wajib diisi.</div>';
        }
    }
}

// --- LOGIKA UNTUK MENAMPILKAN FORM EDIT ---
if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $id_edit = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM barang WHERE id = ?");
    $stmt->execute([$id_edit]);
    $barang_diedit = $stmt->fetch();
}

// Ambil notifikasi status dari URL
if (isset($_GET['status'])) {
    $status_map = [
        'sukses_tambah' => '<div class="alert alert-success">Barang baru berhasil ditambahkan!</div>',
        'sukses_update' => '<div class="alert alert-success">Data barang berhasil diperbarui!</div>',
        'sukses_hapus' => '<div class="alert alert-success">Data barang berhasil dihapus!</div>'
    ];
    $pesan = $status_map[$_GET['status']] ?? '';
}

// --- LOGIKA PENCARIAN & PENGAMBILAN DATA BARANG ---
$sql_select = "SELECT * FROM barang";
$params = [];
if (!empty($search_query)) {
    $sql_select .= " WHERE nama LIKE ? OR kode_barang LIKE ?";
    $params[] = '%' . $search_query . '%';
    $params[] = '%' . $search_query . '%';
}
$sql_select .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql_select);
$stmt->execute($params);
$daftar_barang = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Barang</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📦 Kelola Data Barang</h2>
            <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>
        
        <?php echo $pesan; ?>

        <div class="card mb-4">
            <div class="card-header"><strong><?= $barang_diedit ? 'Form Edit Barang' : 'Form Tambah Barang Baru' ?></strong></div>
            <div class="card-body">
                <form method="POST" action="kelola_barang.php">
                    <input type="hidden" name="id" value="<?= $barang_diedit['id'] ?? '' ?>">
                    <div class="form-group">
                        <label for="kode_barang">Kode Barang (Barcode/QR)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="kode_barang" id="kode_barang_input" value="<?= htmlspecialchars($barang_diedit['kode_barang'] ?? '') ?>" required>
                            <div class="input-group-append">
                                <button class="btn btn-info" type="button" data-toggle="modal" data-target="#scannerModal">📷 Scan</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6"><label>Nama Barang</label><input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($barang_diedit['nama'] ?? '') ?>" required></div>
                        <div class="form-group col-md-6"><label>Kategori</label><input type="text" class="form-control" name="kategori" value="<?= htmlspecialchars($barang_diedit['kategori'] ?? '') ?>" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3"><label>Harga Supplier</label><input type="number" class="form-control" name="harga_supplier" value="<?= htmlspecialchars($barang_diedit['harga_supplier'] ?? '') ?>" required></div>
                        <div class="form-group col-md-3"><label>Harga Jual</label><input type="number" class="form-control" name="harga_jual" value="<?= htmlspecialchars($barang_diedit['harga_jual'] ?? '') ?>" required></div>
                        <div class="form-group col-md-3"><label>Stok</label><input type="number" class="form-control" name="stok" value="<?= htmlspecialchars($barang_diedit['stok'] ?? '') ?>" required></div>
                        <div class="form-group col-md-3"><label>Satuan</label><input type="text" class="form-control" name="satuan" value="<?= htmlspecialchars($barang_diedit['satuan'] ?? '') ?>" required></div>
                    </div>

                    <?php if ($barang_diedit): ?>
                        <button type="submit" name="update_barang" class="btn btn-primary">Update Barang</button>
                        <a href="kelola_barang.php" class="btn btn-light">Batal</a>
                    <?php else: ?>
                        <button type="submit" name="tambah_barang" class="btn btn-primary">Tambah Barang</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Daftar Stok Barang</h3>
            <form action="kelola_barang.php" method="GET" class="form-inline">
                <div class="input-group">
                    <input type="text" class="form-control" name="q" placeholder="Cari barang..." value="<?= htmlspecialchars($search_query) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </div>
            </form>
        </div>
        
        <table class="table table-bordered table-striped">
            <thead class="thead-dark"><tr><th>ID</th><th>Kode Barang</th><th>Nama</th><th>Stok</th><th>Harga Jual</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if (empty($daftar_barang)): ?>
                    <tr><td colspan="6" class="text-center">Barang tidak ditemukan.</td></tr>
                <?php else: ?>
                    <?php foreach ($daftar_barang as $barang): ?>
                    <tr>
                        <td><?= htmlspecialchars($barang['id']) ?></td>
                        <td><?= htmlspecialchars($barang['kode_barang']) ?></td>
                        <td><?= htmlspecialchars($barang['nama']) ?></td>
                        <td><?= htmlspecialchars($barang['stok']) ?></td>
                        <td>Rp <?= number_format($barang['harga_jual']) ?></td>
                        <td>
                            <a href="kelola_barang.php?aksi=edit&id=<?= $barang['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="kelola_barang.php?aksi=hapus&id=<?= $barang['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus barang ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="scannerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Pindai Barcode/QR Code</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                <div class="modal-body"><div id="reader" style="width:100%;"></div></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        // Script untuk scanner tidak berubah
        const html5QrCode = new Html5Qrcode("reader");
        const onScanSuccess = (decodedText) => {
            document.getElementById('kode_barang_input').value = decodedText;
            $('#scannerModal').modal('hide');
        };
        $('#scannerModal').on('shown.bs.modal', function () {
            html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 250, height: 250 } }, onScanSuccess)
            .catch(err => console.log('Gagal memulai scanner.', err));
        });
        $('#scannerModal').on('hidden.bs.modal', function () {
            html5QrCode.stop().catch(err => console.log('Gagal menghentikan scanner.', err));
        });
    </script>
</body>
</html>