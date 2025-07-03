<?php
$conn = new mysqli('localhost', 'root', '', 'barang_db');

$keyword = $_GET['keyword'] ?? '';
$keyword = $conn->real_escape_string($keyword);

$sql = "SELECT * FROM barang";
if (!empty($keyword)) {
  $sql .= " WHERE id LIKE '%$keyword%' OR nama LIKE '%$keyword%'";
}
$stmt = $conn->prepare($sql);
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>
      <td>{$row['nama']}</td>
      <td>{$row['satuan']}</td>
      <td>{$row['kategori']}</td>
      <td>
        <a href='#' class='editBtn' data-id='{$row['id']}' data-nama='{$row['nama']}' data-satuan='{$row['satuan']}' data-kategori='{$row['kategori']}'>Edit</a>
        <a href='#' class='hapusBtn' data-id='{$row['id']}'>Hapus</a>
      </td>
    </tr>";
  }
} else {
  echo "<tr><td colspan='4'>Data tidak ditemukan.</td></tr>";
}
?>
<script>
  document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      const nama = this.getAttribute('data-nama');
      const satuan = this.getAttribute('data-satuan');
      const kategori = this.getAttribute('data-kategori');

      document.querySelector('#id').value = id;
      document.querySelector('#nama').value = nama;
      document.querySelector('#satuan').value = satuan;
      document.querySelector('#kategori').value = kategori;

      document.querySelector('#modalEdit').style.display = 'block';
    });
  });

  document.querySelectorAll('.hapusBtn').forEach(btn => {
    btn.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        fetch('UTS_P/hapus.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'id=' + id
        })
        .then(response => response.text())
        .then(data => {
          alert(data);
          location.reload();
        });
      }
    });
  });