// payment-system.js - Payment System Functions

        // Calculate grand total
function calculateGrandTotal() {
    const discountPercent =
        parseFloat(document.getElementById("discount-input")?.value) || 0;
    const taxPercent =
        parseFloat(document.getElementById("tax-input")?.value) || 0;

    const subtotal = window.orderItems.reduce(
        (sum, item) => sum + (item.totalHarga_jual || 0),
        0
    );
    const discountAmount = (subtotal * discountPercent) / 100;
    const subtotalAfterDiscount = subtotal - discountAmount;
    const taxAmount = (subtotalAfterDiscount * taxPercent) / 100;

    return Math.round(subtotalAfterDiscount + taxAmount);
}

// Process cash payment
function processPayment() {
    if (window.orderItems.length === 0) {
        showMessage("No items in order!", "warning");
        return;
    }

    const grandTotal = calculateGrandTotal();
    const totalCostHarga_jual = window.orderItems.reduce(
        (sum, item) => sum + item.totalCostHarga_jual,
        0
    );

    // Show payment confirmation with SweetAlert2
    if (typeof Swal !== "undefined") {
        showCashPaymentModal(grandTotal, totalCostHarga_jual);
    } else {
        // Fallback confirmation
        if (
            confirm(
                `Process cash payment of Rp ${grandTotal.toLocaleString(
                    "id-ID"
                )}?`
            )
        ) {
            submitCashPayment(grandTotal, totalCostHarga_jual);
        }
    }
}

// Show cash payment modal
function showCashPaymentModal(grandTotal, totalCostHarga_jual) {
    Swal.fire({
        title: "Cash Payment",
        html: `
            <div class="text-left">
                <div class="bg-gray-100 p-4 rounded-lg mb-4">
                    <div class="text-center">
                        <h3 class="text-lg font-semibold mb-2">Total Amount</h3>
                        <p class="text-2xl font-bold text-green-600">Rp ${grandTotal.toLocaleString(
                            "id-ID"
                        )}</p>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cash Received:</label>
                    <input type="number" id="cash-received" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="Enter cash amount" min="${grandTotal}" step="1000">
                </div>
                <div id="change-display" class="hidden bg-green-50 p-3 rounded-lg">
                    <div class="flex justify-between">
                        <span class="font-medium">Change:</span>
                        <span id="change-amount" class="font-bold text-green-600">Rp 0</span>
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: "Process Payment",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#16a34a",
        didOpen: () => {
            const cashInput = document.getElementById("cash-received");
            const changeDisplay = document.getElementById("change-display");
            const changeAmount = document.getElementById("change-amount");

            cashInput.addEventListener("input", function () {
                const cashReceived = parseFloat(this.value) || 0;
                if (cashReceived >= grandTotal) {
                    const change = cashReceived - grandTotal;
                    changeAmount.textContent = `Rp ${change.toLocaleString(
                        "id-ID"
                    )}`;
                    changeDisplay.classList.remove("hidden");
                } else {
                    changeDisplay.classList.add("hidden");
                }
            });

            // Focus on input
            cashInput.focus();
        },
        preConfirm: () => {
            const cashReceived = parseFloat(
                document.getElementById("cash-received").value
            );

            if (!cashReceived || cashReceived < grandTotal) {
                Swal.showValidationMessage(
                    `Cash received must be at least Rp ${grandTotal.toLocaleString(
                        "id-ID"
                    )}`
                );
                return false;
            }

            return {
                cashReceived,
                change: cashReceived - grandTotal,
            };
        },
    }).then((result) => {
        if (result.isConfirmed) {
            const { cashReceived, change } = result.value;
            submitCashPayment(grandTotal, totalCostHarga_jual, cashReceived, change);
        }
    });
}

// Process QRIS payment
function processQRISPayment() {
    if (window.orderItems.length === 0) {
        showMessage("No items in order!", "warning");
        return;
    }

    const grandTotal = calculateGrandTotal();
    const totalCostHarga_jual = window.orderItems.reduce(
        (sum, item) => sum + item.totalCostHarga_jual,
        0
    );

    // Show QRIS payment confirmation
    if (typeof Swal !== "undefined") {
        showQRISPaymentModal(grandTotal, totalCostHarga_jual);
    } else {
        // Fallback confirmation
        if (
            confirm(
                `Process QRIS payment of Rp ${grandTotal.toLocaleString(
                    "id-ID"
                )}?`
            )
        ) {
            submitQRISPayment(grandTotal, totalCostHarga_jual);
        }
    }
}

// Show QRIS payment modal
function showQRISPaymentModal(grandTotal, totalCostHarga_jual) {
    Swal.fire({
        title: "QRIS Payment",
        html: `
            <div class="text-center">
                <div class="bg-gray-100 p-4 rounded-lg mb-4">
                    <h3 class="text-lg font-semibold mb-2">Total Amount</h3>
                    <p class="text-2xl font-bold text-blue-600">Rp ${grandTotal.toLocaleString(
                        "id-ID"
                    )}</p>
                </div>
                <div class="bg-white border-2 border-dashed border-gray-300 rounded-lg p-8 mb-4">
                    <div class="text-6xl mb-4">📱</div>
                    <p class="text-gray-600 mb-2">Show this to customer</p>
                    <p class="text-sm text-gray-500">Scan QR code to pay</p>
                </div>
                <div class="text-sm text-gray-600">
                    <p>Please confirm payment has been received</p>
                    <p class="font-medium mt-2">Check your payment app for confirmation</p>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: "Payment Confirmed",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#2563eb",
        cancelButtonColor: "#6b7280",
    }).then((result) => {
        if (result.isConfirmed) {
            submitQRISPayment(grandTotal, totalCostHarga_jual);
        }
    });
}

// Submit cash payment
function submitCashPayment(
    grandTotal,
    totalCostHarga_jual,
    cashReceived = null,
    change = null
) {
    try {
        // Show processing message
        showMessage("Processing cash payment...", "info");

        // Prepare transaction data
        const transactionData = {
            subtotal: grandTotal,
            total_cost_harga_jual: totalCostHarga_jual,
            name_user:
                document.getElementById("transaction_name_user")?.value ||
                "Unknown User",
            payment_method: "cash",
            timestamp: getCurrentTimestamp(),
            cash_received: cashReceived,
            change_amount: change,
        };

        // Prepare products array from orderItems
        const products = window.orderItems.map(item => ({
            product_id: item.productId,
            qty: item.amount
        }));
        
        transactionData.products = products;

        // Update hidden form fields in transactionForm
        const form = document.getElementById("transactionForm");
        if (!form) {
            console.error("Transaction form not found!");
            showMessage("Form error. Please refresh the page.", "error");
            return;
        }

        // Update form fields
        updateTransactionForm("transactionForm", transactionData);
        
        // Clear existing product inputs to avoid duplicates
        form.querySelectorAll('input[name^="products["]').forEach(input => input.remove());
        
        // Add products as hidden inputs
        products.forEach((product, index) => {
            const productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = `products[${index}][product_id]`;
            productIdInput.value = product.product_id;
            form.appendChild(productIdInput);
            
            const qtyInput = document.createElement('input');
            qtyInput.type = 'hidden';
            qtyInput.name = `products[${index}][qty]`;
            qtyInput.value = product.qty;
            form.appendChild(qtyInput);
        });
        
        form.submit();
    } catch (error) {
        console.error("Error processing cash payment:", error);
        showMessage("Error processing payment. Please try again.", "error");
    }
}

// Submit QRIS payment
function submitQRISPayment(grandTotal, totalCostHarga_jual) {
    try {
        // Show processing message
        showMessage("Processing QRIS payment...", "info");

        // Prepare transaction data
        const transactionData = {
            subtotal: grandTotal,
            total_cost_harga_jual: totalCostHarga_jual,
            name_user:
                document.getElementById("transaction_qris_name_user")?.value ||
                "Unknown User",
            payment_method: "qris",
            timestamp: getCurrentTimestamp(),
        };

        // Prepare products array from orderItems
        const products = window.orderItems.map(item => ({
            product_id: item.productId,
            qty: item.amount
        }));
        
        transactionData.products = products;

        // Update hidden form fields
        updateTransactionForm("transactionQRISForm", transactionData);

        // Submit the form
        const form = document.getElementById("transactionQRISForm");
        if (!form) {
            console.error("QRIS transaction form not found!");
            showMessage("Form error. Please refresh the page.", "error");
            return;
        }

        // Clear existing product inputs to avoid duplicates
        form.querySelectorAll('input[name^="products["]').forEach(input => input.remove());
        
        // Add products as hidden inputs
        products.forEach((product, index) => {
            const productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = `products[${index}][product_id]`;
            productIdInput.value = product.product_id;
            form.appendChild(productIdInput);
            
            const qtyInput = document.createElement('input');
            qtyInput.type = 'hidden';
            qtyInput.name = `products[${index}][qty]`;
            qtyInput.value = product.qty;
            form.appendChild(qtyInput);
        });
        
        form.submit();
    } catch (error) {
        console.error("Error processing QRIS payment:", error);
        showMessage("Error processing payment. Please try again.", "error");
    }
}

// Update transaction form fields
function updateTransactionForm(formId, data) {
    const form = document.getElementById(formId);
    if (!form) return;

    // Update form fields
    Object.keys(data).forEach((key) => {
        const input = form.querySelector(`input[name="${key}"]`);
        if (input) {
            input.value = data[key];
        }
    });
}

// Handle successful payment (fallback when form submission isn't available)
function handlePaymentSuccess(paymentMethod, grandTotal, totalCostHarga_jual) {
    // Show success message
    showMessage(
        `Payment successful! Amount: Rp ${grandTotal.toLocaleString("id-ID")}`,
        "success",
        5000
    );

    // Generate and print receipt automatically
    generateAndPrintReceipt(paymentMethod, grandTotal);

    // Clear the order after successful payment
    setTimeout(() => {
        performClearOrder();
    }, 1000);
}

// Generate and print receipt after payment
function generateAndPrintReceipt(paymentMethod, totalAmount) {
    if (window.orderItems.length === 0) {
        console.warn("No items to print receipt for");
        return;
    }

    // Store order data before clearing
    const orderData = {
        items: [...window.orderItems],
        totals: {
            subtotal: window.orderItems.reduce(
                (sum, item) => sum + item.totalHarga_jual,
                0
            ),
            discount:
                parseFloat(document.getElementById("discount-input")?.value) ||
                0,
            tax: parseFloat(document.getElementById("tax-input")?.value) || 0,
        },
        paymentMethod: paymentMethod,
        totalAmount: totalAmount,
        timestamp: new Date(),
    };

    // Generate receipt content
    generateReceiptFromOrderData(orderData);

    // Auto print receipt
    setTimeout(() => {
        printReceiptDirectly();
    }, 500);
}

// Process Debit payment
function processDebitPayment() {
    if (window.orderItems.length === 0) {
        showMessage("No items in order!", "warning");
        return;
    }

    const grandTotal = calculateGrandTotal();
    const totalCostHarga_jual = window.orderItems.reduce(
        (sum, item) => sum + item.totalCostHarga_jual,
        0
    );

    // Show debit payment confirmation
    if (typeof Swal !== "undefined") {
        showDebitPaymentModal(grandTotal, totalCostHarga_jual);
    } else {
        // Fallback confirmation
        if (
            confirm(
                `Process debit payment of Rp ${grandTotal.toLocaleString(
                    "id-ID"
                )}?`
            )
        ) {
            submitDebitPayment(grandTotal, totalCostHarga_jual);
        }
    }
}

// Show debit payment modal
function showDebitPaymentModal(grandTotal, totalCostHarga_jual) {
    Swal.fire({
        title: "Debit Card Payment",
        html: `
            <div class="text-center">
                <div class="bg-gray-100 p-4 rounded-lg mb-4">
                    <h3 class="text-lg font-semibold mb-2">Total Amount</h3>
                    <p class="text-2xl font-bold text-purple-600">Rp ${grandTotal.toLocaleString(
                        "id-ID"
                    )}</p>
                </div>
                <div class="bg-white border-2 border-dashed border-gray-300 rounded-lg p-8 mb-4">
                    <div class="text-6xl mb-4">💳</div>
                    <p class="text-gray-600 mb-2">Card Payment Terminal</p>
                    <p class="text-sm text-gray-500">Please insert or tap your debit card</p>
                </div>
                <div class="text-sm text-gray-600">
                    <p>Amount: Rp ${grandTotal.toLocaleString("id-ID")}</p>
                    <p class="font-medium mt-2">Confirm payment has been processed</p>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: "Payment Confirmed",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#9333ea",
        cancelButtonColor: "#6b7280",
    }).then((result) => {
        if (result.isConfirmed) {
            submitDebitPayment(grandTotal, totalCostHarga_jual);
        }
    });
}

// Submit Debit payment
function submitDebitPayment(grandTotal, totalCostHarga_jual) {
    try {
        // Show processing message
        showMessage("Processing debit payment...", "info");

        // Prepare transaction data
        const transactionData = {
            subtotal: grandTotal,
            total_cost_harga_jual: totalCostHarga_jual,
            name_user:
                document.getElementById("transaction_debit_name_user")?.value ||
                "Unknown User",
            payment_method: "debit",
            timestamp: getCurrentTimestamp(),
        };

        // Prepare products array from orderItems
        const products = window.orderItems.map(item => ({
            product_id: item.productId,
            qty: item.amount
        }));
        
        transactionData.products = products;

        // Update hidden form fields
        updateTransactionForm("transactionDebitForm", transactionData);

        // Submit the form
        const form = document.getElementById("transactionDebitForm");
        if (!form) {
            console.error("Debit transaction form not found!");
            showMessage("Form error. Please refresh the page.", "error");
            return;
        }

        // Clear existing product inputs to avoid duplicates
        form.querySelectorAll('input[name^="products["]').forEach(input => input.remove());
        
        // Add products as hidden inputs
        products.forEach((product, index) => {
            const productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = `products[${index}][product_id]`;
            productIdInput.value = product.product_id;
            form.appendChild(productIdInput);
            
            const qtyInput = document.createElement('input');
            qtyInput.type = 'hidden';
            qtyInput.name = `products[${index}][qty]`;
            qtyInput.value = product.qty;
            form.appendChild(qtyInput);
        });
        
        form.submit();
    } catch (error) {
        console.error("Error processing debit payment:", error);
        showMessage("Error processing payment. Please try again.", "error");
    }
}

// Export functions for global access
window.processPayment = processPayment;
window.processQRISPayment = processQRISPayment;
window.processDebitPayment = processDebitPayment;
window.calculateGrandTotal = calculateGrandTotal;
window.handlePaymentSuccess = handlePaymentSuccess;
