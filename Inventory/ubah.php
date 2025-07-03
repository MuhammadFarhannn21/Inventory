<?php
$conn = new mysqli('localhost', 'root', '', 'barang_db');

$id = $_POST['id'];
$nama = $_POST['nama'];
$satuan = $_POST['satuan'];
$kategori = $_POST['kategori'];

$sql = "UPDATE barang SET nama='$nama', satuan='$satuan', kategori='$kategori' WHERE id=$id";

echo $conn->query($sql) ? 'Data berhasil diubah' : 'Gagal mengubah data';
?>
