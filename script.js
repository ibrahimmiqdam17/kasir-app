// script.js - Enhanced Version untuk halaman yang membutuhkan
document.addEventListener('DOMContentLoaded', function() {
    // Update date and time untuk halaman yang memiliki elemen ini
    const dateTimeElement = document.getElementById('current-date-time');
    if (dateTimeElement) {
        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            dateTimeElement.textContent = now.toLocaleDateString('id-ID', options);
        }
        
        updateDateTime();
        setInterval(updateDateTime, 1000);
    }

    // Notification system
    window.showNotification = function(message, type = 'success') {
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
        `;
        
        if (type === 'success') {
            notification.style.background = 'var(--success)';
        } else if (type === 'error') {
            notification.style.background = 'var(--danger)';
        } else if (type === 'warning') {
            notification.style.background = 'var(--warning)';
        }
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    };

    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<span class="loading"></span> Memproses...';
                submitBtn.disabled = true;
                
                // Re-enable button after 5 seconds if still disabled (safety)
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.innerHTML = 'Submit';
                        submitBtn.disabled = false;
                    }
                }, 5000);
            }
        });
    });

    // Table row highlighting
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
            });
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });
    });

    // Auto-focus search inputs
    const searchInputs = document.querySelectorAll('input[type="search"], input[placeholder*="cari"], input[placeholder*="Cari"]');
    searchInputs.forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.form.submit();
            }
        });
    });

    // Modal functionality
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        const closeButtons = modal.querySelectorAll('[data-close]');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        });

        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Print functionality
    const printButtons = document.querySelectorAll('[onclick*="print"]');
    printButtons.forEach(button => {
        button.addEventListener('click', function() {
            window.print();
        });
    });

    // Responsive table handling
    function handleResponsiveTables() {
        const tables = document.querySelectorAll('.table-container');
        tables.forEach(container => {
            const table = container.querySelector('table');
            if (table && container.offsetWidth < table.offsetWidth) {
                container.style.overflowX = 'auto';
            }
        });
    }

    handleResponsiveTables();
    window.addEventListener('resize', handleResponsiveTables);

    // Loading states
    const buttons = document.querySelectorAll('.btn-primary, .btn-secondary, .btn-success, .btn-warning');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.type === 'submit' || this.getAttribute('type') === 'submit') {
                this.classList.add('loading');
            }
        });
    });

    // Add CSS for loading animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .btn-primary.loading::after,
        .btn-secondary.loading::after,
        .btn-success.loading::after,
        .btn-warning.loading::after {
            content: '';
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 8px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .table-container {
            position: relative;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr !important;
            }
            
            .summary-cards {
                grid-template-columns: 1fr !important;
            }
            
            .professional-layout {
                grid-template-columns: 1fr !important;
            }
            
            .product-sidebar {
                display: none !important;
            }
        }
    `;
    document.head.appendChild(style);
});

// Utility functions
const KasirUtils = {
    // Format currency
    formatCurrency: function(amount) {
        return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
    },
    
    // Format date
    formatDate: function(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    },
    
    // Format datetime
    formatDateTime: function(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },
    
    // Calculate change
    calculateChange: function(amountPaid, totalAmount) {
        const change = amountPaid - totalAmount;
        return change >= 0 ? change : 0;
    },
    
    // Validate email
    validateEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },
    
    // Validate phone number (Indonesian format)
    validatePhone: function(phone) {
        const re = /^(\+62|62|0)8[1-9][0-9]{6,9}$/;
        return re.test(phone);
    }
};

// Export for global use
window.KasirUtils = KasirUtils;