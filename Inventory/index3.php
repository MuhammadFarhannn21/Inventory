<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barang CRUD AJAX</title>
  <link rel="stylesheet" href="style.css">
  <script src="jquery.js"></script>
  <script src="script.js" defer></script>
</head>
<body>
  <nav>
    <ul>
      <li><a href="#">Dashboard</a></li>
      <li><a href="#">Barang</a></li>
    </ul>
  </nav>

  <div id="content">
    <h1>Data Barang</h1>
    <button id="openModal">Tambah Barang</button>
<input type="text" id="searchInput" placeholder="Cari berdasarkan ID atau Nama..." style="margin-bottom: 10px; padding: 5px; width: 300px;">
    <button id="searchBtn">Cari</button>
    <button id="resetBtn">Reset</button>

    <table id="tabelBarang">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Satuan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <!-- Data dimuat lewat Ajax -->
      </tbody>
    </table>
  </div>

  <div id="modalTambah" class="modal" style="display: none;">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2 id="modalTitle">Tambah Barang</h2>
      <form id="formBarang">
        <input type="hidden" name="id" id="id">
        <label>Nama:</label>
        <input type="text" name="nama" id="nama" required><br>
        <label>Satuan:</label>
        <input type="text" name="satuan" id="satuan" required><br>
        <label>Kategori:</label>
        <input type="text" name="kategori" id="kategori" required><br><br>
        <input type="submit" id="submitBtn" value="Tambah">
      </form>
    </div>
  </div>
</body>
</html>