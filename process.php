<?php
// process.php - VERSI PROFESIONAL DIPERBAIKI
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_POST['customer_id'] ?? 1; // Default to Umum if not selected
    $paymentMethod = $_POST['payment_method'];
    $amountPaid = $_POST['amount_paid'];
    $cartData = json_decode($_POST['cart_data'], true);
    $total = $_POST['total'];
    
    if (empty($cartData)) {
        header('Location: index.php?error=Keranjang belanja kosong');
        exit;
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Add sale
        $tanggal = date('Y-m-d');
        $saleData = [
            'tanggal' => $tanggal,
            'total_harga' => $total,
            'pelanggan_id' => $customerId,
            'metode_pembayaran' => $paymentMethod
        ];
        
        $penjualanID = addSale($saleData);
        
        if (!$penjualanID) {
            throw new Exception("Gagal menyimpan transaksi");
        }
        
        // Add sale details and update stock
        foreach ($cartData as $item) {
            $result = addSaleDetail(
                $penjualanID, 
                $item['productId'], 
                $item['quantity'], 
                $item['subtotal']
            );
            
            if (!$result) {
                throw new Exception("Gagal menyimpan detail transaksi");
            }
            
            // Update stock
            $stockResult = updateStock($item['productId'], $item['quantity']);
            if (!$stockResult) {
                throw new Exception("Gagal update stok produk");
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        // Redirect to receipt dengan parameter bayar
        header("Location: receipt.php?id=$penjualanID&bayar=$amountPaid");
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        header('Location: index.php?error=' . urlencode($e->getMessage()));
        exit;
    }
} else {
    header('Location: index.php');
}
?>