<?php
$conn = new mysqli('localhost', 'root', '', 'barang_db');

$nama = $_POST['nama'];
$satuan = $_POST['satuan'];
$kategori = $_POST['kategori'];

$sql = "INSERT INTO barang (nama, satuan, kategori) VALUES ('$nama', '$satuan', '$kategori')";

echo $conn->query($sql) ? 'Data berhasil ditambahkan' : 'Gagal menambahkan data';
?>
