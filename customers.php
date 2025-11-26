<?php
// customers.php - VERSI PROFESIONAL DIPERBAIKI
include 'functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $sql = "INSERT INTO `kasir pelanggan` 
                        (NamaPelanggan, Alamat, NomorTelepon, Email) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", 
                    $_POST['nama_pelanggan'],
                    $_POST['alamat'],
                    $_POST['nomor_telepon'],
                    $_POST['email']
                );
                
                if ($stmt->execute()) {
                    $message = "✅ Pelanggan berhasil ditambahkan!";
                    $messageType = "success";
                } else {
                    $message = "❌ Gagal menambahkan pelanggan!";
                    $messageType = "error";
                }
                break;
                
            case 'update':
                $sql = "UPDATE `kasir pelanggan` SET 
                        NamaPelanggan = ?, Alamat = ?, NomorTelepon = ?, Email = ?
                        WHERE pelangganID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi",
                    $_POST['nama_pelanggan'],
                    $_POST['alamat'],
                    $_POST['nomor_telepon'],
                    $_POST['email'],
                    $_POST['pelanggan_id']
                );
                
                if ($stmt->execute()) {
                    $message = "✅ Pelanggan berhasil diperbarui!";
                    $messageType = "success";
                } else {
                    $message = "❌ Gagal memperbarui pelanggan!";
                    $messageType = "error";
                }
                break;
                
            case 'delete':
                $sql = "DELETE FROM `kasir pelanggan` WHERE pelangganID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $_POST['pelanggan_id']);
                
                if ($stmt->execute()) {
                    $message = "✅ Pelanggan berhasil dihapus!";
                    $messageType = "success";
                } else {
                    $message = "❌ Gagal menghapus pelanggan!";
                    $messageType = "error";
                }
                break;
        }
    }
}

$customers = getCustomers();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pelanggan - KASIR PRO</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="professional-header">
            <div class="header-left">
                <h1>🛒 KASIR PRO</h1>
                <span class="store-info">Manajemen Pelanggan - Sistem Kasir Modern</span>
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
            <a href="products.php">📦 Produk</a>
            <a href="customers.php" class="nav-active">👥 Pelanggan</a>
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
                    <h2>Manajemen Pelanggan</h2>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <span class="product-stock high">Total: <?= count($customers) ?> Pelanggan</span>
                        <button type="button" class="btn-primary" onclick="document.getElementById('customer-form').scrollIntoView({behavior: 'smooth'})">
                            👥 Tambah Pelanggan Baru
                        </button>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 400px 1fr; gap: 2rem;">
                    <!-- Form Tambah/Edit Pelanggan -->
                    <div class="form-section" style="height: fit-content;">
                        <h3 id="form-title">Tambah Pelanggan Baru</h3>
                        <form id="customer-form" method="POST">
                            <input type="hidden" name="action" id="form-action" value="add">
                            <input type="hidden" name="pelanggan_id" id="pelanggan-id">
                            
                            <div class="form-group">
                                <label for="nama_pelanggan">Nama Pelanggan</label>
                                <input type="text" id="nama_pelanggan" name="nama_pelanggan" required 
                                       placeholder="Masukkan nama lengkap pelanggan">
                            </div>
                            
                            <div class="form-group">
                                <label for="alamat">Alamat Lengkap</label>
                                <textarea id="alamat" name="alamat" rows="3" required 
                                          placeholder="Masukkan alamat lengkap pelanggan"></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nomor_telepon">Nomor Telepon</label>
                                    <input type="text" id="nomor_telepon" name="nomor_telepon" required 
                                           placeholder="Contoh: 08123456789">
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" 
                                           placeholder="email@contoh.com (opsional)">
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-primary" id="submit-btn">
                                    👥 Tambah Pelanggan
                                </button>
                                <button type="button" class="btn-secondary" id="cancel-edit" style="display: none;">
                                    ❌ Batal Edit
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Daftar Pelanggan -->
                    <div>
                        <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1.5rem;">
                            <h3>Daftar Pelanggan</h3>
                            <div style="display: flex; gap: 0.5rem;">
                                <input type="text" id="search-customers" placeholder="🔍 Cari pelanggan..." 
                                       style="padding: 0.5rem 1rem; border: 1px solid #ddd; border-radius: 6px;">
                            </div>
                        </div>

                        <div class="customers-grid" id="customers-container">
                            <?php foreach($customers as $customer): 
                                $initial = strtoupper(substr($customer['NamaPelanggan'], 0, 1));
                            ?>
                                <div class="customer-card animate-slide-in" data-customer-name="<?= strtolower(htmlspecialchars($customer['NamaPelanggan'])) ?>">
                                    <div class="customer-avatar">
                                        <?= $initial ?>
                                    </div>
                                    
                                    <div class="customer-name">
                                        <?= htmlspecialchars($customer['NamaPelanggan']) ?>
                                    </div>
                                    
                                    <div class="customer-contact">
                                        <div class="contact-item">
                                            <span>📞</span>
                                            <span><?= htmlspecialchars($customer['NomorTelepon']) ?></span>
                                        </div>
                                        <?php if (!empty($customer['Email'])): ?>
                                            <div class="contact-item">
                                                <span>📧</span>
                                                <span><?= htmlspecialchars($customer['Email']) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="customer-address">
                                        <?= htmlspecialchars($customer['Alamat']) ?>
                                    </div>
                                    
                                    <div class="product-actions">
                                        <button class="btn-primary btn-edit" 
                                                data-id="<?= $customer['pelangganID'] ?>"
                                                data-nama="<?= htmlspecialchars($customer['NamaPelanggan']) ?>"
                                                data-alamat="<?= htmlspecialchars($customer['Alamat']) ?>"
                                                data-telepon="<?= htmlspecialchars($customer['NomorTelepon']) ?>"
                                                data-email="<?= htmlspecialchars($customer['Email'] ?? '') ?>">
                                            ✏️ Edit
                                        </button>
                                        <button class="btn-secondary btn-delete" 
                                                data-id="<?= $customer['pelangganID'] ?>" 
                                                data-nama="<?= htmlspecialchars($customer['NamaPelanggan']) ?>">
                                            🗑️ Hapus
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if (empty($customers)): ?>
                                <div class="form-section" style="text-align: center; padding: 3rem; grid-column: 1 / -1;">
                                    <div style="font-size: 4rem; margin-bottom: 1rem;">👥</div>
                                    <h3 style="color: var(--secondary); margin-bottom: 0.5rem;">Belum Ada Pelanggan</h3>
                                    <p style="color: var(--secondary);">Tambahkan pelanggan pertama Anda untuk memulai</p>
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
            <p id="delete-message">Apakah Anda yakin ingin menghapus pelanggan ini?</p>
            <form id="delete-form" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="pelanggan_id" id="delete-id">
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="cancel-delete">❌ Batal</button>
                    <button type="submit" class="btn-primary" style="background: var(--danger);">🗑️ Hapus Pelanggan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Customer management functionality
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
            
            const form = document.getElementById('customer-form');
            const formTitle = document.getElementById('form-title');
            const formAction = document.getElementById('form-action');
            const pelangganId = document.getElementById('pelanggan-id');
            const submitBtn = document.getElementById('submit-btn');
            const cancelEdit = document.getElementById('cancel-edit');
            
            // Customer search functionality
            const searchInput = document.getElementById('search-customers');
            const customerCards = document.querySelectorAll('.customer-card');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                customerCards.forEach(card => {
                    const customerName = card.dataset.customerName;
                    if (customerName.includes(searchTerm)) {
                        card.style.display = 'block';
                        card.classList.add('animate-slide-in');
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
            
            // Edit customer
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function() {
                    formTitle.textContent = '✏️ Edit Pelanggan';
                    formAction.value = 'update';
                    pelangganId.value = this.dataset.id;
                    document.getElementById('nama_pelanggan').value = this.dataset.nama;
                    document.getElementById('alamat').value = this.dataset.alamat;
                    document.getElementById('nomor_telepon').value = this.dataset.telepon;
                    document.getElementById('email').value = this.dataset.email;
                    submitBtn.textContent = '💾 Update Pelanggan';
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
            
            // Delete customer
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const customerName = this.dataset.nama;
                    document.getElementById('delete-id').value = this.dataset.id;
                    document.getElementById('delete-message').textContent = 
                        `Apakah Anda yakin ingin menghapus pelanggan "${customerName}"? Tindakan ini tidak dapat dibatalkan.`;
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
                formTitle.textContent = 'Tambah Pelanggan Baru';
                formAction.value = 'add';
                pelangganId.value = '';
                submitBtn.textContent = '👥 Tambah Pelanggan';
                cancelEdit.style.display = 'none';
            }
            
            // Auto-focus nama pelanggan
            document.getElementById('nama_pelanggan').focus();
        });
    </script>
</body>
</html>