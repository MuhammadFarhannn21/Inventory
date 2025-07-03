<?php
$conn = new mysqli('localhost', 'root', '', 'barang_db');

$id = $_POST['id'];
$sql = "DELETE FROM barang WHERE id=$id";

echo $conn->query($sql) ? 'Data berhasil dihapus' : 'Gagal menghapus data';
?>
