// order-management.js - Order Management Functions

// Ensure orderItems is initialized as an array
if (typeof window.orderItems === 'undefined') {
    window.orderItems = [];
}

// Update order display
function updateOrderDisplay() {
    const itemList = document.getElementById('item-list');
    if (!itemList) return;

    if (window.orderItems.length === 0) {
        itemList.innerHTML = '<p class="text-center text-gray-500 text-sm">No items in order</p>';
        return;
    }

    itemList.innerHTML = '';

    window.orderItems.forEach((item, index) => {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'flex flex-col border-b pb-3 mb-3';
        itemDiv.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex justify-between">
                        <span class="font-semibold text-sm">${escapeHtml(item.productName)} x${item.amount}</span>
                    </div>
                    <ul class="ml-2 text-gray-600 text-xs mt-1 space-y-0.5">
                        <li>Size: ${escapeHtml(item.customizations.size)}</li>
                        <li>Sugar: ${escapeHtml(item.customizations.sugar)}</li>
                        <li>Ice: ${escapeHtml(item.customizations.ice)}</li>
                        <li>Topping: ${escapeHtml(item.customizations.topping)}</li>
                        <li>Unit Price: Rp ${item.itemHarga_jual.toLocaleString('id-ID')}</li>
                        <li class="font-semibold text-green-600">Total: Rp ${item.totalHarga_jual.toLocaleString('id-ID')}</li>
                    </ul>
                </div>
                <div class="flex gap-2 ml-2">
                    <button type="button" onclick="editOrderItem(${index})"
                        class="text-blue-500 hover:text-blue-700 text-sm font-bold w-6 h-6 flex items-center justify-center"
                        title="Edit item">✎</button>
                    <button type="button" onclick="removeOrderItem(${index})"
                        class="text-red-500 hover:text-red-700 text-lg font-bold w-6 h-6 flex items-center justify-center"
                        title="Remove item">×</button>
                </div>
            </div>
        `;
        itemList.appendChild(itemDiv);
    });
}

// Update totals
function updateTotals() {
    const discountPercent = parseFloat(document.getElementById('discount-input')?.value) || 0;
    const taxPercent = parseFloat(document.getElementById('tax-input')?.value) || 0;

    // Calculate subtotal
    const subtotal = window.orderItems.reduce((sum, item) => sum + item.totalHarga_jual, 0);

    // Calculate discount
    const discountAmount = (subtotal * discountPercent) / 100;

    // Calculate subtotal after discount
    const subtotalAfterDiscount = subtotal - discountAmount;

    // Calculate tax
    const taxAmount = (subtotalAfterDiscount * taxPercent) / 100;

    // Calculate grand total
    const grandTotal = subtotalAfterDiscount + taxAmount;

    // Update display
    updateDisplayElement('subtotal', `Rp ${subtotal.toLocaleString('id-ID')}`);
    updateDisplayElement('discount-amount', `Rp ${discountAmount.toLocaleString('id-ID')}`);
    updateDisplayElement('tax-amount', `Rp ${taxAmount.toLocaleString('id-ID')}`);
    updateDisplayElement('grand-total', `Rp ${grandTotal.toLocaleString('id-ID')}`);
}

// Edit order item
function editOrderItem(index) {
    if (index < 0 || index >= window.orderItems.length) {
        showMessage('Item not found!', 'error');
        return;
    }

    const item = window.orderItems[index];

    // Show advanced edit modal with SweetAlert2
    if (typeof Swal !== 'undefined') {
        showAdvancedEditModal(item, index);
    } else {
        // Fallback to simple prompt
        showSimpleEditPrompt(item, index);
    }
}

// Advanced edit modal using SweetAlert2
function showAdvancedEditModal(item, index) {
    Swal.fire({
        title: `Edit ${item.productName}`,
        html: `
            <div class="text-left">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity:</label>
                    <input type="number" id="edit-amount" value="${item.amount}" min="1" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Size:</label>
                    <select id="edit-size" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="M" ${item.customizations.size === 'M' ? 'selected' : ''}>M</option>
                        <option value="L" ${item.customizations.size === 'L' ? 'selected' : ''}>L (+Rp 3,000)</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sugar:</label>
                    <select id="edit-sugar" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="25%" ${item.customizations.sugar === '25%' ? 'selected' : ''}>25%</option>
                        <option value="50%" ${item.customizations.sugar === '50%' ? 'selected' : ''}>50%</option>
                        <option value="75%" ${item.customizations.sugar === '75%' ? 'selected' : ''}>75%</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ice:</label>
                    <select id="edit-ice" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="25%" ${item.customizations.ice === '25%' ? 'selected' : ''}>25%</option>
                        <option value="50%" ${item.customizations.ice === '50%' ? 'selected' : ''}>50%</option>
                        <option value="75%" ${item.customizations.ice === '75%' ? 'selected' : ''}>75%</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Topping:</label>
                    <select id="edit-topping" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="No Topping" ${item.customizations.topping === 'No Topping' ? 'selected' : ''}>No Topping</option>
                        <option value="Susu Oat" ${item.customizations.topping === 'Susu Oat' ? 'selected' : ''}>Susu Oat (+Rp 5,000)</option>
                        <option value="Espresso" ${item.customizations.topping === 'Espresso' ? 'selected' : ''}>Espresso (+Rp 4,000)</option>
                    </select>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Update Item',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#005281',
        preConfirm: () => {
            const amount = parseInt(document.getElementById('edit-amount').value);
            const size = document.getElementById('edit-size').value;
            const sugar = document.getElementById('edit-sugar').value;
            const ice = document.getElementById('edit-ice').value;
            const topping = document.getElementById('edit-topping').value;

            if (!amount || amount < 1) {
                Swal.showValidationMessage('Please enter a valid quantity');
                return false;
            }

            return {
                amount,
                size,
                sugar,
                ice,
                topping
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updateOrderItemData(index, result.value);
        }
    });
}

// Simple edit prompt fallback
function showSimpleEditPrompt(item, index) {
    const newAmount = prompt(`Edit quantity for ${item.productName}:`, item.amount);

    if (newAmount && parseInt(newAmount) > 0) {
        const amount = parseInt(newAmount);
        updateOrderItemData(index, {
            amount,
            size: item.customizations.size,
            sugar: item.customizations.sugar,
            ice: item.customizations.ice,
            topping: item.customizations.topping
        });
    }
}

// Update order item data
function updateOrderItemData(index, data) {
    const item = window.orderItems[index];

    // Update customizations
    item.customizations = {
        size: data.size,
        sugar: data.sugar,
        ice: data.ice,
        topping: data.topping
    };

    // Get base harga_jual from product form (more reliable)
    // Try to find the product form to get base price
    const productForm = document.querySelector(`form[data-product-id="${item.productId}"]`);
    let baseHarga_jual = parseInt(productForm?.getAttribute('data-base-harga_jual')) || (parseInt(item.itemHarga_jual / item.amount));
    
    // Recalculate item price
    let newItemHarga_jual = baseHarga_jual;

    // Add size modifier
    if (data.size === 'L') {
        newItemHarga_jual += 3000;
    }

    // Add topping modifier
    if (data.topping === 'Susu Oat') {
        newItemHarga_jual += 5000;
    } else if (data.topping === 'Espresso') {
        newItemHarga_jual += 4000;
    }

    // Update item data
    item.amount = data.amount;
    item.itemHarga_jual = newItemHarga_jual;
    item.totalHarga_jual = newItemHarga_jual * data.amount;
    item.totalCostHarga_jual = item.costHarga_jual * data.amount;

    updateOrderDisplay();
    updateTotals();
    showMessage('Item updated successfully!', 'success');
}

// Remove order item
function removeOrderItem(index) {
    if (index < 0 || index >= window.orderItems.length) {
        showMessage('Item not found!', 'error');
        return;
    }

    const item = window.orderItems[index];

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Remove Item?',
            text: `Remove ${item.productName} from order?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                performRemoveItem(index);
            }
        });
    } else {
        if (confirm(`Remove ${item.productName} from order?`)) {
            performRemoveItem(index);
        }
    }
}

// Perform item removal
function performRemoveItem(index) {
    const item = window.orderItems[index];
    window.orderItems.splice(index, 1);
    updateOrderDisplay();
    updateTotals();
    showMessage(`${item.productName} removed from order!`, 'success');
}

// Clear order
function clearOrder() {
    if (window.orderItems.length === 0) {
        showMessage('Order is already empty!', 'warning');
        return;
    }

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Clear Order?',
            text: "This will remove all items from the current order.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, clear it!'
        }).then((result) => {
            if (result.isConfirmed) {
                performClearOrder();
            }
        });
    } else {
        if (confirm('Clear entire order?')) {
            performClearOrder();
        }
    }
}

// Perform order clearing
function performClearOrder() {
    window.orderItems = [];
    updateOrderDisplay();
    updateTotals();
    showMessage('Order cleared successfully!', 'success');
}

// Export functions for global access
window.updateOrderDisplay = updateOrderDisplay;
window.updateTotals = updateTotals;
window.editOrderItem = editOrderItem;
window.removeOrderItem = removeOrderItem;
window.clearOrder = clearOrder;
