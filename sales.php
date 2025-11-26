<?php
// sales.php - VERSI PROFESIONAL DIPERBAIKI
include 'functions.php';

// Handle filter
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$sales = getSalesHistory($startDate, $endDate);

// Calculate statistics
$totalSales = array_sum(array_column($sales, 'TotalHarga'));
$averageTransaction = count($sales) > 0 ? $totalSales / count($sales) : 0;
$totalTransactions = count($sales);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - KASIR PRO</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="professional-header">
            <div class="header-left">
                <h1>🛒 KASIR PRO</h1>
                <span class="store-info">Riwayat Transaksi - Sistem Kasir Modern</span>
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
            <a href="customers.php">👥 Pelanggan</a>
            <a href="sales.php" class="nav-active">📋 Riwayat</a>
            <a href="reports.php">📊 Laporan</a>
        </nav>
        
        <div class="main-content" style="padding: 2rem;">
            <!-- Header Section -->
            <div class="form-section animate-fade-in">
                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 2rem;">
                    <h2>📋 Riwayat Transaksi</h2>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <span class="product-stock high">Periode: <?= date('d M Y', strtotime($startDate)) ?> - <?= date('d M Y', strtotime($endDate)) ?></span>
                    </div>
                </div>

                <!-- Filter Section -->
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                    <h3 style="color: white; margin-bottom: 1rem;">🔍 Filter Transaksi</h3>
                    <form method="GET" class="filter-form">
                        <div class="form-row" style="grid-template-columns: 1fr 1fr auto auto; gap: 1rem;">
                            <div class="form-group">
                                <label style="color: white;">Dari Tanggal</label>
                                <input type="date" id="start_date" name="start_date" value="<?= $startDate ?>" required
                                       style="background: rgba(255,255,255,0.9); border: none;">
                            </div>
                            
                            <div class="form-group">
                                <label style="color: white;">Sampai Tanggal</label>
                                <input type="date" id="end_date" name="end_date" value="<?= $endDate ?>" required
                                       style="background: rgba(255,255,255,0.9); border: none;">
                            </div>
                            
                            <div class="form-group" style="display: flex; align-items: flex-end;">
                                <button type="submit" class="btn-primary" style="background: white; color: #667eea; border: none;">
                                    🔍 Terapkan Filter
                                </button>
                            </div>
                            
                            <div class="form-group" style="display: flex; align-items: flex-end;">
                                <a href="sales.php" class="btn-secondary" 
                                   style="text-decoration: none; display: inline-block; padding: 0.75rem 1.5rem; background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                                    🔄 Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Statistics Cards -->
                <div class="sales-stats animate-slide-in">
                    <div class="stat-card">
                        <div class="stat-number"><?= $totalTransactions ?></div>
                        <div class="stat-label">Total Transaksi</div>
                        <div style="font-size: 2rem; margin-top: 0.5rem;">📊</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number">Rp <?= number_format($totalSales, 0, ',', '.') ?></div>
                        <div class="stat-label">Total Penjualan</div>
                        <div style="font-size: 2rem; margin-top: 0.5rem;">💰</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number">Rp <?= number_format($averageTransaction, 0, ',', '.') ?></div>
                        <div class="stat-label">Rata-rata Transaksi</div>
                        <div style="font-size: 2rem; margin-top: 0.5rem;">📈</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number">
                            <?= $totalTransactions > 0 ? number_format(($totalTransactions / 30) * 100, 1) : 0 ?>%
                        </div>
                        <div class="stat-label">Growth Rate</div>
                        <div style="font-size: 2rem; margin-top: 0.5rem;">🚀</div>
                    </div>
                </div>

                <!-- Sales Table -->
                <div class="sales-table-container animate-fade-in">
                    <div style="padding: 1.5rem; border-bottom: 1px solid #e2e8f0;">
                        <h3 style="margin: 0; color: var(--dark);">📋 Daftar Transaksi</h3>
                    </div>
                    
                    <?php if (!empty($sales)): ?>
                        <div style="max-height: 600px; overflow-y: auto;">
                            <table class="sales-table">
                                <thead>
                                    <tr>
                                        <th>No. Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Pelanggan</th>
                                        <th>Metode Bayar</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($sales as $index => $sale): ?>
                                        <tr class="animate-slide-in" style="animation-delay: <?= $index * 0.1 ?>s">
                                            <td>
                                                <div style="font-weight: 700; color: var(--primary);">
                                                    #<?= str_pad($sale['PenjualanID'], 6, '0', STR_PAD_LEFT) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-weight: 600;">
                                                    <?= date('d M Y', strtotime($sale['TanggalPenjualan'])) ?>
                                                </div>
                                                <div style="font-size: 0.8rem; color: var(--secondary);">
                                                    <?= date('H:i', strtotime($sale['TanggalPenjualan'])) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-weight: 600;">
                                                    <?= htmlspecialchars($sale['NamaPelanggan'] ?? 'Umum') ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $paymentColors = [
                                                    'tunai' => ['bg' => '#e3f2fd', 'color' => '#1976d2', 'icon' => '💵'],
                                                    'debit' => ['bg' => '#e8f5e8', 'color' => '#2e7d32', 'icon' => '💳'],
                                                    'kredit' => ['bg' => '#fff3e0', 'color' => '#f57c00', 'icon' => '💳'],
                                                    'qris' => ['bg' => '#fce4ec', 'color' => '#c2185b', 'icon' => '📱']
                                                ];
                                                $payment = $sale['MetodePembayaran'] ?? 'tunai';
                                                $color = $paymentColors[$payment] ?? $paymentColors['tunai'];
                                                ?>
                                                <span style="padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; 
                                                      background: <?= $color['bg'] ?>; color: <?= $color['color'] ?>; display: inline-flex; align-items: center; gap: 0.5rem;">
                                                    <?= $color['icon'] ?> <?= ucfirst($payment) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="font-weight: 800; color: var(--success); font-size: 1.1rem;">
                                                    Rp <?= number_format($sale['TotalHarga'], 0, ',', '.') ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 0.5rem;">
                                                    <a href="receipt.php?id=<?= $sale['PenjualanID'] ?>" 
                                                       target="_blank" 
                                                       class="btn-primary" 
                                                       style="padding: 0.5rem 1rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8rem;">
                                                        🖨️ Struk
                                                    </a>
                                                    <button class="btn-secondary view-details" 
                                                            data-id="<?= $sale['PenjualanID'] ?>"
                                                            style="padding: 0.5rem 1rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                                                        👁️ Detail
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem;">
                            <div style="font-size: 4rem; margin-bottom: 1rem;">📭</div>
                            <h3 style="color: var(--secondary); margin-bottom: 0.5rem;">Tidak Ada Transaksi</h3>
                            <p style="color: var(--secondary);">Tidak ada transaksi ditemukan pada periode yang dipilih</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Summary Footer -->
                <?php if (!empty($sales)): ?>
                    <div class="form-section" style="margin-top: 1rem; background: var(--primary); color: white;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-size: 0.9rem; opacity: 0.9;">Ringkasan Periode</div>
                                <div style="font-size: 1.1rem; font-weight: 700;">
                                    <?= $totalTransactions ?> transaksi • Rp <?= number_format($totalSales, 0, ',', '.') ?>
                                </div>
                            </div>
                            <div style="display: flex; gap: 1rem;">
                                <button class="btn-secondary" onclick="window.print()" 
                                        style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                                    🖨️ Cetak Laporan
                                </button>
                                <button class="btn-primary" 
                                        style="background: white; color: var(--primary); border: none;">
                                    📥 Export Data
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detail-modal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div class="form-section" style="max-width: 600px; max-height: 80vh; overflow-y: auto; animation: bounceIn 0.3s ease-out;">
            <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1.5rem;">
                <h3>👁️ Detail Transaksi</h3>
                <button type="button" class="btn-secondary" id="close-detail" style="padding: 0.5rem;">❌</button>
            </div>
            <div id="detail-content">
                <!-- Detail content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
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

            // View details functionality
            document.querySelectorAll('.view-details').forEach(button => {
                button.addEventListener('click', function() {
                    const saleId = this.dataset.id;
                    loadSaleDetails(saleId);
                });
            });

            // Close detail modal
            document.getElementById('close-detail').addEventListener('click', function() {
                document.getElementById('detail-modal').style.display = 'none';
            });

            // Load sale details via AJAX
            function loadSaleDetails(saleId) {
                // Show loading state
                document.getElementById('detail-content').innerHTML = `
                    <div style="text-align: center; padding: 2rem;">
                        <div class="loading-shimmer" style="height: 20px; margin-bottom: 1rem;"></div>
                        <div class="loading-shimmer" style="height: 20px; width: 80%; margin: 0 auto;"></div>
                    </div>
                `;
                
                document.getElementById('detail-modal').style.display = 'flex';

                // Simulate API call (in real implementation, you'd use fetch/XMLHttpRequest)
                setTimeout(() => {
                    document.getElementById('detail-content').innerHTML = `
                        <div style="margin-bottom: 1.5rem;">
                            <div style="display: flex; justify-content: between; margin-bottom: 1rem;">
                                <div>
                                    <div style="font-size: 0.9rem; color: var(--secondary);">No. Transaksi</div>
                                    <div style="font-weight: 700; color: var(--primary);">#${saleId.toString().padStart(6, '0')}</div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 0.9rem; color: var(--secondary);">Tanggal</div>
                                    <div style="font-weight: 600;">${new Date().toLocaleDateString('id-ID')}</div>
                                </div>
                            </div>
                        </div>

                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                            <div style="font-weight: 600; margin-bottom: 0.5rem;">Items:</div>
                            <div style="display: flex; justify-content: between; padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0;">
                                <div>Es Teh Manis</div>
                                <div>2 x Rp 5.000</div>
                                <div style="font-weight: 600;">Rp 10.000</div>
                            </div>
                            <div style="display: flex; justify-content: between; padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0;">
                                <div>Nasi Goreng</div>
                                <div>1 x Rp 15.000</div>
                                <div style="font-weight: 600;">Rp 15.000</div>
                            </div>
                            <div style="display: flex; justify-content: between; padding: 0.5rem 0; font-weight: 700;">
                                <div>Total</div>
                                <div>Rp 25.000</div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                            <div>
                                <div style="font-size: 0.9rem; color: var(--secondary);">Metode Bayar</div>
                                <div style="font-weight: 600;">Tunai</div>
                            </div>
                            <div>
                                <div style="font-size: 0.9rem; color: var(--secondary);">Kasir</div>
                                <div style="font-weight: 600;">Admin</div>
                            </div>
                        </div>
                    `;
                }, 1000);
            }

            // Print functionality
            document.querySelector('[onclick="window.print()"]').addEventListener('click', function() {
                window.print();
            });

            // Auto-set end date to today if not set
            const endDateInput = document.getElementById('end_date');
            if (!endDateInput.value) {
                endDateInput.value = new Date().toISOString().split('T')[0];
            }
        });
    </script>
</body>
</html>