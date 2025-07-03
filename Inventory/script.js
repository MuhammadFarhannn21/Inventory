$(document).ready(function () {
  loadData();
  // Function to load data from the server
  // and populate the table
  function loadData(keyword = '') {
    $.get('data.php', { keyword: keyword }, function (data) {
      $('#tabelBarang tbody').html(data);
    });
  }
  // Live search
  $('#searchInput').on('keyup', function () {
    const keyword = $(this).val();
    loadData(keyword);
  });

  $('#formBarang').on('submit', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();
    const actionUrl = $('#id').val() ? 'ubah.php' : 'simpan.php';

    $.post(actionUrl, formData, function (response) {
      alert(response);
      $('#modalTambah').hide();
      loadData();
    });
  });

  $(document).on('click', '.hapusBtn', function () {
    if (confirm('Yakin ingin menghapus data ini?')) {
      const id = $(this).data('id');
      $.ajax({
        url: 'hapus.php',
        type: 'POST',
        data: { id: id },
        success: function (res) {
          alert(res);
          loadData();
        },
        error: function (xhr, status, error) {
          alert('Error: ' + error);
        }
      });
    }
  });

  $(document).on('click', '.editBtn', function () {
    $('#modalTitle').text('Ubah Barang');
    $('#submitBtn').val('Ubah');
    $('#id').val($(this).data('id'));
    $('#nama').val($(this).data('nama'));
    $('#satuan').val($(this).data('satuan'));
    $('#kategori').val($(this).data('kategori'));
    $('#modalTambah').show();
  });

  $('#openModal').on('click', function () {
    $('#modalTitle').text('Tambah Barang');
    $('#submitBtn').val('Tambah');
    $('#formBarang')[0].reset();
    $('#id').val('');
    $('#modalTambah').show();
  });

  $('.close').on('click', function () {
    $('#modalTambah').hide();
  });
});
// Function to format currency