<?php
// reports.php - VERSI PROFESIONAL DIPERBAIKI
include 'functions.php';

$period = $_GET['period'] ?? 'hari';
$reports = getSalesReport($period);

// Calculate totals
$totalTransactions = array_sum(array_column($reports, 'JumlahTransaksi'));
$totalSales = array_sum(array_column($reports, 'TotalPenjualan'));
$averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

// Prepare chart data
$chartLabels = [];
$chartSales = [];
$chartTransactions = [];

foreach($reports as $report) {
    if ($period === 'hari') {
        $chartLabels[] = date('d M', strtotime($report['Periode']));
    } elseif ($period === 'minggu') {
        $chartLabels[] = 'Minggu ' . $report['Minggu'];
    } else {
        $chartLabels[] = date('M Y', mktime(0, 0, 0, $report['Bulan'], 1, $report['Tahun']));
    }
    $chartSales[] = $report['TotalPenjualan'];
    $chartTransactions[] = $report['JumlahTransaksi'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - KASIR PRO</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <header class="professional-header">
            <div class="header-left">
                <h1>🛒 KASIR PRO</h1>
                <span class="store-info">Laporan Penjualan - Sistem Kasir Modern</span>
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
            <a href="sales.php">📋 Riwayat</a>
            <a href="reports.php" class="nav-active">📊 Laporan</a>
        </nav>
        
        <div class="main-content" style="padding: 2rem;">
            <!-- Header Section -->
            <div class="form-section animate-fade-in">
                <div class="reports-header">
                    <div>
                        <h2>📊 Laporan Penjualan</h2>
                        <p style="color: var(--secondary); margin-top: 0.5rem;">
                            Analisis performa penjualan dan trend bisnis Anda
                        </p>
                    </div>
                    <div class="period-selector">
                        <button type="submit" name="period" value="hari" 
                                class="period-btn <?= $period === 'hari' ? 'active' : '' ?>">
                            📅 Harian
                        </button>
                        <button type="submit" name="period" value="minggu" 
                                class="period-btn <?= $period === 'minggu' ? 'active' : '' ?>">
                            📆 Mingguan
                        </button>
                        <button type="submit" name="period" value="bulan" 
                                class="period-btn <?= $period === 'bulan' ? 'active' : '' ?>">
                            📈 Bulanan
                        </button>
                    </div>
                </div>

                <!-- Statistics Overview -->
                <div class="summary-cards animate-slide-in">
                    <div class="summary-card">
                        <div class="summary-number"><?= count($reports) ?></div>
                        <div class="stat-label">Total Periode</div>
                        <div style="font-size: 2rem; margin-top: 0.5rem;">
                            <?= $period === 'hari' ? '📅' : ($period === 'minggu' ? '📆' : '📈') ?>
                        </div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="summary-number"><?= $totalTransactions ?></div>
                        <div class="stat-label">Total Transaksi</div>
                        <div style="font-size: 2rem; margin-top: 0.5rem;">💳</div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="summary-number">Rp <?= number_format($totalSales, 0, ',', '.') ?></div>
                        <div class="stat-label">Total Pendapatan</div>
                        <div style="font-size: 2rem; margin-top: 0.5rem;">💰</div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="summary-number">Rp <?= number_format($averageTransaction, 0, ',', '.') ?></div>
                        <div class="stat-label">Rata-rata/Transaksi</div>
                        <div style="font-size: 2rem; margin-top: 0.5rem;">📊</div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="charts-grid animate-fade-in">
                    <div class="chart-card">
                        <div class="chart-title">📈 Trend Pendapatan</div>
                        <canvas id="salesChart" height="250"></canvas>
                    </div>
                    
                    <div class="chart-card">
                        <div class="chart-title">📊 Volume Transaksi</div>
                        <canvas id="transactionsChart" height="250"></canvas>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0;">
                    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">🚀</div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: var(--success);">
                            <?= $totalTransactions > 0 ? number_format(($totalTransactions / 30) * 100, 1) : 0 ?>%
                        </div>
                        <div style="color: var(--secondary); font-size: 0.9rem;">Growth Rate</div>
                    </div>
                    
                    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">⭐</div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: var(--warning);">
                            <?= count($reports) > 0 ? number_format(max($chartSales) / 1000, 1) : 0 ?>K
                        </div>
                        <div style="color: var(--secondary); font-size: 0.9rem;">Peak Sales</div>
                    </div>
                    
                    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">📅</div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">
                            <?= count($reports) > 0 ? date('d M', strtotime($reports[0]['Periode'])) : '-' ?>
                        </div>
                        <div style="color: var(--secondary); font-size: 0.9rem;">Latest Data</div>
                    </div>
                </div>

                <!-- Detailed Report Table -->
                <div class="sales-table-container animate-slide-in">
                    <div style="padding: 1.5rem; border-bottom: 1px solid #e2e8f0;">
                        <h3 style="margin: 0; color: var(--dark);">📋 Laporan Detail</h3>
                    </div>
                    
                    <?php if (!empty($reports)): ?>
                        <table class="sales-table">
                            <thead>
                                <tr>
                                    <th>Periode</th>
                                    <th>Jumlah Transaksi</th>
                                    <th>Total Penjualan</th>
                                    <th>Rata-rata per Transaksi</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($reports as $index => $report): 
                                    $performance = $report['TotalPenjualan'] / $report['JumlahTransaksi'];
                                    $prevPerformance = isset($reports[$index + 1]) ? 
                                        $reports[$index + 1]['TotalPenjualan'] / $reports[$index + 1]['JumlahTransaksi'] : $performance;
                                    $trend = $performance > $prevPerformance ? 'up' : ($performance < $prevPerformance ? 'down' : 'stable');
                                ?>
                                    <tr class="animate-slide-in" style="animation-delay: <?= $index * 0.1 ?>s">
                                        <td>
                                            <div style="font-weight: 600;">
                                                <?php
                                                if ($period === 'hari') {
                                                    echo date('d M Y', strtotime($report['Periode']));
                                                } elseif ($period === 'minggu') {
                                                    echo 'Minggu ' . $report['Minggu'] . ' - ' . $report['Tahun'];
                                                } else {
                                                    echo date('F Y', mktime(0, 0, 0, $report['Bulan'], 1, $report['Tahun']));
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: var(--primary);">
                                                <?= $report['JumlahTransaksi'] ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: var(--success);">
                                                Rp <?= number_format($report['TotalPenjualan'], 0, ',', '.') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;">
                                                Rp <?= number_format($report['TotalPenjualan'] / $report['JumlahTransaksi'], 0, ',', '.') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <?php if ($trend === 'up'): ?>
                                                    <span style="color: var(--success);">📈</span>
                                                    <span style="color: var(--success); font-weight: 600;">Naik</span>
                                                <?php elseif ($trend === 'down'): ?>
                                                    <span style="color: var(--danger);">📉</span>
                                                    <span style="color: var(--danger); font-weight: 600;">Turun</span>
                                                <?php else: ?>
                                                    <span style="color: var(--secondary);">➡️</span>
                                                    <span style="color: var(--secondary); font-weight: 600;">Stabil</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem;">
                            <div style="font-size: 4rem; margin-bottom: 1rem;">📊</div>
                            <h3 style="color: var(--secondary); margin-bottom: 0.5rem;">Tidak Ada Data Laporan</h3>
                            <p style="color: var(--secondary);">Tidak ada data laporan tersedia untuk periode yang dipilih</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Export Options -->
                <?php if (!empty($reports)): ?>
                    <div class="form-section" style="margin-top: 2rem; text-align: center;">
                        <h3 style="margin-bottom: 1rem;">📥 Export Laporan</h3>
                        <div style="display: flex; gap: 1rem; justify-content: center;">
                            <button class="btn-primary" onclick="window.print()">
                                🖨️ Cetak Laporan
                            </button>
                            <button class="btn-secondary">
                                📄 Export PDF
                            </button>
                            <button class="btn-secondary">
                                📊 Export Excel
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
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

            // Chart data from PHP
            const chartData = {
                labels: <?= json_encode($chartLabels) ?>,
                sales: <?= json_encode($chartSales) ?>,
                transactions: <?= json_encode($chartTransactions) ?>
            };

            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: chartData.sales,
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgba(52, 152, 219, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        }
                    }
                }
            });
            
            // Transactions Chart
            const transactionsCtx = document.getElementById('transactionsChart').getContext('2d');
            new Chart(transactionsCtx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Jumlah Transaksi',
                        data: chartData.transactions,
                        backgroundColor: 'rgba(46, 204, 113, 0.8)',
                        borderColor: 'rgba(46, 204, 113, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Period selector form
            document.querySelectorAll('.period-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const form = document.createElement('form');
                    form.method = 'GET';
                    form.innerHTML = `<input type="hidden" name="period" value="${this.value}">`;
                    document.body.appendChild(form);
                    form.submit();
                });
            });

            // Add loading animation to export buttons
            document.querySelectorAll('.btn-primary, .btn-secondary').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.textContent.includes('Export') || this.textContent.includes('Cetak')) {
                        const originalText = this.textContent;
                        this.innerHTML = '⏳ Memproses...';
                        this.disabled = true;
                        
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.disabled = false;
                        }, 2000);
                    }
                });
            });
        });
    </script>
</body>
</html>