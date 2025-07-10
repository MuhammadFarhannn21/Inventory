<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Kasir</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f7f6; }
        #scanner-container { width: 100%; border-radius: 8px; overflow: hidden; }
        .cart-container { display: flex; flex-direction: column; height: 100%; }
        .cart-items-table { flex-grow: 1; }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">🛒 Halaman Kasir</h2>
            <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>
        <div class="row">
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <strong>Pindai atau Cari Barang</strong>
                    </div>
                    <div class="card-body">
                        <div id="scanner-container" class="mb-3"></div>
                        <input type="text" id="searchInput" class="form-control" placeholder="Scan Barcode atau Ketik Nama/ID...">
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="cart-container">
                    <div class="card shadow-sm cart-items-table mb-3">
                        <div class="card-header">
                            <strong>Keranjang Belanja</strong>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th style="width: 100px;">Jumlah</th>
                                        <th class="text-right">Subtotal</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Total: <span id="total-price" class="font-weight-bold">Rp 0</span></h3>
                            <button id="btn-bayar" class="btn btn-success btn-lg">Proses Pembayaran</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">✅ Pembayaran Berhasil</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                <div class="modal-body">
                    <h6 id="nomorTransaksi"></h6>
                    <p>Rincian transaksi:</p>
                    <table class="table table-sm">
                        <thead><tr><th>Barang</th><th>Jumlah</th><th>Subtotal</th></tr></thead>
                        <tbody id="strukItems"></tbody>
                    </table>
                    <p class="text-right font-weight-bold" id="strukTotal"></p>
                </div>
                <div class="modal-footer">
                    <a href="index.php" class="btn btn-primary">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        // Seluruh kode JavaScript Anda tetap sama, tidak perlu diubah.
        // Cukup salin-tempel seluruh kode JavaScript dari file kasir.php Anda sebelumnya ke sini.
        document.addEventListener('DOMContentLoaded', () => {
            let cart = [];
            const searchInput = document.getElementById('searchInput');

            const html5QrCode = new Html5Qrcode("scanner-container");
            const qrCodeSuccess = (decodedText) => {
                searchInput.value = decodedText;
                handleSearch();
            };
            html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, qrCodeSuccess);

            const handleSearch = async () => {
                const keyword = searchInput.value;
                if (!keyword) return;
                const response = await fetch(`ajax_get_barang.php?keyword=${keyword}`);
                const barang = await response.json();
                if (barang) {
                    addItemToCart(barang);
                    searchInput.value = '';
                } else {
                    alert('Barang tidak ditemukan!');
                }
            };
            searchInput.addEventListener('change', handleSearch);

            const addItemToCart = (item) => {
                const existing = cart.find(cartItem => cartItem.id === item.id);
                if (existing) {
                    if (existing.jumlah < item.stok) existing.jumlah++;
                    else alert('Stok tidak mencukupi!');
                } else {
                    if (item.stok > 0) cart.push({ ...item, jumlah: 1 });
                    else alert('Stok barang habis!');
                }
                renderCart();
            };

            const renderCart = () => {
                const cartBody = document.getElementById('cart-items');
                const totalPriceEl = document.getElementById('total-price');
                cartBody.innerHTML = '';
                let total = 0;
                if(cart.length === 0){
                    cartBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Keranjang masih kosong</td></tr>';
                }
                cart.forEach((item, index) => {
                    const subtotal = item.jumlah * item.harga_jual;
                    total += subtotal;
                    const row = `<tr><td>${item.nama}</td><td><input type="number" value="${item.jumlah}" min="1" max="${item.stok}" class="form-control form-control-sm" onchange="updateJumlah(${index}, this.value)"></td><td class="text-right">Rp ${subtotal.toLocaleString()}</td><td class="text-center"><button class="btn btn-danger btn-sm" onclick="removeItem(${index})">&times;</button></td></tr>`;
                    cartBody.innerHTML += row;
                });
                totalPriceEl.textContent = `Rp ${total.toLocaleString()}`;
            };
            
            window.updateJumlah = (index, jumlah) => {
                const newJumlah = parseInt(jumlah);
                if (newJumlah > 0 && newJumlah <= cart[index].stok) cart[index].jumlah = newJumlah;
                else alert(`Stok hanya ${cart[index].stok}`);
                renderCart();
            };
            window.removeItem = (index) => {
                cart.splice(index, 1);
                renderCart();
            };

            document.getElementById('btn-bayar').addEventListener('click', async () => {
                if (cart.length === 0) return alert('Keranjang belanja kosong.');
                const cartForStruk = [...cart]; 
                const response = await fetch('ajax_proses_transaksi.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(cart.map(item => ({ id: item.id, jumlah: item.jumlah })))
                });
                const result = await response.json();
                if (response.ok && result.message === 'Transaksi berhasil!') {
                    document.getElementById('nomorTransaksi').textContent = `Nomor Transaksi: ${result.nomor_transaksi}`;
                    const strukBody = document.getElementById('strukItems');
                    const strukTotalEl = document.getElementById('strukTotal');
                    strukBody.innerHTML = '';
                    let totalStruk = 0;
                    cartForStruk.forEach(item => {
                        const subtotal = item.jumlah * item.harga_jual;
                        totalStruk += subtotal;
                        strukBody.innerHTML += `<tr><td>${item.nama}</td><td>${item.jumlah}</td><td>Rp ${subtotal.toLocaleString()}</td></tr>`;
                    });
                    strukTotalEl.textContent = `Total: Rp ${totalStruk.toLocaleString()}`;
                    $('#successModal').modal('show');
                    cart = [];
                    renderCart();
                } else {
                    alert(`Error: ${result.message}`);
                }
            });
            renderCart(); // Panggil pertama kali untuk menampilkan pesan keranjang kosong
        });
    </script>
</body>
</html>