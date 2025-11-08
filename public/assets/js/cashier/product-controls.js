// product-controls.js - Product Control Functions

// Amount control functions
function decrementAmount(button) {
    const input = button.parentElement.querySelector('input[name="amount"]');
    if (!input) return;

    const currentValue = parseInt(input.value) || 1;
    if (currentValue > 1) {
        input.value = currentValue - 1;

        // Trigger harga_jual update if in detailed view
        if (window.currentViewMode === "detailed") {
            updateDetailedHarga_jual(input);
        }
    }
}

function incrementAmount(button) {
    const input = button.parentElement.querySelector('input[name="amount"]');
    if (!input) return;

    const currentValue = parseInt(input.value) || 1;
    input.value = currentValue + 1;

    // Trigger harga_jual update if in detailed view
    if (window.currentViewMode === "detailed") {
        updateDetailedHarga_jual(input);
    }
}

// harga_jual update functions
function updateDetailedHarga_jual(element) {
    const form = element.closest("form");
    if (!form) return;

    const productId = form.getAttribute("data-product-id");
    const baseHarga_jual = parseInt(form.getAttribute("data-base-harga_jual")) || 0;

    // Get current selections
    const amount =
        parseInt(form.querySelector('input[name="amount"]')?.value) || 1;
    const sizeInput = form.querySelector(
        `input[name="size_${productId}"]:checked`
    );
    const toppingInput = form.querySelector(
        `input[name="topping_${productId}"]:checked`
    );

    let totalHarga_jual = baseHarga_jual;

    // Add size modifier
    if (sizeInput && sizeInput.getAttribute("data-harga_jual-modifier")) {
        totalHarga_jual +=
            parseInt(sizeInput.getAttribute("data-harga_jual-modifier")) || 0;
    }

    // Add topping modifier
    if (toppingInput && toppingInput.getAttribute("data-harga_jual-modifier")) {
        totalHarga_jual +=
            parseInt(toppingInput.getAttribute("data-harga_jual-modifier")) || 0;
    }

    // Multiply by amount
    totalHarga_jual *= amount;

    // Update display
    const harga_jualDisplay = document.getElementById(`total-harga-${productId}`);
    if (harga_jualDisplay) {
        harga_jualDisplay.textContent = `Rp ${totalHarga_jual.toLocaleString("id-ID")}`;
    }

    // Update form data
    form.setAttribute("data-item-harga_jual", totalHarga_jual);

    console.log(
        `harga_jual updated for product ${productId}: Rp ${totalHarga_jual.toLocaleString(
            "id-ID"
        )}`
    );
}

// Add to order function
function addToOrder(event) {
    event.preventDefault();

    const form = event.target;
    const productId = form.getAttribute("data-product-id");
    const productName = form.getAttribute("data-product-name");
    const costHarga_jual = parseInt(form.getAttribute("data-cost-harga_jual")) || 0;
    const amount =
        parseInt(form.querySelector('input[name="amount"]')?.value) || 1;

    if (!productId || !productName) {
        showMessage("Product information missing!", "error");
        return;
    }

    // Get customizations
    let customizations = {
        size: "M",
        sugar: "50%",
        ice: "50%",
        topping: "No Topping",
    };

    // For detailed view, get all selections
    if (window.currentViewMode === "detailed") {
        const sizeInput = form.querySelector(
            `input[name="size_${productId}"]:checked`
        );
        const sugarInput = form.querySelector(
            `input[name="sugar_${productId}"]:checked`
        );
        const iceInput = form.querySelector(
            `input[name="ice_${productId}"]:checked`
        );
        const toppingInput = form.querySelector(
            `input[name="topping_${productId}"]:checked`
        );

        if (sizeInput) customizations.size = sizeInput.value;
        if (sugarInput) customizations.sugar = sugarInput.value + "%";
        if (iceInput) customizations.ice = iceInput.value + "%";
        if (toppingInput) customizations.topping = toppingInput.value;
    }

    // Calculate item harga_jual
    const baseharga_jual = parseInt(form.getAttribute("data-base-harga_jual")) || 0;
    let itemharga_jual = baseharga_jual;

    // Add size modifier
    if (customizations.size === "L") {
        itemharga_jual += 3000;
    }

    // Add topping modifier
    if (customizations.topping === "Susu Oat") {
        itemharga_jual += 5000;
    } else if (customizations.topping === "Espresso") {
        itemharga_jual += 4000;
    }

    const totalharga_jual = itemharga_jual * amount;

    // Create order item (with consistent casing)
    const orderItem = {
        id: window.orderCounter++,
        productId: productId,
        productName: productName,
        amount: amount,
        itemHarga_jual: itemharga_jual,
        totalHarga_jual: totalharga_jual,
        costHarga_jual: costHarga_jual,
        totalCostHarga_jual: costHarga_jual * amount,
        customizations: customizations,
    };

    // Add to order
    if (!window.orderItems) {
        window.orderItems = [];
    }
    window.orderItems.push(orderItem);

    // Update display
    updateOrderDisplay();
    updateTotals();

    // Reset form to default values
    resetForm(form);

    // Show success message
    showMessage(`${productName} added to order!`, "success");

    console.log("Item added to order:", orderItem);
}

// Reset form to default state
function resetForm(form) {
    if (!form) return;

    // Reset amount
    const amountInput = form.querySelector('input[name="amount"]');
    if (amountInput) {
        amountInput.value = "1";
    }

    // Reset to default selections in detailed view
    if (window.currentViewMode === "detailed") {
        const productId = form.getAttribute("data-product-id");

        // Reset size to M
        const sizeM = form.querySelector(
            `input[name="size_${productId}"][value="M"]`
        );
        if (sizeM) sizeM.checked = true;

        // Reset sugar to 50%
        const sugar50 = form.querySelector(
            `input[name="sugar_${productId}"][value="50"]`
        );
        if (sugar50) sugar50.checked = true;

        // Reset ice to 50%
        const ice50 = form.querySelector(
            `input[name="ice_${productId}"][value="50"]`
        );
        if (ice50) ice50.checked = true;

        // Reset topping to No Topping
        const noTopping = form.querySelector(
            `input[name="topping_${productId}"][value="No Topping"]`
        );
        if (noTopping) noTopping.checked = true;

        // Update harga_jual display
        updateDetailedHarga_jual(amountInput);
    }
}

// Export functions for global access
window.decrementAmount = decrementAmount;
window.incrementAmount = incrementAmount;
window.updateDetailedHarga_jual = updateDetailedHarga_jual;
window.updateDetailedharga_jual = updateDetailedHarga_jual; // Legacy compatibility
window.addToOrder = addToOrder;
// Show message function
function showMessage(message, type = "info") {
    const messageContainer = document.getElementById("message-container");
    if (!messageContainer) return;

    const messageElement = document.createElement("div");
    messageElement.className = `alert alert-${type}`;
    messageElement.textContent = message;

    // Clear previous messages
    messageContainer.innerHTML = "";
    messageContainer.appendChild(messageElement);

    // Auto-hide after 3 seconds
    setTimeout(() => {
        messageContainer.removeChild(messageElement);
    }, 3000);
}