<?php
// receipt.php - VERSI PROFESIONAL DIPERBAIKI
include 'functions.php';

$penjualanID = $_GET['id'] ?? 0;
$bayar = $_GET['bayar'] ?? 0;

// Get sale data menggunakan fungsi baru
$saleData = getSaleData($penjualanID);

if (!$saleData) {
    die("Transaksi tidak ditemukan!");
}

$sale = $saleData['sale'];
$saleDetails = $saleData['details'];

// Hitung total dari detail jika diperlukan
$totalHarga = $sale['TotalHarga'];
$kembali = $bayar - $totalHarga;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #<?= $penjualanID ?> - KASIR PRO</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
            background: #fff;
            padding: 10px;
        }
        
        .receipt {
            width: 80mm;
            margin: 0 auto;
            padding: 5px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .company-address {
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        .receipt-info {
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }
        
        .receipt-info .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        
        .items {
            margin-bottom: 10px;
        }
        
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            padding-bottom: 2px;
            border-bottom: 1px dotted #ccc;
        }
        
        .item-name {
            flex: 2;
        }
        
        .item-qty, .item-price, .item-total {
            flex: 1;
            text-align: right;
        }
        
        .summary {
            margin-bottom: 10px;
            padding-top: 5px;
            border-top: 1px dashed #000;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .total-row {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 3px;
            margin-top: 3px;
        }
        
        .payment-section {
            margin: 10px 0;
            padding: 8px;
            border: 1px dashed #000;
            border-radius: 5px;
            background: #f9f9f9;
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            padding: 2px 0;
        }
        
        .payment-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #000;
            font-size: 10px;
        }
        
        .barcode {
            text-align: center;
            margin: 10px 0;
            font-family: 'Libre Barcode 128', cursive;
            font-size: 24px;
        }
        
        .thank-you {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .receipt {
                width: 80mm;
                margin: 0;
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="company-name">KASIR PRO</div>
            <div class="company-address">
                Toko Serba Ada<br>
                Jl. Contoh No. 123, Jakarta<br>
                Telp: 021-1234567
            </div>
        </div>
        
        <div class="receipt-info">
            <div class="row">
                <span>No. Transaksi:</span>
                <span>#<?= str_pad($sale['PenjualanID'], 6, '0', STR_PAD_LEFT) ?></span>
            </div>
            <div class="row">
                <span>Tanggal:</span>
                <span><?= date('d/m/Y', strtotime($sale['TanggalPenjualan'])) ?></span>
            </div>
            <div class="row">
                <span>Waktu:</span>
                <span><?= date('H:i:s') ?></span>
            </div>
            <div class="row">
                <span>Kasir:</span>
                <span>Admin</span>
            </div>
        </div>
        
        <?php if ($sale['NamaPelanggan'] && $sale['NamaPelanggan'] != 'Umum'): ?>
        <div class="receipt-info">
            <div class="row">
                <span>Pelanggan:</span>
                <span><?= htmlspecialchars($sale['NamaPelanggan']) ?></span>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="items">
            <div class="item-row" style="font-weight: bold; border-bottom: 1px solid #000;">
                <div class="item-name">Item</div>
                <div class="item-qty">Qty</div>
                <div class="item-price">Harga</div>
                <div class="item-total">Subtotal</div>
            </div>
            
            <?php foreach($saleDetails as $item): ?>
            <div class="item-row">
                <div class="item-name"><?= htmlspecialchars($item['NamaProduk']) ?></div>
                <div class="item-qty"><?= $item['JumlahProduk'] ?></div>
                <div class="item-price"><?= number_format($item['Harga'], 0, ',', '.') ?></div>
                <div class="item-total"><?= number_format($item['Subtotal'], 0, ',', '.') ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="summary">
            <div class="summary-row total-row">
                <span>TOTAL:</span>
                <span>Rp <?= number_format($totalHarga, 0, ',', '.') ?></span>
            </div>
        </div>
        
        <!-- Section Bayar & Kembali -->
        <div class="payment-section">
            <div class="payment-row">
                <span>Bayar:</span>
                <span>Rp <?= number_format($bayar, 0, ',', '.') ?></span>
            </div>
            <div class="payment-row payment-total">
                <span>Kembali:</span>
                <span>Rp <?= number_format($kembali, 0, ',', '.') ?></span>
            </div>
        </div>
        
        <div class="receipt-info">
            <div class="row">
                <span>Pembayaran:</span>
                <span style="text-transform: uppercase;"><?= $sale['MetodePembayaran'] ?></span>
            </div>
        </div>
        
        <div class="barcode">
            *<?= str_pad($sale['PenjualanID'], 6, '0', STR_PAD_LEFT) ?>*
        </div>
        
        <div class="thank-you">
            TERIMA KASIH ATAS KUNJUNGAN ANDA
        </div>
        
        <div class="footer">
            Barang yang sudah dibeli tidak dapat ditukar/dikembalikan<br>
            www.kasirpro.com
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" class="btn-primary" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; margin: 5px;">🖨️ Cetak Struk</button>
        <button onclick="window.close()" class="btn-secondary" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin: 5px;">❌ Tutup</button>
    </div>

    <script>
        // Auto print ketika halaman dibuka
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>