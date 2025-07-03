<?php
include 'koneksi.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM barang";
if (!empty($search)) {
    $query .= " WHERE id LIKE '%$search%' OR nama LIKE '%$search%'";
}
$query .= " ORDER BY id ASC";

$result = mysqli_query($koneksi, $query);

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['nama'] . "</td>";
    echo "<td>" . $row['satuan'] . "</td>";
    echo "<td>
            <button class='btn btn-sm btn-warning edit-btn' data-id='" . $row['id'] . "'>
                <i class='fas fa-edit'></i> Edit
            </button>
            <button class='btn btn-sm btn-danger delete-btn' data-id='" . $row['id'] . "'>
                <i class='fas fa-trash'></i> Hapus
            </button>
          </td>";
    echo "</tr>";
}

if (mysqli_num_rows($result) == 0) {
    echo "<tr><td colspan='4' class='text-center'>Tidak ada data ditemukan</td></tr>";
}
?> 