// utils.js - Utility Functions

// Show message function with improved styling and positioning
function showMessage(message, type = 'info', duration = 3000) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.toast-message');
    existingMessages.forEach(msg => msg.remove());

    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className =
        `toast-message fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white text-sm max-w-sm transform transition-all duration-300 ease-in-out`;

    // Set colors and icons based on type
    let bgColor, icon;
    switch (type) {
        case 'success':
            bgColor = 'bg-green-600';
            icon = '✓';
            break;
        case 'warning':
            bgColor = 'bg-yellow-600';
            icon = '⚠';
            break;
        case 'error':
            bgColor = 'bg-red-600';
            icon = '✕';
            break;
        case 'info':
        default:
            bgColor = 'bg-blue-600';
            icon = 'ℹ';
            break;
    }

    // Add background color
    messageDiv.classList.add(bgColor);

    // Set message content with icon
    messageDiv.innerHTML = `
        <div class="flex items-center">
            <span class="mr-2 font-bold">${icon}</span>
            <span>${message}</span>
        </div>
    `;

    // Add to document
    document.body.appendChild(messageDiv);

    // Initial animation (slide in from right)
    setTimeout(() => {
        messageDiv.style.transform = 'translateX(0)';
        messageDiv.style.opacity = '1';
    }, 10);

    // Set initial position (off-screen)
    messageDiv.style.transform = 'translateX(100%)';
    messageDiv.style.opacity = '0';

    // Auto remove after duration
    setTimeout(() => {
        // Slide out animation
        messageDiv.style.transform = 'translateX(100%)';
        messageDiv.style.opacity = '0';

        // Remove from DOM after animation
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.parentNode.removeChild(messageDiv);
            }
        }, 300);
    }, duration);

    // Optional: Add click to dismiss
    messageDiv.addEventListener('click', () => {
        messageDiv.style.transform = 'translateX(100%)';
        messageDiv.style.opacity = '0';
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.parentNode.removeChild(messageDiv);
            }
        }, 300);
    });

    // Add hover effect to pause auto-dismiss
    let timeoutId;
    const startDismissTimer = () => {
        timeoutId = setTimeout(() => {
            messageDiv.style.transform = 'translateX(100%)';
            messageDiv.style.opacity = '0';
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.parentNode.removeChild(messageDiv);
                }
            }, 300);
        }, duration);
    };

    messageDiv.addEventListener('mouseenter', () => {
        clearTimeout(timeoutId);
    });

    messageDiv.addEventListener('mouseleave', () => {
        startDismissTimer();
    });

    // Start the dismiss timer
    startDismissTimer();

    return messageDiv;
}

// Utility function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, function(m) {
        return map[m];
    });
}

// Helper function to update display elements safely
function updateDisplayElement(id, value) {
    const element = document.getElementById(id);
    if (element) {
        element.textContent = value;
    }
}

// Update date and time
function updateDateTime() {
    const now = new Date();
    const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        hour12: false,
    };

    // Update if element exists
    const dateElement = document.querySelector(".text-xs.text-gray-500");
    if (dateElement && !dateElement.id) {
        dateElement.textContent = now.toLocaleDateString("id-ID", options);
    }
}

// Generate order number
function generateOrderNumber() {
    const now = new Date();
    const dateStr = now.toISOString().slice(0, 10).replace(/-/g, '');
    const timeStr = now.toTimeString().slice(0, 8).replace(/:/g, '');
    const randomStr = Math.random().toString(36).substr(2, 4).toUpperCase();
    return `${dateStr}-${timeStr}-${randomStr}`;
}

// Format currency
function formatCurrency(amount) {
    return `Rp ${amount.toLocaleString('id-ID')}`;
}

// Get current timestamp
function getCurrentTimestamp() {
    return new Date().toISOString();
}

// Validate order items
function validateOrderItems(items) {
    if (!items || !Array.isArray(items)) {
        return false;
    }
    
    if (items.length === 0) {
        return false;
    }
    
    return items.every(item => 
        item.productId && 
        item.productName && 
        item.amount > 0 && 
        item.totalHarga_jual > 0
    );
}

// Calculate totals helper
function calculateOrderTotals(items, discountPercent = 0, taxPercent = 0) {
    const subtotal = items.reduce((sum, item) => sum + item.totalHarga_jual, 0);
    const discountAmount = (subtotal * discountPercent) / 100;
    const subtotalAfterDiscount = subtotal - discountAmount;
    const taxAmount = (subtotalAfterDiscount * taxPercent) / 100;
    const grandTotal = subtotalAfterDiscount + taxAmount;
    const totalCostHarga_jual = items.reduce((sum, item) => sum + item.totalCostHarga_jual, 0);

    return {
        subtotal: Math.round(subtotal),
        discountAmount: Math.round(discountAmount),
        subtotalAfterDiscount: Math.round(subtotalAfterDiscount),
        taxAmount: Math.round(taxAmount),
        grandTotal: Math.round(grandTotal),
        totalCostHarga_jual: Math.round(totalCostHarga_jual)
    };
}

// Export utility functions for global access
window.showMessage = showMessage;
window.escapeHtml = escapeHtml;
window.updateDisplayElement = updateDisplayElement;
window.updateDateTime = updateDateTime;
window.generateOrderNumber = generateOrderNumber;
window.formatCurrency = formatCurrency;
window.getCurrentTimestamp = getCurrentTimestamp;
window.validateOrderItems = validateOrderItems;
window.calculateOrderTotals = calculateOrderTotals;