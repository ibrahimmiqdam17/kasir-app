<?php
// index.php - VERSI PROFESIONAL DIPERBAIKI
include 'functions.php';
$products = getProducts();
$customers = getCustomers();
$categories = getCategories();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Profesional - POS System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="professional-header">
            <div class="header-left">
                <h1>🛒 KASIR PRO</h1>
                <span class="store-info">Toko Serba Ada - Sistem Kasir Modern</span>
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
            <a href="index.php" class="nav-active">💳 Transaksi</a>
            <a href="products.php">📦 Produk</a>
            <a href="customers.php">👥 Pelanggan</a>
            <a href="sales.php">📋 Riwayat</a>
            <a href="reports.php">📊 Laporan</a>
        </nav>
        
        <div class="main-content professional-layout">
            <!-- Sidebar Produk -->
            <div class="product-sidebar">
                <div class="sidebar-header">
                    <h3>📦 DAFTAR PRODUK</h3>
                    <div class="search-box">
                        <input type="text" id="product-search" placeholder="Cari produk...">
                        <button type="button" id="search-btn">🔍</button>
                    </div>
                </div>
                
                <div class="category-filter">
                    <select id="category-filter">
                        <option value="">Semua Kategori</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="products-list" id="products-list">
                    <?php foreach($products as $product): ?>
                        <div class="product-item" data-category="<?= htmlspecialchars($product['Kategori']) ?>">
                            <div class="product-info">
                                <div class="product-name"><?= htmlspecialchars($product['NamaProduk']) ?></div>
                                <div class="product-price">Rp <?= number_format($product['Harga'], 0, ',', '.') ?></div>
                                <div class="product-stock">Stok: <?= $product['Stok'] ?></div>
                            </div>
                            <button type="button" class="btn-add-product" 
                                    data-product-id="<?= $product['ProdukID'] ?>"
                                    data-product-name="<?= htmlspecialchars($product['NamaProduk']) ?>"
                                    data-price="<?= $product['Harga'] ?>"
                                    data-stock="<?= $product['Stok'] ?>">
                                +
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Area Transaksi -->
            <div class="transaction-area">
                <div class="transaction-header">
                    <h2>💳 TRANSAKSI BARU</h2>
                    <div class="transaction-info">
                        <div class="info-item">
                            <span>No. Transaksi:</span>
                            <strong id="transaction-number">-</strong>
                        </div>
                        <div class="info-item">
                            <span>Status:</span>
                            <span class="status-badge">Siap</span>
                        </div>
                    </div>
                </div>
                
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">✅ <?= htmlspecialchars($_GET['success']) ?></div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-error">❌ <?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>
                
                <form id="transaction-form" action="process.php" method="POST">
                    <div class="customer-section">
                        <label for="customer">👤 Pilih Pelanggan:</label>
                        <select id="customer" name="customer_id" required>
                            <option value="">-- Umum --</option>
                            <?php foreach($customers as $customer): ?>
                                <option value="<?= $customer['pelangganID'] ?>">
                                    <?= htmlspecialchars($customer['NamaPelanggan']) ?> 
                                    (<?= htmlspecialchars($customer['NomorTelepon']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Keranjang Belanja - VERSI DIPERBAIKI -->
                    <div class="cart-section">
                        <div class="cart-header">
                            <h3>🛒 KERANJANG BELANJA</h3>
                            <span class="item-count" id="item-count">0 item</span>
                        </div>
                        
                        <div class="cart-items" id="cart-items">
                            <div class="empty-cart" id="empty-cart">
                                <p>Keranjang belanja kosong</p>
                                <small>Pilih produk dari daftar di samping</small>
                            </div>
                            <!-- Cart items will be dynamically inserted here -->
                        </div>
                        
                        <div class="cart-summary">
                            <div class="summary-row">
                                <span>Total Item:</span>
                                <span id="total-items">0</span>
                            </div>
                            <div class="summary-row total">
                                <span>💵 TOTAL BAYAR:</span>
                                <span class="total-amount">Rp <span id="total-amount">0</span></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pembayaran -->
                    <div class="payment-section">
                        <h3>💳 PEMBAYARAN</h3>
                        <div class="payment-methods">
                            <label>Metode Pembayaran:</label>
                            <div class="method-buttons">
                                <button type="button" class="method-btn active" data-method="tunai">💵 Tunai</button>
                                <button type="button" class="method-btn" data-method="debit">💳 Debit</button>
                                <button type="button" class="method-btn" data-method="kredit">💳 Kredit</button>
                                <button type="button" class="method-btn" data-method="qris">📱 QRIS</button>
                            </div>
                            <input type="hidden" name="payment_method" id="payment-method" value="tunai" required>
                        </div>
                        
                        <div class="payment-input">
                            <div class="input-group">
                                <label for="amount-paid">Jumlah Bayar:</label>
                                <input type="number" id="amount-paid" name="amount_paid" min="0" placeholder="0" required>
                            </div>
                            <div class="input-group">
                                <label for="change-amount">Kembalian:</label>
                                <input type="text" id="change-amount" placeholder="0" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="cart_data" id="cart-data">
                    <input type="hidden" name="total" id="total-input">
                    
                    <div class="transaction-actions">
                        <button type="button" id="clear-cart" class="btn-secondary">🗑️ Kosongkan</button>
                        <button type="button" id="hold-transaction" class="btn-warning">⏸️ Tunda</button>
                        <button type="submit" class="btn-success" id="process-payment">
                            💰 PROSES PEMBAYARAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
// Professional POS System - VERSI SIMPLE & FIXED
document.addEventListener('DOMContentLoaded', function() {
    // Global cart variable
    window.cart = [];
    let totalAmount = 0;
    let totalItems = 0;
    
    // Generate transaction number
    function generateTransactionNumber() {
        const now = new Date();
        const timestamp = now.getTime().toString().slice(-6);
        document.getElementById('transaction-number').textContent = `TRX-${timestamp}`;
    }
    
    generateTransactionNumber();
    
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
    
    // Product search
    document.getElementById('product-search').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const productItems = document.querySelectorAll('.product-item');
        
        productItems.forEach(item => {
            const productName = item.querySelector('.product-name').textContent.toLowerCase();
            if (productName.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Category filter
    document.getElementById('category-filter').addEventListener('change', function() {
        const selectedCategory = this.value;
        const productItems = document.querySelectorAll('.product-item');
        
        productItems.forEach(item => {
            if (selectedCategory === '' || item.dataset.category === selectedCategory) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Add product to cart - SIMPLE VERSION
    document.querySelectorAll('.btn-add-product').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const price = parseFloat(this.dataset.price);
            const stock = parseInt(this.dataset.stock);
            
            console.log('➕ Menambah produk:', productName);
            
            // Check if product already in cart
            const existingItem = window.cart.find(item => item.productId === productId);
            
            if (existingItem) {
                // Update quantity if stock available
                if (existingItem.quantity < stock) {
                    existingItem.quantity++;
                    existingItem.subtotal = existingItem.quantity * price;
                    showNotification(`✅ ${productName} (+1)`, 'success');
                } else {
                    showNotification('❌ Stok tidak mencukupi!', 'error');
                    return;
                }
            } else {
                // Add new item to cart
                if (stock > 0) {
                    window.cart.push({
                        productId: productId,
                        productName: productName,
                        price: price,
                        quantity: 1,
                        subtotal: price
                    });
                    showNotification(`✅ ${productName} ditambahkan`, 'success');
                } else {
                    showNotification('❌ Stok habis!', 'error');
                    return;
                }
            }
            
            updateCartDisplay();
        });
    });
    
    // Payment method selection
    document.querySelectorAll('.method-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.method-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('payment-method').value = this.dataset.method;
            
            // Auto fill amount for non-cash payments
            if (this.dataset.method !== 'tunai') {
                document.getElementById('amount-paid').value = totalAmount;
                calculateChange();
            }
        });
    });
    
    // Calculate change
    document.getElementById('amount-paid').addEventListener('input', calculateChange);
    
    function calculateChange() {
        const amountPaid = parseFloat(document.getElementById('amount-paid').value) || 0;
        const change = amountPaid - totalAmount;
        document.getElementById('change-amount').value = change >= 0 ? 
            'Rp ' + change.toLocaleString('id-ID') : 'Rp 0';
    }
    
    // Update cart display - SIMPLE & CLEAN
    function updateCartDisplay() {
        const cartContainer = document.getElementById('cart-items');
        const emptyCart = document.getElementById('empty-cart');
        
        // Calculate totals
        totalAmount = window.cart.reduce((sum, item) => sum + item.subtotal, 0);
        totalItems = window.cart.reduce((sum, item) => sum + item.quantity, 0);
        
        // Update UI totals
        document.getElementById('total-amount').textContent = totalAmount.toLocaleString('id-ID');
        document.getElementById('total-input').value = totalAmount;
        document.getElementById('item-count').textContent = totalItems + ' item';
        document.getElementById('total-items').textContent = totalItems;
        
        // Show/hide empty cart message
        if (window.cart.length === 0) {
            emptyCart.style.display = 'block';
            cartContainer.innerHTML = '';
        } else {
            emptyCart.style.display = 'none';
            
            // Clear and rebuild cart items
            cartContainer.innerHTML = '';
            
            window.cart.forEach((item, index) => {
                const cartItem = document.createElement('div');
                cartItem.className = 'cart-item';
                cartItem.innerHTML = `
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.productName}</div>
                        <div class="cart-item-price">Rp ${item.price.toLocaleString('id-ID')}</div>
                    </div>
                    <div class="cart-item-controls">
                        <button type="button" class="qty-btn minus" onclick="decreaseQuantity(${index})">-</button>
                        <span class="qty-display">${item.quantity}</span>
                        <button type="button" class="qty-btn plus" onclick="increaseQuantity(${index})">+</button>
                        <button type="button" class="remove-btn" onclick="removeItem(${index})">×</button>
                    </div>
                    <div class="cart-item-total">
                        Rp ${item.subtotal.toLocaleString('id-ID')}
                    </div>
                `;
                
                cartContainer.appendChild(cartItem);
            });
        }
        
        calculateChange();
    }
    
    // Global functions for cart operations
    window.decreaseQuantity = function(index) {
        const item = window.cart[index];
        if (item.quantity > 1) {
            item.quantity--;
            item.subtotal = item.quantity * item.price;
            showNotification(`📉 ${item.productName} (-1)`, 'warning');
        } else {
            removeItem(index);
        }
        updateCartDisplay();
    };
    
    window.increaseQuantity = function(index) {
        const item = window.cart[index];
        const maxStock = getProductStock(item.productId);
        
        if (item.quantity < maxStock) {
            item.quantity++;
            item.subtotal = item.quantity * item.price;
            showNotification(`📈 ${item.productName} (+1)`, 'success');
        } else {
            showNotification('❌ Stok tidak mencukupi!', 'error');
        }
        updateCartDisplay();
    };
    
    window.removeItem = function(index) {
        const itemName = window.cart[index].productName;
        window.cart.splice(index, 1);
        showNotification(`🗑️ ${itemName} dihapus`, 'warning');
        updateCartDisplay();
    };
    
    function getProductStock(productId) {
        const productButton = document.querySelector(`.btn-add-product[data-product-id="${productId}"]`);
        return productButton ? parseInt(productButton.dataset.stock) : 0;
    }
    
    function showNotification(message, type) {
        // Remove existing notifications
        document.querySelectorAll('.notification').forEach(notif => notif.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            color: white;
            border-radius: 5px;
            z-index: 1000;
            animation: slideIn 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            font-weight: 600;
            font-size: 14px;
        `;
        
        if (type === 'success') {
            notification.style.background = '#10b981';
        } else if (type === 'error') {
            notification.style.background = '#ef4444';
        } else if (type === 'warning') {
            notification.style.background = '#f59e0b';
        }
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }
    
    // Clear cart
    document.getElementById('clear-cart').addEventListener('click', function() {
        if (window.cart.length > 0 && confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
            window.cart = [];
            updateCartDisplay();
            showNotification('🗑️ Keranjang dikosongkan', 'warning');
        }
    });
    
    // Hold transaction
    document.getElementById('hold-transaction').addEventListener('click', function() {
        if (window.cart.length > 0) {
            localStorage.setItem('heldTransaction', JSON.stringify({
                cart: window.cart,
                timestamp: new Date().toISOString()
            }));
            showNotification('⏸️ Transaksi ditunda', 'warning');
        } else {
            showNotification('❌ Keranjang kosong', 'error');
        }
    });
    
    // Load held transaction if exists
    const heldTransaction = localStorage.getItem('heldTransaction');
    if (heldTransaction) {
        try {
            const transactionData = JSON.parse(heldTransaction);
            if (confirm('Ada transaksi yang ditunda. Muat transaksi tersebut?')) {
                window.cart = transactionData.cart || [];
                updateCartDisplay();
                showNotification('🔄 Transaksi dimuat', 'success');
                localStorage.removeItem('heldTransaction');
            }
        } catch (e) {
            console.error('Error loading held transaction:', e);
            localStorage.removeItem('heldTransaction');
        }
    }
    
    // Form validation
    document.getElementById('transaction-form').addEventListener('submit', function(e) {
        const amountPaid = parseFloat(document.getElementById('amount-paid').value) || 0;
        
        if (window.cart.length === 0) {
            e.preventDefault();
            showNotification('❌ Keranjang belanja kosong!', 'error');
            return;
        }
        
        if (amountPaid < totalAmount) {
            e.preventDefault();
            showNotification('❌ Jumlah bayar kurang dari total!', 'error');
            return;
        }
        
        // Update hidden inputs
        document.getElementById('cart-data').value = JSON.stringify(window.cart);
        
        // Show loading state
        const submitBtn = document.getElementById('process-payment');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '⏳ Memproses...';
        submitBtn.disabled = true;
    });

    // Initialize
    console.log('🛒 POS System initialized');
    updateCartDisplay();
});
</script>
</body>
</html>