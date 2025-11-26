<?php
// products.php - VERSI PROFESIONAL DIPERBAIKI
include 'functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $data = [
                    'nama_produk' => $_POST['nama_produk'],
                    'harga' => $_POST['harga'],
                    'stok' => $_POST['stok'],
                    'kategori' => $_POST['kategori'],
                    'deskripsi' => $_POST['deskripsi']
                ];
                
                if (addProduct($data)) {
                    $message = "✅ Produk berhasil ditambahkan!";
                    $messageType = "success";
                } else {
                    $message = "❌ Gagal menambahkan produk!";
                    $messageType = "error";
                }
                break;
                
            case 'update':
                $produkID = $_POST['produk_id'];
                $data = [
                    'nama_produk' => $_POST['nama_produk'],
                    'harga' => $_POST['harga'],
                    'stok' => $_POST['stok'],
                    'kategori' => $_POST['kategori'],
                    'deskripsi' => $_POST['deskripsi']
                ];
                
                if (updateProduct($produkID, $data)) {
                    $message = "✅ Produk berhasil diperbarui!";
                    $messageType = "success";
                } else {
                    $message = "❌ Gagal memperbarui produk!";
                    $messageType = "error";
                }
                break;
                
            case 'delete':
                $produkID = $_POST['produk_id'];
                if (deleteProduct($produkID)) {
                    $message = "✅ Produk berhasil dihapus!";
                    $messageType = "success";
                } else {
                    $message = "❌ Gagal menghapus produk!";
                    $messageType = "error";
                }
                break;
        }
    }
}

$products = getProducts();
$categories = getCategories();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - KASIR PRO</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="professional-header">
            <div class="header-left">
                <h1>🛒 KASIR PRO</h1>
                <span class="store-info">Manajemen Produk - Sistem Kasir Modern</span>
            </div>
            <div class="header-right">
                <div class="datetime-display">
                    <span id="current-date"></span>
                    <span id="current-time"></span>
                </div>
                <div class="user-info">
                    <span>👤 Kasir: <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></strong></span>
                    <a href="logout.php" style="color: white; margin-left: 10px; text-decoration: none; font-size: 0.9rem; padding: 0.3rem 0.6rem; border: 1px solid rgba(255,255,255,0.3); border-radius: 4px;" 
                    onclick="return confirm('Yakin ingin logout?')">
                        🚪 Logout
                    </a>
                </div>
            </div>
        </header>
        
        <nav class="professional-nav">
            <a href="index.php">💳 Transaksi</a>
            <a href="products.php" class="nav-active">📦 Produk</a>
            <a href="customers.php">👥 Pelanggan</a>
            <a href="sales.php">📋 Riwayat</a>
            <a href="reports.php">📊 Laporan</a>
        </nav>
        
        <div class="main-content" style="padding: 2rem;">
            <?php if (isset($message)): ?>
                <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'error' ?> animate-slide-in">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="form-section animate-fade-in">
                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 2rem;">
                    <h2>Manajemen Produk</h2>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <span class="product-stock high">Total: <?= count($products) ?> Produk</span>
                        <button type="button" class="btn-primary" onclick="document.getElementById('product-form').scrollIntoView({behavior: 'smooth'})">
                            📦 Tambah Produk Baru
                        </button>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 400px 1fr; gap: 2rem;">
                    <!-- Form Tambah/Edit Produk -->
                    <div class="form-section" style="height: fit-content;">
                        <h3 id="form-title">Tambah Produk Baru</h3>
                        <form id="product-form" method="POST">
                            <input type="hidden" name="action" id="form-action" value="add">
                            <input type="hidden" name="produk_id" id="produk-id">
                            
                            <div class="form-group">
                                <label for="nama_produk">Nama Produk</label>
                                <input type="text" id="nama_produk" name="nama_produk" required 
                                       placeholder="Masukkan nama produk">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="harga">Harga (Rp)</label>
                                    <input type="number" id="harga" name="harga" min="0" step="100" required 
                                           placeholder="0">
                                </div>
                                
                                <div class="form-group">
                                    <label for="stok">Stok</label>
                                    <input type="number" id="stok" name="stok" min="0" required 
                                           placeholder="0">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="kategori">Kategori</label>
                                <select id="kategori" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach($categories as $category): ?>
                                        <option value="<?= htmlspecialchars($category) ?>">
                                            <?= htmlspecialchars($category) ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi Produk</label>
                                <textarea id="deskripsi" name="deskripsi" rows="3" 
                                          placeholder="Deskripsi produk (opsional)"></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-primary" id="submit-btn">
                                    ➕ Tambah Produk
                                </button>
                                <button type="button" class="btn-secondary" id="cancel-edit" style="display: none;">
                                    ❌ Batal Edit
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Daftar Produk -->
                    <div>
                        <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1.5rem;">
                            <h3>Daftar Produk</h3>
                            <div style="display: flex; gap: 0.5rem;">
                                <input type="text" id="search-products" placeholder="🔍 Cari produk..." 
                                       style="padding: 0.5rem 1rem; border: 1px solid #ddd; border-radius: 6px;">
                            </div>
                        </div>

                        <div class="products-grid" id="products-container">
                            <?php foreach($products as $product): 
                                $stockClass = $product['Stok'] == 0 ? 'low' : ($product['Stok'] < 10 ? 'medium' : 'high');
                            ?>
                                <div class="product-card animate-slide-in" data-product-name="<?= strtolower(htmlspecialchars($product['NamaProduk'])) ?>">
                                    <div class="product-card-header">
                                        <div>
                                            <div class="product-name"><?= htmlspecialchars($product['NamaProduk']) ?></div>
                                            <div class="product-price">Rp <?= number_format($product['Harga'], 0, ',', '.') ?></div>
                                        </div>
                                        <span class="product-stock <?= $stockClass ?>">
                                            Stok: <?= $product['Stok'] ?>
                                        </span>
                                    </div>
                                    
                                    <div class="product-category">
                                        <?= htmlspecialchars($product['Kategori']) ?>
                                    </div>
                                    
                                    <?php if (!empty($product['Deskripsi'])): ?>
                                        <div class="product-description">
                                            <?= htmlspecialchars($product['Deskripsi']) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="product-actions">
                                        <button class="btn-primary btn-edit" 
                                                data-id="<?= $product['ProdukID'] ?>"
                                                data-nama="<?= htmlspecialchars($product['NamaProduk']) ?>"
                                                data-harga="<?= $product['Harga'] ?>"
                                                data-stok="<?= $product['Stok'] ?>"
                                                data-kategori="<?= htmlspecialchars($product['Kategori']) ?>"
                                                data-deskripsi="<?= htmlspecialchars($product['Deskripsi'] ?? '') ?>">
                                            ✏️ Edit
                                        </button>
                                        <button class="btn-secondary btn-delete" data-id="<?= $product['ProdukID'] ?>" data-nama="<?= htmlspecialchars($product['NamaProduk']) ?>">
                                            🗑️ Hapus
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if (empty($products)): ?>
                                <div class="form-section" style="text-align: center; padding: 3rem;">
                                    <div style="font-size: 4rem; margin-bottom: 1rem;">📦</div>
                                    <h3 style="color: var(--secondary); margin-bottom: 0.5rem;">Belum Ada Produk</h3>
                                    <p style="color: var(--secondary);">Tambahkan produk pertama Anda untuk memulai</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div class="form-section" style="max-width: 400px; animation: bounceIn 0.3s ease-out;">
            <h3>🗑️ Konfirmasi Hapus</h3>
            <p id="delete-message">Apakah Anda yakin ingin menghapus produk ini?</p>
            <form id="delete-form" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="produk_id" id="delete-id">
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="cancel-delete">❌ Batal</button>
                    <button type="submit" class="btn-primary" style="background: var(--danger);">🗑️ Hapus Produk</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Product management functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Update date and time
            function updateDateTime() {
                const now = new Date();
                const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
                
                document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', optionsDate);
                document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID', optionsTime);
            }
            
            updateDateTime();
            setInterval(updateDateTime, 1000);
            
            const form = document.getElementById('product-form');
            const formTitle = document.getElementById('form-title');
            const formAction = document.getElementById('form-action');
            const produkId = document.getElementById('produk-id');
            const submitBtn = document.getElementById('submit-btn');
            const cancelEdit = document.getElementById('cancel-edit');
            
            // Product search functionality
            const searchInput = document.getElementById('search-products');
            const productCards = document.querySelectorAll('.product-card');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                productCards.forEach(card => {
                    const productName = card.dataset.productName;
                    if (productName.includes(searchTerm)) {
                        card.style.display = 'block';
                        card.classList.add('animate-slide-in');
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
            
            // Edit product
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function() {
                    formTitle.textContent = '✏️ Edit Produk';
                    formAction.value = 'update';
                    produkId.value = this.dataset.id;
                    document.getElementById('nama_produk').value = this.dataset.nama;
                    document.getElementById('harga').value = this.dataset.harga;
                    document.getElementById('stok').value = this.dataset.stok;
                    document.getElementById('kategori').value = this.dataset.kategori;
                    document.getElementById('deskripsi').value = this.dataset.deskripsi;
                    submitBtn.textContent = '💾 Update Produk';
                    cancelEdit.style.display = 'inline-block';
                    
                    // Scroll to form dengan animasi
                    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    
                    // Highlight form
                    form.style.boxShadow = '0 0 0 2px var(--primary)';
                    setTimeout(() => {
                        form.style.boxShadow = '';
                    }, 2000);
                });
            });
            
            // Cancel edit
            cancelEdit.addEventListener('click', function() {
                resetForm();
            });
            
            // Delete product
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const productName = this.dataset.nama;
                    document.getElementById('delete-id').value = this.dataset.id;
                    document.getElementById('delete-message').textContent = 
                        `Apakah Anda yakin ingin menghapus produk "${productName}"? Tindakan ini tidak dapat dibatalkan.`;
                    document.getElementById('delete-modal').style.display = 'flex';
                });
            });
            
            // Cancel delete
            document.getElementById('cancel-delete').addEventListener('click', function() {
                document.getElementById('delete-modal').style.display = 'none';
            });
            
            // Reset form
            function resetForm() {
                form.reset();
                formTitle.textContent = 'Tambah Produk Baru';
                formAction.value = 'add';
                produkId.value = '';
                submitBtn.textContent = '➕ Tambah Produk';
                cancelEdit.style.display = 'none';
                
                // Reset kategori ke pilihan default
                document.getElementById('kategori').selectedIndex = 0;
            }
            
            // Form submission animation
            form.addEventListener('submit', function(e) {
                const originalText = submitBtn.textContent;
                submitBtn.innerHTML = '⏳ Memproses...';
                submitBtn.disabled = true;
                
                // Re-enable after 3 seconds if still processing
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                }, 3000);
            });
            
            // Auto-focus nama produk
            document.getElementById('nama_produk').focus();
        });
    </script>
</body>
</html>